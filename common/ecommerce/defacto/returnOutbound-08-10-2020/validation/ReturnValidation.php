<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 9:34
 */

namespace common\ecommerce\defacto\returnOutbound\validation;


use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\returnOutbound\repository\ReturnRepository;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use stockDepartment\modules\wms\managers\defacto\ReturnStatus;
use common\ecommerce\constants\OutboundStatus;

class ReturnValidation
{
    private $barcodeService;
    private $outboundRepository;
    private $returnRepository;
    private $stockService;

    /**
     * ReturnValidation constructor.
     */
    public function __construct()
    {
        $this->barcodeService = new BarcodeService();
        $this->outboundRepository = new OutboundRepository();
        $this->returnRepository = new ReturnRepository();
        $this->stockService = new Service();
    }

    public function isOrderNumber($orderNumber) {
        $orderReturn = $this->returnRepository->getOrderByAny($orderNumber);
        return empty($orderReturn->client_IsRefundable);
    }

    public function isOrderComplete($orderNumber) {
        $orderReturn = $this->returnRepository->getOrderByAny($orderNumber);
        return !empty($orderReturn) && $orderReturn->status == ReturnOutboundStatus::DONE;
    }

    public function isOrderReturnWithDifferentScannedProduct($orderNumber) {
        $orderReturn = $this->returnRepository->getOrderByAny($orderNumber);
        return $orderReturn->expected_qty != $orderReturn->accepted_qty;
    }

    public function isOurInboundBoxBarcode($boxBarcode) {
        return $this->barcodeService->isOurInboundBoxBarcode($boxBarcode);
    }

    public function isDefactoProductBarcode($boxBarcode) {
        return $this->barcodeService->isDefactoProductBarcode($boxBarcode);
    }

    public function isProductNotExistInOrder($orderNumber,$productBarcode) {
        $returnOrder = $this->returnRepository->getOrderByAny($orderNumber);
        return !empty($returnOrder) && $this->returnRepository->isOrderItemExist($returnOrder->id,$productBarcode);
    }

    public function isExtraBarcodeInOrder($orderNumber, $productBarcode){
        $returnOrder = $this->returnRepository->getOrderByAny($orderNumber);
        return !empty($returnOrder) && $this->returnRepository->isExtraBarcodeInOrder($returnOrder->id, $productBarcode);
    }

    public function isOrderGivenToCourier($orderNumber) {
        $orderOutbound = $this->outboundRepository->getOrderByAny($orderNumber);
        return !empty($orderOutbound) && $orderOutbound->status == OutboundStatus::DONE;
    }
}