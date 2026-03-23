<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.08.2017
 * Time: 15:32
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceStock;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class ReservationPlaceAddressSortingService
{
    public function beforeReservationSorting($orderIdList)
    {
        $ids = EcommerceOutbound::find()->select('id')->andWhere(['id' => $orderIdList])->orderBy('expected_qty')->column();
        return $ids;
    }

    public function beforePrintPickingList($orderIdList)
    {
        $orderListForSort = [];
        $orderOnStock = EcommerceStock::find()
                        ->select('outbound_id, place_address_sort1, product_barcode, box_address_barcode, place_address_barcode, product_model, product_name, count(*) as productQty')
                        ->andWhere(['outbound_id'=>$orderIdList])
                        ->groupBy('product_barcode, box_address_barcode,outbound_id')
                        ->asArray()
                        ->all();

        $outboundOrderListInfo = [];

        foreach ($orderOnStock as $key=>$productOnStock) {
//            echo $productOnStock['place_address_barcode'].' =  '.$this->makePlaceAddressSort1($productOnStock['place_address_barcode'])."<br />";

            if(!isset($outboundOrderListInfo[$productOnStock['outbound_id']])) {
                $order = EcommerceOutbound::find()->andWhere(['id' => $productOnStock['outbound_id']])->one();
                $outboundOrderListInfo[$productOnStock['outbound_id']] = [
                    'orderNumber' => $order->order_number,
                    'orderID' => $order->id,
                    'clientID' => $order->client_id,
                    'showPriority' => $order->client_Priority,
                    'showShippingCity' => $order->client_ShippingCity,
                    'showPackMessage' => $order->client_PackMessage,
                    'showGiftWrappingMessage' => $order->client_GiftWrappingMessage,
                ];
            }

            $orderListForSort [] = [
                'order'=>$outboundOrderListInfo[$productOnStock['outbound_id']],
                'outboundId'=>$productOnStock['outbound_id'],
                'placeAddressSort1'=>$productOnStock['place_address_sort1'],
                'placeAddressBarcode'=>$productOnStock['place_address_barcode'],
                'productBarcode'=>$productOnStock['product_barcode'],
                'boxAddressBarcode'=>$productOnStock['box_address_barcode'],
                'productModel'=>$productOnStock['product_model'],
                'productName'=>$productOnStock['product_name'],
                'productQty'=>$productOnStock['productQty'],
            ] ;
        }

        ArrayHelper::multisort($orderListForSort,['outboundId','placeAddressSort1']);
        $orderListForSort = ArrayHelper::index($orderListForSort,null,'outboundId');
        uasort($orderListForSort,function($a,$b) {
            $aCount = count($a)-1;
            $bCount = count($b)-1;
            return  $a[$aCount]['placeAddressSort1'] < $b[$bCount]['placeAddressSort1'] ? -1 : 1;
        });

        return $orderListForSort;
    }

    public static function makePlaceAddressSort1($addressBarcode) {
        $addressBarcodeList = explode('-',$addressBarcode);
        if(empty($addressBarcodeList) || !is_array($addressBarcodeList) || !isset($addressBarcodeList[1])) {
            return 999999;
        }
        $addressBarcodeResult = $addressBarcodeList[1].$addressBarcodeList[2];
        return $addressBarcodeResult;
    }

    public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode) {
        EcommerceStock::updateAll([
            'place_address_sort1'=> self::makePlaceAddressSort1($PlaceBarcode)
        ],[
            'box_address_barcode'=>$BoxBarcode,
            'place_address_barcode'=>$PlaceBarcode
        ]);
    }
}