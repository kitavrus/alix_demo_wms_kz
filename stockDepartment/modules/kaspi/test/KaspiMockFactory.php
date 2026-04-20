<?php

namespace stockDepartment\modules\kaspi\test;

use stockDepartment\modules\kaspi\services\KaspiOrderHydrator;

/** Моки ответов Kaspi API (useMock). */
class KaspiMockFactory
{
    private static $defaultOrderId = 'ODk2ODg0NjEw';
    private static $defaultEntryId = 'ODk2ODg0NjEwIyMw';
    private static $defaultCustomerId = 'NzAyNjM4NTcwNQ';
    // Артикул реального товара для поллинга через cron/kaspi-poll-orders —
    // должен существовать в product_v2 / ecommerce_stock, иначе импорт упадёт на резерве.
    private static $defaultProductCode = '1100005012';
    private static $defaultProductName = 'ЖИДКИЙ КОНСИЛЕР 101 LIGHT IVORY';
    private static $defaultProductPrice = 4990;

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
                'status' => 'APPROVED_BY_BANK',
                'state' => 'NEW',
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
        $orderId = self::$defaultOrderId;

        return [
            'data' => [
                self::getSampleOrderResource($orderId),
            ],
            'included' => [],
            'meta' => [
                'pageCount' => 1,
                'totalCount' => 1,
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
        $full = self::getOrdersApiResponse();
        $included = isset($full['included']) ? $full['included'] : [];
        foreach ($full['data'] as $row) {
            if (isset($row['id']) && $row['id'] === $orderId) {
                return KaspiOrderHydrator::hydrateSingleOrder($row, $included);
            }
        }

        return null;
    }

    public static function getOrderApiResponse($orderId)
    {
        $order = self::getSampleOrderResource($orderId);
        $order['attributes']['code'] = 'KZ-' . $orderId;

        return ['data' => $order];
    }

    public static function getOrderByIdRawApiResponse($orderId)
    {
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
                    'quantity' => 1,
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
        return [
            'data' => [
                [
                    'type' => 'orderEntries',
                    'id' => self::$defaultEntryId,
                    'attributes' => [
                        'quantity' => 1,
                        'basePrice' => self::$defaultProductPrice,
                        'totalPrice' => self::$defaultProductPrice,
                        'productCode' => self::$defaultProductCode,
                        'productName' => self::$defaultProductName,
                    ],
                ],
            ],
            'orderId' => $orderId,
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
