<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 13:12
 */

namespace common\clientObject\main\outbound\service;

use common\clientObject\main\outbound\repository\OutboundRepository;
use common\clientObject\main\outbound\service\OutboundService;
use common\clientObject\main\service\SpreadsheetService;
use common\modules\product\service\ProductService;
use yii\helpers\VarDumper;

class OutboundOrderUploadService
{
    private $repository;
    private $productService;
    private $outboundService;

    /**
     * InboundOrderUploadService constructor.
     * @param $params
     */
    public function __construct($params = [])
    {
        $this->repository = new OutboundRepository($params);
        $this->productService = new ProductService();
        $this->outboundService = new \common\clientObject\main\outbound\service\OutboundService([],$params);
        $this->stockService = new \common\modules\stock\service\Service();
    }

    public function create($dto)
    {
        $this->outboundService->create($this->makeDTOForCreateOutboundOrder($dto));
    }

    public function getOrderId() {
        return  $this->outboundService->getOrderId();
    }

    private function makeDTOForCreateOutboundOrder($dto)
    {
        $dtoForCreateOutboundOrder = new \stdClass();
        $dtoForCreateOutboundOrder->orderNumber = $dto->orderNumber;
        $dtoForCreateOutboundOrder->description = $dto->comment;
        $dtoForCreateOutboundOrder->fromPointId = 4;
        $dtoForCreateOutboundOrder->toPointId = $dto->storeId;
        $dtoForCreateOutboundOrder->status = $this->stockService->getStatusOutboundNew();
        $dtoForCreateOutboundOrder->expectedQty = 0;
        $dtoForCreateOutboundOrder->expectedNumberPlacesQty = 0;
        $dtoForCreateOutboundOrder->items = [];


        if (is_file($dto->pathToPreparedOrderFile) && file_exists($dto->pathToPreparedOrderFile)) {
            $preparedDataFromFile = SpreadsheetService::parseFile($dto->pathToPreparedOrderFile);
            $dtoForCreateOutboundOrder->expectedQty = $preparedDataFromFile->expectedTotalProductQty;
            $dtoForCreateOutboundOrder->expectedTotalPlaceQty = $preparedDataFromFile->expectedTotalPlaceQty;
            $dtoForCreateOutboundOrder->totalQtyRows = $preparedDataFromFile->totalQtyRows;
            $dtoForCreateOutboundOrder->items = $preparedDataFromFile->items;
        }

        return $dtoForCreateOutboundOrder;
    }

//    private function preparedRowDataFromFile($rowFromFile)
//    {
//        $rowNumber = isset($rowFromFile[0]) ? trim($rowFromFile[0]) : '';
//        $productModel = isset($rowFromFile[1]) ? trim($rowFromFile[1]) : '';
//        $productName = isset($rowFromFile[2]) ? trim($rowFromFile[2]) : '';
//        $productQty = isset($rowFromFile[3]) ? intval(trim($rowFromFile[3])) : 0;
//
//        $row = new \stdClass();
//        $row->productName = $productName;
//        $row->productQty = $productQty;
//        $row->productModel = $productModel;
//        $row->productBarcode = $this->productService->getProductBarcodeByModel($productModel,$this->outboundService->getClientID());
//        $row->expectedNumberPlacesQty = 0;
//        return $row;
//    }

    public function getClientID() {
        return $this->repository->getClientID();
    }

    /**
     * @return mixed
     */
//    public function getInboundOrderID()
//    {
//        return $this->repository->getInboundOrderID();
//    }

//    public function addNewProduct($inboundOrderID)
//    {
//        $inboundOrderItems = $this->repository->getItemsByOrderId($inboundOrderID);
//        foreach ($inboundOrderItems as $item) {
//
//            $dtoForCreateProduct = new \stdClass();
//            $dtoForCreateProduct->client_id = $this->repository->getClientID();
//            $dtoForCreateProduct->model = $item->product_model;
//
//            $this->productService->createIfProductModelNoExist($this->repository->getClientID(),$item->product_model,$dtoForCreateProduct);
//        }
//
//    }
}