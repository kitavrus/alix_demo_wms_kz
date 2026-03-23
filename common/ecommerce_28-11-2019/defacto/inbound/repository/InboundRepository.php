<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:01
 */

namespace common\ecommerce\defacto\inbound\repository;

use common\ecommerce\constants\InboundStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockInboundStatus;
use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItem;
use common\ecommerce\entities\EcommerceStock;
use yii\db\Expression;

class InboundRepository
{
    private $inboundOrderID;
//    private $clientId;

    /**
     * InboundRepository constructor.
     * @param $inboundOrderID
     */
    public function __construct($dto = [])
    {
//        $this->clientId = 96;
    }
    //
    public function getClientID()
    {
        return 2;
    }
    //
    public function create($data)
    {
        $orderID = $this->createOrder($data);
        $this->createOrderItems($data, $orderID);

        $this->setInboundOrderID($orderID);
        return $orderID;
    }
    //
    public function isOrderExist($orderNumber)
    {
        return EcommerceInbound::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->exists();
    }
    //
    private function createOrder($data)
    {
        $inboundOrder = new EcommerceInbound();
        $inboundOrder->client_id = $this->getClientID();
        $inboundOrder->order_number = $data->orderNumber;
//        $inboundOrder->supplier_id = $data->supplierId;
//        $inboundOrder->order_type = EcommerceInbound::ORDER_TYPE_INBOUND;
        $inboundOrder->status = InboundStatus::_NEW;
//        $inboundOrder->cargo_status = EcommerceInbound::CARGO_STATUS_NEW;
        $inboundOrder->expected_box_qty = $data->expectedBoxQty;
//        $inboundOrder->accepted_qty = 0;
//        $inboundOrder->accepted_number_places_qty = $data->expectedTotalPlaceQty;
//        $inboundOrder->expected_number_places_qty = 0;
//        $inboundOrder->comments = '?';
        $inboundOrder->save(false);

        return $inboundOrder->id;
    }
    //
    private function createOrderItems($data, $orderId)
    {
        if(empty($data->items)) { return;}

        foreach ($data->items as $item) {
            $inboundOrderItem = new EcommerceInboundItem();
            $inboundOrderItem->inbound_order_id = $orderId;
            $inboundOrderItem->product_name = $item->productName;
            $inboundOrderItem->product_model = $item->productModel;
            $inboundOrderItem->product_barcode = $item->productModel;
            $inboundOrderItem->expected_qty = $item->expectedProductQty;
            $inboundOrderItem->expected_number_places_qty = $item->expectedPlaceQty;
            $inboundOrderItem->save(false);
        }
    }
    //
    public function isProductModelBarcodeExistInOrder($productModelBarcode, $inboundOrderID)
    {
        return EcommerceInboundItem::find()->andWhere([
            'inbound_order_id' => $inboundOrderID,
            'product_model' => $productModelBarcode,
        ])->exists();
    }
//    //
//    public function isProductBarcodeExistInOrder($productBarcode, $inboundOrderID)
//    {
//        return EcommerceInboundItem::find()->andWhere([
//            'inbound_id' => $inboundOrderID,
//            'product_barcode' => $productBarcode,
//        ])->exists();
//    }
    //
    public function isPlusQtyBarcodeInOrder($productBarcode, $inboundOrderID,$qty)
    {
        return EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $inboundOrderID,
            'product_barcode' => $productBarcode,
        ])->andWhere('expected_qty >= accepted_qty + '.(int)$qty.' AND expected_qty != 0')->exists();
    }
    //
//    public function isExtraBarcodeInOrder($productBarcode, $inboundOrderID)
//    {
//        return EcommerceInboundItem::find()->andWhere([
//            'inbound_id' => $inboundOrderID,
//            'product_barcode' => $productBarcode,
//        ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')->exists();
//    }
    //
    public function getNewAndInProcessOrder()
    {
        return EcommerceInbound::find()
            ->andWhere(['client_id' => $this->getClientID()])
            ->andWhere(['status' =>InboundStatus::getNewAndInProcessOrder()])
            ->asArray()
            ->all();
    }
    //
    public function getQtyInOrder($id)
    {
        $inboundOrder = EcommerceInbound::find()
            ->select('expected_box_qty, accepted_box_qty, expected_lot_qty, accepted_lot_qty, expected_product_qty, accepted_product_qty')
            ->andWhere(['id' => $id, 'client_id' => $this->getClientID()])
            ->one();

        $dto = new \stdClass();
        $dto->expected_box_qty = 0;
        $dto->accepted_box_qty = 0;
        $dto->expected_lot_qty = 0;
        $dto->accepted_lot_qty = 0;
        $dto->expected_product_qty = 0;
        $dto->accepted_product_qty = 0;

        if ($inboundOrder != null) {
            $dto->expected_box_qty = $inboundOrder->expected_box_qty;
            $dto->accepted_box_qty = $inboundOrder->accepted_box_qty;
            $dto->expected_lot_qty = $inboundOrder->expected_lot_qty;
            $dto->accepted_lot_qty = $inboundOrder->accepted_lot_qty;
            $dto->expected_product_qty = $inboundOrder->expected_product_qty;
            $dto->accepted_product_qty = $inboundOrder->accepted_product_qty;
        }

        return $dto;
    }
    //
    public function getQtyModelsInOrder($inboundOrderID, $productModel)
    {
        $inboundOrderItem = EcommerceInboundItem::find()->select('expected_qty, accepted_qty')
            ->andWhere(['product_barcode' => $productModel, 'inbound_id' => $inboundOrderID])
            ->one();

        $dto = new \stdClass();
        $dto->expected_qty = 0;
        $dto->accepted_qty = 0;

        if ($inboundOrderItem != null) {
            $dto->expected_qty = $inboundOrderItem->expected_qty;
            $dto->accepted_qty = $inboundOrderItem->accepted_qty;
        }
        return $dto;
    }
    //
//    public function addScannedProductToStock($dto)
//    {
//        $stock = new EcommerceStock();
//        $stock->client_id = $this->getClientID();
//        $stock->inbound_id = $dto->orderNumberId;
//        $stock->product_barcode = $dto->productBarcode;
////        $stock->product_model = $dto->productModel;
//        $stock->primary_address = $dto->transportedBoxBarcode;
////        $stock->status = EcommerceStock::STATUS_INBOUND_SCANNED;
//        $stock->status_inbound = StockInboundStatus::SCANNED;
//        $stock->status_availability = StockAvailability::NO;
//        $stock->scan_in_datetime = time();
//        $stock->save(false);
//
//        $inboundItemID = $this->updateAcceptedQtyItemByProductBarcode($dto->orderNumberId, $dto->productBarcode);
//        $stock->inbound_order_item_id = $inboundItemID;
//        $stock->save(false);
//
//        return $stock->id;
//    }

    //
//    public function updateAcceptedQtyItemByProductBarcode($inboundId, $productBarcode)
//    {
//        $inboundItem = EcommerceInboundItem::find()->andWhere([
//            'inbound_id' => $inboundId,
//            'product_barcode' => $productBarcode,
//        ])->one();
//
//        if ($inboundItem) {
//            $inboundItem->accepted_qty = $this->getScannedProductQtyByOrderInStock($inboundId, $productBarcode);
//            $inboundItem->save(false);
//            return $inboundItem->id;
//        }
//        return -1;
//    }

    //
    public function updateAcceptedQtyItemByProductModelBarcode($inboundId, $productBarcode)
    {
        $inboundItem = EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $inboundId,
            'product_barcode' => $productBarcode,
        ])->one();

        if ($inboundItem) {
            $inboundItem->accepted_qty = $this->getScannedProductQtyByModelOrderInStock($inboundId, $productBarcode);
            $inboundItem->save(false);
            return $inboundItem->id;
        }
        return -1;
    }

    //
    public function updateAcceptedQtyItems($inboundId,$clientBoxBarcode,$productBarcodeWithQtyInBox)
    {
        foreach($productBarcodeWithQtyInBox as $product) {

            $inboundItems = EcommerceInboundItem::find()->andWhere([
                'inbound_id' => $inboundId,
                'product_barcode' => $product['product_barcode'],
                'client_box_barcode' =>$clientBoxBarcode,
            ])->all();

            foreach ($inboundItems as $inboundItem) {

                $productAcceptedQty = $this->getScannedProductQtyByModelOrderInStock($inboundId,$clientBoxBarcode, $inboundItem->product_barcode);
                $inboundItem->product_accepted_qty = $productAcceptedQty;

                if($productAcceptedQty < 1) {
                    $inboundItem->status = InboundStatus::_NEW;
                }

                $inboundItem->save(false);
            }
        }
    }

//    //
//    private function getScannedProductQtyByOrderInStock($inboundId, $productBarcode)
//    {
//        return EcommerceStock::find()->andWhere([
//            'inbound_id' => $inboundId,
//            'product_barcode' => $productBarcode,
////            'status' => Stock::STATUS_INBOUND_SCANNED,
//        ])->count();
//    }

    //
    private function getScannedProductQtyByModelOrderInStock($inboundId,$clientBoxBarcode, $productBarcode)
    {
        return EcommerceStock::find()->andWhere([
            'inbound_id' => $inboundId,
            'product_barcode' => $productBarcode,
            'client_box_barcode' =>$clientBoxBarcode,
            'status_inbound' =>[
                StockInboundStatus::SCANNED,
                StockInboundStatus::OVER_SCANNED
            ],
        ])->count();
    }

    //
//    public function updateQtyScannedInOrder($orderId, $acceptedQty)
//    {
//        $inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
//
//        if(empty($inbound)) {
//            return;
//        }
//        $inbound->accepted_qty = $acceptedQty;
//
//        if (empty($inbound->begin_datetime)) {
//            $inbound->begin_datetime = time();
//        }
//
//        $inbound->end_datetime = time();
//        $inbound->save(false);
//    }

    //
    public function setOrderStatusInProcess($orderId)
    {
        $inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
        if ($inbound) {
            $inbound->status = InboundStatus::SCANNING;
            $inbound->save(false);
        }
    }

    //
//    public function setOrderStatusClose($orderId)
//    {
//        $inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
//        if ($inbound) {
//            $inbound->status = InboundStatus::DONE;
//            $inbound->save(false);
//        }
//    }

//    //
//    public function setOrderItemStatusClose($orderId)
//    {
//        $inboundItems = EcommerceInboundItem::find()->andWhere(['inbound_id' => $orderId])->all();
//
//        if ($inboundItems) {
//            foreach ($inboundItems as $inboundItem) {
//                $inboundItem->status = InboundStatus::DONE;
//                $inboundItem->save(false);
//            }
//        }
//    }

//    public function setDateConfirm($orderId)
//    {
//        $inbound = EcommerceInboundItem::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
//        if ($inbound) {
//            $inbound->date_confirm = time();
//            $inbound->save(false);
//        }
//    }

    //
//    public function setOrderItemStatusInProcess($orderId, $productBarcode)
//    {
//
//        $inboundItem = EcommerceInboundItem::find()->andWhere([
//            'inbound_id' => $orderId,
//            'product_barcode' => $productBarcode,
//        ])->one();
//
//        if ($inboundItem) {
//            $inboundItem->status = InboundStatus::SCANNING;
//            $inboundItem->save(false);
//
//        }
//    }

    //
//    public function getItemByProductBarcode($inboundId, $productBarcode)
//    {
//        $inboundItem = EcommerceInboundItem::find()->andWhere([
//            'inbound_id' => $inboundId,
//            'product_barcode' => $productBarcode,
//        ])->one();
//
//        if ($inboundItem) {
//            return $inboundItem->id;
//        }
//        return -1;
//    }

    //
//    public function getItemsByOrderId($inboundOrderId)
//    {
//        return EcommerceInboundItem::find()->select('*,(expected_qty - accepted_qty) as order_by')
//            ->andWhere(['inbound_id' => $inboundOrderId])
//            ->orderBy(new Expression('order_by != 0 DESC'))
//            ->all();
//    }

    //
//    public function getItemsForDiffReportByOrderId($inboundOrderId)
//    {
//        return EcommerceInboundItem::find()->select('*,(expected_qty - accepted_qty) as order_by')
//            ->andWhere(['inbound_id' => $inboundOrderId])
//            ->orderBy(new Expression('client_box_barcode, order_by != 0 DESC'))
//            ->asArray()
//            ->all();
//    }

    //
    public function setProductBarcodeToItemByProductModel($productBarcode, $inboundId, $productModel)
    {
        $inboundItem = EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $inboundId,
            'product_model' => $productModel,
        ])->one();

        if ($inboundItem) {
            $inboundItem->product_barcode = $productBarcode;
            $inboundItem->save(false);
        }
    }

    //
    public function closeOrder($inboundOrderId)
    {

    }


    /**
     * @return mixed
     */
    public function getInboundOrderID()
    {
        return $this->inboundOrderID;
    }

    /**
     * @param mixed $inboundOrderID
     */
    public function setInboundOrderID($inboundOrderID)
    {
        $this->inboundOrderID = $inboundOrderID;
    }
//////////////////////////////////////////////////////////////////////
/////////////////////////// NEW /////////////////////////////////
//////////////////////////////////////////////////////////////////////

    public function lotQtyInClientBox($aInboundId,$aBoxBarcode) {
        return EcommerceInboundItem::find()
            ->select('count(distinct lot_barcode) as lotQty')
            ->andWhere([
                'inbound_id' => $aInboundId,
                'client_box_barcode' => $aBoxBarcode,
            ])
            ->scalar();
    }

    public function productSumQtyInLot($aInboundId,$aBoxBarcode,$aLotBarcode) {
        return EcommerceInboundItem::find()
            ->select('sum(product_accepted_qty) as productAcceptedQty, sum(product_expected_qty) as productExpectedQty')
            ->andWhere([
                'inbound_id' => $aInboundId,
                'client_box_barcode' => $aBoxBarcode,
                'lot_barcode' => $aLotBarcode,
            ])
            ->asArray()
            ->one();
    }

    public function productSumQtyInClientBox($aInboundId,$aBoxBarcode) {
        return EcommerceInboundItem::find()
            ->select('sum(product_accepted_qty) as productAcceptedQty, sum(product_expected_qty) as productExpectedQty')
            ->andWhere([
                'inbound_id' => $aInboundId,
                'client_box_barcode' => $aBoxBarcode,
            ])
            ->asArray()
            ->one();
    }

    public function productSumQtyInOurBox($aInboundId,$aOurBoxBarcode) {
        return EcommerceStock::find()
            ->andWhere([
                'inbound_id' => $aInboundId,
                'box_address_barcode' => $aOurBoxBarcode,
            ])
            ->count();
    }

    public function productBarcodeQtyInClientBox($aInboundId,$aBoxBarcode,$aProductBarcode) {
        return EcommerceInboundItem::find()
            ->select('product_expected_qty as productExpectedQty, product_accepted_qty as productAcceptedQty')
            ->andWhere([
                'inbound_id' => $aInboundId,
                'client_box_barcode' => $aBoxBarcode,
                'product_barcode' => $aProductBarcode,
            ])
            ->asArray()
            ->one();
    }

    //
    public function isProductBarcodeExistInBox($aInboundId, $aBoxBarcode, $aProductBarcode)
//    public function isProductBarcodeExistInOrder($aInboundId, $aBoxBarcode, $aLotBarcode, $aProductBarcode)
    {
        return EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
//            'lot_barcode' => $aLotBarcode,
            'product_barcode' => $aProductBarcode,
        ])->exists();
    }

    //
    public function isLotBarcodeExistInOrder($aInboundId, $aBoxBarcode, $aLotBarcode)
    {
        return EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
            'lot_barcode' => $aLotBarcode,
        ])->exists();
    }

    public function isClientBarcodeExistInOrder($aInboundId, $aBoxBarcode)
    {
        return EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
        ])->exists();
    }

//    public function isExtraBarcodeInOrder($aInboundId, $aBoxBarcode, $aLotBarcode, $aProductBarcode)
    public function isExtraBarcodeInOrder($aInboundId, $aBoxBarcode, $aProductBarcode)
    {
        return EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
            'product_barcode' => $aProductBarcode,
//            'lot_barcode' => $aLotBarcode,
        ])->andWhere('product_expected_qty = product_accepted_qty AND product_expected_qty != 0')->exists();
    }

    public function getItemByProductBarcode($aInboundId, $aBoxBarcode, $aProductBarcode)
    {
        return  EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
            'product_barcode' => $aProductBarcode,
        ])->one();

//        if ($inboundItem) {
//            return $inboundItem->id;
//        }
//        return -1;
    }

    //
    public function updateAcceptedQtyItemByProductBarcode($aInboundId, $aBoxBarcode, $aProductBarcode)
    {
        $inboundItem = EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
            'product_barcode' => $aProductBarcode,
        ])->one();

        if ($inboundItem) {
//            $inboundItem->accepted_product_qty = $this->getScannedProductQtyByOrderInStock($aInboundId, $aBoxBarcode, $aProductBarcode);
            $inboundItem->product_accepted_qty = $this->getScannedProductQtyByOrderInStock($aInboundId, $aBoxBarcode, $aProductBarcode);
            $inboundItem->save(false);
            return $inboundItem->id;
        }
        return -1;
    }

    //
    private function getScannedProductQtyByOrderInStock($aInboundId, $aBoxBarcode, $aProductBarcode)
    {
        return EcommerceStock::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
            'product_barcode' => $aProductBarcode,
        ])->count();
    }

    public function setOrderItemStatusInProcess($aInboundId, $aBoxBarcode, $aProductBarcode)
    {

        $inboundItem = EcommerceInboundItem::find()->andWhere([
            'inbound_id' => $aInboundId,
            'client_box_barcode' => $aBoxBarcode,
            'product_barcode' => $aProductBarcode,
        ])->one();

        if ($inboundItem) {
            $inboundItem->status = InboundStatus::SCANNING;
            $inboundItem->save(false);

        }
    }

    public function updateQtyScannedInOrder($orderId, $acceptedQty)
    {
        $inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();

        if(empty($inbound)) {
            return;
        }
        $inbound->accepted_product_qty = $acceptedQty;

        if (empty($inbound->begin_datetime)) {
            $inbound->begin_datetime = time();
        }

        $inbound->end_datetime = time();
        $inbound->save(false);
    }

    public function getItemsForDiffReportByOrderId($inboundOrderId)
    {
        return EcommerceInboundItem::find()->select('*,(product_expected_qty - product_accepted_qty) as order_by')
            ->andWhere(['inbound_id' => $inboundOrderId])
            ->orderBy(new Expression('client_box_barcode, order_by != 0 DESC'))
            ->asArray()
            ->all();
    }

    public function getItemsByOrderId($inboundOrderId)
    {
        return EcommerceInboundItem::find()->select('*,(product_expected_qty - product_accepted_qty) as order_by')
            ->andWhere(['inbound_id' => $inboundOrderId])
            ->orderBy(new Expression('order_by != 0 DESC'))
            ->all();
    }

    public function setOrderStatusClose($orderId)
    {
        $inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
        if ($inbound) {
            $inbound->status = InboundStatus::DONE;
            $inbound->save(false);
        }
    }

    public function setDateConfirm($orderId)
    {
        $inbound = EcommerceInbound::find()->andWhere(['id' => $orderId])->one();
        if ($inbound) {
            $inbound->date_confirm = time();
            $inbound->save(false);
        }
    }

    //
    public function setOrderItemStatusClose($orderId)
    {
        $inboundItems = EcommerceInboundItem::find()->andWhere(['inbound_id' => $orderId])->all();

        if ($inboundItems) {
            foreach ($inboundItems as $inboundItem) {
                $inboundItem->status = InboundStatus::DONE;
                $inboundItem->save(false);
            }
        }
    }

}