<?php
namespace common\ecommerce\defacto\inbound\service;

use common\ecommerce\entities\EcommerceInboundItem;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class InboundItemService
{
    public static function save($aLotAndProducts,$aInboundId,$aLcBarcode,$aClientInboundId,$aClientLotSKU) {

        foreach($aLotAndProducts as $product) {

            $isExist = EcommerceInboundItem::find()->andWhere([
                'inbound_id'=>$aInboundId,
                'client_box_barcode'=>$aLcBarcode,
                'lot_barcode'=>$product->LotBarcode,
                'product_barcode'=>$product->ProductBarcode,
            ])->exists();

            if($isExist) {
                return true;
            }

            $response = new EcommerceInboundItem();
            $response->inbound_id = $aInboundId;
            $response->client_box_barcode = $aLcBarcode;
            $response->lot_barcode = $product->LotBarcode;
            $response->product_barcode = $product->ProductBarcode;
            $response->product_expected_qty = $product->Quantity;
            $response->client_inbound_id = $aClientInboundId;
            $response->client_lot_sku = $aClientLotSKU;
            $response->save(false);
        }

        return true;
    }
}