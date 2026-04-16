# API документация: Приход товара и Kaspi интеграция

## Содержание

- [Аутентификация](#аутентификация)
- [Приход товара (Inbound)](#приход-товара-inbound)
- [Kaspi API](#kaspi-api)
  - [Цены](#цены)
  - [Остатки](#остатки)
  - [Прайс-лист (Excel)](#прайс-лист-excel)
  - [Заказы](#заказы)
  - [Возвраты](#возвраты)
  - [Синхронизация номенклатуры](#синхронизация-номенклатуры)

---

## Аутентификация

### Inbound API (`/alix/api/v1/...`)

Bearer-токен — поле `auth_key` из таблицы `user`.

```
Authorization: Bearer <auth_key>
```

### Kaspi API (`/kaspi/api/v1/...`)

Один из вариантов:
- Заголовок `Authorization: Bearer <inboundApiToken>`
- Заголовок `X-Kaspi-Inbound-Token: <inboundApiToken>`
- Авторизованная сессия (cookie)

---

## Приход товара (Inbound)

Процесс по схеме **1С → Nomadex → 1С**:

```
1С: Поступление ТМЗ  --API-->  Nomadex: InBound order (NEW)
                                         ↓
                                Приёмка на склад (SCANNING → SCANNED)
                                         ↓
                                InBound order (CONFIRM)  --API-->  1С: Приходный ордер
```

### Создание прихода

```
POST /alix/api/v1/inbound/orders
```

**Заголовки:**
```
Content-Type: application/json
Authorization: Bearer <auth_key>
```

**Тело запроса:**
```json
{
  "order_id": "ORDER-001",
  "1с_uuid": "unique-uuid-from-1c",
  "comment": "Поступление ТМЗ от поставщика",
  "items": [
    {
      "barcode": "8682538223186",
      "article": "1100014950",
      "guid": "e8a98110-2cf0-11f1-9759-ac1f6b8140a5",
      "quantity": 10,
      "datamatrix": ["YmFzZTY0ZW5jb2RlZA=="]
    },
    {
      "barcode": "8682538208367",
      "article": "1100004567",
      "guid": "0a8f1d8e-3133-11f1-975b-ac1f6b8140a5",
      "quantity": 5
    }
  ]
}
```

| Поле | Тип | Обязательно | Описание |
|------|-----|-------------|----------|
| `order_id` | string | да | Номер заказа |
| `1с_uuid` | string | да | Уникальный UUID из 1С (для дедупликации) |
| `comment` | string | нет | Комментарий к заказу |
| `items` | array | да | Массив товаров (минимум 1) |
| `items[].barcode` | string | да | Штрих-код товара |
| `items[].article` | string | да | Артикул товара |
| `items[].guid` | string | да | GUID товара из product_v2 |
| `items[].quantity` | int | да | Ожидаемое количество |
| `items[].datamatrix` | array | нет | Data Matrix коды (base64) |

**Успешный ответ (200):**
```json
{
  "status": "success",
  "message": "",
  "code": "",
  "wms_id": 123
}
```

**Ошибка валидации (400):**
```json
{
  "status": "error",
  "message": "Описание ошибки",
  "code": "",
  "wms_id": ""
}
```

**Примечания:**
- `client_id = 103` (Alix Avien) захардкожен
- Товарные данные (name, brand, color) подтягиваются из таблицы `product_v2` по `guid`
- При повторной отправке того же `1с_uuid` в статусе NEW — старые items удаляются и создаются новые
- При повторной отправке того же `1с_uuid` НЕ в статусе NEW — вернётся ошибка

### Создание возврата

```
POST /alix/api/v1/inbound/returns
```

Тело запроса аналогично приходу. Создаёт заказ с `order_type = 2` (Return).

---

## Kaspi API

### Цены

#### Обновить цену товара

```
POST /kaspi/api/v1/price-update
```

Принимает одиночный объект или массив.

**Тело запроса:**
```json
[
  {
    "product_guid": "1100014950",
    "price": 5990,
    "price_type": "BASE",
    "note": "Весенняя коллекция",
    "effective_from": "2026-05-01"
  },
  {
    "product_guid": "0a8f1d8e-3133-11f1-975b-ac1f6b8140a5",
    "price": 3490
  }
]
```

| Поле | Тип | Обязательно | Описание |
|------|-----|-------------|----------|
| `product_guid` | string | да | **Артикул** или **GUID** товара из product_v2 |
| `price` | number | да | Цена в KZT (минимум 1) |
| `price_type` | string | нет | Тип цены: `BASE` (по умолчанию), `SALE`, `PROMO` |
| `note` | string | нет | Произвольная заметка |
| `effective_from` | string | нет | Дата активации `Y-m-d` (по умолчанию — сегодня) |

**Логика:**
- Если `effective_from` <= сегодня — цена применяется немедленно, генерируется Excel
- Если `effective_from` в будущем — запись сохраняется как `PENDING`, активируется cron-задачей
- Каждый вызов создаёт новую запись в `kaspi_price_history` (полная история)
- В `kaspi_price_history` сохраняются и GUID, и артикул

**Ответ:**
```json
{
  "status": "generated",
  "prices": 3
}
```

Возможные значения `status`:
- `generated` — Excel создан, цены активны
- `scheduled` — все цены запланированы на будущее
- `validation_error` — ошибки валидации
- `error` — ошибка генерации

### Остатки

#### Обновить остаток товара

```
POST /kaspi/api/v1/stock-update
```

Принимает одиночный объект или массив.

**Тело запроса:**
```json
[
  {
    "product_guid": "1100014950",
    "qty": 5,
    "note": "Ручная корректировка",
    "effective_from": "2026-05-01"
  }
]
```

| Поле | Тип | Обязательно | Описание |
|------|-----|-------------|----------|
| `product_guid` | string | да | Артикул или GUID товара |
| `qty` | int | да | Количество на складе (>= 0) |
| `note` | string | нет | Заметка |
| `effective_from` | string | нет | Дата активации `Y-m-d` (по умолчанию — сегодня) |

**Ответ:** аналогичен `price-update`.

### Прайс-лист (Excel)

#### Скачать прайс-лист

```
GET /kaspi/api/v1/price-list-download
```

Возвращает файл `kaspi-price-list.xlsx` для загрузки в кабинет Kaspi.

Формат Excel (лист «Лист1»):

| SKU | model | brand | price | PP1 | PP2 | PP3 | PP4 | PP5 | preorder |
|-----|-------|-------|-------|-----|-----|-----|-----|-----|----------|
| 1100014950 | СИЯЮЩИЙ ПРАЙМЕР | Alix Avien | 5990 | 10 | 0 | 0 | 0 | 0 | |

- **SKU** — артикул из `product_v2.article`
- **model** — название товара
- **brand** — бренд товара
- **price** — цена в тенге (целое число)
- **PP1** — количество на складе
- **PP2–PP5** — 0 (Kaspi требует все 5 столбцов)
- **preorder** — пустая ячейка (товар в наличии)

#### Перегенерировать прайс-лист

```
POST /kaspi/api/v1/price-list-generate
```

Принудительно перегенерировать Excel по текущим данным (без изменения цен).

**Ответ:**
```json
{
  "status": "generated",
  "products": 3
}
```

### Заказы

#### Получить список заказов

```
GET /kaspi/api/v1/orders
```

#### Передать заказ курьеру

```
POST /kaspi/api/v1/orders/<orderId>/transfer-to-courier
```

Создаёт накладную (waybill) и переводит заказ в статус `KASPI_DELIVERY`.

#### Скачать этикетку заказа

```
GET /kaspi/api/v1/orders/<orderId>/label
```

Возвращает PDF-этикетку для печати.

### Возвраты

#### Отмена заказа с возвратом в сток

```
POST /kaspi/api/v1/orders/<orderId>/cancel-return-to-stock
```

**Тело (опционально):**
```json
{
  "reason": "Клиент отказался"
}
```

Отменяет заказ в Kaspi и возвращает зарезервированные товары в `ecommerce_stock` со статусом `AVAILABLE`.

#### Частичный возврат

```
POST /kaspi/api/v1/orders/<orderId>/partial-return
```

**Тело запроса:**
```json
{
  "items": [
    {"product_guid": "1100014950", "qty": 1}
  ],
  "refund_code": "KASPI_REFUND_123",
  "note": "Возврат по браку"
}
```

Создаёт `EcommerceInbound` (возврат) с привязкой к Kaspi-заказу. Физическая приёмка возврата идёт через стандартный inbound-флоу.

#### Подтвердить завершение возврата

```
POST /kaspi/api/v1/orders/<orderId>/confirm-return-completed
```

Переводит заказ в Kaspi в статус `RETURNED`. Триггерит возврат денежных средств покупателю.

### Синхронизация номенклатуры

#### Синхронизировать товары из Alix 1C

```
POST /kaspi/api/v1/alix-sync-items
GET  /kaspi/api/v1/alix-sync-items
```

Загружает номенклатуру из сервиса Alix 1C в таблицы `product_v2` / `product_barcodes_v2`.

**Ответ:**
```json
{
  "status": "OK",
  "fetched": 900,
  "created": 10,
  "updated": 890,
  "barcodes_added": 15,
  "errors": 0
}
```

Также доступен как cron-команда:
```bash
php yii cron/alix-sync-items
```

---

## Cron-задачи

| Команда | Описание |
|---------|----------|
| `php yii cron/kaspi-activate-pending-prices` | Активирует PENDING цены с наступившей датой |
| `php yii cron/alix-sync-items` | Синхронизация номенклатуры из Alix 1C |

---

## Статусы прихода (InboundOrder)

| Статус | Код | Описание |
|--------|-----|----------|
| NEW | 1 | Создан, ожидает приёмки |
| SCANNING | 3 | Идёт сканирование товаров |
| SCANNED | 4 | Все товары отсканированы |
| CONFIRM | 9 | Приёмка подтверждена, товары доступны на складе |

## Статусы записей kaspi_price_history

| Статус | Описание |
|--------|----------|
| PENDING | Ожидает активации (дата в будущем) |
| SENT | Цена активна, попала в Excel |
| ERROR | Ошибка при генерации |
| SKIPPED | Пропущена |
