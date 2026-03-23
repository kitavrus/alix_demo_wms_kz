<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace common\ecommerce\main\outbound\service;


use common\ecommerce\main\outbound\repository\OutboundRepository;
use common\modules\stock\models\Stock;

class OutboundService
{
    private $repository;
    private $outboundService;
    private $dto;

    public function __construct($dto = [],$params = [])
    {
        $this->repository = new OutboundRepository($params);
        $this->outboundService = new \common\modules\outbound\service\Service();
        $this->dto = $dto;
    }

    public function create($dto) {
        $dto->clientId = $this->repository->getClientID();
        $this->outboundService->create($dto);
    }
    public function getOrderId() {
        return  $this->outboundService->getOrderId();
    }

    public function getOrdersForPrintPickingList()
    {
        return $this->repository->getOrdersForPrintPickList();
    }

    public function getOrderInfo($id = null)
    {
        $id = is_null($id) ? isset($this->dto->order) ? $this->dto->order->id : 0 : $id;
        return $this->repository->getOrderInfo($id);
    }

    public function runReservation($outboundInfo) {
        if(isset($outboundInfo->order) && $this->canReallocateOrder($outboundInfo->order)) {
            $this->reallocateOrder($outboundInfo->order->id);
            $outboundReservation = new \common\ecommerce\main\outbound\service\OutboundReservationService();
            $outboundReservation->run($this->getOrderInfo($outboundInfo->order->id));
        }
    }

    private function canReallocateOrder($outboundModel) {
        if(empty($outboundModel) ) { return false; }
        return $outboundModel->allocated_qty != $outboundModel->expected_qty;
    }

    public function reallocateOrder($outboundOrderID) {
        Stock::resetByOutboundOrderId($outboundOrderID);
    }

    public function delete($outboundOrderID)
    {
        Stock::resetByOutboundOrderId($outboundOrderID);
        $this->outboundService->delete($outboundOrderID);
    }
}
