<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 13:12
 */

namespace common\clientObject\hyundaiAuto\outbound\service;

use common\clientObject\hyundaiAuto\outbound\repository\OutboundRepository;
use common\clientObject\hyundaiAuto\outbound\service\OutboundService;
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
     */
    public function __construct()
    {
        $this->repository = new OutboundRepository();
        $this->productService = new ProductService();
        $this->outboundService = new OutboundService();
        $this->stockService = new \common\modules\stock\service\Service();
    }

    public function create($dto)
    {
        $this->outboundService->create($this->makeDTOForCreateOutboundOrder($dto));
    }


    private function makeDTOForCreateOutboundOrder($dto)
    {
        $dtoForCreateOutboundOrder = new \stdClass();
        $dtoForCreateOutboundOrder->orderNumber = $dto->orderNumber;
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


    private function makeDTOForCreateOutboundOrderCSV($dto)
    {
        $dtoForCreateOutboundOrder = new \stdClass();
        $dtoForCreateOutboundOrder->orderNumber = trim($dto->orderNumber);
        $dtoForCreateOutboundOrder->fromPointId = 4;
        $dtoForCreateOutboundOrder->toPointId = $dto->storeId;
        $dtoForCreateOutboundOrder->status = $this->stockService->getStatusOutboundNew();
        $dtoForCreateOutboundOrder->expectedQty = 0;
        $dtoForCreateOutboundOrder->expectedNumberPlacesQty = 0;
        $dtoForCreateOutboundOrder->items = [];

        if (file_exists($dto->pathToPreparedOrderFile)) {
            if (($handle = fopen($dto->pathToPreparedOrderFile, "r")) !== FALSE) {
                $row = 0;
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $row++;
                    $preparedDataFromRow = $this->preparedRowDataFromFile($data);
                    if ($row > 1 && !empty($preparedDataFromRow->productName) && !empty($preparedDataFromRow->productQty) && !empty($preparedDataFromRow->productModel)) {
                        $dtoForCreateOutboundOrder->items[] = $preparedDataFromRow;
                        $dtoForCreateOutboundOrder->expectedQty += $preparedDataFromRow->productQty;
                    }
                }
            }
        }

        return $dtoForCreateOutboundOrder;
    }

    private function preparedRowDataFromFile($rowFromFile)
    {
        $rowNumber = isset($rowFromFile[0]) ? trim($rowFromFile[0]) : '';
        $productModel = isset($rowFromFile[1]) ? trim($rowFromFile[1]) : '';
        $productName = isset($rowFromFile[2]) ? trim($rowFromFile[2]) : '';
        $productQty = isset($rowFromFile[3]) ? intval(trim($rowFromFile[3])) : 0;

        $row = new \stdClass();
        $row->productName = $productName;
        $row->productQty = $productQty;
        $row->productModel = $productModel;
        $row->productBarcode = $productModel;
//        $row->productBarcode = $this->productService->getProductBarcodeByModel($productModel,$this->outboundService->getClientID());
        $row->expectedNumberPlacesQty = 0;
        return $row;
    }

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