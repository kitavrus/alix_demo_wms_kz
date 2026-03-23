<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:03
 */

namespace common\modules\store\service;


class Service
{
    private $repository;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\modules\store\repository\Repository();
    }
    public function isRusStore($storeId) {
        return $this->repository->isRusStore($storeId);
    }
    public function isBelStore($storeId) {
        return $this->repository->isBelStore($storeId);
    }
    public function isKzStore($storeId) {
        return $this->repository->isKzStore($storeId);
    }
    public function getStoreByClient($clientID) {
//        return $this->repository->getStockPointArray($clientID);
        return $this->repository->getStoreByClientID($clientID);
    }

    public function getStoreCityNameByClientWithPattern($clientID,$pattern = 'stock') {
        return $this->repository->getStoreCityNameByClientWithPattern($clientID,$pattern);
    }

    public function getPointNameById($pointId,$pattern = 'stock') {
        return $this->repository->getPointNameById($pointId,$pattern);
    }
}