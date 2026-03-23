<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 14.04.2020
 * Time: 12:28
 */

namespace common\ecommerce\defacto\transfer\repository;

use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\constants\TransferDefaultValue;
use common\ecommerce\constants\TransferStatus;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceTransfer;
use common\ecommerce\entities\EcommerceTransferB2cToB2b;
use common\ecommerce\entities\EcommerceTransferItems;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class TransferRepository
{
    public function getOrdersForPrintPickList()
    {
        $query = EcommerceTransfer::find()->andWhere([
            'status'=>TransferStatus::getOrdersForPrintPickList(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
        ]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => ['defaultOrder' => ['created_at' => SORT_ASC]]
        ]);
    }

    public function getOrderInfo($aTransferId)
    {
        $order = EcommerceTransfer::find()->andWhere(["id" => $aTransferId])->one();
        $items = EcommerceTransferItems::find()->andWhere(['transfer_id' => $aTransferId])->all();

        $result = new \stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }

    public function canPrintPickingList($aTransferId) {
        return EcommerceTransfer::find()->andWhere([
            'id' =>$aTransferId,
            'status' => TransferStatus::getOrdersForPrintPickList(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
//            'client_id' => $this->getClientID()
        ])->exists();
    }

    public function isReadyPrintPickListForScanning($aPikingListBarcode) {

       $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return  EcommerceTransfer::find()->andWhere([
            'id' =>$parsePikingList->transferId,
            'client_BatchId' =>$parsePikingList->batchId,
            'status' => TransferStatus::getReadyPrintPickListForScanning(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
        ])->exists();
    }

    public function getPrintPickListInfo($aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return EcommerceTransfer::find()->andWhere([
            'id' =>$parsePikingList->transferId,
            'client_BatchId' =>$parsePikingList->batchId,
            'status' => TransferStatus::getReadyPrintPickListForScanning(),
            'client_LcBarcode'=>TransferDefaultValue::MAIN_VIRTUAL_BOX,
        ])->one();
    }

    public function showOrderItems($aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $items = EcommerceTransferItems::find()->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
        ])
        ->orderBy('accepted_qty')
        ->asArray()
        ->all();

        return $items;
    }

    public function showBoxItems($aLcBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $items = EcommerceStock::find()->select('product_barcode, outbound_box, count(product_barcode) as qtyProduct')->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'outbound_box' =>  $aLcBarcode,
        ])
        ->groupBy('outbound_box, product_barcode')
        ->asArray()
        ->all();

        return $items;
    }

    public function getQtyProductsInBox($aLcBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return  EcommerceStock::find()->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'outbound_box' =>  $aLcBarcode,
        ])->count();
    }

    public function makeScannedProduct($dto)
    {
        $parsePikingList = self::parsePikingListBarcode($dto->pickingListBarcode);

        $this->makeScannedStock($dto);
        $outboundOrderItem = $this->makeScannedItem($dto);
        $this->makeScannedOrder($parsePikingList->transferId);

        return $outboundOrderItem;
    }

    private function makeScannedStock($dto)
    {
        $parsePikingList = self::parsePikingListBarcode($dto->pickingListBarcode);

        $stock = EcommerceStock::find()->andWhere([
            'product_barcode' => $dto->productBarcode,
            'transfer_id' => $parsePikingList->transferId,
            'status_transfer' => StockTransferStatus::getReadyForScanning(),
            'client_id' => $this->getClientID(),
        ])
            ->one();

        if ($stock) {
            $stock->status_transfer = StockTransferStatus::SCANNED;
            $stock->outbound_box = $dto->lcBarcode;
//            $stock->scan_out_employee_id = $dto->employee->id;
            $stock->scan_out_datetime = time();
            $stock->save(false);
        }
    }

    private function makeScannedItem($dto)
    {
        $parsePikingList = self::parsePikingListBarcode($dto->pickingListBarcode);

        $outboundOrderItem = EcommerceTransferItems::find()->andWhere([
            'transfer_id' =>  $parsePikingList->transferId,
            'product_barcode' => $dto->productBarcode,
        ])->one();

        if ($outboundOrderItem) {

            if (intval($outboundOrderItem->accepted_qty) < 1) {
                $outboundOrderItem->begin_datetime = time();
                $outboundOrderItem->status = TransferStatus::SCANNING;
            }

            $outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode,$parsePikingList->transferId);

            if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty ) {
                $outboundOrderItem->status = TransferStatus::SCANNED;
            }

            $outboundOrderItem->end_datetime = time();
            $outboundOrderItem->save(false);
        }

        return $outboundOrderItem;
    }

    private function makeScannedOrder($orderId)
    {
        $outboundOrder = EcommerceTransfer::find()
            ->andWhere([
                'id'=>$orderId,
                'client_id' => $this->getClientID()
            ])->one();

        if(intval($outboundOrder->accepted_qty) < 1) {
            $outboundOrder->begin_datetime = time();
            $outboundOrder->status = TransferStatus::SCANNING;
        }

        $outboundOrder->accepted_qty = $this->getQtyScanned($orderId);

        if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty ) {
            $outboundOrder->status = TransferStatus::SCANNED;
        }

        $outboundOrder->end_datetime = time();
        $outboundOrder->save(false);
    }

    private function getQtyScannedProduct($productBarcode,$orderId) {
        return EcommerceStock::find()->andWhere([
            'product_barcode'=>$productBarcode,
            'transfer_id' => $orderId,
            'status_transfer'=>StockTransferStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    private function getQtyScanned($orderId) {
        return EcommerceStock::find()->andWhere([
            'transfer_id' => $orderId,
            'status_transfer'=>StockTransferStatus::SCANNED,
            'client_id' => $this->getClientID(),
        ])->count();
    }

    public function emptyPackage($aLcBarcode,$aPikingListBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        $stocks = EcommerceStock::find()->andWhere([
            'client_id'=>$this->getClientID(),
            'transfer_id' => $parsePikingList->transferId,
            'outbound_box'=>$aLcBarcode,
            'status_transfer'=>StockTransferStatus::SCANNED,
        ])->all();

        foreach ($stocks as $stock) {

            $stock->outbound_box = '';
            $stock->status_transfer = StockTransferStatus::PRINTED_PICKING_LIST;
            $stock->save(false);

            $inboundItem = EcommerceTransferItems::find()->andWhere([
                'transfer_id' =>  $parsePikingList->transferId,
                'product_barcode' => $stock->product_barcode,
            ])->one();

            if ($inboundItem) {
                $inboundItem->accepted_qty = $this->getQtyScannedProduct($stock->product_barcode, $parsePikingList->transferId);
                $inboundItem->save(false);
            }
        }

        $this->makeScannedOrder($parsePikingList->transferId);
    }

//    public function sendByAPI($aPikingListBarcode) {
//
//        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);
//
//        $transferItems = EcommerceTransferItems::find()->andWhere(['transfer_id' =>  $parsePikingList->transferId])->all();
//
//        $result = [];
//        foreach ($transferItems as $item) {
//

//
//            if(empty($productInBox) || !is_array($productInBox) ) {
//                continue;
//            }
//
//            foreach ($productInBox as $product) {
//                $productData = new \stdClass();
//                $productData->OutBoundId = $item->client_OutboundId;
//                $productData->LcBarcode = $product['outbound_box'];
//                $productData->LotOrSingleBarcode = $product['product_barcode'];
//                $productData->LotOrSingleQuantity = $product['qtyProductInBox'];
//                $productData->WaybillNumber =  $item->transfer_id;
//
//                $result [] = $productData;
//            }
//        }
//
//        return  $result;
//    }

    public function getProductInBoxForSendByAPI($aTransferId,$aTransferItemId) {
        return EcommerceStock::find()->select('outbound_box,product_barcode, count(product_barcode) as qtyProductInBox')->andWhere([
                    'client_id'=>$this->getClientID(),
                    'transfer_id' => $aTransferId,
                    'transfer_item_id' => $aTransferItemId,
                    'status_transfer'=>StockTransferStatus::SCANNED,
                ])
                ->groupBy('outbound_box, product_barcode')
                ->asArray()
                ->all();
    }

    public function isExtraBarcodeInOrder($aPikingListBarcode, $aProductBarcode)
    {
        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return EcommerceTransferItems::find()->andWhere([
            'transfer_id' => $parsePikingList->transferId,
            'product_barcode' => $aProductBarcode,
        ])->andWhere('expected_qty = accepted_qty AND expected_qty != 0')
         ->exists();
    }

    public function isProductBarcodeExist($aPikingListBarcode,$aProductBarcode) {

        $parsePikingList = self::parsePikingListBarcode($aPikingListBarcode);

        return EcommerceTransferItems::find()->andWhere([
            'transfer_id' => $parsePikingList->transferId,
            'product_barcode' => $aProductBarcode,
        ])->exists();
    }


    public static function parsePikingListBarcode($aPikingListBarcode) {
        $result = new \stdClass();
        $explodeBarcode = explode('-',$aPikingListBarcode);
        $result->transferId  = ArrayHelper::getValue($explodeBarcode,'0',0);
        $result->batchId  = ArrayHelper::getValue($explodeBarcode,'1',0);

        return $result;
    }

    public function getClientID()
    {
        return 2;
    }
}