<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 15.09.2017
 * Time: 10:32
 */

namespace common\modules\city\RouteDirection\service;


use common\modules\city\RouteDirection\repository\Repository;
use common\modules\client\models\Client;

class Service
{
    private $repository;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new Repository();
    }
    // TODO add search by client id
    public function getDirectionStoresIDsGroupType() {
        return $this->repository->getDirectionStoresIDsGroupType();
    }

    // 8 Алматы
    public function isAlmatyStore($storeId,$clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->isAlmatyStore($storeId,$clientId);
    }
    // 4 ЮГ
    public function isSouthStore($storeId,$clientId = Client::CLIENT_DEFACTO){
        return $this->repository->isSouthStore($storeId,$clientId);
    }
    // 5 Север
    public function isNorthStore($storeId,$clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->isNorthStore($storeId,$clientId);
    }
    // 6 Запад
    public function isWestStore($storeId,$clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->isWestStore($storeId,$clientId);
    }
    //7	Восток
    public function isEasternStore($storeId,$clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->isEasternStore($storeId,$clientId);
    }

    public function getAlmatyStore($clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->getActiveStoreIDsByClient(8,$clientId);
    }

    public function getSouthStore($clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->getActiveStoreIDsByClient(4,$clientId);
    }

    public function getNorthStore($clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->getActiveStoreIDsByClient(5,$clientId);
    }

    public function getWestStore($clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->getActiveStoreIDsByClient(6,$clientId);
    }

    public function getEasternStore($clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->getActiveStoreIDsByClient(7,$clientId);
    }
	
	// Беларус
    public function getBelarusStore($clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->getActiveStoreIDsByClient(9,$clientId);
    }
	
	// Россия
    public function getRussiaStore($clientId = Client::CLIENT_DEFACTO) {
        return $this->repository->getActiveStoreIDsByClient(10,$clientId);
    }
}