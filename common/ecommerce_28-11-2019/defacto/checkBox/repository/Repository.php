<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\ecommerce\defacto\checkBox\repository;

use common\ecommerce\constants\CheckBoxStatus;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\entities\EcommerceCheckBox;
use common\ecommerce\entities\EcommerceCheckBoxStock;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceStock;
use common\overloads\ArrayHelper;

class Repository
{
    public function getClientID()
    {
        return 2;
    }

    public function getWarehouseID()
    {
        return 1;
    }

    public function isExistBox($title,$boxBarcode,$inventoryKey = null) {
        return EcommerceCheckBox::find()
            ->andWhere(["title"=>$title,'box_barcode'=>$boxBarcode,'client_id'=>$this->getClientID()])
            ->andFilterWhere(["inventory_key"=>$inventoryKey])
            ->exists();
    }

    //
    public function getStockByBoxBarcode($boxBarcode) {
        return EcommerceStock::find()->andWhere([
            'box_address_barcode'=>$boxBarcode,
            'client_id'=>$this->getClientID(),
            'status_outbound'=>StockOutboundStatus::getExistInBox()
        ])->asArray()->all();
    }

    public function isExistProductInBoxForScanning($productBarcode,$boxBarcode,$placeAddress,$title = null,$inventoryKey = null)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(["product_barcode"=>$productBarcode,'box_barcode'=>$boxBarcode,'place_address'=>$placeAddress,'status'=>CheckBoxStatus::NO])
            ->andWhere(["title"=>$title,'inventory_key'=>$inventoryKey])
            ->exists();
    }

    public function productScan($productBarcode,$boxBarcode,$placeAddress,$title,$inventoryKey = null)
    {
        $checkBoxStockOne = EcommerceCheckBoxStock::find()
            ->andWhere(["product_barcode"=>$productBarcode,'box_barcode'=>$boxBarcode,'place_address'=>$placeAddress,'status'=>CheckBoxStatus::NO])
            ->andWhere(["title"=>$title,'inventory_key'=>$inventoryKey])
            ->one();

        if($checkBoxStockOne) {
            $checkBoxStockOne->status = CheckBoxStatus::YES;
            $checkBoxStockOne->scanned_datetime = time();
            $checkBoxStockOne->save(false);
        }

        return $checkBoxStockOne;
    }

    public function qtyProductInBox($boxBarcode,$placeAddress,$title,$inventoryKey = null)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["title"=>$title,'inventory_key'=>$inventoryKey])
            ->count();
    }

    public function qtyScannedProductInBox($boxBarcode,$placeAddress,$title,$inventoryKey = null)
    {
        return EcommerceCheckBoxStock::find()
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress,'status'=>CheckBoxStatus::YES])
            ->andWhere(["title"=>$title,'inventory_key'=>$inventoryKey])
            ->count();
    }

    public function getProductListInBox($boxBarcode,$placeAddress,$title,$inventoryKey = null)
    {
        return EcommerceCheckBoxStock::find()
            ->select('product_barcode,count(product_barcode) as qtyProduct, box_barcode, place_address, stock_outbound_id, stock_outbound_status, status')
            ->andWhere(['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress])
            ->andWhere(["title"=>$title,'inventory_key'=>$inventoryKey])
            ->groupBy('product_barcode, box_barcode, place_address, stock_outbound_id, stock_outbound_status,status')
            ->orderBy('product_barcode')
            ->asArray()
            ->all();
    }

    public function emptyBox($boxBarcode,$placeAddress,$title,$inventoryKey = null)
    {
        return EcommerceCheckBoxStock::updateAll(['status'=>CheckBoxStatus::NO],
                ['box_barcode'=>$boxBarcode,'place_address'=>$placeAddress,"title"=>$title,'inventory_key'=>$inventoryKey]
            );
    }


    //
    public function createBox($dto) {
        $checkBox = new EcommerceCheckBox();
        $checkBox->client_id = ArrayHelper::getValue($dto,'clientId');
        $checkBox->warehouse_id = ArrayHelper::getValue($dto,'warehouseId');
        $checkBox->employee_id = ArrayHelper::getValue($dto,'employeeId');
        $checkBox->inventory_key = ArrayHelper::getValue($dto,'inventoryKey');
        $checkBox->title = ArrayHelper::getValue($dto,'title');
        $checkBox->box_barcode = ArrayHelper::getValue($dto,'boxBarcode');
        $checkBox->place_address = ArrayHelper::getValue($dto,'placeAddress');
        $checkBox->expected_qty = ArrayHelper::getValue($dto,'expectedQty');
        $checkBox->scanned_qty = ArrayHelper::getValue($dto,'scannedQty');
        $checkBox->save(false);

        return $checkBox;
    }

    //
    public function createStock($dto) {
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
        $checkBoxStock->inventory_key = ArrayHelper::getValue($dto,'inventoryKey');
        $checkBoxStock->title = ArrayHelper::getValue($dto,'title');
        $checkBoxStock->box_barcode = ArrayHelper::getValue($dto,'boxBarcode');
        $checkBoxStock->place_address = ArrayHelper::getValue($dto,'placeAddress');

        $checkBoxStock->save(false);
        return $checkBoxStock;
    }

}