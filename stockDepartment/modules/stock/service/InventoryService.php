<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 19.10.2017
 * Time: 12:39
 */

namespace stockDepartment\modules\stock\service;


use common\components\BarcodeManager;
use common\modules\stock\models\Inventory;
use common\modules\stock\models\Stock;

class InventoryService
{
    // @param $primary_address Inventory primary address
    // @param $productBarcode Product barcode
    // @param $secondary_address Array secondary addresses
    // @param $inventoryId
    public function findProductInStockStatusProcess($primaryAddress,$productBarcode,$secondaryAddress,$inventoryId)
    {
        return $this->findProductInStockWithStatus(Inventory::STATUS_SCAN_PROCESS,$primaryAddress,$productBarcode,$secondaryAddress,$inventoryId);
    }
    // @param $primary_address Inventory primary address
    // @param $productBarcode Product barcode
    // @param $secondary_address Array secondary addresses
    // @param $inventoryId
    public function findProductInStockOnScanned($primaryAddress,$productBarcode,$secondaryAddress,$inventoryId) {
        return $this->findProductInStockWithStatus(Inventory::STATUS_SCAN_YES,$primaryAddress,$productBarcode,$secondaryAddress,$inventoryId);
    }
    // @param $status
    // @param $primary_address Inventory primary address
    // @param $productBarcode Product barcode
    // @param $secondary_address Array secondary addresses
    // @param $inventoryId
    private function findProductInStockWithStatus($status,$primaryAddress,$productBarcode,$secondaryAddress,$inventoryId) {
        return Stock::find()->andWhere([
            'inventory_primary_address'=> $primaryAddress,// $inventoryForm->primary_address,
            'product_barcode'=>$productBarcode, //$inventoryForm->product_barcode,
            'secondary_address'=>$secondaryAddress,//$minMax,
            'status_inventory'=>$status,//Inventory::STATUS_SCAN_PROCESS,
            'inventory_id'=> $inventoryId,//$inventoryForm->inventory_id
        ])->one();
    }

    public static function isReturnBoxBarcode($barcode) {
        return BarcodeManager::isReturnBoxBarcode($barcode);
    }
}