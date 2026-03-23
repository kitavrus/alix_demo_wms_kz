<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:17
 */

namespace common\ecommerce\defacto\transfer\validation;

use common\ecommerce\defacto\barcodeManager\service\BarcodeService;

class TransferValidationV2
{
    private $repository;

    public function __construct() {
        $this->repository = new \common\ecommerce\defacto\transfer\repository\TransferRepositoryV2();
        $this->barcodeService = new BarcodeService();
    }

    public function isReadyPrintPickListForScanning($aPikingListBarcode) {
        return $this->repository->isReadyPrintPickListForScanning($aPikingListBarcode);
    }

    public function isOurBoxBarcode($aBarcode) {
        return $this->barcodeService->isOurInboundBoxBarcode($aBarcode);
    }

    public function isProductOurBoxBarcodeExist($aPikingListBarcode,$aBarcode) {
        return $this->repository->isProductOurBoxBarcodeExist($aPikingListBarcode,$aBarcode);
    }

    public function isLcBarcode($aLCBarcode) {
        return $this->barcodeService->isDefactoBoxBarcode($aLCBarcode);
    }

    public function isLcBarcodeExist($aLCBarcode) {
        return $this->barcodeService->isDefactoBoxBarcode($aLCBarcode);
    }

    public function isProductBarcode($aProductBarcode) {
        return $this->barcodeService->isDefactoProductBarcode($aProductBarcode);
    }

    public function isProductBarcodeExist($aOurBoxBarcode,$aProductBarcode) {
        return $this->repository->isProductBarcodeExist($aOurBoxBarcode,$aProductBarcode);
    }

    public function isExtraBarcodeInOrder($aPikingListBarcode,$aProductBarcode) {
        return !$this->repository->isExtraBarcodeInOrder($aPikingListBarcode,$aProductBarcode);
    }

    public function isExtraBarcodeInBox($aOurBoxBarcode,$aPikingListBarcode, $aProductBarcode) {
        return $this->repository->isExtraBarcodeInBox($aOurBoxBarcode,$aPikingListBarcode, $aProductBarcode);
    }
}