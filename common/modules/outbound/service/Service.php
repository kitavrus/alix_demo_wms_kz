<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:18
 */

namespace common\modules\outbound\service;


class Service
{
    private $repository;
    private $stockService;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\modules\outbound\repository\Repository();
        $this->stockService = new \common\modules\stock\service\Service();
    }

    public function isExistEmptyM3($outboundOrderID) {
        return $this->stockService->isExistEmptyM3($outboundOrderID);
    }

    public function isExistEmptyKg($outboundOrderID) {
        return $this->stockService->isExistEmptyKg($outboundOrderID);
    }

    public function create($dto) {
        $this->repository->createOrder($dto);
        $this->repository->createOrderItems($dto, $this->repository->getId());
    }

    public function getOrderId()
    {
        return $this->repository->getId();
    }

    public function delete($outboundOrderID) {
        $this->repository->deleteOrder($outboundOrderID);
        $this->repository->deleteOrderItems($outboundOrderID);
    }
}