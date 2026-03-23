<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:18
 */

namespace common\modules\inbound\service;


class Service
{
    private $repository;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\modules\inbound\repository\Repository();
    }

    public function delete($orderID) {
        $this->repository->deleteOrder($orderID);
        $this->repository->deleteOrderItems($orderID);
    }
}