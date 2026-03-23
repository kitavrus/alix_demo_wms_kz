<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 01.10.2017
 * Time: 17:19
 */

namespace common\modules\placementUnit\service;


use common\modules\placementUnit\models\PlacementUnitFlow;
use common\modules\placementUnit\repository\Constant;

class Service
{
    private $repository;
    private $dto;

    /**
     * Service constructor.
     * @param $dto array | \stdClass
     */
    public function __construct($dto = []) {
        $this->repository = new \common\modules\placementUnit\repository\Repository();
        $this->dto = $dto;
    }

    public function create($dto){}
    public function createFlow($dto,$stockId)
    {
        $this->repository->setStatusWork($dto->transportedBoxBarcode);
        $this->repository->createFlow($dto,$stockId);
    }

    public function isFree($barcode) {
        return $this->repository->isFree($barcode);
    }
    //
    public function isWork($barcode) {
        return $this->repository->isWork($barcode) ;
    }
    //
    public function isNotEmptyUnitFlow($barcode) {
        return $this->repository->isNotEmptyUnitFlow($barcode);
    }
    //
    public function isClose($barcode) {
        return $this->repository->isClose($barcode);
    }

    public function isExist($barcode) {
        return $this->repository->isExist($barcode);
    }

    public function isWorkWithOrder($barcode,$inboundOrderId) {
        return $this->repository->isWorkWithOrder($barcode,$inboundOrderId);
    }

    public function getQtyInUnitByBarcodeInOrder($barcode,$inboundOrderId) {
        return $this->repository->getQtyInUnitByBarcode($barcode,$inboundOrderId);
    }

    public function cleanTransportedBox($boxBarcode,$inboundOrderId) {

        $stocksIds = $this->repository->getStocksIds($boxBarcode,$inboundOrderId);
        $stockService = new \common\modules\stock\service\Service();
        $stockService->removeByIDs($stocksIds);
        $this->repository->removeWorkUnitFlowItemsByBarcode($boxBarcode,$inboundOrderId);
    }

    public function setPrimaryAddress($fromAddressBarcode,$toPlaceAddress)
    {
        $stocksIds = $this->repository->getStocksIdsByBarcode($fromAddressBarcode);

        $stockService = new \common\modules\stock\service\Service();
        $stockService->setPrimaryAddressForIds($stocksIds,$toPlaceAddress);
    }

    public function setSecondaryAddress($fromAddressBarcode,$toPlaceAddress)
    {
        $stockService = new \common\modules\stock\service\Service();
        $stocksIds = $stockService->getIdsByByPrimaryAddress($fromAddressBarcode);
        $stockService->setSecondaryAddressForIds($stocksIds,$toPlaceAddress);
    }

    public function setStatusFree($barcode) {
        return $this->repository->setStatusFree($barcode);
    }
}