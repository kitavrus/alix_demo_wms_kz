<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace stockDepartment\modules\wms\models\miele\service;


use stockDepartment\modules\wms\managers\miele\MovementHistoryService;
use stockDepartment\modules\wms\managers\miele\OutboundSyncService;
use stockDepartment\modules\wms\models\miele\repository\OutboundRepository;

class ServiceOutbound
{
    private $repository;
    private $dto;

    public function __construct($dto = [])
    {
        $this->repository = new OutboundRepository();
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

    public function qtyProductInBox()
    {
        return $this->repository->qtyProductInBox($this->dto->order->id, $this->dto->boxBarcode);
    }

    public function makeScanned()
    {
        $this->repository->makeScannedProduct($this->dto);
    }

    public function makeScannedFab()
    {
        $this->repository->makeScannedFab($this->dto);
    }

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
        return $this->repository->getOrderItemsForDiffReport($this->dto->pickList->id);
    }

    public function getBoxesInOrder()
    {
        return $this->repository->getBoxesInOrder($this->dto->order->id);
    }

    public function acceptedOrder($orderId) {
        $this->repository->acceptedOrder($orderId);
        $orderInfo = $this->repository->getOrderInfo($orderId);
        // Change status on sync
        $sync = new OutboundSyncService();
        $sync->setOurStatusComplete( $orderInfo->order->client_order_id);

        // Add data to movement history
        $movementHistory = new MovementHistoryService();
        $movementHistory->makeOutboundOrder( $orderInfo->order);
    }
}
