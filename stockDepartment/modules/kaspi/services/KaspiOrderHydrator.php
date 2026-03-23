<?php

namespace stockDepartment\modules\kaspi\services;

use stockDepartment\modules\kaspi\dto\CustomerDto;
use stockDepartment\modules\kaspi\dto\KaspiOrderListPayload;
use stockDepartment\modules\kaspi\dto\OrderDto;

class KaspiOrderHydrator
{
    public static function hydrateOrderListResponse(array $apiResponse)
    {
        $payload = new KaspiOrderListPayload();
        $included = isset($apiResponse['included']) && is_array($apiResponse['included'])
            ? $apiResponse['included'] : [];
        $payload->orders = self::hydrateOrdersFromApi(
            isset($apiResponse['data']) && is_array($apiResponse['data']) ? $apiResponse['data'] : [],
            $included
        );
        $payload->meta = isset($apiResponse['meta']) && is_array($apiResponse['meta'])
            ? $apiResponse['meta'] : [];
        $payload->included = $included;

        return $payload;
    }

    public static function hydrateOrdersFromApi(array $ordersData, array $included = [])
    {
        return array_map(function (array $orderData) use ($included) {
            return self::hydrateSingleOrder($orderData, $included);
        }, $ordersData);
    }

    public static function hydrateSingleOrder(array $orderData, array $included = [])
    {
        $attributes = isset($orderData['attributes']) ? $orderData['attributes'] : [];
        $customerData = isset($attributes['customer']) ? $attributes['customer'] : [];

        $customer = new CustomerDto(
            isset($customerData['firstName']) ? $customerData['firstName'] : '',
            isset($customerData['lastName']) ? $customerData['lastName'] : '',
            isset($customerData['cellPhone']) ? $customerData['cellPhone'] : ''
        );

        $dto = new OrderDto(
            isset($orderData['id']) ? $orderData['id'] : '',
            isset($attributes['code']) ? $attributes['code'] : '',
            $customer,
            isset($attributes['totalPrice']) ? $attributes['totalPrice'] : 0,
            isset($attributes['deliveryMode']) ? $attributes['deliveryMode'] : '',
            isset($attributes['paymentMode']) ? $attributes['paymentMode'] : '',
            isset($attributes['state']) ? $attributes['state'] : '',
            isset($attributes['creationDate']) ? $attributes['creationDate'] : 0,
            isset($attributes['status']) ? $attributes['status'] : '',
            isset($attributes['deliveryCost']) ? $attributes['deliveryCost'] : 0,
            isset($attributes['isKaspiDelivery']) ? $attributes['isKaspiDelivery'] : null,
            isset($attributes['signatureRequired']) ? $attributes['signatureRequired'] : null,
            isset($attributes['creditTerm']) ? $attributes['creditTerm'] : null,
            isset($attributes['approvedByBankDate']) ? $attributes['approvedByBankDate'] : null,
            isset($attributes['isImeiRequired']) ? $attributes['isImeiRequired'] : null,
            isset($attributes['category']) ? $attributes['category'] : null,
            isset($attributes['preOrder']) ? $attributes['preOrder'] : null,
            isset($attributes['plannedDeliveryDate']) ? $attributes['plannedDeliveryDate'] : null,
            isset($attributes['reservationDate']) ? $attributes['reservationDate'] : null,
            isset($attributes['waybill']) ? $attributes['waybill'] : null,
            isset($attributes['courierTransmissionPlanningDate']) ? $attributes['courierTransmissionPlanningDate'] : null,
            isset($attributes['courierTransmissionDate']) ? $attributes['courierTransmissionDate'] : null,
            isset($attributes['deliveryAddress']) ? $attributes['deliveryAddress'] : null,
            isset($attributes['waybillNumber']) ? $attributes['waybillNumber'] : null,
            isset($attributes['deliveryCostForSeller']) ? $attributes['deliveryCostForSeller'] : null,
            isset($attributes['express']) ? $attributes['express'] : null,
            isset($attributes['returnedToWarehouse']) ? $attributes['returnedToWarehouse'] : null,
            isset($attributes['entries']) ? $attributes['entries'] : null,
            isset($attributes['user']) ? $attributes['user'] : null
        );

        $dto->resourceType = isset($orderData['type']) ? $orderData['type'] : null;
        $dto->resourceLinks = isset($orderData['links']) && is_array($orderData['links'])
            ? $orderData['links'] : null;
        $dto->relationships = isset($orderData['relationships']) && is_array($orderData['relationships'])
            ? $orderData['relationships'] : null;
        $dto->customerIncluded = self::pickCustomerFromIncluded($orderData, $included);

        return $dto;
    }

    private static function pickCustomerFromIncluded(array $orderData, array $included)
    {
        $userRel = isset($orderData['relationships']['user']['data'])
            ? $orderData['relationships']['user']['data'] : null;
        if (!is_array($userRel) || !isset($userRel['type'], $userRel['id'])) {
            return null;
        }
        if ($userRel['type'] !== 'customers') {
            return null;
        }
        $cid = $userRel['id'];
        foreach ($included as $item) {
            if (!is_array($item) || !isset($item['type'], $item['id'])) {
                continue;
            }
            if ($item['type'] === 'customers' && $item['id'] === $cid) {
                $a = isset($item['attributes']) ? $item['attributes'] : [];

                return new CustomerDto(
                    isset($a['firstName']) ? $a['firstName'] : '',
                    isset($a['lastName']) ? $a['lastName'] : '',
                    isset($a['cellPhone']) ? $a['cellPhone'] : ''
                );
            }
        }

        return null;
    }
}
