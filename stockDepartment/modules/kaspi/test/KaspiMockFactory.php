<?php

namespace stockDepartment\modules\kaspi\test;

use stockDepartment\modules\kaspi\services\KaspiOrderHydrator;

/** Моки ответов Kaspi API (useMock). */
class KaspiMockFactory
{
    private static $defaultOrderId = 'orderID';

    private static function getSampleOrderResource($orderId = null)
    {
        $id = $orderId !== null ? $orderId : self::$defaultOrderId;

        return [
            'type' => 'orders',
            'id' => $id,
            'attributes' => [
                'customer' => [
                    'firstName' => 'Иван Иваныч',
                    'lastName' => 'Иванов',
                    'cellPhone' => '7xx0xxxxxx',
                ],
                'code' => 'ordercode',
                'totalPrice' => 96045,
                'deliveryMode' => 'DELIVERY_PICKUP',
                'paymentMode' => 'PAY_WITH_CREDIT',
                'signatureRequired' => false,
                'state' => 'PICKUP',
                'creationDate' => 1479470446241,
                'approvedByBankDate' => 1479470451108,
                'status' => 'ACCEPTED_BY_MERCHANT',
                'deliveryCost' => 1000,
                'isImeiRequired' => false,
            ],
            'relationships' => [
                'entries' => [
                    'links' => [
                        'self' => "/v2/orders/{$id}/relationships/entries",
                        'related' => "/v2/orders/{$id}/entries",
                    ],
                ],
                'user' => [
                    'links' => [
                        'self' => "/v2/orders/{$id}/relationships/user",
                        'related' => "/v2/orders/{$id}/user",
                    ],
                    'data' => [
                        'type' => 'customers',
                        'id' => 'customerID',
                    ],
                ],
            ],
            'links' => [
                'self' => "/v2/orders/{$id}",
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
            'included' => [
                [
                    'type' => 'customers',
                    'id' => 'customerID',
                    'attributes' => [
                        'firstName' => 'Иван',
                        'lastName' => 'Иваныч',
                        'cellPhone' => '7xx0xxxxxx',
                    ],
                    'relationships' => [],
                    'links' => [
                        'self' => '/v2/customers/customerID',
                    ],
                ],
            ],
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
                    'basePrice' => 10000,
                    'totalPrice' => 10000,
                    'price' => 10000,
                    'title' => 'Наименование (mock)',
                    'category' => 'Категория (mock)',
                    'imei' => '353490123456789',
                    'productCode' => 'SKU-123',
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
                    'id' => 'entry-1',
                    'attributes' => [
                        'quantity' => 1,
                        'basePrice' => 10000,
                        'totalPrice' => 10000,
                        'productCode' => 'SKU-123',
                        'productName' => 'Пример товара',
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

    public static function getOrderWaybillApiResponse($orderId)
    {
        return [
            'orderId' => $orderId,
            'waybillNumber' => 'WB-' . $orderId,
            'waybillUrl' => 'https://kaspi.kz/waybills/' . $orderId . '.pdf',
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
