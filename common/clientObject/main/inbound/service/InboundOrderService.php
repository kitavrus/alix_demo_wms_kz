<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:02
 */

namespace common\clientObject\main\inbound\service;


use common\overloads\ArrayHelper;

class InboundOrderService
{
    private $inboundRepository;
    private $stockService;
    private $productService;
    private $dto;

    /**
     * ServiceInbound constructor.
     * @param $dto array | \stdClass
     */
    public function __construct($dto = []) {
        $this->inboundRepository = new \common\clientObject\main\inbound\repository\InboundRepository($dto);
        $this->stockService = new \common\modules\stock\service\Service();
        $this->productService = new \common\modules\product\service\ProductService();

        $this->dto = $dto;
    }
    //
    public function getNewAndInProcessOrder() {
        return ArrayHelper::map($this->inboundRepository->getNewAndInProcessOrder(),'id','order_number');
    }
    //
    public function getQtyInOrder() {
        return $this->inboundRepository->getQtyInOrder($this->dto->orderNumberId);
    }
    //
    public function getQtyModelsInOrder() {
        return $this->inboundRepository->getQtyModelsInOrder($this->dto->orderNumberId,$this->dto->productModel);
    }
    //
    public function cleanTransportedBox() {
        $placementUnitService = new \common\modules\placementUnit\service\Service();
        $placementUnitService->cleanTransportedBox($this->dto->transportedBoxBarcode,$this->dto->orderNumberId);

        $this->inboundRepository->updateAcceptedQtyItemByProductModelBarcode($this->dto->orderNumberId,$this->dto->productModel);
        $this->inboundRepository->updateQtyScannedInOrder($this->dto->orderNumberId,$this->stockService->getScannedQtyByOrderInStock($this->dto->orderNumberId));
    }
    //
    public function addScannedProductToStock($dto)
    {
        $this->productService->addBarcodeByModel($this->inboundRepository->getClientID(),$dto->productBarcode,$dto->productModel);

         // TODO For this create mapper
        $dtoForCreateStock = new \stdClass();
        $dtoForCreateStock->clientId = $this->inboundRepository->getClientID();
        $dtoForCreateStock->inboundOrderId  = $dto->orderNumberId;
        $dtoForCreateStock->productBarcode  = $dto->productBarcode;
        $dtoForCreateStock->productModel  = $dto->productModel;
        $dtoForCreateStock->primaryAddress  = $dto->transportedBoxBarcode;
        $dtoForCreateStock->status  = $this->stockService->getStatusInboundScanned();
        $dtoForCreateStock->statusAvailability  = $this->stockService->getStatusAvailabilityNO();
        $dtoForCreateStock->scanInDatetime  = $this->stockService->makeScanInboundDatetime();
        $dtoForCreateStock->inboundOrderItemId  = $this->inboundRepository->getItemByProductBarcode($dto->orderNumberId,$dto->productBarcode);

        $this->stockService->create($dtoForCreateStock);

        $this->inboundRepository->setProductBarcodeToItemByProductModel($dto->productBarcode,$dto->orderNumberId,$dto->productModel);
        $this->inboundRepository->updateAcceptedQtyItemByProductBarcode($dto->orderNumberId,$dto->productBarcode);
        $this->inboundRepository->updateQtyScannedInOrder($dto->orderNumberId,$this->stockService->getScannedQtyByOrderInStock($dto->orderNumberId));
        $this->inboundRepository->setOrderStatusInProcess($dto->orderNumberId);
        $this->inboundRepository->setOrderItemStatusInProcess($dto->orderNumberId,$dto->productBarcode);

        $dto->clientId = $this->inboundRepository->getClientID();

        $placementUnitService = new \common\modules\placementUnit\service\Service($dto);
        $placementUnitService->createFlow($dto,$this->stockService->getStockId());
    }
    //
    public function isEmptyProductBarcodeByModel()
    {
        $productService = new \common\modules\product\service\ProductService();
        return $productService->isEmptyProductBarcodeByModel($this->inboundRepository->getClientID(),$this->dto->productModel);
    }
    //
    public function getQtyInUnitByBarcodeInOrder() {
        $placementUnitService = new \common\modules\placementUnit\service\Service();
        return $placementUnitService->getQtyInUnitByBarcodeInOrder($this->dto->transportedBoxBarcode,$this->dto->orderNumberId);
    }
    //
    public function getOrderItems() {
        return $this->inboundRepository->getItemsByOrderId($this->dto->orderNumberId);
    }
    //
    public function getItemsForDiffReportByOrderId($orderNumberId) {
        return $this->inboundRepository->getItemsForDiffReportByOrderId($orderNumberId);
    }
    //
    public function closeOrder()
    {
        $this->inboundRepository->setOrderStatusClose($this->dto->orderNumberId);
        $this->inboundRepository->setDateConfirm($this->dto->orderNumberId);
        $this->inboundRepository->setOrderItemStatusClose($this->dto->orderNumberId);

        $this->stockService->setStatusNewAndAvailableYes($this->dto->orderNumberId);
    }

    public function getOrderInfo($id)
    {
        return $this->inboundRepository->getOrderInfo($id);
    }

    public function delete($id)
    {
        $this->inboundRepository->delete($id);
    }
}