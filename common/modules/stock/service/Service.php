<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\modules\stock\service;


class Service
{
    private $repository;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\modules\stock\repository\Repository();
    }

    public function isExistEmptyM3($outboundOrderID) {
        return $this->repository->isExistEmptyM3($outboundOrderID);
    }
    public function isExistEmptyKg($outboundOrderID) {
        return $this->repository->isExistEmptyKg($outboundOrderID);
    }
    //
    public function create($dto)
    {
       return $this->repository->create($dto);
    }
    //
    public function getStockId()
    {
       return $this->repository->getId();
    }
    public function getScannedQtyByOrderInStock($inboundOrderId)
    {
        return $this->repository->getScannedQtyByOrderInStock($inboundOrderId);
    }

    public function getStatusInboundScanned() {
        return$this->repository->getStatusInboundScanned();
    }

    public function getStatusOutboundNew() {
        return$this->repository->getStatusOutboundNew();
    }

    public function getStatusAvailabilityNO() {
        return$this->repository->getStatusAvailabilityNO();
    }

    public function makeScanInboundDatetime()
    {
        return time();
    }
    //
    public function removeByIDs($stockIds) {
        $this->repository->removeByIDs($stockIds);
    }
    //
    public function setStatusNewAndAvailableYes($inboundOrderId)
    {
        $this->repository->setStatusNewAndAvailableYes($inboundOrderId);
    }
    //
    public function setPrimaryAddressForIds($stockIds,$primaryAddress)
    {
        $this->repository->setPrimaryAddressForIds($stockIds,$primaryAddress);
    }
    //
    public function setSecondaryAddressForIds($stockIds,$secondaryAddress)
    {
        $this->repository->setSecondaryAddressForIds($stockIds,$secondaryAddress);
    }
    //
    public function getIdsByByPrimaryAddress($primaryAddress)
    {
        return $this->repository->getIdsByPrimaryAddress($primaryAddress);
    }

    public function IsNotEmptyPrimaryAddress($primaryAddress) {
        return $this->repository->IsNotEmptyPrimaryAddress($primaryAddress);
    }

    public function deleteByInboundId($inboundOrderId) {
        $this->repository->deleteByInboundId($inboundOrderId);
    }

    public function changeProductCondition($stockId,$conditionType) {
        $this->repository->changeConditionType($stockId,$conditionType);
    }
	
	 
    public function inboundPutAway($aInboundId) {
       return  $this->repository->inboundPutAway($aInboundId);
    }
	
}