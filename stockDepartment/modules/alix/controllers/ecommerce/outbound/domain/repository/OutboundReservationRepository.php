<?php

namespace stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\repository;

use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\constants\OutboundPlaceAddressSorting;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use stockDepartment\modules\alix\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceStock;
use common\modules\stock\models\Stock;
use stockDepartment\modules\alix\controllers\product\domains\ProductService;

/**
 * Резерв склада под outbound-заказ. Работает c `ecommerce_stock`.
 * `Stock` подключён только для констант статусов.
 */
class OutboundReservationRepository
{
    public function getClientID()
    {
        return 103;
    }

    public function beforeReservationSorting($orderIdList)
    {
        return EcommerceOutbound::find()
            ->select('id')
            ->andWhere(['id' => $orderIdList])
            ->orderBy('expected_qty')
            ->column();
    }

    public static function makePlaceAddressSort1($addressBarcode)
    {
        $addressBarcodeList = explode('-', (string) $addressBarcode);
        if (empty($addressBarcodeList) || !is_array($addressBarcodeList) || !isset($addressBarcodeList[1], $addressBarcodeList[2])) {
            return OutboundPlaceAddressSorting::INCORRECT_PLACE_ADDRESS_SORT1;
        }
        return $addressBarcodeList[1] . $addressBarcodeList[2];
    }

    public function changeBoxPlaceAddress($BoxBarcode, $PlaceBarcode)
    {
        EcommerceStock::updateAll(
            ['place_address_sort1' => self::makePlaceAddressSort1($PlaceBarcode)],
            [
                'box_address_barcode'   => $BoxBarcode,
                'place_address_barcode' => $PlaceBarcode,
            ]
        );
    }

    public function getStocksByProductBarcode($clientId, $productBarcode, $expectedQty)
    {
        return EcommerceStock::find()
            ->andWhere([
                'client_id'           => $clientId,
                'product_id'          => (new ProductService())->getProductIdByBarcode($productBarcode),
                'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                'condition_type'      => [Stock::CONDITION_TYPE_NOT_SET, Stock::CONDITION_TYPE_UNDAMAGED],
                'deleted'             => 0,
            ])
            ->orderBy('place_address_sort1')
            ->limit($expectedQty)
            ->all();
    }

    public function getStocksByProductSku($clientId, $productSku, $expectedQty)
    {
        return EcommerceStock::find()
            ->andWhere([
                'client_id'           => $clientId,
                'product_sku'         => $productSku,
                'status_availability' => EcommerceStock::STATUS_AVAILABILITY_YES,
                'condition_type'      => [Stock::CONDITION_TYPE_NOT_SET, Stock::CONDITION_TYPE_UNDAMAGED],
                'deleted'             => 0,
            ])
            ->orderBy('place_address_sort1')
            ->limit($expectedQty)
            ->all();
    }

    public function resetByOutboundOrderId($outbound_order_id)
    {
        if ($outboundOrder = EcommerceOutbound::findOne($outbound_order_id)) {
            EcommerceOutbound::updateAll(
                ['accepted_qty' => '0', 'allocated_qty' => '0', 'status' => OutboundStatus::getNEW()],
                ['id' => $outboundOrder->id]
            );
            EcommerceOutboundItem::updateAll(
                ['accepted_qty' => '0', 'allocated_qty' => '0', 'status' => OutboundStatus::getNEW()],
                ['outbound_id' => $outboundOrder->id]
            );
            EcommerceStock::updateAll(
                [
                    'box_barcode'          => '',
                    'outbound_id'          => 0,
                    'outbound_item_id'     => 0,
                    'scan_out_datetime'    => 0,
                    'scan_out_employee_id' => 0,
                    'status'               => Stock::STATUS_INBOUND_CONFIRM,
                    'status_availability'  => EcommerceStock::STATUS_AVAILABILITY_YES,
                ],
                ['outbound_id' => $outboundOrder->id]
            );
        }
    }
}
