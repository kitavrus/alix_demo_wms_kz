<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 13:12
 */

namespace common\clientObject\subaruAuto\inbound\service;


use common\clientObject\main\service\SpreadsheetService;
use common\clientObject\subaruAuto\inbound\repository\InboundRepository;
use common\modules\product\service\ProductService;
use yii\helpers\VarDumper;

class InboundOrderUploadService
{
    private $repository;
    private $productService;

    /**
     * InboundOrderUploadService constructor.
     */
    public function __construct($dto = [])
    {
        $this->repository = new InboundRepository($dto);
        $this->productService = new ProductService();
    }

    public function create($dto)
    {
        $this->repository->create($this->makeDTOForCreateInboundOrder($dto));
        $this->addNewProduct($this->repository->getInboundOrderID());
    }


    private function makeDTOForCreateInboundOrder($dto)
    {
        $dtoForCreateInboundOrder = new \stdClass();
        $dtoForCreateInboundOrder->orderNumber = trim($dto->orderNumber);
        $dtoForCreateInboundOrder->supplierId = $dto->supplierId;
        $dtoForCreateInboundOrder->expectedTotalProductQty = 0;
        $dtoForCreateInboundOrder->expectedTotalPlaceQty = 0;
        $dtoForCreateInboundOrder->totalQtyRows = 0;
        $dtoForCreateInboundOrder->items = [];

        if (is_file($dto->pathToPreparedOrderFile) && file_exists($dto->pathToPreparedOrderFile)) {
            $preparedDataFromFile = SpreadsheetService::parseFile($dto->pathToPreparedOrderFile);
            $dtoForCreateInboundOrder->expectedTotalProductQty = $preparedDataFromFile->expectedTotalProductQty;
            $dtoForCreateInboundOrder->expectedTotalPlaceQty = $preparedDataFromFile->expectedTotalPlaceQty;
            $dtoForCreateInboundOrder->totalQtyRows = $preparedDataFromFile->totalQtyRows;
            $dtoForCreateInboundOrder->items = $preparedDataFromFile->items;
        }
        return $dtoForCreateInboundOrder;
    }

    private function makeDTOForCreateInboundOrderCSV($dto)
    {
        $dtoForCreateInboundOrder = new \stdClass();
        $dtoForCreateInboundOrder->orderNumber = trim($dto->orderNumber);
        $dtoForCreateInboundOrder->supplierId = $dto->supplierId;
        $dtoForCreateInboundOrder->expectedTotalProductQty = 0;
        $dtoForCreateInboundOrder->expectedTotalPlaceQty = 0;
        $dtoForCreateInboundOrder->totalQtyRows = 0;
        $dtoForCreateInboundOrder->items = [];

        if (is_file($dto->pathToPreparedOrderFile) && file_exists($dto->pathToPreparedOrderFile)) {

            $preparedDataFromFile = SpreadsheetService::parseFile($dto->pathToPreparedOrderFile);
            $dtoForCreateInboundOrder->expectedTotalProductQty = $preparedDataFromFile->expectedTotalProductQty;
            $dtoForCreateInboundOrder->expectedTotalPlaceQty = $preparedDataFromFile->expectedTotalPlaceQty;
            $dtoForCreateInboundOrder->totalQtyRows = $preparedDataFromFile->totalQtyRows;
            $dtoForCreateInboundOrder->items = $preparedDataFromFile->items;
        }
        return $dtoForCreateInboundOrder;
    }

    /**
     * @return mixed
     */
    public function getInboundOrderID()
    {
        return $this->repository->getInboundOrderID();
    }

    public function addNewProduct($inboundOrderID)
    {
        $inboundOrderItems = $this->repository->getItemsByOrderId($inboundOrderID);
        foreach ($inboundOrderItems as $item) {

            $dtoForCreateProduct = new \stdClass();
            $dtoForCreateProduct->client_id = $this->repository->getClientID();
            $dtoForCreateProduct->model = $item->product_model;

            $this->productService->createIfProductModelNoExist($this->repository->getClientID(),$item->product_model,$dtoForCreateProduct);
        }

    }
}