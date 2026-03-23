<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:17
 */
namespace common\ecommerce\defacto\checkBox\validation;

use common\ecommerce\defacto\checkBox\service\CheckBoxService;

class Validation
{
    private $repository;
    private $changeAddressPlaceValidation;

    public function __construct() {
        $this->repository = new CheckBoxService();
        $this->changeAddressPlaceValidation = new \common\ecommerce\defacto\changeAddressPlace\validation\Validation();
    }

    public function isBoxNotEmpty($boxBarcode) {
        return $this->changeAddressPlaceValidation->isBoxNotEmpty($boxBarcode);
    }

    public function ourBoxBarcode($boxBarcode) {
        return $this->changeAddressPlaceValidation->ourBoxBarcode($boxBarcode);
    }


    public function palaceAddress($addressBarcode)
    {
       return $this->changeAddressPlaceValidation->palaceAddress($addressBarcode);
    }

    public function isExistPalaceAddress($addressBarcode)
    {
        return $this->changeAddressPlaceValidation->isExistPalaceAddress($addressBarcode);
    }

    public function isBoxOnPlaceAddress($boxBarcode,$addressBarcode)
    {
        return $this->changeAddressPlaceValidation->isBoxOnPlaceAddress($boxBarcode,$addressBarcode);
    }

    public function getPlaceAddressByBoxBarcode($boxBarcode)
    {
        return $this->changeAddressPlaceValidation->getPlaceAddressByBoxBarcode($boxBarcode);
    }


    public function isProductBarcode($productBarcode) {
        return $this->changeAddressPlaceValidation->productBarcode($productBarcode);
//        $this->changeAddressPlaceValidation->isProductExistInBox($productBarcode,$boxBarcode);
    }

    public function isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress) {
        return $this->repository->isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress);
//        $this->changeAddressPlaceValidation->isProductExistInBox($productBarcode,$boxBarcode);
    }

    public function isExistProductInStockBoxForScanning($boxBarcode,$placeAddress= null) {
        return $this->repository->isExistProductInStockBoxForScanning($boxBarcode,$placeAddress);
    }
}