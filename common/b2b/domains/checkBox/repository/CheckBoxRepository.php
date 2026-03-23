<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\b2b\domains\checkBox\repository;

use common\b2b\domains\checkBox\constants\CheckBoxStatus;
use common\b2b\domains\checkBox\constants\CheckBoxType;
use common\b2b\domains\checkBox\entities\CheckBox;
use common\b2b\domains\checkBox\entities\CheckBoxInventory;
use common\b2b\domains\checkBox\entities\CheckBoxStock;
use common\modules\stock\models\Stock;
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
        return CheckBox::find()
            ->andWhere(["inventory_id"=>$inventoryId,'box_barcode'=>$boxBarcode,'client_id'=>$this->getClientID()])
            ->exists();
    }

    public function getById($aId) {
        return CheckBox::find()->andWhere(["id"=>$aId])->one();
    }

    public function getBoxInfo($inventoryId,$boxBarcode) {
        return CheckBox::find()
            ->andWhere(["inventory_id"=>$inventoryId,'box_barcode'=>$boxBarcode,'client_id'=>$this->getClientID()])
            ->one();
    }
    
    //
    public function getStockByBoxBarcode($boxBarcode, $inventoryType = CheckBoxType::PART_BY_BOX ) {

        $q =  Stock::find();
        $q->andWhere([
            'primary_address'=>$boxBarcode,
            'client_id'=>$this->getClientID(),
        ]);

//        if($inventoryType == CheckBoxType::PART_BY_BOX) {
//           $q->andWhere(['status'=>Stock::getExistInBox()]);
//        } else {
           $q->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES]);
//        }

       return $q->asArray()->all();
    }

    public function getCountAvailableStockByBoxBarcode($boxBarcode,$placeAddress= null) {

        $q = Stock::find();
        $q->andWhere([
            'primary_address'=>$boxBarcode,
            'client_id'=>$this->getClientID(),
            'status_availability'=>Stock::STATUS_AVAILABILITY_YES
        ]);

        return (int)$q->count();
    }

    public function isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress)
    {
        return CheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["product_barcode"=>$productBarcode,'box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES,'status'=>CheckBoxStatus::NEW_])
            ->exists();
    }

    public function getProductReadyScan($inventoryId,$productBarcode,$boxBarcode,$placeAddress)
    {
        $checkBoxStockOne = CheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["product_barcode"=>$productBarcode,'box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES,'status'=>CheckBoxStatus::NEW_])
            ->one();
        
        return $checkBoxStockOne;
    }

    public function qtyProductInBox($inventoryId,$boxBarcode,$placeAddress)
    {
        return CheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES])
            ->count();
    }

    public function qtyScannedProductInBox($inventoryId,$boxBarcode,$placeAddress)
    {
        return CheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES,'status'=>CheckBoxStatus::END_SCANNED])
            ->count();
    }

    public function getProductListInBox($inventoryId,$boxBarcode,$placeAddress)
    {
        return CheckBoxStock::find()
            ->andWhere(["inventory_id"=>$inventoryId])
            ->select('product_barcode,count(product_barcode) as qtyProduct, box_barcode, place_address, stock_outbound_id, stock_outbound_status, status')
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->groupBy('product_barcode, box_barcode, place_address, stock_outbound_id, stock_outbound_status,status')
            ->orderBy('status,product_barcode')
            ->asArray()
            ->all();
    }

    public function emptyBox($inventoryId,$boxBarcode,$placeAddress)
    {

        return CheckBoxStock::updateAll(['status'=>CheckBoxStatus::NEW_],
                [
                 'box_barcode'=>$boxBarcode,
                 'place_address'=>$placeAddress,
                 "inventory_id"=>$inventoryId,
                 "stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES,
                 'status'=>CheckBoxStatus::END_SCANNED
                ]
            );
    }

    public function getInventoryKeyList() {
        return CheckBoxInventory::find()
            ->select('id, inventory_key')
            ->andWhere(['status'=>CheckBoxStatus::getNewAndInProcessOrder()])
            ->orderBy(['id'=>SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getAllInventoryKeyList() {
        return CheckBoxInventory::find()
            ->select('id, inventory_key')
//            ->andWhere(['status'=>CheckBoxStatus::getNewAndInProcessOrder()])
            ->orderBy(['id'=>SORT_DESC])
            ->asArray()
            ->all();
    }

    public function getInventoryInfo($inventoryId) {
        return CheckBoxInventory::find()
            ->andWhere(["id"=>$inventoryId])
            ->one();
    }

    public function qtyProductInInventory($inventoryId)
    {
        return CheckBoxStock::find()
            ->select('COUNT(id)')
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES])
            ->scalar();
//            ->count();
    }

    public function qtyScannedProductInInventory($inventoryId)
    {
        return CheckBoxStock::find()
            ->select('COUNT(id)')
            ->andWhere(["inventory_id"=>$inventoryId])
            ->andWhere(["stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES,'status'=>CheckBoxStatus::END_SCANNED])
            ->scalar();
    }

    public function qtyBoxInInventory($inventoryId)
    {
//        return EcommerceCheckBoxStock::find()
        return CheckBox::find()
            ->select('COUNT(id)')
//            ->select('COUNT(DISTINCT box_barcode)')
            ->andWhere(["inventory_id"=>$inventoryId])
//            ->andWhere("scanned_qty > 0")
//            ->andWhere(["stock_status_availability"=>Stock::STATUS_AVAILABILITY_YES])
//            ->groupBy('box_barcode')
            ->scalar();
    }

    public function qtyScannedBoxInInventory($inventoryId)
    {
//        return EcommerceCheckBoxStock::find()
        return CheckBox::find()
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
        CheckBox::deleteAll(['id'=>$boxInfo->id,'inventory_id'=>$inventoryId]);
        CheckBoxStock::deleteAll(['check_box_id'=>$boxInfo->id,'inventory_id'=>$inventoryId]);

        return null;
    }

    /*
     * Удалить inventory и начать сканировать его заново
     * */
    public function deleteInventory($inventoryId) {

        $inventoryInfo = $this->getInventoryInfo($inventoryId);
        CheckBoxInventory::updateAll(['deleted'=>1],['id'=>$inventoryInfo->id]);
        CheckBox::updateAll(['deleted'=>1],['inventory_id'=>$inventoryInfo->id]);
        CheckBoxStock::updateAll(['deleted'=>1],['inventory_id'=>$inventoryInfo->id]);

        return null;
    }

    /*
     * Начать сканировать ряд заново
     * */
    public function resetRow($inventoryId,$minMaxPlaceAddress) {

         $q = CheckBox::find();
         $q->select('id');
         $q->andWhere(['client_id'=>$this->getClientID()]);
         $q->andWhere(['inventory_id'=>$inventoryId]);
         $q->andWhere(['place_address_barcode'=>$minMaxPlaceAddress]);

         $checkBoxList = $q->asArray()->all();
         foreach($checkBoxList as $checkBox) {
            CheckBox::updateAll(['scanned_qty'=>0,'status'=>CheckBoxStatus::NEW_],['id'=>$checkBox['id'],'inventory_id'=>$inventoryId]);
            CheckBoxStock::updateAll(['status'=>CheckBoxStatus::NEW_],['check_box_id'=>$checkBox['id'],'inventory_id'=>$inventoryId]);
         }
    }

    //
    public function createBox($dto) {
        $checkBox = new CheckBox();
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

        $checkBoxStock = new CheckBoxStock();
        $checkBoxStock->check_box_id = ArrayHelper::getValue($dto,'checkBoxId');
        $checkBoxStock->stock_id = ArrayHelper::getValue($dto,'stockId');
        $checkBoxStock->stock_inbound_id = ArrayHelper::getValue($dto,'stockInboundId');
        $checkBoxStock->stock_inbound_item_id = ArrayHelper::getValue($dto,'stockInboundItemId');
        $checkBoxStock->stock_outbound_id = ArrayHelper::getValue($dto,'stockOutboundId');
        $checkBoxStock->stock_outbound_item_id = ArrayHelper::getValue($dto,'stockOutboundItemId');
        $checkBoxStock->stock_status_availability = ArrayHelper::getValue($dto,'stockStatusAvailability');
        $checkBoxStock->stock_client_product_sku = ArrayHelper::getValue($dto,'stockClientProductSku');

//        $checkBoxStock->stock_inbound_status = ArrayHelper::getValue($dto,'stockInboundStatus');
//        $checkBoxStock->stock_outbound_status = ArrayHelper::getValue($dto,'stockOutboundStatus');
        $checkBoxStock->stock_condition_type = ArrayHelper::getValue($dto,'stockConditionType');

        $checkBoxStock->product_barcode = ArrayHelper::getValue($dto,'productBarcode');
        $checkBoxStock->serialized_data_stock = ArrayHelper::getValue($dto,'serializedDataStock');
        $checkBoxStock->status = ArrayHelper::getValue($dto,'status');

        $checkBoxStock->client_id = ArrayHelper::getValue($dto,'clientId');
        $checkBoxStock->warehouse_id = ArrayHelper::getValue($dto,'warehouseId');
        $checkBoxStock->inventory_id = ArrayHelper::getValue($dto,'inventoryId');
        $checkBoxStock->box_barcode = ArrayHelper::getValue($dto,'boxBarcode');
        $checkBoxStock->place_address = ArrayHelper::getValue($dto,'placeAddress');

//        $checkBoxStock->stock_transfer_id = ArrayHelper::getValue($dto,'stockTransferId');
//        $checkBoxStock->stock_transfer_outbound_box = ArrayHelper::getValue($dto,'stockTransferOutboundBox');
//        $checkBoxStock->stock_status_transfer = ArrayHelper::getValue($dto,'stockStatusTransfer');
        $checkBoxStock->deleted = 0;

        return $checkBoxStock;
    }

    //
    public function createStockBatchInsert($rows) {

        if(empty($rows)) {
            return null;
        }

        $checkBoxStock = new CheckBoxStock;
        \Yii::$app->db->createCommand()->batchInsert(CheckBoxStock::tableName(), $checkBoxStock->attributes(), $rows)->execute();
    }

    //
    public function getAllBoxAddressForFullInventory($inventoryType = CheckBoxType::PART_BY_BOX) {
        $q =  Stock::find();

        $q->select('primary_address as boxAddress, secondary_address as placeAddress')
          ->andWhere(['client_id'=>$this->getClientID()])
          ->groupBy('primary_address, secondary_address');

//        if($inventoryType == CheckBoxType::PART_BY_BOX) {
//            $q->andWhere(['status'=>Stock::getExistInBox()]);
//        } else {
            $q->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES]);
//        }

        return $q->asArray()->all();
    }

      //
    public function getAllBoxAddressByRowAddress($minMaxPlaceAddress,$inventoryType = CheckBoxType::PART_BY_BOX) {
        $q =  Stock::find();
        $q->select('primary_address as boxAddress, secondary_address as placeAddress')
          ->andWhere([
            'client_id'=>$this->getClientID(),
            'secondary_address'=>$minMaxPlaceAddress,
        ])
        ->groupBy('primary_address,secondary_address');

//        if($inventoryType == CheckBoxType::PART_BY_BOX) {
//            $q->andWhere(['status'=>Stock::getExistInBox()]);
//        } else {
            $q->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES]);
//        }

        return $q->asArray()->all();
    }
}