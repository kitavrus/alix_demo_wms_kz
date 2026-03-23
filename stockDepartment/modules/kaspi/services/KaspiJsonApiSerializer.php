<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\dto\KaspiOrderListPayload;
use stockDepartment\modules\kaspi\dto\OrderDto;

class KaspiJsonApiSerializer
{
    public static function orderListToResponse(KaspiOrderListPayload $payload)
    {
        $data = [];
        foreach ($payload->orders as $order) {
            $data[] = self::orderDtoToResource($order);
        }

        $included = $payload->included;
        if ($included === [] || $included === null) {
            $included = self::collectIncludedFromOrders($payload->orders);
        }

        $meta = $payload->meta;
        if ($meta === [] || $meta === null) {
            $meta = new \stdClass();
        }

        return [
            'data' => $data,
            'included' => $included,
            'meta' => $meta,
        ];
    }

    private static function collectIncludedFromOrders(array $orders)
    {
        $byKey = [];
        foreach ($orders as $o) {
            if (!$o->customerIncluded) {
                continue;
            }
            $userData = isset($o->relationships['user']['data']) ? $o->relationships['user']['data'] : null;
            if (!is_array($userData) || !isset($userData['id'])) {
                continue;
            }
            if (isset($userData['type']) && $userData['type'] !== 'customers') {
                continue;
            }
            $cid = $userData['id'];
            $key = 'customers:' . $cid;
            if (isset($byKey[$key])) {
                continue;
            }
            $byKey[$key] = [
                'type' => 'customers',
                'id' => $cid,
                'attributes' => $o->customerIncluded->toArray(),
                'relationships' => new \stdClass(),
                'links' => [
                    'self' => '/v2/customers/' . $cid,
                ],
            ];
        }

        return array_values($byKey);
    }

    public static function singleOrderToResponse(OrderDto $order)
    {
        $out = [
            'data' => self::orderDtoToResource($order),
        ];
        $included = self::buildIncludedForOrder($order);
        if ($included !== []) {
            $out['included'] = $included;
        }

        return $out;
    }

    public static function orderDtoToResource(OrderDto $o)
    {
        $relationships = $o->relationships !== null && is_array($o->relationships)
            ? $o->relationships
            : self::fallbackRelationships($o->id);
        $links = $o->resourceLinks !== null && is_array($o->resourceLinks)
            ? $o->resourceLinks
            : self::defaultLinks($o->id);

        return [
            'type' => $o->resourceType !== null && $o->resourceType !== '' ? $o->resourceType : 'orders',
            'id' => $o->id,
            'attributes' => self::buildAttributes($o),
            'relationships' => $relationships,
            'links' => $links,
        ];
    }

    private static function buildAttributes(OrderDto $o)
    {
        $attrs = [];

        $attrs['customer'] = $o->customer ? $o->customer->toArray() : [
            'firstName' => '',
            'lastName' => '',
            'cellPhone' => '',
        ];
        $attrs['code'] = $o->code;
        $attrs['totalPrice'] = $o->totalPrice;
        $attrs['deliveryMode'] = $o->deliveryMode;
        if ($o->isKaspiDelivery !== null) {
            $attrs['isKaspiDelivery'] = $o->isKaspiDelivery;
        }
        $attrs['paymentMode'] = $o->paymentMode;
        if ($o->signatureRequired !== null) {
            $attrs['signatureRequired'] = $o->signatureRequired;
        }
        if ($o->creditTerm !== null && $o->creditTerm !== '') {
            $attrs['creditTerm'] = $o->creditTerm;
        }
        $attrs['state'] = $o->state;
        $attrs['creationDate'] = $o->creationDate;
        if ($o->approvedByBankDate !== null) {
            $attrs['approvedByBankDate'] = $o->approvedByBankDate;
        }
        $attrs['status'] = $o->status;
        $attrs['deliveryCost'] = $o->deliveryCost;
        if ($o->isImeiRequired !== null) {
            $attrs['isImeiRequired'] = $o->isImeiRequired;
        }

        $extraKeys = [
            'preOrder', 'plannedDeliveryDate', 'reservationDate', 'waybill',
            'courierTransmissionPlanningDate', 'courierTransmissionDate',
            'deliveryAddress', 'waybillNumber', 'category', 'deliveryCostForSeller',
            'express', 'returnedToWarehouse', 'entries', 'user',
        ];
        foreach ($extraKeys as $key) {
            $v = $o->$key;
            if ($v !== null && $v !== '') {
                $attrs[$key] = $v;
            }
        }

        return $attrs;
    }

    private static function fallbackRelationships($orderId)
    {
        $id = (string) $orderId;

        return [
            'entries' => [
                'links' => [
                    'self' => "/v2/orders/{$id}/relationships/entries",
                    'related' => "/v2/orders/{$id}/entries",
                ],
            ],
        ];
    }

    private static function defaultLinks($orderId)
    {
        return [
            'self' => '/v2/orders/' . $orderId,
        ];
    }

    private static function buildIncludedForOrder(OrderDto $o)
    {
        if (!$o->customerIncluded) {
            return [];
        }
        $userData = isset($o->relationships['user']['data']) ? $o->relationships['user']['data'] : null;
        if (!is_array($userData) || !isset($userData['id'])) {
            return [];
        }
        $cid = $userData['id'];

        return [
            [
                'type' => 'customers',
                'id' => $cid,
                'attributes' => $o->customerIncluded->toArray(),
                'relationships' => new \stdClass(),
                'links' => [
                    'self' => '/v2/customers/' . $cid,
                ],
            ],
        ];
    }
}
