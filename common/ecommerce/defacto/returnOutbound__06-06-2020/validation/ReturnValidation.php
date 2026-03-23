<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 9:34
 */

namespace common\ecommerce\defacto\returnOutbound\validation;


use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\returnOutbound\repository\ReturnRepository;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;

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
        return $this->outboundRepository->isOrderExistByAny($orderNumber);
//        return $this->barcodeService->subStr($orderNumber) == 'OMC';
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

        if(empty($returnOrder->outbound_id)) { return false; }

        return !empty($returnOrder->outbound_id) && !$this->returnRepository->isOrderItemExist($returnOrder->id,$productBarcode);
    }

    public function isExtraBarcodeInOrder($orderNumber, $productBarcode){
        $returnOrder = $this->returnRepository->getOrderByAny($orderNumber);
        return !empty($returnOrder->outbound_id) && $this->returnRepository->isExtraBarcodeInOrder($returnOrder->id, $productBarcode);
    }


}