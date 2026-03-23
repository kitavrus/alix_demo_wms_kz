<?php
namespace common\ecommerce\defacto\inbound\service;

use common\ecommerce\entities\EcommerceInboundItem;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class InboundItemService
{
	
	    public static function addProduct($aProductDto) {

            $isExist = EcommerceInboundItem::find()->andWhere([
                'inbound_id'=>$aProductDto->ourInboundId,
                'client_box_barcode'=>$aProductDto->clientLcBarcode,
                'product_barcode'=>$aProductDto->clientProductBarcode,
                'client_product_sku'=>$aProductDto->clientProductSKU,
            ])->exists();

            if($isExist) {
                return false;
            }

            $response = new EcommerceInboundItem();
            $response->inbound_id = $aProductDto->ourInboundId;
            $response->client_box_barcode = $aProductDto->clientLcBarcode;
            $response->lot_barcode = '';
            $response->product_barcode = $aProductDto->clientProductBarcode;
            $response->product_expected_qty = $aProductDto->clientProductQuantity;
            $response->client_inbound_id = $aProductDto->clientInboundId;
            $response->client_product_sku = $aProductDto->clientProductSKU;
            $response->save(false);

        return $response;
    }

	
	
    public static function save($aLotAndProducts,$aInboundId,$aLcBarcode,$aClientInboundId,$aClientLotSKU,$lotOrSingleQuantity = 1) {

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
            $response->product_expected_qty = $product->Quantity * $lotOrSingleQuantity;
            $response->client_inbound_id = $aClientInboundId;
            $response->client_lot_sku = $aClientLotSKU;
            $response->save(false);
        }

        return true;
    }
}