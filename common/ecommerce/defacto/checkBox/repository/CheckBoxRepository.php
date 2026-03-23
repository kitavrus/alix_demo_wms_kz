<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\ecommerce\defacto\checkBox\repository;

use common\ecommerce\constants\CheckBoxStatus;
use common\ecommerce\constants\CheckBoxType;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\entities\EcommerceCheckBox;
use common\ecommerce\entities\EcommerceCheckBoxInventory;
use common\ecommerce\entities\EcommerceCheckBoxStock;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceStock;
use common\overloads\ArrayHelper;

class CheckBoxRepository
{
    public function getClientID()
    {
        return 2;
    }

    public function getWarehouseID()
    {
        return 1;
    }

    public function isExistBox($inventoryId,$boxBarcode) {
        return EcommerceCheckBox::find()
            ->andWhere(["inventory_id"=>$inventoryId,'box_barcode'=>$boxBarcode,'client_id'=>$this->getClientID()])
            ->exists();
    }

    public function getById($aId) {
        return EcommerceCheckBox::find()->andWhere(["id"=>$aId])->one();
    }

    public function getBoxInfo($inventoryId,$boxBarcode) {
        return EcommerceCheckBox::find()
            ->andWhere(["inventory_id"=>$inventoryId,'box_barcode'=>$boxBarcode,'client_id'=>$this->getClientID()])
            ->one();
    }
    
    //
    public function getStockByBoxBarcode($boxBarcode, $inventoryType = CheckBoxType::PART_BY_BOX ) {

        $q =  EcommerceStock::find();
        $q->andWhere([
            'box_address_barcode'=>$boxBarcode,
            'client_id'=>$this->getClientID(),
        ]);

        if($inventoryType == CheckBoxType::PART_BY_BOX) {
           $q->andWhere(['status_outbound'=>StockOutboundStatus::getExistInBox(),'status_transfer'=>StockTransferStatus::getExistInBox()]);
        } else {
           $q->andWhere(['status_availability'=>StockAvailability::YES]);
        }

       return $q->asArray()->all();
    }

    public function getCountAvailableStockByBoxBarcode($boxBarcode,$placeAddress= null) {

        $q = EcommerceStock::find();
        $q->andWhere([
            'box_address_barcode'=>$boxBarcode,
            'client_id'=>$this->getClientID(),
            'status_availability'=>StockAvailability::YES
        ]);

        return (int)$q->count();
    }

    public function isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["product_barcode"=>$productBarcode,'box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>StockAvailability::YES,'status'=>CheckBoxStatus::NEW_])
            ->exists();
    }

    public function getProductReadyScan($inventoryId,$productBarcode,$boxBarcode,$placeAddress)
    {
        $checkBoxStockOne = EcommerceCheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["product_barcode"=>$productBarcode,'box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>StockAvailability::YES,'status'=>CheckBoxStatus::NEW_])
            ->one();
        
        return $checkBoxStockOne;
    }

    public function qtyProductInBox($inventoryId,$boxBarcode,$placeAddress)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>StockAvailability::YES])
            ->count();
    }

    public function qtyScannedProductInBox($inventoryId,$boxBarcode,$placeAddress)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>StockAvailability::YES,'status'=>CheckBoxStatus::END_SCANNED])
            ->count();
    }

    public function getProductListInBox($inventoryId,$boxBarcode,$placeAddress)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->select('product_barcode,count(product_barcode) as qtyProduct, box_barcode, place_address, stock_outbound_id, stock_outbound_status, status, stock_transfer_id, stock_status_transfer')
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->groupBy('product_barcode, box_barcode, place_address, stock_outbound_id, stock_outbound_status,status')
            ->orderBy('status,product_barcode')
            ->asArray()
            ->all();
    }

    public function emptyBox($inventoryId,$boxBarcode,$placeAddress)
    {

        return EcommerceCheckBoxStock::updateAll(['status'=>CheckBoxStatus::NEW_],
                [
                 'box_barcode'=>$boxBarcode,
                 'place_address'=>$placeAddress,
                 "inventory_id"=>$inventoryId,
                 "stock_status_availability"=>StockAvailability::YES,
                 'status'=>CheckBoxStatus::END_SCANNED
                ]
            );
    }

    public function getInventoryKeyList() {
        return EcommerceCheckBoxInventory::find()
            ->select('id, inventory_key')
            ->andWhere(['status'=>CheckBoxStatus::getNewAndInProcessOrder()])
            ->orderBy(['id'=>SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getAllInventoryKeyList() {
        return EcommerceCheckBoxInventory::find()
            ->select('id, inventory_key')
//            ->andWhere(['status'=>CheckBoxStatus::getNewAndInProcessOrder()])
            ->orderBy(['id'=>SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getInventoryInfo($inventoryId) {
        return EcommerceCheckBoxInventory::find()
            ->andWhere(["id"=>$inventoryId])
            ->one();
    }

    public function qtyProductInInventory($inventoryId)
    {
        return EcommerceCheckBoxStock::find()
            ->select('COUNT(id)')
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["stock_status_availability"=>StockAvailability::YES])
            ->scalar();
//            ->count();
    }

    public function qtyScannedProductInInventory($inventoryId)
    {
        return EcommerceCheckBoxStock::find()
            ->select('COUNT(id)')
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["stock_status_availability"=>StockAvailability::YES,'status'=>CheckBoxStatus::END_SCANNED])
            ->scalar();
    }

    public function qtyBoxInInventory($inventoryId)
    {
//        return EcommerceCheckBoxStock::find()
        return EcommerceCheckBox::find()
            ->select('COUNT(id)')
//            ->select('COUNT(DISTINCT box_barcode)')
            ->andWhere(["inventory_id"=>$inventoryId])
//            ->andWhere("scanned_qty > 0")
//            ->andWhere(["stock_status_availability"=>StockAvailability::YES])
//            ->groupBy('box_barcode')
            ->scalar();
    }

    public function qtyScannedBoxInInventory($inventoryId)
    {
//        return EcommerceCheckBoxStock::find()
        return EcommerceCheckBox::find()
//            ->select('COUNT(DISTINCT box_barcode)')
            ->select('COUNT(id)')
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere("scanned_qty > 0")
            ->scalar();
    }
    
    
    /*
     * Найти все не отсканированные коробки в ряду
     * */
    public function getAllNotScannedBoxInRow() {}

    /*
     * Найти коробки в которых есть расходжения в ряду или по всем рядам
     * */
    public function getBoxWithDiffScannedInRow() {}

    /*
     * Найти товары которых нехватает в других местах
     * */
    public function getProductInOtherPlaces($aProductBarcode) {}

    /*
     * Удалить короб и начать сканировать его заново
     * */
    public function deleteBox($inventoryId,$aBoxBarcode) {

        $boxInfo = $this->getBoxInfo($inventoryId,$aBoxBarcode);
        EcommerceCheckBox::deleteAll(['id'=>$boxInfo->id,'inventory_id'=>$inventoryId]);
        EcommerceCheckBoxStock::deleteAll(['check_box_id'=>$boxInfo->id,'inventory_id'=>$inventoryId]);

        return null;
    }

    /*
     * Удалить inventory и начать сканировать его заново
     * */
    public function deleteInventory($inventoryId) {

        $inventoryInfo = $this->getInventoryInfo($inventoryId);
        EcommerceCheckBoxInventory::updateAll(['deleted'=>1],['id'=>$inventoryInfo->id]);
        EcommerceCheckBox::updateAll(['deleted'=>1],['inventory_id'=>$inventoryInfo->id]);
        EcommerceCheckBoxStock::updateAll(['deleted'=>1],['inventory_id'=>$inventoryInfo->id]);

        return null;
    }

    /*
     * Начать сканировать ряд заново
     * */
    public function resetRow($inventoryId,$minMaxPlaceAddress) {

         $q = EcommerceCheckBox::find();
         $q->select('id');
         $q->andWhere(['client_id'=>$this->getClientID()]);
         $q->andWhere(['inventory_id'=>$inventoryId]);
         $q->andWhere(['place_address_barcode'=>$minMaxPlaceAddress]);

         $checkBoxList = $q->asArray()->all();
         foreach($checkBoxList as $checkBox) {
            EcommerceCheckBox::updateAll(['scanned_qty'=>0,'status'=>CheckBoxStatus::NEW_],['id'=>$checkBox['id'],'inventory_id'=>$inventoryId]);
            EcommerceCheckBoxStock::updateAll(['status'=>CheckBoxStatus::NEW_],['check_box_id'=>$checkBox['id'],'inventory_id'=>$inventoryId]);
         }
    }

    //
    public function createBox($dto) {
        $checkBox = new EcommerceCheckBox();
        $checkBox->client_id = ArrayHelper::getValue($dto,'clientId');
        $checkBox->warehouse_id = ArrayHelper::getValue($dto,'warehouseId');
        $checkBox->employee_id = ArrayHelper::getValue($dto,'employeeId');
        $checkBox->inventory_id = ArrayHelper::getValue($dto,'inventoryId');
        $checkBox->box_barcode = ArrayHelper::getValue($dto,'boxBarcode');
        $checkBox->place_address = ArrayHelper::getValue($dto,'placeAddress');

        $checkBox->place_address_part1 = ArrayHelper::getValue($dto,'placeAddressPart1');
        $checkBox->place_address_part2 = ArrayHelper::getValue($dto,'placeAddressPart2');
        $checkBox->place_address_part3 = ArrayHelper::getValue($dto,'placeAddressPart3');
        $checkBox->place_address_part4 = ArrayHelper::getValue($dto,'placeAddressPart4');

        $checkBox->expected_qty = ArrayHelper::getValue($dto,'expectedQty');
        $checkBox->scanned_qty = ArrayHelper::getValue($dto,'scannedQty');
        $checkBox->save(false);

        return $checkBox;
    }

    //
    public function createStock($dto) {

        $checkBoxStock = $this->makeStockByDto($dto);
        $checkBoxStock->save(false);
        return $checkBoxStock;
    }

    //
    public function makeStockByDto($dto) {

        $checkBoxStock = new EcommerceCheckBoxStock();
        $checkBoxStock->check_box_id = ArrayHelper::getValue($dto,'checkBoxId');
        $checkBoxStock->stock_id = ArrayHelper::getValue($dto,'stockId');
        $checkBoxStock->stock_inbound_id = ArrayHelper::getValue($dto,'stockInboundId');
        $checkBoxStock->stock_inbound_item_id = ArrayHelper::getValue($dto,'stockInboundItemId');
        $checkBoxStock->stock_outbound_id = ArrayHelper::getValue($dto,'stockOutboundId');
        $checkBoxStock->stock_outbound_item_id = ArrayHelper::getValue($dto,'stockOutboundItemId');
        $checkBoxStock->stock_status_availability = ArrayHelper::getValue($dto,'stockStatusAvailability');
        $checkBoxStock->stock_client_product_sku = ArrayHelper::getValue($dto,'stockClientProductSku');

        $checkBoxStock->stock_inbound_status = ArrayHelper::getValue($dto,'stockInboundStatus');
        $checkBoxStock->stock_outbound_status = ArrayHelper::getValue($dto,'stockOutboundStatus');
        $checkBoxStock->stock_condition_type = ArrayHelper::getValue($dto,'stockConditionType');

        $checkBoxStock->product_barcode = ArrayHelper::getValue($dto,'productBarcode');
        $checkBoxStock->serialized_data_stock = ArrayHelper::getValue($dto,'serializedDataStock');
        $checkBoxStock->status = ArrayHelper::getValue($dto,'status');

        $checkBoxStock->client_id = ArrayHelper::getValue($dto,'clientId');
        $checkBoxStock->warehouse_id = ArrayHelper::getValue($dto,'warehouseId');
        $checkBoxStock->inventory_id = ArrayHelper::getValue($dto,'inventoryId');
        $checkBoxStock->box_barcode = ArrayHelper::getValue($dto,'boxBarcode');
        $checkBoxStock->place_address = ArrayHelper::getValue($dto,'placeAddress');

        $checkBoxStock->stock_transfer_id = ArrayHelper::getValue($dto,'stockTransferId');
        $checkBoxStock->stock_transfer_outbound_box = ArrayHelper::getValue($dto,'stockTransferOutboundBox');
        $checkBoxStock->stock_status_transfer = ArrayHelper::getValue($dto,'stockStatusTransfer');
        $checkBoxStock->deleted = 0;

        return $checkBoxStock;
    }

    //
    public function createStockBatchInsert($rows) {

        if(empty($rows)) {
            return null;
        }

        $checkBoxStock = new EcommerceCheckBoxStock;
        \Yii::$app->db->createCommand()->batchInsert(EcommerceCheckBoxStock::tableName(), $checkBoxStock->attributes(), $rows)->execute();
    }

    //
    public function getAllBoxAddressForFullInventory($inventoryType = CheckBoxType::PART_BY_BOX) {
        $q =  EcommerceStock::find();

        $q->select('box_address_barcode as boxAddress, place_address_barcode as placeAddress')
          ->andWhere(['client_id'=>$this->getClientID()])
          ->groupBy('box_address_barcode, place_address_barcode');

        if($inventoryType == CheckBoxType::PART_BY_BOX) {
            $q->andWhere(['status_outbound'=>StockOutboundStatus::getExistInBox(),'status_transfer'=>StockTransferStatus::getExistInBox()]);
        } else {
            $q->andWhere(['status_availability'=>StockAvailability::YES]);
        }

        return $q->asArray()->all();
    }

      //
    public function getAllBoxAddressByRowAddress($minMaxPlaceAddress,$inventoryType = CheckBoxType::PART_BY_BOX) {
        $q =  EcommerceStock::find();
        $q->select('box_address_barcode as boxAddress, place_address_barcode as placeAddress')
          ->andWhere([
            'client_id'=>$this->getClientID(),
            'place_address_barcode'=>$minMaxPlaceAddress,
        ])
        ->groupBy('box_address_barcode,place_address_barcode');

        if($inventoryType == CheckBoxType::PART_BY_BOX) {
            $q->andWhere(['status_outbound'=>StockOutboundStatus::getExistInBox(),'status_transfer'=>StockTransferStatus::getExistInBox()]);
        } else {
            $q->andWhere(['status_availability'=>StockAvailability::YES]);
        }

        return $q->asArray()->all();
    }

    public function getNotScannedProductInInventory($inventoryId)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["stock_status_availability"=>StockAvailability::YES,'status'=>CheckBoxStatus::NEW_])
            ->all();
    }

    public function getStockById($aStockId) {
        return EcommerceStock::find()->andWhere(["id"=>$aStockId])->one();
    }


}