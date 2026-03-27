# Модуль Kaspi

Интеграция с [Kaspi Partner API](https://guide.kaspi.kz/partner/ru/shop/api/): **товары** (`/shop/api/…`, в гиде — «Товары») и **заказы** (`/shop/api/v2/…`, в гиде — «Заказы»).

| В коде / доке | Официальный гид |
|----------------|-----------------|
| **products** (import, classification) | [Товары](https://guide.kaspi.kz/partner/ru/shop/api/goods) |
| **orders** | [Заказы](https://guide.kaspi.kz/partner/ru/shop/api/orders) |

---

## Структура модуля

```
stockDepartment/modules/kaspi/
├── README.md
├── kaspi.php                         # Yii-модуль: DI apiService, stockService, kaspiService
├── constants/
│   └── KaspiConstants.php            # базовые URL, пути эндпоинтов, дефолты
├── controllers/
│   └── api/
│       └── v1/
│           └── KaspiController.php   # входящие HTTP-эндпоинты приложения
├── dto/
│   ├── AttributeDto.php
│   ├── CustomerDto.php
│   ├── KaspiOrderListPayload.php
│   ├── OrderDto.php
│   └── StockDto.php
├── enums/
│   ├── DeliveryMode.php
│   ├── OrderStatus.php
│   ├── PaymentMode.php
│   └── StateOrder.php
├── exceptions/
│   └── KaspiApiException.php
├── services/
│   ├── KaspiAPIService.php         # низкоуровневые запросы к Kaspi (mock / live)
│   ├── KaspiJsonApiSerializer.php
│   ├── KaspiOrderHydrator.php      # JSON:API заказы → DTO
│   ├── KaspiService.php            # сценарии: orders, import из stock, classification
│   └── StockService.php            # выборка остатков, Excel, поле kaspi_stock_status
├── test/
│   └── KaspiMockFactory.php        # мок-ответы при useMock = true
└── stock/
    └── kaspi-stock.xlsx            # генерируемая выгрузка остатков (путь в ответе import)
```

---
```

### Доступ к HTTP API приложения

- сессия авторизованного пользователя, **или**
- `inboundApiToken` + заголовок `X-Kaspi-Inbound-Token` / `Authorization: Bearer …`, **или**
- `allowGuestApi => true` (только для разработки)

---

## Сервисы (из кода)

```php
/** @var \stockDepartment\modules\kaspi\services\KaspiAPIService $api */
$api = Yii::$app->getModule('kaspi')->get('apiService');

/** @var \stockDepartment\modules\kaspi\services\StockService $stock */
$stock = Yii::$app->getModule('kaspi')->get('stockService');

/** @var \stockDepartment\modules\kaspi\services\KaspiService $kaspi */
$kaspi = Yii::$app->getModule('kaspi')->get('kaspiService');
```

| Сервис | Роль |
|--------|------|
| **KaspiAPIService** | Прямые вызовы Kaspi: import, classification, orders, смена статусов заказа и т.д. |
| **StockService** | Данные таблицы `ecommerce_stock`: доступный остаток, отбор на импорт, `kaspi_stock_status`, экспорт xlsx |
| **KaspiService** | Обёртка для контроллера: заказы, классификация, сценарий `productsImportFromRequest()` |

---

## HTTP API приложения (`KaspiController`)

Префикс маршрута Yii: **`/kaspi/api/v1/kaspi/`** (модуль `kaspi`, контроллер `api/v1/kaspi`).

| Метод | Действие | Описание | Kaspi (гайд) |
|-------|----------|----------|----------------|
| GET | `orders` | Список заказов (query передаётся в Kaspi, пагинация `page[number]`, `page[size]`, фильтры в т.ч. `filter[orders][creationDate]`) | `GET /v2/orders` |
| POST | `products-import` | Импорт карточек из остатков (`StockService`), батчи, мин. 5 SKU, пометка `kaspi_stock_status` | `POST …/products/import` |
| GET | `products-classification-categories` | Категории для классификации | [q3216](https://guide.kaspi.kz/partner/ru/shop/api/goods/q3216) |
| GET | `products-classification-attributes` | Атрибуты по категории, **обязательный** query `c` — код категории | [q3217](https://guide.kaspi.kz/partner/ru/shop/api/goods/q3217) |

Примеры:

- `GET /kaspi/api/v1/kaspi/orders?page[number]=1&page[size]=20`
- `POST /kaspi/api/v1/kaspi/products-import`
- `GET /kaspi/api/v1/kaspi/products-classification-categories`
- `GET /kaspi/api/v1/kaspi/products-classification-attributes?c=Master%20-%20…`

В `stockDepartment/config/main.php` может быть алиас (проверьте актуальность маршрута): `shop/api/products/import` → `kaspi/api/v2/kaspi/products-import`.

Ответы JSON: в экшенах выставляется `Yii::$app->response->format = Response::FORMAT_JSON`.

---

## Остатки и синхронизация с Kaspi

- Импорт берёт строки из `ecommerce_stock`, где товар доступен и `kaspi_stock_status` пустой / `NEW` (см. `StockService::getStockToImportToKaspi()`).
- После успешного ответа Kaspi SKU помечаются как **`SYNCED`** (`EcommerceStock::KASPI_STOCK_STATUS_SYNCED`, поле `kaspi_stock_status`).
- Для заказов предусмотрено поле **`kaspi_order_status`** в модели `ecommerce_stock` (заполнение — по вашему сценарию).
- Константы статусов: `common/ecommerce/entities/EcommerceStock.php`.

---

## KaspiAPIService (не все методы выведены в HTTP)

Ниже — что реализовано в `KaspiAPIService` для вызова из PHP / консоли; часть эндпоинтов приложения пока не проброшена в `KaspiController`.

**Товары (goods):** `getProductsImportSchema`, `postProductsImport` / `postProductsImportResponse`, `getProductsImportStatus`, `getProductsClassificationCategories`, `getProductsClassificationAttributes`, `getProductsClassificationAttributeValues`, а также обёртки вокруг v2 offers/categories/attributes (см. код).

**Заказы (v2):** `getOrders` / `getOrdersPage` / `getOrdersResponse`, `getOrderById`, `getOrderByCode`, `postOrderPayload`, `acceptOrder`, `completeOrder`, `cancelOrder`, `getOrderEntries`, `deleteOrderEntry`, и др. — см. `KaspiAPIService.php`.

Примеры тел для смены статуса заказа через `postOrderPayload` (спецификация Kaspi — **POST** `/v2/orders`):

```json
{"data":{"type":"orders","id":"…","attributes":{"status":"ACCEPTED_BY_MERCHANT"}}}
{"data":{"id":"…","attributes":{"status":"COMPLETED"}}}
{"data":{"id":"…","attributes":{"status":"CANCELLED","cancellationReason":"…"}}}
```

---

## Логи и отладка

- При проблемах с запросами смотрите категорию логов **`kaspi`** и `stockDepartment/runtime/logs/app.log` (в т.ч. логирование параметров/импорта в коде).
