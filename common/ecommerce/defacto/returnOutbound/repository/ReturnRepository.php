<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 9:33
 */

namespace common\ecommerce\defacto\returnOutbound\repository;


use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;

class ReturnRepository
{
    public function getClientID()
    {
        return 2;
    }
    //
    public function isOrderExist($orderNumber)
    {
        return EcommerceReturn::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->exists();
    }
    //
    public function isOrderItemExist($returnId,$productBarcode)
    {
        return EcommerceReturnItem::find()
            ->andWhere(['return_id' => $returnId, 'product_barcode' => $productBarcode])
            ->andWhere('expected_qty > 0')
            ->exists();
    }

    public function isOrderExistByAny($anyField)
    {
        return EcommerceReturn::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere([
                'or',
                ['like', 'client_ReferenceNumber', $anyField],
                ['like', 'order_number', $anyField],
            ])
            ->exists();
    }

    //
    public function getOrderByAny($anyField)
    {
        return EcommerceReturn::find()
            ->andWhere(['client_id' => $this->getClientID()])
        ->andWhere([
            'or',
            ['like', 'client_ReferenceNumber', $anyField],
            ['like', 'order_number', $anyField],
        ])->one();
    }

    //
    public function getOrderItemByProductBarcode($returnId,$productBarcode)
    {
        return EcommerceReturnItem::find()->andWhere(['return_id' => $returnId, 'product_barcode' => $productBarcode])->one();
    }
    //
    public function getOrderByOrderNumber($orderNumber)
    {
        return EcommerceReturn::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->one();
    }
    //
    public function getOrderInfo($id)
    {
        $order = EcommerceReturn::find()->andWhere([
            "id" => $id,
            "client_id" => $this->getClientID(),
        ])->one();

        $items = EcommerceReturnItem::find()->andWhere(['return_id' => $order->id])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }
    //
    public function getQtyInBox($returnId,$boxBarcode)
    {
        return EcommerceStock::find()->andWhere([
            'client_id' => $this->getClientID(),
            'return_id' => $returnId,
            'box_address_barcode' => $boxBarcode,
        ])->count();
    }
    //
    public function getItemsInBox($returnId,$boxBarcode)
    {
        return EcommerceStock::find()->select('product_barcode, box_address_barcode, count(product_barcode) as qtyProduct')->andWhere([
            'client_id' => $this->getClientID(),
            'return_id' => $returnId,
            'box_address_barcode' => $boxBarcode,
        ])
        ->groupBy('product_barcode')
        ->orderBy(['id'=>SORT_DESC])
        ->asArray()
        ->all();
    }
    //
    public function getItemsInOrder($returnId)
    {
        return EcommerceReturnItem::find()->select('product_barcode, product_barcode1, product_barcode2, product_barcode3, expected_qty, accepted_qty')->andWhere([
            'return_id' => $returnId,
        ])
        ->orderBy(['id'=>SORT_DESC])
        ->asArray()
        ->all();
    }

    public function isExtraBarcodeInOrder($returnId, $productBarcode)
    {
        return EcommerceReturnItem::find()->andWhere([
            'return_id' => $returnId,
            'product_barcode' => $productBarcode,
        ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();
    }

    public function emptyBoxOnStock($returnId,$boxBarcode)
    {
        return EcommerceStock::deleteAll([
                'client_id' => $this->getClientID(),
                'return_id' => $returnId,
                'box_address_barcode' => $boxBarcode,
            ]);
    }

    public function getScannedProductOnStock($returnId,$productBarcode)
    {
        return EcommerceStock::find()->andWhere(
            [
                'client_id' => $this->getClientID(),
                'return_id' => $returnId,
                'product_barcode' => $productBarcode,
            ])->count();
    }

    public function getAllScannedProductOnStock($returnId)
    {
        return EcommerceStock::find()->andWhere(
            [
                'client_id' => $this->getClientID(),
                'return_id' => $returnId,
            ])->count();
    }

    public function getProductsReadyForSendByAPI($returnId)
    {
        $order = EcommerceReturn::find()->andWhere([
            "id" => $returnId,
            "client_id" => $this->getClientID(),
        ])->one();

        $items = EcommerceStock::find()
            ->select('product_barcode, condition_type, count(product_barcode) as qtyProduct, client_product_sku')
            ->andWhere([
                'return_id' => $returnId,
                'client_id' => $this->getClientID(),
                'api_status' => [StockAPIStatus::NO],
            ])
            ->groupBy('product_barcode, condition_type')
            ->asArray()
            ->all();

        foreach ($items as &$item) {
            $item['client_product_sku'] =  EcommerceReturnItem::find()
                                           ->select('client_SkuId')
                                           ->andWhere(['return_id' => $returnId,'product_barcode' => $item['product_barcode']])
                                           ->scalar();
        }

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }

    public function complete($returnId)
    {
        $order = EcommerceReturn::find()->andWhere([
            "id" => $returnId,
            "client_id" => $this->getClientID(),
        ])->one();

        $order->status = ReturnOutboundStatus::DONE;
        $order->save(false);

        $attributes = [
            'api_status'=>StockAPIStatus::YES,
            'status_availability'=>StockAvailability::YES,
        ];
        $condition = [
            'return_id' => $returnId,
            'client_id' => $this->getClientID(),
        ];

        return EcommerceStock::updateAll($attributes,$condition);
    }

    public function isExistOrderFromAPI($aExternalShipmentId,$aExternalOrderId) {
        return EcommerceReturn::find()->andWhere([
            "client_ExternalShipmentId" => $aExternalShipmentId,
            "client_ExternalOrderId" => $aExternalOrderId,
            "client_id" => $this->getClientID(),
        ])->exists();
    }

    public function getRealProductBarcodeByBarcode($aProductBarcode,$returnId)
    {
        return EcommerceReturnItem::find()
            ->andWhere(['return_id' => $returnId])
            ->andWhere('product_barcode = :productBarcode OR product_barcode1 = :productBarcode OR product_barcode2 = :productBarcode OR product_barcode3 = :productBarcode OR product_barcode4 = :productBarcode',[':productBarcode' => $aProductBarcode])
            ->one();

//        return !empty($one) ? $aProductBarcode : '-1';
    }

    public function getOutboundBox($aOutboundId)
    {
       return EcommerceStock::find()->select('outbound_box')
            ->andWhere(['outbound_id' => $aOutboundId])
            ->andWhere('outbound_box != 0 AND outbound_box IS NOT NULL')
            ->scalar();
    }
}