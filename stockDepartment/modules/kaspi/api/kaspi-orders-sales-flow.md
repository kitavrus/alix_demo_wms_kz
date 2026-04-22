# Kaspi sales flow: заказ → отгрузка → 1С

Спецификация sales-flow по диаграмме `Alix/Alix Avien 1C nomadex integration (sales).pdf`:

```
Kaspi (клиент оплатил) → APPROVED_BY_BANK
  → poll cron → EcommerceOutbound (наш WMS) + резерв PP1
  → acceptOrder (ACCEPTED_BY_MERCHANT)
  → сборка на складе (наш pick-flow)
  → transfer-to-courier (assembleOrder: ASSEMBLE + numberOfSpace)
  → печать этикетки (скачивание PDF по waybill URL из заказа)
  → Kaspi курьер забирает, state=KASPI_DELIVERY
  → Kaspi завершает, status=COMPLETED
  → sync-order-statuses (ставит one_c_status=PENDING)
  → sync-completed-to-1c → Alix1CApiService::postSale (сейчас заглушка)
```

## Статусы Kaspi (q3209, q3212 и др.)

| Код | Когда |
|---|---|
| `APPROVED_BY_BANK`    | Банк одобрил оплату. Заказ ждёт принятия продавцом. |
| `ACCEPTED_BY_MERCHANT`| Мы приняли заказ (`acceptOrder`). |
| `ASSEMBLE`            | Мы сформировали накладную, передаём Kaspi Доставке (`assembleOrder`). |
| `KASPI_DELIVERY`      | Курьер забрал заказ и везёт клиенту (обновляется Kaspi). |
| `COMPLETED`           | Выдан покупателю. Для Kaspi Delivery — автоматически; для own-delivery/pickup — через двухэтапный `sendCompletionCode` + `confirmCompletionWithCode`. |
| `CANCELLED`           | Отменён (нами или Kaspi). |
| `CANCELLING`          | В процессе отмены. |
| `KASPI_DELIVERY_RETURN_REQUESTED` / `RETURNED` / `ARRIVED_BACKWARD` | Поток возврата (см. `Alix/...(return).pdf`). |
| `ARRIVED`             | Предзаказ прибыл на склад. |

## Связанные эндпоинты Kaspi v2

Все запросы — `application/vnd.api+json` + `X-Auth-Token`. Базовый URL: `https://kaspi.kz/shop/api/v2/`.

| Шаг | Метод | URL | Наш метод |
|---|---|---|---|
| Список заказов       | `GET`  | `/orders?filter[orders][status]=…&creationDate[$ge]=…&page[number]=…&page[size]=…` | `KaspiAPIService::getOrdersPage` |
| Заказ по id          | `GET`  | `/orders/{id}` | `getOrderById` |
| Позиции заказа       | `GET`  | `/orders/{id}/entries` | `getOrderEntries` |
| Принять заказ        | `POST` | `/orders`  body: `{data:{type:orders,id,attributes:{status:ACCEPTED_BY_MERCHANT}}}` | `acceptOrder` |
| ASSEMBLE (накладная) | `POST` | `/orders`  body: `{data:{type:orders,id,attributes:{status:ASSEMBLE,numberOfSpace:1}}}` | `assembleOrder` |
| Скачать PDF этикетки | `GET`  | URL из поля `waybill` в ответе `GET /orders/{id}` (ссылка уже подписная — без X-Auth-Token) | `getShippingLabel` |
| Отмена               | `POST` | `/orders`  body: `{..., attributes:{status:CANCELLED, cancellationReason:…}}` | `cancelOrder` |
| Двухэтапный COMPLETED| `POST` | `/orders` + заголовки `X-Send-Code: true`, `X-Security-Code: <empty/code>` | `sendCompletionCode` + `confirmCompletionWithCode` |

Ограничения Kaspi:
- `page[size]` — максимум **100**.
- `creationDate` — в миллисекундах; явного лимита окна в доке нет (наш poll — 6 ч).
- `cancellationComment` ≤ 1000 символов.

## Poll-цикл (OrderImportService)

Файл: [`services/OrderImportService.php`](../services/OrderImportService.php).

Параметры запроса:

```
filter[orders][status]            = APPROVED_BY_BANK
filter[orders][state]             = NEW
filter[orders][creationDate][$ge] = (now - pollWindowHours) * 1000
filter[orders][creationDate][$le] = now * 1000
page[number] = 0
page[size]   = 100
```

Для каждого заказа:

1. Идемпотентность — `EcommerceOutbound::findOne(external_order_number = kaspiOrderId)`. Если есть — skip.
2. `getOrderEntries(kaspiOrderId)` — пишем `EcommerceOutboundItem` (sku = `merchantProductCode` Kaspi, без маппинга).
3. Резерв стока в `ecommerce_stock`: для каждого sku берём `qty` строк со `status_availability = YES`, ставим `status_availability = RESERVED`, `outbound_id = <id>`, `kaspi_order_status = APPROVED_BY_BANK`.
4. Если стока не хватает — `ROLLBACK` транзакции + `cancelOrder(MERCHANT_OUT_OF_STOCK)`.
5. Успех → `acceptOrder` → `external_kaspi_status = ACCEPTED_BY_MERCHANT`.

Настраиваемые параметры модуля (`stockDepartment/modules/kaspi/kaspi.php`):

| Поле модуля | Назначение | Default |
|---|---|---|
| `orderPollWindowHours` | Окно poll по `creationDate` | `6` |
| `kaspiClientId`        | `client_id` для создаваемых EcommerceOutbound | `0` |

## Передача курьеру и этикетка

Контроллер: `POST /kaspi/api/v1/orders/<orderId>/transfer-to-courier`.

Поле модели в теле запроса (опционально): `{ "numberOfSpace": 1 }`.

Внутри `KaspiService::transferToCourier()`:
1. `api->assembleOrder(orderId, numberOfSpace)` — POST `/orders` с `status=ASSEMBLE + numberOfSpace` (q3210).
2. `api->getOrderById(orderId)` — читаем `waybill` (URL) и `waybillNumber` из атрибутов.
3. Ответ:
```json
{
  "status": "OK",
  "order_id": "…",
  "order_status": "ASSEMBLE",
  "number_of_space": 1,
  "waybill_url": "https://…pdf",
  "waybill_number": "WB-…"
}
```

Этикетка — `GET /kaspi/api/v1/orders/<orderId>/label`:
- `api->getOrderById(orderId)` → берём поле `waybill`;
- скачиваем PDF простым `GET` по URL (без `X-Auth-Token` — ссылка уже авторизована со стороны Kaspi);
- ответ — `application/pdf` inline.

Если `waybill` ещё пустой — значит заказ ещё не переведён в `ASSEMBLE`: метод возвращает 409.

## Синхронизация статусов (OrderStatusSyncService)

Файл: [`services/OrderStatusSyncService.php`](../services/OrderStatusSyncService.php).

Выбирает активные `EcommerceOutbound` (`external_kaspi_status ∈ {APPROVED_BY_BANK, ACCEPTED_BY_MERCHANT, ASSEMBLE, KASPI_DELIVERY, CANCELLING}`), по каждому делает `getOrderById`:

- `CANCELLING` / `CANCELLED` → снять резерв стока (`RESERVED → YES`, `outbound_id = 0`).
- `COMPLETED` → `one_c_status = PENDING`.
- всегда — обновить `external_kaspi_status` в нашей БД.

## Передача в 1С (OneCSalesSyncService)

Файл: [`services/OneCSalesSyncService.php`](../services/OneCSalesSyncService.php).

Выбирает `EcommerceOutbound` c `one_c_status = PENDING` и `sent_to_1c_at IS NULL`, собирает payload и вызывает `Alix1CApiService::postSale($payload)`.

**Текущее состояние — заглушка**: `postSale()` логирует payload в категорию `alix.1c` и возвращает `status=OK`, `message='stub: 1C endpoint not yet configured'`. Когда 1С отдаст спецификацию — подменяется только тело метода, контракт сервиса не меняется.

Формат payload:

```json
{
  "order_number":   "KASPI-ord-123",
  "kaspi_order_id": "ord-123",
  "completed_at":   1710000000,
  "customer":       { "name": "Иван Иванов", "phone": "77001234567" },
  "items": [
    { "sku": "SKU-1", "name": "...", "barcode": "...", "quantity": 1, "price": 19990.0 }
  ],
  "total_price": 19990.0
}
```

После успешного ответа `status=OK` → `one_c_status=SENT`, `sent_to_1c_at = now`. Ошибка → `one_c_status=ERROR`, `one_c_response = <ответ/exception>`.

## Cron

```
*/15 * * * *  php yii cron/kaspi-poll-orders
*/15 * * * *  php yii cron/kaspi-sync-order-statuses
*/30 * * * *  php yii cron/kaspi-sync-completed-to-1c
```

## Миграция

`m260417_100000_add_kaspi_sales_fields_to_ecommerce_outbound` добавляет в `ecommerce_outbound`:
- `external_kaspi_status` VARCHAR(32) — последний известный статус Kaspi;
- `sent_to_1c_at` INT NULL — момент успешной передачи в 1С;
- `one_c_status` VARCHAR(32) — `PENDING` / `SENT` / `ERROR`;
- `one_c_response` TEXT — тело ответа 1С или exception.

Индексы: `external_order_number`, `external_kaspi_status`, `one_c_status`.

## Открытые вопросы / TODO

- Спецификация 1С sales endpoint (URL, auth, формат) — пока `postSale` заглушка.
- Pagination по нескольким страницам в `OrderImportService::pollAndImportNew` (сейчас только page 0, `page[size]=100`). Для нормального траффика хватает; если прорвётся >100 новых заказов за окно — добавим цикл.
- `KaspiMockFactory` не эмулирует переходы статусов — мок-сервер всегда возвращает один и тот же заказ. Для интеграционного тестирования sales-flow потребуется расширить фабрику или тестировать на реальном sandbox.
