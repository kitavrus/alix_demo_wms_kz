<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:17
 */
namespace stockDepartment\modules\wms\managers\erenRetail\checkBox\validation;

use stockDepartment\modules\wms\managers\erenRetail\checkBox\service\CheckBoxService;
//use common\components\BarcodeManager;
//use common\modules\stock\models\Stock;

class Validation
{
    private $repository;
    private $changeAddressPlaceValidation;

    public function __construct() {
        $this->repository = new CheckBoxService();
        $this->changeAddressPlaceValidation = new \common\b2b\domains\changeAddressPlace\validation\Validation();
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


    public function isProductBarcode($productBarcode,$addressPalace,$boxAddress) {

//		if(BarcodeManager::isBoxLotOrReturnBox($productBarcode,$addressPalace,$boxAddress)) {
//			if(BarcodeManager::isBoxOnlyOur($productBarcode)) {
//				return Stock::find()->select('product_barcode')->andWhere(['primary_address'=>$productBarcode])->scalar();
//			}
//
//			if(BarcodeManager::isDefactoBox($productBarcode)) {
//				return Stock::find()->select('product_barcode')->andWhere(['inbound_client_box'=>$productBarcode])->scalar();
//			}
//		}

//		return $this->changeAddressPlaceValidation->productBarcode($productBarcode);
		return true;
	}

    public function isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress) {
        return $this->repository->isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress);
    }

    public function isExistProductInStockBoxForScanning($boxBarcode,$placeAddress= null) {
        return $this->repository->isExistProductInStockBoxForScanning($boxBarcode,$placeAddress);
    }
}