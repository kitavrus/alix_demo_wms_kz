# POST /kaspi/api/v1/price-update

Принимает новую цену товара (или батч цен) и обрабатывает их:
- если дата активации уже наступила — генерирует прайс-лист для загрузки в кабинет Kaspi;
- если дата в будущем — сохраняет запись со статусом `PENDING`; cron активирует её, когда дата наступит.

---

## Запрос

**Method:** `POST`  
**Content-Type:** `application/json`

### Одиночный объект

```json
{
  "product_guid":   "1100014950",
  "price":          19990.00,
  "price_type":     "BASE",
  "note":           "Весенняя коллекция",
  "effective_from": "2026-05-01"
}
```

### Батч (массив объектов)

```json
[
  {
    "product_guid": "1100014950",
    "price":        5990.00,
    "price_type":   "BASE"
  },
  {
    "product_guid":   "1100005014",
    "price":          3490.00,
    "price_type":     "BASE",
    "note":           "Акция",
    "effective_from": "2026-06-01"
  }
]
```

> `product_guid` принимает как **артикул** (`1100014950`), так и **GUID** (`e8a98110-2cf0-11f1-...`). В `kaspi_price_history` всегда сохраняются оба значения.

### Поля запроса

| Поле             | Тип     | Обязательное | Описание |
|------------------|---------|:------------:|----------|
| `product_guid`   | string  | да           | **Артикул** или **GUID** товара из product_v2. Максимум 128 символов. |
| `price`          | number  | да           | Новая цена в KZT. Минимум `1`. |
| `price_type`     | string  | нет          | Тип цены: `BASE` (по умолчанию), `SALE`, `PROMO`. |
| `note`           | string  | нет          | Произвольный комментарий к изменению цены. |
| `effective_from` | string  | нет          | Дата активации цены в формате `Y-m-d` (например `2026-06-01`). По умолчанию — сегодня. |

---

## Ответы

### 200 — цены применены

```json
{
  "status": "generated",
  "prices_saved": 3,
  "in_price_list": 2,
  "applied": ["1100014950", "1100005014"],
  "not_in_stock": ["1100014749"],
  "download_url_xlsx": "/kaspi/api/v1/price-list-download",
  "download_url_xml": "/kaspi/api/v1/price-list-download-xml"
}
```

| Поле | Описание |
|------|----------|
| `prices_saved` | Сколько записей сохранено в `kaspi_price_history` |
| `in_price_list` | Сколько товаров реально попало в прайс |
| `applied` | Артикулы, цена которых обновилась в прайсе |
| `not_in_stock` | Артикулы, которых нет на складе — цена сохранена в историю, но в прайс не попала (поле отсутствует если все товары на складе) |
| `download_url_xlsx` | URL для скачивания Excel-прайса (ручная загрузка в кабинет Kaspi) |
| `download_url_xml` | URL для скачивания XML-прайса (автоматическая загрузка Kaspi) |

### XML-формат прайса

XML генерируется одновременно с Excel. Формат соответствует спецификации Kaspi:

```xml
<?xml version="1.0" encoding="utf-8"?>
<kaspi_catalog date="2026-04-16 19:42" xmlns="kaspiShopping" ...>
  <company>COMPANY_NAME</company>
  <merchantid>MERCHANT_ID</merchantid>
  <offers>
    <offer sku="1100014950">
      <model>СИЯЮЩИЙ ПРАЙМЕР ДЛЯ ЛИЦА</model>
      <brand>Alix Avien</brand>
      <availabilities>
        <availability available="yes" storeId="STORE_ID" stockCount="10"/>
      </availabilities>
      <price>5990</price>
    </offer>
  </offers>
</kaspi_catalog>
```

Для автоматической загрузки укажите URL XML-файла в кабинете Kaspi:
**Товары → Загрузить прайс-лист → Автоматическая загрузка**

> Константы `COMPANY_NAME`, `MERCHANT_ID`, `STORE_ID` — плейсхолдеры. Замените на реальные данные из кабинета Kaspi в `PriceListService.php`.

### 200 — часть цен запланирована на будущее

```json
{
  "status": "generated",
  "prices_saved": 3,
  "in_price_list": 1,
  "applied": ["1100014950"],
  "not_in_stock": ["1100014749"],
  "scheduled": [
    {
      "product_guid": "1100005014",
      "effective_from": "2026-06-01"
    }
  ]
}
```

### 200 — все даты в будущем (всё запланировано)

```json
{
  "status": "scheduled",
  "prices_saved": 2,
  "scheduled": [
    {
      "product_guid": "1100014950",
      "effective_from": "2026-07-01"
    }
  ]
}
```

### 200 — ошибка генерации Excel (данные сохранены, файл не создан)

```json
{
  "status":  "error",
  "saved":   3,
  "message": "<причина>"
}
```

### 200 — ошибка валидации входящих данных

```json
{
  "status": "validation_error",
  "errors": [
    {
      "index":  0,
      "errors": {
        "product_guid": ["Необходимо заполнить «GUID товара»."]
      }
    }
  ]
}
```

### 400 Bad Request — тело запроса пустое или не JSON

```
Request body is required (JSON)
```

---

## Жизненный цикл записи (push_status)

| Статус    | Описание |
|-----------|----------|
| `PENDING` | Дата активации ещё не наступила. Запись ждёт cron. |
| `SENT`    | Excel сгенерирован, цена готова к загрузке в Kaspi. |
| `ERROR`   | Ошибка при генерации Excel. |
| `SKIPPED` | Запись пропущена (например, вытеснена более новой ценой). |

---

## Активация отложенных цен (cron)

Записи со статусом `PENDING`, у которых `effective_from` уже наступил, активируются автоматически:

```
php yii cron/kaspi-activate-pending-prices
```

После активации перегенерируется общий Excel-файл со всеми актуальными ценами.
