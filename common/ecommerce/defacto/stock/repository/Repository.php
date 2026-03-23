<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\ecommerce\defacto\stock\repository;


//use common\modules\stock\models\Stock;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockInboundStatus;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\entities\EcommerceStock;
use common\overloads\ArrayHelper;
use common\ecommerce\entities\EcommerceInboundItem;

class Repository
{
    public function getClientID()
    {
        return 2;
    }

    private $id;

    //
    public function isExistEmptyM3($outboundOrderID) {
        return EcommerceStock::find()
            ->andWhere(["box_size_m3"=>0,'outbound_order_id'=>$outboundOrderID])
            ->orWhere(['box_size_barcode'=>null,'outbound_order_id'=>$outboundOrderID])->exists();
    }
    //
    public function isExistEmptyKg($outboundOrderID) {
        return EcommerceStock::find()->andWhere("box_kg = ''")->andWhere(['outbound_order_id'=>$outboundOrderID])->exists();

    }

    public function IsNotEmptyPrimaryAddress($primaryAddress)
    { // TODO Сделать это одним запросом

        $qtyNOAvailable =  EcommerceStock::find()->andWhere([
            'box_address_barcode'=>$primaryAddress,
            'status_availability'=>[
                $this->getStatusAvailabilityNO()
            ],
        ])->count();

        $qtyYesAvailable =  EcommerceStock::find()->andWhere([
            'box_address_barcode'=>$primaryAddress,
            'status_availability'=>[
                $this->getStatusAvailabilityYES()
            ],
        ])->count();

        return $qtyNOAvailable != $qtyYesAvailable && $qtyYesAvailable > 0;
    }
    //
    public function create($dto) {
        $stock = new EcommerceStock();
        $stock->scan_in_employee_id = ArrayHelper::getValue($dto,'scanInEmployeeId');
        $stock->scan_out_employee_id = ArrayHelper::getValue($dto,'scanOutEmployeeId');
        $stock->client_id = ArrayHelper::getValue($dto,'clientId');
        $stock->inbound_id = ArrayHelper::getValue($dto,'inboundId');
        $stock->inbound_item_id = ArrayHelper::getValue($dto,'inboundItemId');
        $stock->return_id = ArrayHelper::getValue($dto,'returnId');
        $stock->return_item_id = ArrayHelper::getValue($dto,'returnItemId');
        $stock->outbound_id = ArrayHelper::getValue($dto,'outboundId');
        $stock->outbound_item_id = ArrayHelper::getValue($dto,'outboundItemId');
        $stock->warehouse_id = ArrayHelper::getValue($dto,'warehouseId');
        $stock->product_id = ArrayHelper::getValue($dto,'productId');
        $stock->product_name = ArrayHelper::getValue($dto,'productName');
        $stock->product_barcode = ArrayHelper::getValue($dto,'productBarcode');
        $stock->product_model = ArrayHelper::getValue($dto,'productModel');
        $stock->product_sku = ArrayHelper::getValue($dto,'productSku');
        $stock->condition_type = ArrayHelper::getValue($dto,'conditionType');
        $stock->status_inbound = ArrayHelper::getValue($dto,'statusInbound');
        $stock->status_availability = ArrayHelper::getValue($dto,'statusAvailability');
        $stock->api_status = ArrayHelper::getValue($dto,'apiStatus',StockAPIStatus::NO);
        $stock->box_address_barcode = ArrayHelper::getValue($dto,'boxAddressBarcode');
        $stock->place_address_barcode = ArrayHelper::getValue($dto,'placeAddressBarcode');
        $stock->scan_out_datetime = ArrayHelper::getValue($dto,'scanOutDatetime');
        $stock->scan_in_datetime = ArrayHelper::getValue($dto,'scanInDatetime');
        $stock->client_box_barcode = ArrayHelper::getValue($dto,'clientBoxBarcode');
        $stock->lot_barcode = ArrayHelper::getValue($dto,'lotBarcode');

        $stock->client_inbound_id = ArrayHelper::getValue($dto,'clientInboundId');
        $stock->client_lot_sku = ArrayHelper::getValue($dto,'clientLotSku');

        $stock->stock_adjustment_id = ArrayHelper::getValue($dto,'stockAdjustmentId'); //
        $stock->stock_adjustment_status = ArrayHelper::getValue($dto,'stockAdjustmentStatus');
		
        $stock->save(false);

        $this->setId($stock->id);

        return $stock;
    }
    //
    public function getScannedQtyByOrderInStock($inboundOrderId) {
        return EcommerceStock::find()->andWhere([
            'inbound_id'=>$inboundOrderId,
        ])->count();
    }
    //
    public function removeByIDs($stockIDs) {
        EcommerceStock::deleteAll(['id'=>$stockIDs]);
    }
    //
    public function getStatusInboundScanned()
    {
        return StockInboundStatus::SCANNED;
    }

      //
    public function getStatusOutboundNew()
    {
        return StockOutboundStatus::_NEW;
    }

    //
    public function getStatusAvailabilityNO() {
        return StockAvailability::NO;
    }

    //
    public function getStatusAvailabilityYES() {
        return StockAvailability::YES;
    }

    public function setStatusNewAndAvailableYes($inboundOrderId)
    {
        EcommerceStock::updateAll([
            'status_inbound'=>StockInboundStatus::DONE,
            'status_availability'=>StockAvailability::YES,
        ],[
            'inbound_id'=>$inboundOrderId,
            'status_availability'=>StockAvailability::NO,
            'status_inbound'=>[
                StockInboundStatus::SCANNED,
                StockInboundStatus::OVER_SCANNED,
            ]
        ]);
    }
    //
    public function setPrimaryAddressForIds($stockIds,$primaryAddress)
    {
        file_put_contents('setPrimaryAddressForIds.log',$primaryAddress.';'.implode(',',$stockIds).';'."\n",FILE_APPEND);
        EcommerceStock::updateAll([
            'box_address_barcode'=>$primaryAddress
        ],[
            'id'=>$stockIds,
        ]);
    }
    //
    public function setSecondaryAddressForIds($stockIds,$secondaryAddress)
    {
        file_put_contents('setSecondaryAddressForIds.log',$secondaryAddress.';'.implode(',',$stockIds).';'."\n",FILE_APPEND);
        EcommerceStock::updateAll([
            'place_address_barcode'=>$secondaryAddress
        ],[
            'id'=>$stockIds,
        ]);
    }

//    //
//    public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode)
//    {
//        EcommerceStock::updateAll([
//            'place_address_barcode'=>$PlaceBarcode
//        ],[
//            'box_address_barcode'=>$BoxBarcode,
//        ]);
//    }
//
//    //
//    public function moveProductFromBoxToBox($fromBoxBarcode,$productBarcode,$toBoxBarcode)
//    {
//        $productStockOne = EcommerceStock::find()->andWhere([
//            'box_address_barcode'=>$fromBoxBarcode,
//            'product_barcode'=>$productBarcode,
//            'status_availability'=>[StockAvailability::YES,StockAvailability::NO]]
//        )->one();
//
//        if($productStockOne) {
//            $productStockOne->box_address_barcode = $toBoxBarcode;
//            $productStockOne->save(false);
//        }
//    }
//
//    public function moveAllProductsFromBoxToBox($fromBoxBarcode,$toBoxBarcode)
//    {
//        EcommerceStock::updateAll([
//            'box_address_barcode'=>$toBoxBarcode
//        ],[
//            'box_address_barcode'=>$fromBoxBarcode,
//            'status_availability'=>[StockAvailability::YES,StockAvailability::NO]
//        ]
//        );
//    }

    //
    public function getIdsByPrimaryAddress($primaryAddress)
    {
        return EcommerceStock::find()->select('id')->andWhere([
            'box_address_barcode'=>$primaryAddress,
        ])->column();
    }

    //
    public function setSecondaryAddressByPrimaryAddress($primaryAddress,$secondaryAddress)
    {
        EcommerceStock::updateAll([
            'place_address_barcode'=>$secondaryAddress
        ],[
            'box_address_barcode'=>$primaryAddress,
        ]);
    }

    public function deleteByInboundId($inboundOrderId)
    {
        EcommerceStock::deleteAll(['inbound_id'=>$inboundOrderId]);
    }

    public function changeConditionType($stockId,$conditionType) {
        if($stock = EcommerceStock::findOne($stockId)) {
            $stock->condition_type = $conditionType;
            $stock->system_status = "restored";
            $stock->system_status_description = "Восстановлен из поврежденного";
            $stock->save(false);
        }
    }

    public function inboundPutAway($aInboundId) {
        return $stock = EcommerceStock::find()
            ->select('SQL_CALC_FOUND_ROWS `place_address_barcode`, `box_address_barcode`, `product_barcode`, COUNT(`product_barcode`) as qty')
            ->andWhere(['inbound_id'=>$aInboundId])
            ->groupBy('`place_address_barcode`, `box_address_barcode`, `product_barcode`')
            ->asArray()
            ->all();

    }

    public function cleanOurBox($boxBarcode,$inboundOrderId) {
        $productBarcodeWithQty = EcommerceStock::find()
            ->select('product_barcode, count(product_barcode)as productQty')
            ->andWhere([
                'inbound_id'=>$inboundOrderId,
                'box_address_barcode'=>$boxBarcode
            ])
            ->groupBy('product_barcode')
            ->asArray()
            ->all();

        $stocksIds = EcommerceStock::find()
            ->select('id')
            ->andWhere([
                'inbound_id'=>$inboundOrderId,
                'box_address_barcode'=>$boxBarcode
            ])
            ->column();
        $this->removeByIDs($stocksIds);

        return $productBarcodeWithQty;
    }

    public function getQtyByBoxBarcodeInOrder($boxBarcode,$inboundOrderId) {
        return EcommerceStock::find()
            ->andWhere([
                'inbound_id'=>$inboundOrderId,
                'box_address_barcode'=>$boxBarcode
            ])
            ->count();
    }

    public function getItemsForDiffReportByOrderId($inboundOrderId,$productBarcode,$lotBarcode,$boxBarcode) {
        return EcommerceStock::find()
                ->select('id, product_barcode, box_address_barcode, place_address_barcode, lot_barcode, client_box_barcode, count(*) as items')
                ->andWhere([
                    'inbound_id' => $inboundOrderId,
                    'product_barcode' => $productBarcode,
                    'client_box_barcode' => $boxBarcode,
                    //'lot_barcode' => $lotBarcode,
                    'status_inbound' =>StockInboundStatus::getScannedList(),
                ])
//                ->groupBy('product_barcode, lot_barcode, box_address_barcode')
                ->groupBy('product_barcode, box_address_barcode, place_address_barcode')
                ->orderBy([
                    'place_address_barcode' => SORT_DESC,
                    'box_address_barcode' => SORT_DESC,
                ])
                ->asArray()
                ->all();
    }
	
    public function getItemsForDiffReportByOrderIdOnlyBox($inboundOrderId,$boxBarcode,$productBarcode) {
        return EcommerceStock::find()
                ->select('id, product_barcode, box_address_barcode, place_address_barcode, lot_barcode, client_box_barcode, count(*) as items')
                ->andWhere([
                    'inbound_id' => $inboundOrderId,
                    'client_box_barcode' => $boxBarcode,
                    'product_barcode' => $productBarcode,
                    'status_inbound' =>StockInboundStatus::getScannedList(),
                ])
                ->groupBy('box_address_barcode, place_address_barcode')
                ->orderBy([
                    'place_address_barcode' => SORT_DESC,
                    'box_address_barcode' => SORT_DESC,
                ])
                ->asArray()
                ->all();
    }

   public function getDataForSendByAPI($inboundOrderId) {

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand("SET group_concat_max_len=4096;");
        $command->execute();

        $items = EcommerceStock::find()
            ->select('product_barcode, client_inbound_id, client_box_barcode, condition_type, count(*) as items, group_concat(id) as ids')
            ->andWhere([
                'inbound_id' => $inboundOrderId,
                'api_status' => [StockAPIStatus::NO,StockAPIStatus::ERROR],
//                'api_status' => [StockAPIStatus::NO],
            ])
//            ->andWhere('`client_product_sku` IS NULL')
            ->groupBy('client_inbound_id, product_barcode, client_box_barcode, condition_type')
            ->asArray()
            ->all();
        
        //foreach ($items as &$item) {
           // if(empty($item['client_inbound_id'])) {
               // $item['client_inbound_id'] = EcommerceInboundItem::find()->select('client_inbound_id')->andWhere(['client_box_barcode'=>$item['client_box_barcode']])->andWhere('client_inbound_id != 0')->scalar();
           // }
       // }

        return $items;
    }

    public function getDataForSendByApiByBox($inboundOrderId,$clientBox) {

        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand("SET group_concat_max_len=4096;");
        $command->execute();

        $items = EcommerceStock::find()
            ->select('product_barcode, client_inbound_id, client_box_barcode, condition_type, count(*) as items, group_concat(id) as ids')
            ->andWhere([
                'inbound_id' => $inboundOrderId,
                'client_box_barcode' => $clientBox,
                'api_status' => [StockAPIStatus::NO,StockAPIStatus::ERROR],
//                'api_status' => [StockAPIStatus::NO],
            ])
//            ->andWhere('`client_product_sku` IS NULL')
            ->groupBy('product_barcode, condition_type')
//            ->orderBy('client_box_barcode')
//            ->indexBy('client_box_barcode')
            ->asArray()
            ->all();

        foreach ($items as &$item) {
            if(empty($item['client_inbound_id'])) {
                $item['client_inbound_id'] = EcommerceInboundItem::find()->select('client_inbound_id')->andWhere(['client_box_barcode'=>$item['client_box_barcode']])->andWhere('client_inbound_id != 0')->scalar();
            }
        }

        return $items;
    }

    public function boxReadyToSendByInboundAPI($inboundOrderId,$clientBoxBarcode = '') {
        return EcommerceStock::find()
            ->select('client_box_barcode')
            ->andFilterWhere(['client_box_barcode'=>$clientBoxBarcode])
            ->andWhere([
                'inbound_id' => $inboundOrderId,
                'api_status' => [StockAPIStatus::NO,StockAPIStatus::ERROR],
//                'api_status' => [StockAPIStatus::NO,StockAPIStatus::YES,StockAPIStatus::ERROR],
                //'api_status' => [StockAPIStatus::NO],
            ])
            ->groupBy('client_box_barcode')
            ->column();
    }

    public function setStockApiStatus($inboundOrderId,$StockIds,$ApiStatus) {
        EcommerceStock::updateAll([
            'api_status'=>$ApiStatus,
        ],[
            'id'=>$StockIds,
            'inbound_id'=>$inboundOrderId,
        ]);
    }

    public function boxWithoutPlaceAddress($inboundOrderId) {
        return EcommerceStock::find()
            ->select('box_address_barcode, place_address_barcode')
            ->andWhere(['inbound_id' =>$inboundOrderId])
            ->andWhere('place_address_barcode = "" OR place_address_barcode IS NULL')
            ->andWhere(['not', ['box_address_barcode'=>'']])
            ->groupBy('box_address_barcode')
            ->orderBy([
                'place_address_barcode' => SORT_DESC,
                'box_address_barcode' => SORT_DESC,
            ])
            ->asArray()
            ->all();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    public function getOrderIdByPackageBarcode($packageBarcode) {
        return EcommerceStock::find()->select('outbound_id')->andWhere([
            'outbound_box' =>$packageBarcode,
            'client_id' => $this->getClientID()
        ])->scalar();
    }

    public function getRemainsForSendInventorySnapshot() {
       return EcommerceStock::find()
            ->select('product_barcode, box_address_barcode, place_address_barcode, client_product_sku, count(product_barcode) as productQty')
            ->andWhere([
                'client_id' =>$this->getClientID(),
                'status_availability' =>StockAvailability::YES,
                //'api_status' =>StockAPIStatus::YES,
//                'condition_type' =>StockConditionType::UNDAMAGED,
            ])
            ->groupBy('product_barcode, box_address_barcode, place_address_barcode')
            ->orderBy('place_address_sort1')
            ->asArray();
            //->all();
    }

    public function getStockItemByOutboundOrderProduct($aOutboundId,$aOutboundItemId,$aProductBarcode,$excludeStockIds = []) {
         return  EcommerceStock::find()
            ->andWhere(['outbound_id'=>$aOutboundId,'outbound_item_id'=>$aOutboundItemId,'product_barcode'=>$aProductBarcode])
            ->andWhere(['NOT',['id'=>$excludeStockIds]])
            ->one();
    }	
}