<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 14:48
 */

namespace common\ecommerce\defacto\changeAddressPlace\validation;


use common\ecommerce\constants\InboundStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\changeAddressPlace\repository\Repository;
use common\ecommerce\entities\EcommerceStock;
use common\modules\warehouseAddress\service\RackAddressService;

class Validation
{
    private $rackAddressService;
    private $barcodeService;
    private $repository;
    /**
     * Validation constructor.
     * @param $config array
     */
    public function __construct($config = [])
    {
        $this->rackAddressService = new RackAddressService();
        $this->barcodeService = new BarcodeService();
        $this->repository = new Repository();
    }

    public function ourBoxBarcode($fromAddress) {
        return $this->barcodeService->isOurInboundBoxBarcode($fromAddress);
    }

    public function palaceAddress($toAddress)
    {
        $floor = $this->barcodeService->subStr($toAddress,0,2);
        $availableFloorList = [
            '3-',
            //'10',
        ];

        return in_array($floor,$availableFloorList);
    }

    public function isExistPalaceAddress($toAddress) {
        return $this->rackAddressService->isExists($toAddress);
    }

    public function productBarcode($productBarcode) {
        return $this->barcodeService->isDefactoProductBarcode($productBarcode);
    }
    public function productQty($productQty) {
        return intval($productQty) < 0;
    }

    public function isProductExistInBox($productBarcode,$boxBarcode) {
        return EcommerceStock::find()
            ->andWhere([
                'product_barcode'=>$productBarcode,
                'id'=>$this->repository->getNotEmptyBoxIds($boxBarcode)
//                'box_address_barcode'=>$boxBarcode,

            ])
            ->exists();
    }

    public function isProductExistInBoxForInbound($productBarcode,$boxBarcode) {
        return EcommerceStock::find()
            ->andWhere([
                'product_barcode'=>$productBarcode,
                'id'=>$this->repository->getNotEmptyBoxIds($boxBarcode)
//                'status_availability'=>StockAvailability::NO
            ])
            ->exists();
    }

    public function isBoxOnPlace($boxBarcode) {
        return EcommerceStock::find()
            ->andWhere([
                'box_address_barcode'=>$boxBarcode,
            ])
            ->andWhere('box_address_barcode IS NOT NULL AND box_address_barcode != "" AND place_address_barcode != 0')
            ->exists();
    }

    public function isBoxOnPlaceAddress($boxBarcode,$placeAddressBarcode) {
        return EcommerceStock::find()
            ->andWhere([
                'box_address_barcode'=>$boxBarcode,
                'place_address_barcode'=>$placeAddressBarcode,
            ])
            ->exists();
    }

    public function isBoxNotEmpty($boxBarcode)
    {
        return EcommerceStock::find()
            ->andWhere(['id'=>$this->repository->getNotEmptyBoxIds($boxBarcode)])
            ->exists();
    }
	
	 public function getPlaceAddressByBoxBarcode($boxBarcode) {
        return $this->repository->getPlaceAddressByBoxBarcode($boxBarcode);
    }

//    public function isBoxNotEmpty($boxBarcode)
//    {
//        return EcommerceStock::find()
//            ->andWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::YES,StockAvailability::NO]])
//            ->orWhere(['box_address_barcode'=>$boxBarcode,'status_availability'=>[StockAvailability::NO],'status_inbound'=>InboundStatus::SCANNED])
//            ->exists();
//    }
}