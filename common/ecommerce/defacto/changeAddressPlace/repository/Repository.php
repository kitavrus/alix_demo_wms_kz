<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 13.12.2019
 * Time: 14:54
 */
namespace common\ecommerce\defacto\changeAddressPlace\repository;

use common\ecommerce\constants\InboundStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\entities\EcommerceChangeAddressPlace;
use common\ecommerce\entities\EcommerceStock;

class Repository
{

    public function getPlaceAddressByBoxBarcode($boxBarcode) {
        return EcommerceStock::find()
            ->select('place_address_barcode')
            ->andWhere([
                'box_address_barcode'=>$boxBarcode,
            ])
            ->scalar();
    }

    public function getNotEmptyBoxIdsQuery($boxBarcode)
    {
        return EcommerceStock::find()->select('id')
            ->andWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::YES,StockAvailability::NO]])
            ->orWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::NO],'status_inbound'=>InboundStatus::SCANNED]);
    }

    public function getNotEmptyBoxIds($boxBarcode)
    {
        return EcommerceStock::find()->select('id')
            ->andWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::YES,StockAvailability::NO]])
            ->orWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::NO],'status_inbound'=>InboundStatus::SCANNED])->column();
    }

    //
    public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode)
    {
        EcommerceStock::updateAll([
            'place_address_barcode'=>$PlaceBarcode
        ],[
            'box_address_barcode'=>$BoxBarcode,
        ]);
    }

    //
    public function moveProductFromBoxToBox($fromBoxBarcode,$productBarcode,$toBoxBarcode)
    {
        $productStockOne = EcommerceStock::find()->andWhere([
//                'box_address_barcode'=>$fromBoxBarcode,
                'product_barcode'=>$productBarcode,
                'id'=>$this->getNotEmptyBoxIdsQuery($fromBoxBarcode)
//                'status_availability'=>[StockAvailability::YES,StockAvailability::NO]]
        ])->one();

        if($productStockOne) {
            $productStockOne->box_address_barcode = $toBoxBarcode;
            $productStockOne->save(false);
        }
    }

    public function moveAllProductsFromBoxToBox($fromBoxBarcode,$toBoxBarcode)
    {
        $fromBoxPlaceAddressBarcode = EcommerceStock::find()->select('place_address_barcode')->andWhere([
//                'box_address_barcode'=>$fromBoxBarcode,
                'id'=>$this->getNotEmptyBoxIdsQuery($toBoxBarcode)
            ])->scalar();
			
		if(empty($fromBoxPlaceAddressBarcode)) {
            $fromBoxPlaceAddressBarcode = EcommerceStock::find()->select('place_address_barcode')->andWhere([
//                'box_address_barcode'=>$fromBoxBarcode,
                'id'=>$this->getNotEmptyBoxIdsQuery($fromBoxBarcode)
            ])->scalar();
        }

        EcommerceStock::updateAll([
            'box_address_barcode'=>$toBoxBarcode,
            'place_address_barcode'=>$fromBoxPlaceAddressBarcode
        ],[
//                'box_address_barcode'=>$fromBoxBarcode,
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