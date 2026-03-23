<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */
namespace common\ecommerce\defacto\stock\service;


use common\ecommerce\constants\StockAPIStatus;

class Service
{
    private $repository;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \common\ecommerce\defacto\stock\repository\Repository();
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
    public function changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode)
    {
        $this->repository->changeBoxPlaceAddress($BoxBarcode,$PlaceBarcode);
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

    public function cleanOurBox($boxBarcode,$inboundOrderId) {
        return $this->repository->cleanOurBox($boxBarcode,$inboundOrderId);
    }

    public function getQtyByBoxBarcodeInOrder($boxBarcode,$inboundOrderId) {
        return $this->repository->getQtyByBoxBarcodeInOrder($boxBarcode,$inboundOrderId);
    }

    public function getItemsForDiffReportByOrderId($inboundOrderId,$productBarcode,$lotBarcode,$boxBarcode) {
        return $this->repository->getItemsForDiffReportByOrderId($inboundOrderId,$productBarcode,$lotBarcode,$boxBarcode);
    }

    public function getDataForSendByAPI($inboundOrderId)
    {
        return $this->repository->getDataForSendByAPI($inboundOrderId);
    }

    public function setStockApiStatusYes($inboundOrderId,$StockIds)
    {
       $this->repository->setStockApiStatus($inboundOrderId,$StockIds,StockAPIStatus::YES);
    }
    public function setStockApiStatusError($inboundOrderId,$StockIds)
    {
       $this->repository->setStockApiStatus($inboundOrderId,$StockIds,StockAPIStatus::ERROR);
    }

    public function usePackageBarcodeInOtherOrder($inboundId,$packageBarcode) {
        return $this->repository->usePackageBarcodeInOtherOrder($inboundId,$packageBarcode);
    }

    public function boxWithoutPlaceAddress($inboundId) {
        return $this->repository->boxWithoutPlaceAddress($inboundId);
    }


    public function getDataForSendByApiByBox($inboundOrderId,$clientBox) {
        return $this->repository->getDataForSendByApiByBox($inboundOrderId,$clientBox);
    }

    public function boxReadyToSendByInboundAPI($inboundOrderId,$clientBoxBarcode = '') {
        return $this->repository->boxReadyToSendByInboundAPI($inboundOrderId,$clientBoxBarcode);
    }

    public function getOrderIdByPackageBarcode($packageBarcode) {
        return $this->repository->getOrderIdByPackageBarcode($packageBarcode);
    }
}