<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 13.12.2019
 * Time: 14:54
 */
namespace common\b2b\domains\changeAddressPlace\repository;

use common\ecommerce\constants\StockAvailability;
use common\ecommerce\entities\EcommerceChangeAddressPlace;
use common\modules\stock\models\Stock;

class Repository
{

    public function getPlaceAddressByBoxBarcode($boxBarcode) {
        return Stock::find()
            ->select('secondary_address')
            ->andWhere([
                'primary_address'=>$boxBarcode,
            ])
            ->scalar();
    }

    public function getNotEmptyBoxIdsQuery($boxBarcode)
    {
        return Stock::find()->select('id')
            ->andWhere(['primary_address'=>$boxBarcode,'status_availability'=>[StockAvailability::YES,StockAvailability::NO]])
            ->orWhere(['primary_address'=>$boxBarcode,'status_availability'=>[StockAvailability::NO],'status'=>Stock::STATUS_INBOUND_SCANNED]);
    }

    public function getNotEmptyBoxIds($boxBarcode)
    {
        return Stock::find()->select('id')
            ->andWhere(['primary_address'=>$boxBarcode,'status_availability'=>[StockAvailability::YES,StockAvailability::NO]])
            ->orWhere(['primary_address'=>$boxBarcode,'status_availability'=>[StockAvailability::NO],'status'=>Stock::STATUS_INBOUND_SCANNED])->column();
    }

    //
    public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode)
    {
        Stock::updateAll([
            'secondary_address'=>$PlaceBarcode
        ],[
            'primary_address'=>$BoxBarcode,
        ]);
    }

    //
    public function moveProductFromBoxToBox($fromBoxBarcode,$productBarcode,$toBoxBarcode)
    {
        $productStockOne = Stock::find()->andWhere([
//                'primary_address'=>$fromBoxBarcode,
                'product_barcode'=>$productBarcode,
                'id'=>$this->getNotEmptyBoxIdsQuery($fromBoxBarcode)
//                'status_availability'=>[StockAvailability::YES,StockAvailability::NO]]
        ])->one();

        if($productStockOne) {
            $productStockOne->primary_address = $toBoxBarcode;
            $productStockOne->save(false);
        }
    }

    public function moveAllProductsFromBoxToBox($fromBoxBarcode,$toBoxBarcode)
    {
        $fromBoxPlaceAddressBarcode = Stock::find()->select('secondary_address')->andWhere([
//                'primary_address'=>$fromBoxBarcode,
                'id'=>$this->getNotEmptyBoxIdsQuery($toBoxBarcode)
            ])->scalar();

        Stock::updateAll([
            'primary_address'=>$toBoxBarcode,
            'secondary_address'=>$fromBoxPlaceAddressBarcode
        ],[
//                'primary_address'=>$fromBoxBarcode,
                'id'=>$this->getNotEmptyBoxIds($fromBoxBarcode)
            ]
        );
    }

    /**
     * @param $FromAddress
     * @param $ToAddress
     * @param string $ProductBarcode
     * @param int $ProductQty
     */
    public function saveHistory($FromAddress,$ToAddress,$ProductBarcode = '',$ProductQty = 0) {
        $changeAddressPlace = new EcommerceChangeAddressPlace();
        $changeAddressPlace->from_barcode = $FromAddress;
        $changeAddressPlace->to_barcode = $ToAddress;
        $changeAddressPlace->product_barcode = $ProductBarcode;
        $changeAddressPlace->product_qty = $ProductQty;
        $changeAddressPlace->save(false);
    }
}