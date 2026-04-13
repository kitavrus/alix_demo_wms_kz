# Модуль Kaspi

Интеграция с [Kaspi Partner API](https://guide.kaspi.kz/partner/ru/shop/api/):

| В коде / доке | Официальный гид |
|---|---|
| **products** (import, classification) | [Товары — API](https://guide.kaspi.kz/partner/ru/shop/api/goods) |
| **products** (прайс-лист) | [Товары — общее](https://guide.kaspi.kz/partner/ru/shop/goods/general) |
| **orders** | [Заказы](https://guide.kaspi.kz/partner/ru/shop/api/orders) |
| **refund** | [Возврат](https://guide.kaspi.kz/partner/ru/shop/refund) |

---

## Структура модуля

```
stockDepartment/modules/kaspi/
├── README.md                             # этот файл
├── kaspi.php                             # Yii-модуль, DI-регистрация сервисов
├── api/
│   ├── kaspi-price-update.md             # спецификация POST /price-update
│   └── kaspi-stock-update.md             # спецификация POST /stock-update
├── constants/
│   └── KaspiConstants.php                # базовые URL, пути эндпоинтов, дефолты
├── controllers/api/v1/
│   └── KaspiController.php               # входящие HTTP-эндпоинты приложения
├── dto/
│   ├── AttributeDto.php
│   ├── CustomerDto.php
│   ├── KaspiOrderListPayload.php
│   ├── OrderDto.php
│   ├── PartialReturnRequestDto.php       # вход для /partial-return
│   ├── PriceUpdateRequestDto.php         # вход для /price-update
│   ├── StockDto.php
│   └── StockUpdateRequestDto.php         # вход для /stock-update
├── enums/
│   ├── DeliveryMode.php
│   ├── OrderStatus.php
│   ├── PaymentMode.php
│   └── StateOrder.php
├── exceptions/
│   └── KaspiApiException.php
├── models/
│   ├── KaspiPriceHistory.php             # AR для kaspi_price_history
│   └── KaspiStockHistory.php             # AR для kaspi_stock_history
├── price-list/
│   └── kaspi-price-list.xlsx             # сгенерированный прайс (цены + остатки)
├── services/
│   ├── KaspiAPIService.php               # низкоуровневые запросы к Kaspi (mock / live)
│   ├── KaspiJsonApiSerializer.php
│   ├── KaspiOrderHydrator.php            # JSON:API → OrderDto
│   ├── KaspiService.php                  # сценарии контроллера (orders, import, classification, priceUpdate, stockUpdate, returns, label)
│   ├── OrderReturnService.php            # сценарии возврата (A: cancel-return-to-stock, B: partial-return)
│   ├── PriceListService.php              # сборка и запись Excel (прайс-лист для кабинета, с override остатков)
│   ├── PriceService.php                  # применение и отложенная активация цен
│   ├── StockHistoryService.php           # применение и отложенная активация override остатков
│   └── StockService.php                  # ecommerce_stock: доступный остаток, отметка kaspi_stock_status, xlsx
├── stock/                                # legacy выгрузка остатков (по старому импорту)
└── test/
    └── KaspiMockFactory.php              # мок-ответы при useMock = true
```

---

## Как Kaspi принимает цены и остатки

Kaspi **не даёт REST API** для обновления цены/остатка — приёмка идёт через загрузку прайс-листа в кабинет («Товары → Загрузить прайс-лист»), формат xlsx.
Поэтому механизм синхронизации устроен так:

1. Приложение принимает новые цены по API (`POST /kaspi/api/v1/price-update`, батч или один объект).
2. Все записи сохраняются в `kaspi_price_history` с `push_status = PENDING` и датой активации `effective_from`.
3. Если хотя бы одна запись уже активна (`effective_from <= now`) — пересобирается `price-list/kaspi-price-list.xlsx` со **всеми** актуальными товарами и их последними активными ценами.
4. Запланированные цены активирует cron (`cron/kaspi-activate-pending-prices`) — он один раз в интервал проходит по PENDING, у которых дата наступила, помечает их SENT и регенерирует xlsx.
5. xlsx после этого доступен через `GET /kaspi/api/v1/price-list-download` (и ручную загрузку в кабинет Kaspi).

Остатки вписываются в тот же xlsx (колонки PP1–PP5 + available) из `ecommerce_stock`, поэтому отдельный endpoint для остатков на SKU не нужен — любая перегенерация прайса обновляет и цены, и остатки.

---

## HTTP API приложения

Базовый роут Yii: `kaspi/api/v1/kaspi/<action>`. В `stockDepartment/config/main.php` добавлены короткие алиасы для цен:

| Метод | Публичный путь | Внутренний action | Назначение |
|---|---|---|---|
| GET  | `/kaspi/api/v1/kaspi/orders` | `actionOrders` | Прокси к `GET /v2/orders` (пагинация, фильтры) |
| POST | `/kaspi/api/v1/kaspi/products-import` | `actionProductsImport` | Импорт карточек из `ecommerce_stock`, помечает `kaspi_stock_status = SYNCED` |
| GET  | `/kaspi/api/v1/kaspi/products-import-status?i=…` | `actionProductsImportStatus` | Статус задачи импорта товаров по коду |
| GET  | `/kaspi/api/v1/kaspi/products-classification-categories` | — | Список категорий |
| GET  | `/kaspi/api/v1/kaspi/products-classification-attributes?c=…` | — | Атрибуты категории (обязательный `c`) |
| POST | **`/kaspi/api/v1/price-update`** | `actionPriceUpdate` | Приём новой цены/батча с датой активации |
| POST | **`/kaspi/api/v1/stock-update`** | `actionStockUpdate` | Приём нового остатка по артикулу (override для PP1), с датой активации |
| GET  | **`/kaspi/api/v1/price-list-download`** | `actionPriceListDownload` | Скачать последний xlsx прайс-лист |
| POST | **`/kaspi/api/v1/price-list-generate`** | `actionPriceListGenerate` | Перегенерировать xlsx без изменения цен |
| POST | **`/kaspi/api/v1/orders/<id>/transfer-to-courier`** | `actionTransferToCourier` | Создать waybill + перевести заказ в `KASPI_DELIVERY` (JSON-ответ с данными накладной) |
| GET  | **`/kaspi/api/v1/orders/<id>/label`** | `actionOrderLabel` | Скачать PDF-этикетку заказа для печати |
| POST | **`/kaspi/api/v1/orders/<id>/cancel-return-to-stock`** | `actionCancelReturnToStock` | Сценарий A: отмена заказа + возврат резерва в `ecommerce_stock` |
| POST | **`/kaspi/api/v1/orders/<id>/partial-return`** | `actionPartialReturn` | Сценарий B: создать Inbound return по Kaspi-заказу (fallback/manual; основной путь — cron-polling) |
| POST | **`/kaspi/api/v1/orders/<id>/confirm-return-completed`** | `actionConfirmReturnCompleted` | Окончательное подтверждение возврата: перевод Kaspi-заказа в `RETURNED` (после приёмки) |

Форматы запросов:
- `price-update` — [api/kaspi-price-update.md](api/kaspi-price-update.md)
- `stock-update` — [api/kaspi-stock-update.md](api/kaspi-stock-update.md)

### Авторизация

- активная сессия пользователя, **или**
- заголовок `X-Kaspi-Inbound-Token: <inboundApiToken>` / `Authorization: Bearer <inboundApiToken>`, **или**
- `allowGuestApi => true` в конфиге модуля (только dev).

---

## Сервисы (DI)

```php
/** @var \stockDepartment\modules\kaspi\services\KaspiAPIService      $api */
/** @var \stockDepartment\modules\kaspi\services\KaspiService         $kaspi */
/** @var \stockDepartment\modules\kaspi\services\StockService         $stock */
/** @var \stockDepartment\modules\kaspi\services\PriceService         $price */
/** @var \stockDepartment\modules\kaspi\services\PriceListService     $priceList */
/** @var \stockDepartment\modules\kaspi\services\StockHistoryService  $stockHistory */
/** @var \stockDepartment\modules\kaspi\services\OrderReturnService   $orderReturn */

$module       = Yii::$app->getModule('kaspi');
$api          = $module->get('apiService');
$kaspi        = $module->get('kaspiService');
$stock        = $module->get('stockService');
$price        = $module->get('priceService');
$priceList    = $module->get('priceListService');
$stockHistory = $module->get('stockHistoryService');
$orderReturn  = $module->get('orderReturnService');
```

| Сервис | Роль |
|---|---|
| **KaspiAPIService** | Прямые HTTP-вызовы Kaspi: orders v2, products/import, classification, waybill, PDF-этикетка, смена статусов заказа |
| **KaspiService** | Обёртка для контроллера: `orders()`, `productsImportFromRequest()`, `productsClassification*()`, `priceUpdate()`, `stockUpdate()`, `transferToCourier()`, `getOrderLabel()`, `cancelReturnToStock()`, `partialReturn()` |
| **StockService** | Работа с `ecommerce_stock`: `getAvailableStock()`, `getStockToImportToKaspi()`, `markKaspiStockAsSynced()`, `exportAvailableStockToExcel()` |
| **PriceService** | `applyBatchPriceUpdate(PriceUpdateRequestDto[])`, `activatePendingPrices()` — запись в `kaspi_price_history` и регенерация прайса |
| **PriceListService** | `buildCurrentPriceList()` → `generate()`/`generateFromRows()` — пишет `price-list/kaspi-price-list.xlsx` с учётом override остатков из `kaspi_stock_history` |
| **StockHistoryService** | `applyBatchStockUpdate(StockUpdateRequestDto[])`, `activatePendingStocks()` — override остатков с датой активации |
| **OrderReturnService** | `returnToStock(orderId, reason)` (A), `createPartialReturn(orderId, PartialReturnRequestDto)` (B — создание), `pollKaspiReturnsAndCreateInbounds()` (B — автотриггер из Kaspi), `confirmReturnCompleted(orderId)` (B — окончательное подтверждение → RETURNED в Kaspi) |

---

## Модели

**`KaspiPriceHistory`** (`kaspi_price_history`) — история цен.

| Поле | Тип | Описание |
|---|---|---|
| `id` | PK | |
| `product_guid` | string(128) | merchantProductCode / артикул |
| `price` | decimal | цена в KZT |
| `price_type` | string(64) | `BASE` / `SALE` / `PROMO` |
| `note` | string | произвольный комментарий |
| `effective_from` | int (unix) | дата активации |
| `push_status` | enum | `PENDING` / `SENT` / `ERROR` / `SKIPPED` |
| `push_response` | text | ответ/ошибка при попытке публикации |
| `push_at` | int (unix) | момент перехода в SENT |
| `created_at`, `created_user_id` | int | аудит |

Индексы: `product_guid`, `effective_from`, `push_status`.

**`KaspiStockHistory`** (`kaspi_stock_history`) — override остатков по SKU.

Схема симметрична `kaspi_price_history`: `product_guid`, `qty`, `effective_from`, `push_status` (PENDING/SENT/ERROR/SKIPPED), `note`, `push_response`, `push_at`, `created_at`, `created_user_id`. Индексы: `product_guid`, `effective_from`, `push_status`. Активный override (SENT + `effective_from <= now`) подменяет `COUNT(*)` из `ecommerce_stock` в колонках PP1–PP5 xlsx.

**`common\ecommerce\entities\EcommerceStock`** — расширен:
- `kaspi_stock_status` — `NEW` (ждёт импорта) / `SYNCED` (уже в Kaspi)
- `kaspi_order_status` — статус связанного заказа Kaspi

**`common\ecommerce\entities\EcommerceInbound`** — расширен (для сценария B возврата):
- `source_kaspi_order_id` — ID оригинального Kaspi-заказа
- `source_kaspi_refund_code` — код возврата на стороне Kaspi

Миграции:
- `m260323_120000_add_kaspi_status_field_to_stock_table`
- `m260410_100000_create_kaspi_price_history_table`
- `m260414_120000_create_kaspi_stock_history_table`
- `m260414_130000_add_kaspi_source_to_ecommerce_inbound`

---

## Cron

```sh
php yii cron/kaspi-activate-pending-prices
php yii cron/kaspi-poll-returns
```

- `kaspi-activate-pending-prices` — забирает `kaspi_price_history` с `push_status = PENDING` и `effective_from <= now`, помечает их `SENT` и ровно один раз перегенерирует `price-list/kaspi-price-list.xlsx`.
- `kaspi-poll-returns` — опрашивает Kaspi API на предмет заказов в статусе `KASPI_DELIVERY_RETURN_REQUESTED`, для каждого нового (по `source_kaspi_order_id`) создаёт `EcommerceInbound` return с позициями из `getOrderEntries`. Идемпотентно: повторный запуск не создаёт дубликатов.

Рекомендуемое расписание — `*/30 * * * *`. Это закрывает пункты 2 («обновлять цены и остатки раз в 30 минут») и п.9 B (автоматический триггер возврата со стороны Kaspi по диаграмме).

---

## KaspiAPIService — что уже есть в коде (не всё проброшено в HTTP)

**Товары:** `getProductsImportSchema`, `postProductsImport` / `…Response`, `getProductsImportStatus`, `getProductsClassificationCategories`, `getProductsClassificationAttributes`, `getProductsClassificationAttributeValues`, v2-offers (`sendOffers`, `getProductsV2OffersSchema`, `getProductsV2OffersImportStatus`, `getProductsV2Categories`, `getProductsV2Attributes`, `getProductsV2AttributeValues`).

**Заказы (v2):** `getOrders` / `getOrdersPage` / `getOrdersResponse`, `getOrderById` / `getOrderByIdRaw`, `getOrderByCode` / `getOrdersByCodeRaw`, `getOrderEntries`, `getOrderEntry`, `deleteOrderEntry`, `updateEntriesWeight`, `setOrderImei`, `setOrderWeight`, `createWaybill`, `isOrderCancelled`, `postOrders`, `postOrderPayload`, `changeOrderStatus`, `submitOrderKaspiDelivery`, `acceptOrder`, `completeOrder`, `cancelOrder`.

**Этикетки / накладная:** `createWaybill($orderId, $payload)` — POST в `orders/{id}/waybill`; `getShippingLabel($orderId)` — GET того же пути с `Accept: application/pdf`, возвращает `['mime' => 'application/pdf', 'body' => <binary>]` (в useMock — валидный мок-PDF из `KaspiMockFactory::getShippingLabelPdfMock`).

Примеры тел для смены статуса (POST `/v2/orders`):

```json
{"data":{"type":"orders","id":"…","attributes":{"status":"ACCEPTED_BY_MERCHANT"}}}
{"data":{"id":"…","attributes":{"status":"COMPLETED"}}}
{"data":{"id":"…","attributes":{"status":"CANCELLED","cancellationReason":"…"}}}
```

---

## Логи и отладка

- Категория логов: **`kaspi`** (для HTTP) и **`kaspi.price`** (для сервиса цен).
- HTTP-трассировка: `KaspiAPIService->httpLogEnabled = true`.
- Мок-режим: `KaspiAPIService->useMock = true` (фикстуры в `test/KaspiMockFactory.php`).
- Файл прайс-листа: `stockDepartment/modules/kaspi/price-list/kaspi-price-list.xlsx`.
- Лог приложения: `stockDepartment/runtime/logs/app.log`.

---

## Покрытие бизнес-требований

| # | Требование | Статус | Где реализовано |
|---|---|---|---|
| 1 | Механизм управления ценой и остатками с включением в дату | ✅ реализовано | `PriceService`/`StockHistoryService` + `kaspi_price_history`/`kaspi_stock_history` (`effective_from`) |
| 2 | Обновление цен/остатков раз в 30 мин | ⚠️ частично | Cron-экшен для цен (`cron/kaspi-activate-pending-prices`) есть. Нужно: (а) добавить `cron/kaspi-activate-pending-stocks`, (б) завести системный cron `*/30 * * * *` в окружении |
| 3 | Изменить цену товара по артикулу | ✅ реализовано | `POST /kaspi/api/v1/price-update` |
| 4 | Изменить остатки по артикулу | ✅ реализовано | `POST /kaspi/api/v1/stock-update` (override для PP1 через `kaspi_stock_history`) |
| 5 | Получить заказ | ✅ реализовано | `GET /kaspi/api/v1/kaspi/orders`; `KaspiAPIService::getOrderById/getOrderByCode` |
| 6 | Получить статус товара | ✅ реализовано | `GET /kaspi/api/v1/kaspi/products-import-status?i=<code>` (прокси к `getProductsImportStatus()`) |
| 7 | «Чек» о передаче курьеру | ✅ реализовано | `POST /kaspi/api/v1/orders/<id>/transfer-to-courier` — JSON c данными waybill + перевод заказа в `KASPI_DELIVERY` |
| 8 | Этикетка на упаковку после сборки | ✅ реализовано | `GET /kaspi/api/v1/orders/<id>/label` — PDF с Kaspi (`application/pdf`) |
| 9 | Возвраты (2 сценария) | ✅ реализовано | A: `POST /kaspi/api/v1/orders/<id>/cancel-return-to-stock` — cancelOrder + возврат резерва в `ecommerce_stock`. B: cron `kaspi-poll-returns` тянет Kaspi, для новых возвратов создаёт Inbound (`source_kaspi_order_id`); после приёмки — `POST /orders/<id>/confirm-return-completed` переводит Kaspi-заказ в `RETURNED`. Соответствует диаграмме `Alix/...(return).pdf`. |
| 10 | Остатки в формате EXCEL | ✅ реализовано | `StockService::exportAvailableStockToExcel()` + `PriceListService` (в прайс-листе остатки тоже) |

### TODO к следующим итерациям

- Cron-экшен `actionKaspiActivatePendingStocks` в `console/controllers/CronController.php` + системная запись `*/30 * * * *` (пункт 2).
- Реальный Kaspi-токен и переключение `KaspiAPIService::useMock = false`.
- Webhook от Kaspi для авто-инициации частичного возврата (сейчас оператор вручную, п.9 B).
