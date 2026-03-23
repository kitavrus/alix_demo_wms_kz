<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 9:33
 */

namespace common\ecommerce\defacto\returnOutbound\repository;


use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\constants\ReturnOutboundStatus;

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
        return EcommerceReturnItem::find()->andWhere(['return_id' => $returnId, 'product_barcode' => $productBarcode])->exists();
    }

    public function isOrderExistByAny($anyField)
    {
        return EcommerceReturn::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere('client_ReferenceNumber = :client_ReferenceNumber OR order_number = :order_number',[':client_ReferenceNumber'=>$anyField,':order_number'=>$anyField])
            ->exists();
    }

    //
    public function getOrderByAny($anyField)
    {
        return EcommerceReturn::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere('client_ReferenceNumber = :client_ReferenceNumber OR order_number = :order_number',[':client_ReferenceNumber'=>$anyField,':order_number'=>$anyField])
            ->one();
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
        return EcommerceReturnItem::find()->select('product_barcode, expected_qty,	accepted_qty')->andWhere([
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
            ->select('product_barcode, condition_type, count(product_barcode) as qtyProduct')
            ->andWhere([
                'return_id' => $returnId,
                'client_id' => $this->getClientID(),
                'api_status' => [StockAPIStatus::NO],
            ])
            ->groupBy('product_barcode, condition_type')
            ->asArray()
            ->all();

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
            'status_availability'=>StockAvailability::YES
        ];
        $condition = [
            'return_id' => $returnId,
            'client_id' => $this->getClientID(),
        ];

        return EcommerceStock::updateAll($attributes,$condition);

    }

    public function complete_07_04_2020($returnId)
    {
        $attributes = [
            'status_availability'=>StockAvailability::YES
        ];
        $condition = [
            'return_id' => $returnId,
            'client_id' => $this->getClientID(),
        ];

        return EcommerceStock::updateAll($attributes,$condition);

    }

}