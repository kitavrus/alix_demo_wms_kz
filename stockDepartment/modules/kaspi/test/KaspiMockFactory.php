<?php

namespace stockDepartment\modules\kaspi\test;

use stockDepartment\modules\kaspi\services\KaspiOrderHydrator;

/** Моки ответов Kaspi API (useMock). */
class KaspiMockFactory
{
    private static $defaultOrderId = 'ODk3MDE1NDMx';
    private static $defaultEntryId = 'ODk3MDE1NDMxIyMw';
    private static $defaultCustomerId = 'NzAyNjM4NTcwNQ';
    // Артикул реального товара для поллинга через cron/kaspi-poll-orders —
    // должен существовать в product_v2 / ecommerce_stock, иначе импорт упадёт на резерве.
    private static $defaultProductCode = '1100012686';
    private static $defaultProductName = 'РАСЦЕПЛЯЕМАЯ ПУДРА 01 ПРОЗРАЧНАЯ';
    private static $defaultProductPrice = 99999;
    /** @var int Количество в outbound-позиции (сколько купили). Меняется в тестах. */
    public static $defaultOrderQuantity = 1;
    /** @var int|null Сколько клиент возвращает из купленного. null = полный возврат (= $defaultOrderQuantity). */
    public static $defaultReturnedQuantity = null;
    /** @var string Refund code для синтезированного mock-ответа (уникален per test). */
    public static $defaultRefundCode = 'RF-PARTIAL-002';

    /**
     * Тестовый флаг: имитирует эмпирически наблюдаемый «баг» Kaspi, когда фильтр
     * filter[orders][status]=KASPI_DELIVERY_RETURN_REQUESTED игнорируется и в
     * выдаче приходят заказы с другими статусами. Полезно для проверки
     * defensive status-guard в OrderReturnService::pollKaspiReturnsAndCreateEcomReturns.
     *
     * При false (по умолчанию) мок честно фильтрует по статусу.
     *
     * @var bool
     */
    public static $simulateBuggyReturnFilter = false;

    /**
     * Реестр фикстур заказов для мока. Заполняется на основе реальных JSON-ответов
     * Kaspi (Жанна — CANCELLED/ARCHIVE; Нурбек — ACCEPTED_BY_MERCHANT/KASPI_DELIVERY/
     * assembled c waybill) + один синтетический в KASPI_DELIVERY_RETURN_REQUESTED
     * для happy-path тестирования cron/kaspi-poll-returns.
     *
     * Чтобы проверить локально:
     *   1) Включить useMock в KaspiAPIService (kaspi.php config).
     *   2) Запустить `php yii cron/kaspi-sync-order-statuses` — увидите реакцию
     *      handleCancelled на CANCELLED-заказ Жанны.
     *   3) Запустить `php yii cron/kaspi-poll-returns` — синтетическая фикстура
     *      OTEyMjgyNjJa подхватится, на её базе создастся EcommerceReturn.
     *   4) Чтобы проверить defensive status-guard в poll'е возвратов:
     *      KaspiMockFactory::$simulateBuggyReturnFilter = true; — тогда фильтр
     *      RETURN_REQUESTED отдаст ВСЕ фикстуры, и guard их отбросит по статусу.
     *
     * @return array<string, array> orderId => order resource
     */
    private static function orderFixtures()
    {
        $nowMs = (int) floor(microtime(true) * 1000);

        return [
            // ───────── Жанна, CANCELLED, ARCHIVE (real fixture from operator) ─────────
            'OTA2NTAwNDc0' => self::buildOrderResource('OTA2NTAwNDc0', [
                'code' => '906500474',
                'status' => 'CANCELLED',
                'state' => 'ARCHIVE',
                'totalPrice' => 106,
                'deliveryCostForSeller' => 0,
                'deliveryCost' => 0,
                'deliveryMode' => 'DELIVERY_REGIONAL_TODOOR',
                'creationDate' => $nowMs - 4 * 86400 * 1000, // 4 дня назад
                'approvedByBankDate' => $nowMs - 4 * 86400 * 1000,
                'plannedDeliveryDate' => null,
                'assembled' => false,
                'customer' => [
                    'id' => 'Nzc4MTQxMjEzOQ',
                    'firstName' => 'Жанна',
                    'lastName' => 'Б',
                    'name' => 'Жанна',
                    'cellPhone' => '7781412139',
                ],
                'deliveryAddress' => [
                    'streetName' => 'улица Толе-би',
                    'streetNumber' => '255',
                    'town' => 'Толе би (Жамбыл. обл)',
                    'district' => null,
                    'building' => null,
                    'apartment' => '',
                    'formattedAddress' => 'Толе би (Жамбыл. обл), улица Толе-би, 255, ',
                    'latitude' => 43.691431,
                    'longitude' => 73.759267,
                ],
                'kaspiDelivery' => [
                    'waybill' => null,
                    'waybillNumber' => null,
                    'courierTransmissionDate' => null,
                    'courierTransmissionPlanningDate' => $nowMs - 4 * 86400 * 1000 + 3600 * 1000,
                    'express' => false,
                    'returnedToWarehouse' => false,
                    'firstMileCourier' => null,
                ],
            ]),

            // ───────── Нурбек, ACCEPTED_BY_MERCHANT, KASPI_DELIVERY, assembled (real fixture) ─────────
            'OTEyMjgyNjI5' => self::buildOrderResource('OTEyMjgyNjI5', [
                'code' => '912282629',
                'status' => 'ACCEPTED_BY_MERCHANT',
                'state' => 'KASPI_DELIVERY',
                'totalPrice' => 1690,
                'deliveryCostForSeller' => 173,
                'deliveryCost' => 500,
                'deliveryMode' => 'DELIVERY_PICKUP',
                'creationDate' => $nowMs - 1 * 86400 * 1000, // вчера
                'approvedByBankDate' => $nowMs - 1 * 86400 * 1000,
                'plannedDeliveryDate' => $nowMs + 2 * 86400 * 1000,
                'assembled' => true, // already assembled = waybill готов
                'customer' => [
                    'id' => 'NzAyNjM4NTcwNQ',
                    'firstName' => 'Нурбек',
                    'lastName' => 'О',
                    'name' => 'Нурбек',
                    'cellPhone' => '7026385705',
                ],
                'deliveryAddress' => [
                    'streetName' => null,
                    'streetNumber' => null,
                    'town' => 'Шымкент',
                    'district' => null,
                    'building' => null,
                    'apartment' => null,
                    'formattedAddress' => 'Шымкент, ',
                    'latitude' => null,
                    'longitude' => null,
                ],
                'kaspiDelivery' => [
                    'waybill' => 'https://kaspi.kz/shop/api/waybill/MOCK-NUR-OTEyMjgyNjI5',
                    'waybillNumber' => '405934783',
                    'courierTransmissionDate' => null,
                    'courierTransmissionPlanningDate' => $nowMs + 1 * 86400 * 1000,
                    'express' => false,
                    'returnedToWarehouse' => false,
                    'firstMileCourier' => null,
                ],
            ]),

            // ───────── Нурбек (курьерская доставка), ACCEPTED_BY_MERCHANT, assembled ─────────
            // Клон OTEyMjgyNjI5, но deliveryMode = DELIVERY_REGIONAL_TODOOR.
            // Нужен для проверки полного цикла отгрузки с печатью этикетки курьеру
            // (DELIVERY_PICKUP по сути — отдача водителю Kaspi из ПВЗ, а курьерская —
            // выезд курьера к покупателю на адрес).
            'OTEyMjgyOTAw' => self::buildOrderResource('OTEyMjgyOTAw', [
                'code' => '912282900',
                'status' => 'ACCEPTED_BY_MERCHANT',
                'state' => 'KASPI_DELIVERY',
                'totalPrice' => 1690,
                'deliveryCostForSeller' => 173,
                'deliveryCost' => 500,
                'deliveryMode' => 'DELIVERY_REGIONAL_TODOOR',
                'creationDate' => $nowMs - 1 * 86400 * 1000,
                'approvedByBankDate' => $nowMs - 1 * 86400 * 1000,
                'plannedDeliveryDate' => $nowMs + 2 * 86400 * 1000,
                'assembled' => true,
                'customer' => [
                    'id' => 'NzAyNjM4NTcwNQ',
                    'firstName' => 'Нурбек',
                    'lastName' => 'О',
                    'name' => 'Нурбек',
                    'cellPhone' => '7026385705',
                ],
                'deliveryAddress' => [
                    'streetName' => 'улица Абая',
                    'streetNumber' => '150',
                    'town' => 'Шымкент',
                    'district' => null,
                    'building' => null,
                    'apartment' => '12',
                    'formattedAddress' => 'Шымкент, улица Абая, 150, кв. 12',
                    'latitude' => 42.317117,
                    'longitude' => 69.595931,
                ],
                'kaspiDelivery' => [
                    // Тот же waybill URL — мок печати этикетки возвращает PDF для любого orderId,
                    // так что повторение URL не критично для теста цикла.
                    'waybill' => 'https://kaspi.kz/shop/api/waybill/MOCK-NUR-OTEyMjgyNjI5',
                    'waybillNumber' => '405934784',
                    'courierTransmissionDate' => null,
                    'courierTransmissionPlanningDate' => $nowMs + 1 * 86400 * 1000,
                    'express' => false,
                    'returnedToWarehouse' => false,
                    'firstMileCourier' => null,
                ],
            ]),

            // ───────── Синтетика: KASPI_DELIVERY_RETURN_REQUESTED (для poll-returns happy path) ─────────
            'OTEyMjgyNjJa' => self::buildOrderResource('OTEyMjgyNjJa', [
                'code' => '912282630',
                'status' => 'KASPI_DELIVERY_RETURN_REQUESTED',
                'state' => 'KASPI_DELIVERY',
                'totalPrice' => self::$defaultProductPrice,
                'deliveryCostForSeller' => 0,
                'deliveryCost' => 500,
                'deliveryMode' => 'DELIVERY_PICKUP',
                'creationDate' => $nowMs - 5 * 86400 * 1000,
                'approvedByBankDate' => $nowMs - 5 * 86400 * 1000,
                'plannedDeliveryDate' => $nowMs - 2 * 86400 * 1000,
                'assembled' => true,
                'customer' => [
                    'id' => self::$defaultCustomerId,
                    'firstName' => 'Тест',
                    'lastName' => 'Возврат',
                    'name' => 'Тест Возврат',
                    'cellPhone' => '7777777777',
                ],
                'deliveryAddress' => [
                    'streetName' => null,
                    'streetNumber' => null,
                    'town' => 'Алматы',
                    'district' => null,
                    'building' => null,
                    'apartment' => null,
                    'formattedAddress' => 'Алматы, ',
                    'latitude' => null,
                    'longitude' => null,
                ],
                'kaspiDelivery' => [
                    'waybill' => 'https://kaspi.kz/shop/api/waybill/MOCK-RET-OTEyMjgyNjJa',
                    'waybillNumber' => '405934999',
                    'courierTransmissionDate' => $nowMs - 3 * 86400 * 1000,
                    'courierTransmissionPlanningDate' => $nowMs - 4 * 86400 * 1000,
                    'express' => false,
                    'returnedToWarehouse' => false,
                    'firstMileCourier' => null,
                ],
            ]),
        ];
    }

    /**
     * Сборка единичной фикстуры заказа на основе attributes-overrides поверх дефолта.
     * Все поля совпадают по форме с реальным Kaspi /v2/orders/{id} ответом.
     */
    private static function buildOrderResource($id, array $attrsOverride)
    {
        $base = self::getSampleOrderResource($id);
        foreach ($attrsOverride as $k => $v) {
            $base['attributes'][$k] = $v;
        }
        return $base;
    }

    private static function getSampleOrderResource($orderId = null)
    {
        $id = $orderId !== null ? $orderId : self::$defaultOrderId;

        // Всегда свежие таймстемпы, чтобы заказ попадал в окно
        // OrderImportService::pollWindowHours (по умолчанию 6ч).
        $nowMs = (int) floor(microtime(true) * 1000);

        return [
            'type' => 'orders',
            'id' => $id,
            'attributes' => [
                'code' => '896884610',
                'creationDate' => $nowMs,
                'totalPrice' => self::$defaultProductPrice,
                'deliveryCostForSeller' => 0,
                'isKaspiDelivery' => true,
                'preOrder' => false,
                'approvedByBankDate' => $nowMs,
                'signatureRequired' => false,
                // Ключевая пара для poll'а: pollAndImportNew() фильтрует
                // APPROVED_BY_BANK + state=NEW (см. OrderImportService:71-72).
                'status' => 'ACCEPTED_BY_MERCHANT', //'APPROVED_BY_BANK',
                'state' => 'KASPI_DELIVERY', //'NEW',
                'pickupPointId' => '30453464_PP1',
                'deliveryCost' => 500,
                'customer' => [
                    'id' => self::$defaultCustomerId,
                    'name' => 'Нурбек',
                    'cellPhone' => '7026385705',
                    'firstName' => 'Нурбек',
                    'lastName' => 'О',
                ],
                'deliveryAddress' => [
                    'streetName' => null,
                    'streetNumber' => null,
                    'town' => 'Шымкент',
                    'district' => null,
                    'building' => null,
                    'apartment' => null,
                    'formattedAddress' => 'Шымкент, ',
                    'latitude' => null,
                    'longitude' => null,
                ],
                'originAddress' => [
                    'id' => 'MzA0NTM0NjRfUFAx',
                    'displayName' => 'PP1',
                    'address' => [
                        'streetName' => 'улица Первомайская Промзона',
                        'streetNumber' => '282',
                        'town' => 'г. Алматы',
                        'district' => null,
                        'building' => 'Первомайская промышленная зона, 285а/1  склад Nomadex',
                        'apartment' => null,
                        'formattedAddress' => 'г. Алматы, улица Первомайская Промзона, 282 (Первомайская промышленная зона, 285а/1  склад Nomadex)',
                        'latitude' => 43.377010345459,
                        'longitude' => 76.914489746094,
                    ],
                    'city' => [
                        'id' => 'NzUwMDAwMDAw',
                        'code' => '750000000',
                        'name' => 'Алматы',
                        'active' => true,
                    ],
                ],
                'kaspiDelivery' => [
                    'waybill' => null,
                    'courierTransmissionDate' => null,
                    'courierTransmissionPlanningDate' => $nowMs + 3600 * 1000,
                    'waybillNumber' => null,
                    'express' => false,
                    'returnedToWarehouse' => false,
                    'firstMileCourier' => null,
                ],
                'assembled' => false,
                'deliveryMode' => 'DELIVERY_PICKUP',
                'paymentMode' => 'PREPAID',
            ],
            'relationships' => [
                'entries' => [
                    'data' => [
                        [
                            'id' => self::$defaultEntryId,
                            'type' => 'orderentries',
                        ],
                    ],
                    'links' => [
                        'self' => "https://kaspi.kz/shop/api/v2/orders/{$id}/relationships/entries",
                        'related' => "https://kaspi.kz/shop/api/v2/orders/{$id}/entries",
                    ],
                ],
                'user' => [
                    'data' => [
                        'id' => self::$defaultCustomerId,
                        'type' => 'customers',
                    ],
                    'links' => [
                        'self' => "https://kaspi.kz/shop/api/v2/orders/{$id}/relationships/user",
                        'related' => "https://kaspi.kz/shop/api/v2/orders/{$id}/user",
                    ],
                ],
            ],
            'links' => [
                'self' => "https://kaspi.kz/shop/api/v2/orders/{$id}",
            ],
        ];
    }

    public static function getOrdersApiResponse()
    {
        $fixtures = array_values(self::orderFixtures());

        return [
            'data' => $fixtures,
            'included' => [],
            'meta' => [
                'pageCount' => 1,
                'totalCount' => count($fixtures),
            ],
        ];
    }

    /**
     * Вариант mock-ответа под фильтр filter[orders][status]. Честно фильтрует
     * фикстуры по `attributes.status`.
     *
     * Если включён `$simulateBuggyReturnFilter` И фильтр запрашивает
     * KASPI_DELIVERY_RETURN_REQUESTED — отдаём ВСЕ фикстуры (мимикрия эмпирически
     * наблюдаемого поведения Kaspi, когда фильтр игнорируется). Это позволяет
     * проверить, что defensive status-guard в OrderReturnService отсеивает
     * заказы с неподходящим статусом.
     */
    public static function getOrdersApiResponseByStatus($status)
    {
        $fixtures = self::orderFixtures();

        if (self::$simulateBuggyReturnFilter && $status === 'KASPI_DELIVERY_RETURN_REQUESTED') {
            $matching = array_values($fixtures);
        } else {
            $matching = [];
            foreach ($fixtures as $order) {
                $orderStatus = isset($order['attributes']['status']) ? (string) $order['attributes']['status'] : '';
                if ($orderStatus === $status) {
                    $matching[] = $order;
                }
            }
        }

        return [
            'data' => $matching,
            'included' => [],
            'meta' => [
                'pageCount' => 1,
                'totalCount' => count($matching),
            ],
        ];
    }

    public static function getOrdersList()
    {
        $payload = self::getOrdersApiResponse();
        $included = isset($payload['included']) ? $payload['included'] : [];

        return KaspiOrderHydrator::hydrateOrdersFromApi(
            isset($payload['data']) ? $payload['data'] : [],
            $included
        );
    }

    public static function getOrderById($orderId)
    {
        $fixtures = self::orderFixtures();
        if (isset($fixtures[$orderId])) {
            return KaspiOrderHydrator::hydrateSingleOrder($fixtures[$orderId], []);
        }
        // Fallback: синтезируем дефолтный для неизвестных id (чтобы not-fixture тесты не ломались).
        return KaspiOrderHydrator::hydrateSingleOrder(self::getSampleOrderResource($orderId), []);
    }

    public static function getOrderApiResponse($orderId)
    {
        $fixtures = self::orderFixtures();
        if (isset($fixtures[$orderId])) {
            return ['data' => $fixtures[$orderId]];
        }
        $order = self::getSampleOrderResource($orderId);
        $order['attributes']['code'] = 'KZ-' . $orderId;
        return ['data' => $order];
    }

    public static function getOrderByIdRawApiResponse($orderId)
    {
        $fixtures = self::orderFixtures();
        if (isset($fixtures[$orderId])) {
            return [
                'data' => $fixtures[$orderId],
                'included' => [],
            ];
        }
        $base = self::getOrderApiResponse($orderId);
        $base['included'] = [
            [
                'type' => 'warehouses',
                'id' => 'warehouse-mock',
                'attributes' => [
                    'name' => 'Склад (mock)',
                    'address' => 'г. Алматы, ул. Примерная, 1',
                ],
            ],
        ];

        return $base;
    }

    public static function postOrderPayloadResponse(array $payload)
    {
        $id = isset($payload['data']['id']) ? (string) $payload['data']['id'] : 'orderID';
        $attrs = isset($payload['data']['attributes']) && is_array($payload['data']['attributes'])
            ? $payload['data']['attributes'] : [];
        $order = self::getSampleOrderResource($id);
        foreach ($attrs as $k => $v) {
            $order['attributes'][$k] = $v;
        }

        return ['data' => $order, 'included' => []];
    }

    public static function getOrderEntryApiResponse($orderId, $entryId)
    {
        return [
            'data' => [
                'type' => 'orderEntries',
                'id' => $entryId,
                'attributes' => [
                    'quantity' => self::$defaultOrderQuantity,
                    'basePrice' => self::$defaultProductPrice,
                    'totalPrice' => self::$defaultProductPrice,
                    'price' => self::$defaultProductPrice,
                    'title' => self::$defaultProductName,
                    'category' => 'Косметика',
                    'productCode' => self::$defaultProductCode,
                ],
            ],
            'orderId' => $orderId,
        ];
    }

    public static function getOrderEntryDeleteResponse()
    {
        return ['status' => 'deleted'];
    }

    public static function getOrderEntriesApiResponse($orderId)
    {
        $quantity = max(1, (int) self::$defaultOrderQuantity);
        // returnedQuantity = сколько возвращают (может быть < quantity для partial).
        // Используется OrderReturnService::readReturnRequestFromKaspi.
        $returnedQuantity = self::$defaultReturnedQuantity !== null
            ? max(0, (int) self::$defaultReturnedQuantity)
            : $quantity;

        return [
            'data' => [
                [
                    'type' => 'orderEntries',
                    'id' => self::$defaultEntryId,
                    'attributes' => [
                        'quantity'         => $quantity,
                        'returnedQuantity' => $returnedQuantity,
                        'basePrice'        => self::$defaultProductPrice,
                        'totalPrice'       => self::$defaultProductPrice,
                        'productCode'      => self::$defaultProductCode,
                        'productName'      => self::$defaultProductName,
                    ],
                ],
            ],
            'refundCode' => self::$defaultRefundCode,
            'orderId'    => $orderId,
        ];
    }

    public static function getPostOrdersApiResponse($orderId, $code, $status)
    {
        $base = 'https://kaspi.kz/shop/api/v2/orders/' . rawurlencode($orderId);

        return [
            'data' => [
                'type' => 'orders',
                'id' => $orderId,
                'attributes' => [
                    'code' => $code,
                    'status' => $status,
                ],
                'relationships' => [
                    'user' => [
                        'links' => [
                            'self' => $base . '/relationships/user',
                            'related' => $base . '/user',
                        ],
                        'data' => null,
                    ],
                    'entries' => [
                        'links' => [
                            'self' => $base . '/relationships/entries',
                            'related' => $base . '/entries',
                        ],
                    ],
                ],
                'links' => [
                    'self' => $base,
                ],
            ],
            'included' => [],
        ];
    }

    public static function getOrderStatusChangeApiResponse($orderId, $newStatus)
    {
        $order = self::getSampleOrderResource($orderId);
        $order['attributes']['status'] = $newStatus;

        return ['data' => $order];
    }

    /**
     * Минимальный валидный PDF для моков этикетки.
     * Содержит строку с orderId, чтобы по файлу было видно, на какой заказ мок.
     */
    public static function getShippingLabelPdfMock($orderId)
    {
        $text = 'Kaspi label mock for order ' . $orderId;
        $textLen = strlen($text);

        $pdf = "%PDF-1.4\n"
            . "1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n"
            . "2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n"
            . "3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj\n"
            . "4 0 obj<</Length " . ($textLen + 40) . ">>stream\n"
            . "BT /F1 12 Tf 72 720 Td (" . $text . ") Tj ET\n"
            . "endstream endobj\n"
            . "5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj\n"
            . "xref\n0 6\n0000000000 65535 f\n"
            . "trailer<</Size 6/Root 1 0 R>>\nstartxref\n0\n%%EOF";

        return [
            'mime' => 'application/pdf',
            'body' => $pdf,
        ];
    }

    public static function getProductsV2OffersSchema()
    {
        return [
            'schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'Kaspi products v2 offers schema mock',
            'type' => 'object',
            'properties' => [
                'offers' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'code' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'categoryCode' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function getProductsV2OffersImportStatus()
    {
        return [
            'status' => 'COMPLETED',
            'processed' => 1,
            'failed' => 0,
        ];
    }

    // --- Products API (/shop/api/products/…) — моки по гайду ---

    public static function getProductsImportSchema()
    {
        return [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'Kaspi product import (mock)',
            'type' => 'object',
            'properties' => [
                'products' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'code' => ['type' => 'string'],
                            'name' => ['type' => 'string'],
                            'categoryCode' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function getProductsImportPostResponse()
    {
        return [
            'code' => 'mock-import-' . substr(md5((string) microtime(true)), 0, 8),
        ];
    }

    public static function getProductsImportStatusByCode($importCode)
    {
        return [
            'code' => $importCode,
            'status' => 'FINISHED',
            'description' => 'Mock: импорт завершён',
        ];
    }

    public static function getProductsClassificationCategories()
    {
        return [
            [
                'code' => 'Master - 3D glasses for video equipment',
                'title' => '3D-очки для видеотехники',
            ],
            [
                'code' => 'Master - 3D printers',
                'title' => '3D-принтеры',
            ],
            [
                'code' => 'Master - 3D pens',
                'title' => '3D ручки',
            ],
            [
                'code' => 'Master - 3D Scanners',
                'title' => '3D Сканеры',
            ],
            [
                'code' => 'Master - Bluetooth adapters',
                'title' => 'Bluetooth адаптеры',
            ],
        ];
    }

    public static function getProductsClassificationAttributes($categoryCode)
    {
        return [
            [
                'code' => 'Dough sheeters*Power',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => true,
            ],
            [
                'code' => 'Dough sheeters*Performance',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => true,
            ],
            [
                'code' => 'Dough sheeters*Type',
                'type' => 'enum',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Appointment',
                'type' => 'enum',
                'multiValued' => true,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Voltage',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Installation method',
                'type' => 'enum',
                'multiValued' => false,
                'mandatory' => true,
            ],
            [
                'code' => 'Dough sheeters*Width of the unrolled belt',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Number of rolls',
                'type' => 'enum',
                'multiValued' => false,
                'mandatory' => true,
            ],
            [
                'code' => 'Dough sheeters*Roll diameter',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Width',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => true,
            ],
            [
                'code' => 'Dough sheeters*Height',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Length',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Weight',
                'type' => 'number',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Additionally',
                'type' => 'string',
                'multiValued' => false,
                'mandatory' => false,
            ],
            [
                'code' => 'Dough sheeters*Model',
                'type' => 'string',
                'multiValued' => false,
                'mandatory' => true,
            ],
            [
                'code' => 'Home equipment*Colour',
                'type' => 'enum',
                'multiValued' => true,
                'mandatory' => true,
            ],
            [
                'code' => 'Home equipment*Country',
                'type' => 'enum',
                'multiValued' => false,
                'mandatory' => false,
            ],
        ];
    }

    public static function getProductsClassificationAttributeValues($categoryCode, $attributeCode)
    {
        return [
            ['code' => 'red', 'name' => 'Красный'],
            ['code' => 'blue', 'name' => 'Синий'],
        ];
    }
}
