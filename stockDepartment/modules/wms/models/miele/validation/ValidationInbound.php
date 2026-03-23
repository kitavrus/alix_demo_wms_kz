<?php
namespace stockDepartment\modules\wms\models\miele\validation;


use common\components\BarcodeManager;
use stockDepartment\modules\wms\models\miele\repository\InboundRepository;

class ValidationInbound
{
    private $repository;

    /**
     * Validation constructor.
     */
    public function __construct() {
        $this->repository = new InboundRepository();
    }

    public function isProductBarcode($barcode) {
        $isExistBarcode = $this->repository->isProductExists($barcode);
        return (strlen(trim($barcode)) == 13 && $isExistBarcode);
    }

    public function isFabBarcode($barcode) {
        return strlen(trim($barcode)) == 13;
    }

    public function isBoxExist($inboundID,$barcode) {
        return $this->repository->isBoxExist($inboundID,$barcode);
    }

    public function isExistBarcodeInOrder($inboundID,$barcode) {
        return $this->repository->IsExistBarcodeInOrder($inboundID,$barcode);
    }

    public function isExtraBarcodeInOrder($inboundID,$barcode) {
        return $this->repository->IsExtraBarcodeInOrder($inboundID,$barcode);
    }

    public function isBoxOnlyOur($barcode) {
        return BarcodeManager::isBoxOnlyOur($barcode);
    }

    public function getRepository() {
        return $this->repository;
    }
}