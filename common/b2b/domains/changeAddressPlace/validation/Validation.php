<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 07.10.2017
 * Time: 14:48
 */

namespace common\b2b\domains\changeAddressPlace\validation;


use common\components\BarcodeManager;
use common\ecommerce\constants\InboundStatus;
use common\ecommerce\constants\StockAvailability;
use common\components\BarcodeManager as BarcodeService;
use common\b2b\domains\changeAddressPlace\repository\Repository;
use common\modules\stock\models\Stock;
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
        return $this->barcodeService->isBoxOnlyOur($fromAddress);
    }

    public function palaceAddress($toAddress)
    {
        $floor = $this->barcodeService->subStr($toAddress,0,2);
        $availableFloorList = [
            '1-',
            '2-',
            '3-',
            '5-',
            '6-',
            '7-',
            '8-',
            '9-',
            '11',
        ];

        return in_array($floor,$availableFloorList);
    }

    public function isExistPalaceAddress($toAddress) {
        return $this->rackAddressService->isExists($toAddress);
    }

    public function productBarcode($productBarcode) {
        return $this->barcodeService->isDefactoProduct($productBarcode);
    }
    public function productQty($productQty) {
        return intval($productQty) < 0;
    }

    public function isProductExistInBox($productBarcode,$boxBarcode) {
        return Stock::find()
            ->andWhere([
                'product_barcode'=>$productBarcode,
                'id'=>$this->repository->getNotEmptyBoxIds($boxBarcode)
//                'box_address_barcode'=>$boxBarcode,

            ])
            ->exists();
    }

    public function isProductExistInBoxForInbound($productBarcode,$boxBarcode) {
        return Stock::find()
            ->andWhere([
                'product_barcode'=>$productBarcode,
                'id'=>$this->repository->getNotEmptyBoxIds($boxBarcode)
//                'status_availability'=>StockAvailability::NO
            ])
            ->exists();
    }

    public function isBoxOnPlace($boxBarcode) {
        return Stock::find()
            ->andWhere([
                'primary_address'=>$boxBarcode,
            ])
            ->andWhere('primary_address IS NOT NULL AND primary_address != "" AND secondary_address != 0')
            ->exists();
    }

    public function isBoxOnPlaceAddress($boxBarcode,$placeAddressBarcode) {
        return Stock::find()
            ->andWhere([
                'primary_address'=>$boxBarcode,
                'secondary_address'=>$placeAddressBarcode,
            ])
            ->exists();
    }

    public function isBoxNotEmpty($boxBarcode)
    {
        return Stock::find()
            ->andWhere(['id'=>$this->repository->getNotEmptyBoxIds($boxBarcode)])
            ->exists();
    }

    public function getPlaceAddressByBoxBarcode($boxBarcode) {
        return $this->repository->getPlaceAddressByBoxBarcode($boxBarcode);
    }

    public function isBoxLotOrReturnBox($boxOrProductBarcode,$addressList = [],$boxBarcode = '') {
        return $this->_isProductBoxTypeQuery($boxOrProductBarcode,[Stock::IS_PRODUCT_TYPE_RETURN,Stock::IS_PRODUCT_TYPE_LOT_BOX],$addressList,$boxBarcode);
    }

    public function isBoxOrProductTypeLotBox($boxOrProductBarcode) {
        return $this->_isProductBoxTypeQuery($boxOrProductBarcode,Stock::IS_PRODUCT_TYPE_LOT_BOX);
    }

    public function isBoxOrProductTypeLot($boxOrProductBarcode) {
        return $this->_isProductBoxTypeQuery($boxOrProductBarcode,Stock::IS_PRODUCT_TYPE_LOT);
    }

    private function _isProductBoxTypeQuery($boxOrProductBarcode,$productType,$addressList = [],$boxBarcode = '') {
        return Stock::find()
            ->andWhere(['is_product_type'=>$productType])
            ->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES])
            ->andWhere(['secondary_address'=>$addressList])
            ->andWhere('primary_address = :boxBarcode OR inventory_primary_address = :boxBarcode',[':boxBarcode'=>$boxBarcode])
            ->andWhere('product_barcode = :productBarcode OR primary_address = :productBarcode OR inventory_primary_address = :productBarcode',[':productBarcode'=>$boxOrProductBarcode])
            ->exists();
    }

    public function findProductInStockByReturnBarcodeBoxInventory($boxOrProductBarcode,$addressList = [],$boxBarcode = '') {
        return Stock::find()
            ->select('product_barcode')
            ->andWhere(['is_product_type'=>[Stock::IS_PRODUCT_TYPE_RETURN,Stock::IS_PRODUCT_TYPE_LOT_BOX]])
            ->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES])
            ->andWhere(['secondary_address'=>$addressList])
            ->andWhere('primary_address = :boxBarcode OR inventory_primary_address = :boxBarcode',[':boxBarcode'=>$boxBarcode])
            ->andWhere('product_barcode = :productBarcode OR primary_address = :productBarcode OR inventory_primary_address = :productBarcode',[':productBarcode'=>$boxOrProductBarcode])
            ->scalar();

        // Если передали наш шк короба
//        if(BarcodeManager::isBoxOnlyOur($barcode)) {
//            return Stock::find()->select('product_barcode')
//                ->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES])
//                ->andWhere(['primary_address'=>$barcode])
//                ->scalar();
//        }
//
//        if(BarcodeManager::isDefactoBox($barcode)) {
//            return Stock::find()->select('product_barcode')
//                ->andWhere(['status_availability'=>Stock::STATUS_AVAILABILITY_YES])
//                ->andWhere(['inbound_client_box'=>$barcode])
//                ->scalar();
//        }

//        return -1;
    }
}