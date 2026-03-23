<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\clientObject\subaruAuto\outbound\service;


use common\clientObject\subaruAuto\outbound\repository\OutboundRepository;
use common\modules\outbound\models\OutboundPickingLists;
use yii\helpers\VarDumper;

//use common\clientObject\subaruAuto\outbound\service\OutboundReservationService;

class OutboundService
{
    private $repository;
    private $outboundService;
    private $dto;

    public function __construct($dto = [])
    {
        $this->repository = new OutboundRepository();
        $this->outboundService = new \common\modules\outbound\service\Service();
        $this->dto = $dto;
    }

    public function getOrdersForPrintPickingList()
    {
        return $this->repository->getOrdersForPrintPickList();
    }

    public function getOrderInfo($id = null)
    {
//        if(is_null($id)) {
            $id = is_null($id) ? isset($this->dto->order->id) ? $this->dto->order->id : 0 : $id;
//        }
        return $this->repository->getOrderInfo($id);
//        return $this->repository->getOrderInfo($this->dto->order->id);
    }

    public function runReservation($outboundInfo) {
        if(isset($outboundInfo->order) && (int)$outboundInfo->order->allocated_qty < 1) {
            $outboundReservation = new \common\clientObject\subaruAuto\outbound\service\OutboundReservationService();
            $outboundReservation->run($outboundInfo);
        }
    }

    public function qtyProductInBox()
    {
        return $this->repository->qtyProductInBox($this->dto->order->id, $this->dto->boxBarcode);
    }

    public function makeScanned()
    {
        $this->repository->makeScannedProduct($this->dto);
    }


    public function makeScannedQty()
    {
        $this->repository->makeScannedProductQty($this->dto);
    }


//    public function makeScannedFab()
//    {
//        $this->repository->makeScannedFab($this->dto);
//    }

    public function makePrintBoxLabel()
    {
        $this->repository->makePrintBoxLabel($this->dto);
    }

    public function cleanBox()
    {
        $this->repository->cleanBox($this->dto);
    }

    public function getOrderForComplete()
    {
        return $this->repository->getOrderForComplete();
    }

    public function getOrderItemsForDiffReport()
    {
        return $this->repository->getOrderItemsForDiffReport($this->dto->order->id);
//        return $this->repository->getOrderItemsForDiffReport($this->dto->pickList->id);
    }

    public function getBoxesInOrder()
    {
        return $this->repository->getBoxesInOrder($this->dto->order->id);
    }

    public function acceptedOrder($orderId) {
        $this->repository->acceptedOrder($orderId);
    }

    public function create($dto) {
        $dto->clientId = $this->repository->getClientID();
        $this->outboundService->create($dto);
    }
    public function getClientID()
    {
        return $this->repository->getClientID();
    }

    public function getOrderItemsByPickListBarcode($pickListBarcode)
    {
        $pickListIDs = OutboundPickingLists::getPickingListIDsByPickingListBarcode($pickListBarcode);
        return OutboundPickingLists::getStockByPickingIDs($pickListIDs);
    }
}
