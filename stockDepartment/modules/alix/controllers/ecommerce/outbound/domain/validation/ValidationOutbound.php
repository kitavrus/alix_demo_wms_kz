<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:17
 */

namespace stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\validation;


use stockDepartment\modules\intermode\controllers\ecommerce\barcode\domain\service\BarcodeService;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\repository\OutboundRepository;

class ValidationOutbound
{
    private $repository;

    public function __construct() {
        $this->repository = new OutboundRepository();
    }

    public function isProduct($pickList,$barcode) {
        $order = $this->getOrderByPickList($pickList);
        return $this->repository->isProductExistInOrder($order->id,$barcode);
    }

    public function isExtraBarcodeInOrder($pickList,$barcode) {
        $order = $this->getOrderByPickList($pickList);
        return $this->repository->isExtraBarcodeInOrder($order->id,$barcode);
    }

    public function getOrderByPickList($pickList) {
        return $this->repository->findOrderByPickList($pickList);
    }

    public function getEmployeeByBarcode($employeeBarcode) {
        return $this->repository->getEmployeeByBarcode($employeeBarcode);
    }

    public function getPickListByBarcode($pickListBarcode) {
        return $this->repository->getPickListByBarcode($pickListBarcode);
    }

    public function isValidPickingList($pickList) {
        $result = $this->getPickListByBarcode($pickList);
        return  $this->repository->isOrderExistByPickingBarcode($result['id']);
//        return  $this->repository->isOrderExistByPickingBarcode($result['id'],$result['orderNumber']);
    }

    public function isNotDoneOrder($pickList) {
        $result = $this->getPickListByBarcode($pickList);
        return  $this->repository->isNotDoneOrder($result['id']);
//        return  $this->repository->isNotDoneOrder($result['id'],$result['orderNumber']);
    }
	
	public function isCancelOrder($pickList) {
        $result = $this->getPickListByBarcode($pickList);
        return  $this->repository->isCancelOrder($result['id']);
    }

    public function isPackageBarcodeExist($barcode) {
        $pbs = new BarcodeService();
        return $pbs->isExist($barcode);
    }

    public function usePackageBarcodeInOtherOrder($pickList,$barcode) {
        return  !$this->repository->usePackageBarcodeInOtherOrder($pickList,$barcode);
    }

    public function isOrderReserved($pickList) {
        return  $this->repository->isOrderReserved($pickList);
    }

    public function isOrderScanned($pickList) {
        return  $this->repository->isOrderScanned($pickList);
    }

    public function isEmptyPackageBarcodeInOrder($pickList) {
        return $this->repository->isEmptyPackageBarcodeInOrder($pickList);
    }

    public function isExistsProductQRCode($productQRCode) {
        return $this->repository->isExistsProductQRCode($productQRCode);
    }
	
//	public function isKaspiOrder($pickList) {
//        return  $this->repository->isKaspiOrder($pickList);
//    }
//
//    public function isValidKaspiOrder($pickList) {
//        return  $this->repository->isValidKaspiOrder($pickList);
//    }
//
//
//    public function isLamodaOrder($pickList) {
//        return  $this->repository->isLamodaOrder($pickList);
//    }
//
//    public function isValidLamodaOrder($pickList) {
//        return  $this->repository->isValidLamodaOrder($pickList);
//    }
}