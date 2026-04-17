# POST /kaspi/api/v1/price-update

Принимает новую цену товара (или батч цен) и обрабатывает их:

- если дата активации уже наступила — сразу перегенерирует Excel- и XML-прайс со всеми актуальными ценами;
- если дата в будущем — сохраняет запись со статусом `PENDING`; cron активирует её, когда дата наступит.

Источник цен в прайсе: последняя активная запись в `kaspi_price_history` (статус `SENT`, `effective_from <= now`). Если истории нет — берётся `product_price` из `ecommerce_stock`; если нет и там — `0`.

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

> `product_guid` принимает как **артикул** (`1100014950`), так и **GUID** (`e8a98110-2cf0-11f1-...`). В `kaspi_price_history` всегда сохраняются оба значения (`product_guid` + `article`), что бы ни прислали на входе.

### Поля запроса

| Поле             | Тип     | Обязательное | Описание |
|------------------|---------|:------------:|----------|
| `product_guid`   | string  | да           | **Артикул** или **GUID** товара из `product_v2`. Максимум 128 символов. |
| `price`          | number  | да           | Новая цена в KZT. Минимум `1`. |
| `price_type`     | string  | нет          | Тип цены: `BASE` (по умолчанию), `SALE`, `PROMO`. |
| `note`           | string  | нет          | Произвольный комментарий к изменению цены. |
| `effective_from` | string  | нет          | Дата активации цены в формате `Y-m-d` (например `2026-06-01`). По умолчанию — сегодня. Таймзона активации — `Asia/Almaty`. |

---

## Ответы

### 200 — цены применены (есть немедленно активные)

```json
{
  "status": "generated",
  "prices_saved": 3,
  "in_price_list": 2,
  "applied": ["1100014950", "1100005014"],
  "not_in_stock": ["1100014749"],
  "download_url_xlsx": "/kaspi/api/v1/price-list-download",
  "download_url_xml": "/kaspi/api/v1/price-list-download-xml",
  "public_xml_url": "/kaspi-price-list.xml"
}
```

| Поле | Описание |
|------|----------|
| `prices_saved`      | Сколько записей сохранено в `kaspi_price_history` |
| `in_price_list`     | Сколько товаров из батча реально попало в прайс |
| `applied`           | Артикулы, цена которых обновилась в прайсе |
| `not_in_stock`      | Артикулы, которых нет на складе — цена сохранена в историю, но в прайс не попала (поле отсутствует если все товары на складе) |
| `download_url_xlsx` | URL для скачивания Excel-прайса (ручная загрузка в кабинет Kaspi) |
| `download_url_xml`  | URL для скачивания XML-прайса (авторизованная, через API) |
| `public_xml_url`    | Публичный URL XML (без авторизации) — адрес для автозагрузки в кабинете Kaspi |
| `scheduled`         | Необязательное поле. Массив записей, отложенных на будущее (если батч смешанный) |

### 200 — все даты в будущем (всё запланировано)

```json
{
  "status": "scheduled",
  "prices": 2,
  "scheduled": [
    {
      "product_guid":   "1100014950",
      "effective_from": "2026-07-01"
    },
    {
      "product_guid":   "1100005014",
      "effective_from": "2026-08-15"
    }
  ]
}
```

| Поле       | Описание |
|------------|----------|
| `prices`   | Сколько записей сохранено в `kaspi_price_history` со статусом `PENDING` |
| `scheduled`| Список отложенных активаций |

### 200 — часть цен запланирована на будущее

Аналогично статусу `generated`, плюс добавляется массив `scheduled`:

```json
{
  "status": "generated",
  "prices_saved": 3,
  "in_price_list": 1,
  "applied": ["1100014950"],
  "not_in_stock": ["1100014749"],
  "download_url_xlsx": "/kaspi/api/v1/price-list-download",
  "download_url_xml": "/kaspi/api/v1/price-list-download-xml",
  "public_xml_url": "/kaspi-price-list.xml",
  "scheduled": [
    {
      "product_guid":   "1100005014",
      "effective_from": "2026-06-01"
    }
  ]
}
```

### 200 — ошибка генерации файлов (данные сохранены, файлы не созданы)

Статус немедленных записей откатывается обратно в `ERROR`, цены в `kaspi_price_history` остаются.

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

## Excel-формат прайса

Файл `kaspi-price-list.xlsx` перезаписывается при каждой генерации. Лист именуется `Лист1` (строго по шаблону Kaspi), 10 колонок:

| Колонка   | Тип     | Описание |
|-----------|---------|----------|
| SKU       | text    | Артикул товара (из `product_v2.article`, fallback — GUID). Текстовая ячейка — чтобы Excel не обрезал ведущие нули. |
| model     | text    | Название товара |
| brand     | text    | Бренд |
| price     | integer | Цена в KZT без копеек |
| PP1       | integer | Количество на складе PP1 (у нас один склад) |
| PP2–PP5   | integer | `0` — других складов нет, но Kaspi требует все 5 столбцов |
| preorder  | empty   | Пустая ячейка (товар в наличии); число дней — только для предзаказа |

---

## XML-формат прайса

XML генерируется одновременно с Excel и копируется в `stockDepartment/web/kaspi-price-list.xml` для автозагрузки из кабинета Kaspi.

```xml
<?xml version="1.0" encoding="utf-8"?>
<kaspi_catalog date="2026-04-17 19:42" xmlns="kaspiShopping"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="kaspiShopping http://kaspi.kz/kaspishopping.xsd">
  <company>ТОО "GLOMARK KZK"</company>
  <merchantid>30453464</merchantid>
  <offers>
    <offer sku="1100014950">
      <model>СИЯЮЩИЙ ПРАЙМЕР ДЛЯ ЛИЦА</model>
      <brand>Alix Avien</brand>
      <availabilities>
        <availability available="yes" storeId="PP1" stockCount="10"/>
      </availabilities>
      <price>5990</price>
    </offer>
  </offers>
</kaspi_catalog>
```

Для автоматической загрузки укажите публичный URL XML-файла в кабинете Kaspi:
**Товары → Загрузить прайс-лист → Автоматическая загрузка**

Реквизиты продавца зашиты константами в `stockDepartment/modules/kaspi/services/PriceListService.php`:

```php
const KASPI_COMPANY     = 'ТОО "GLOMARK KZK"';
const KASPI_MERCHANT_ID = '30453464';
const KASPI_STORE_ID    = 'PP1';
```

---

## Жизненный цикл записи (`push_status`)

| Статус    | Описание |
|-----------|----------|
| `PENDING` | Дата активации ещё не наступила. Запись ждёт cron. |
| `SENT`    | Цена активна и попала в прайс. |
| `ERROR`   | Ошибка при генерации файлов. |
| `SKIPPED` | Запись пропущена (например, вытеснена более новой ценой). |

---

## Активация отложенных цен (cron)

Записи со статусом `PENDING`, у которых `effective_from` уже наступил, активируются автоматически:

```shell
php yii cron/kaspi-activate-pending-prices
```

После активации перегенерируется общий Excel- и XML-файл со всеми актуальными ценами.

---

## Связанные эндпоинты

| Метод | URL | Назначение |
|-------|-----|------------|
| `GET` | `/kaspi/api/v1/price-list-download`     | Скачать актуальный `kaspi-price-list.xlsx` |
| `GET` | `/kaspi/api/v1/price-list-download-xml` | Скачать актуальный `kaspi-price-list.xml` |
| `POST`| `/kaspi/api/v1/price-list-generate`     | Принудительно перегенерировать файлы прайса по текущим данным (без изменения цен) |
