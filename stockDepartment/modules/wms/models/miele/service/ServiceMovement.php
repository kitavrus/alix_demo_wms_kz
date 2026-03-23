<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */

namespace stockDepartment\modules\wms\models\miele\service;


use stockDepartment\modules\wms\managers\miele\MovementHistoryService;
use stockDepartment\modules\wms\managers\miele\MovementSyncService;
use stockDepartment\modules\wms\models\miele\repository\MovementRepository;

class ServiceMovement
{
    private $repository;

    public function __construct(){
        $this->repository = new MovementRepository();
    }

    public function getOrdersForPrintPickList() {
        return $this->repository->getOrdersForPrintPickList();
    }

    public function getOrderInfo($id) {
        return $this->repository->getOrderInfo($id);
    }

    public function moveToAddress($dto) {
        $this->repository->moveToAddress($dto);
    }

    public function getStockIDsByMovementID($orderId) {
        return $this->repository->getStockIDsByMovementID($orderId);
    }

    public function acceptedOrder($orderId) {
        $this->repository->acceptedOrder($orderId);
        $orderInfo = $this->repository->getOrderInfo($orderId);
        // Change status on sync
        $sync = new MovementSyncService();
        $sync->setOurStatusComplete( $orderInfo->order->client_order_id);

        // Add data to movement history
        $movementHistory = new MovementHistoryService();
        $movementHistory->makeMovementOrder( $orderInfo->order);
    }

    public function getOrderItemsForDiffReport($dto) {
        return $this->repository->getOrderItemsForDiffReport($dto->order->id);
    }
}