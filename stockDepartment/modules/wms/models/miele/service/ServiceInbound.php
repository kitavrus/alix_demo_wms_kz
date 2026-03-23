<?php
namespace stockDepartment\modules\wms\models\miele\service;


use common\overloads\ArrayHelper;
use stockDepartment\modules\wms\managers\miele\Constants;
use stockDepartment\modules\wms\managers\miele\InboundSyncService;
use stockDepartment\modules\wms\managers\miele\MovementHistoryService;
use stockDepartment\modules\wms\models\miele\repository\InboundRepository;

class ServiceInbound
{
    private $repository;
    private $dto;

    /**
     * ServiceInbound constructor.
     */
    public function __construct($dto = []) {
        $this->repository = new InboundRepository();
        $this->dto = $dto;
    }

    public function addScannedToStock($addNoExist = false) {
        $this->repository->addScannedProductToStock($this->dto,$addNoExist);
        $this->repository->updateQtyScannedInOrder($this->dto->inbound->order->id);
        $this->repository->setOrderStatusInProcess($this->dto->inbound->order->id);
        $this->repository->setOrderItemStatusInProcess($this->dto->inbound->order->id,$this->dto->product_barcode);
        $syncDto = $this->repository->makeDtoForSync($this->dto->inbound->order->id);
        $sync = new InboundSyncService();
        $sync->setOurStatusInWorking($syncDto->client_order_id);
    }

    public function addFabBarcodeToProduct() {
        $this->repository->addFabBarcodeToProduct($this->dto);
    }

    public function isWaitFabBarcode() {
        return $this->repository->isScanByFabBarcode($this->dto->product_barcode);
    }

    public function getNewAndInProcessOrder() {
        return ArrayHelper::map($this->repository->getNewAndInProcessOrder(),
            function ($m) {
                return $m->id;
            },
            function ($m) {
                $const =  new Constants();
                return $m->order_number.' / Зона: '.$const->getClientZone($m->zone);
            }
        );
    }

    public function getQtyInOrder() {
        return $this->repository->getQtyInOrder($this->dto->inbound->order->id);
    }

    public function getQtyScannedInBox() {
        return $this->repository->getQtyScannedInBox($this->dto->inbound->order->id,$this->dto->primary_address);
    }

    public function cleanBox() {
        $this->repository->cleanOurBox($this->dto->inbound->order->id,$this->dto->primary_address);
    }

    public function getOrderItemsForDiffReport() {
        return $this->repository->getOrderItemsForDiffReport($this->dto->inbound->order->id);
    }

    public function getOrderForComplete() {
        return $this->repository->getOrderForComplete();
    }

    public function acceptedOrder($orderId) {
        $this->repository->acceptedOrder($orderId);
        $orderInfo = $this->repository->getOrderInfo($orderId);

        // Change status on sync
        $sync = new InboundSyncService();
        $sync->setOurStatusAccepted( $orderInfo->order->client_order_id);

        // Add data to movement history
        $movementHistory = new MovementHistoryService();
        $movementHistory->makeInboundOrder( $orderInfo->order);

    }
}