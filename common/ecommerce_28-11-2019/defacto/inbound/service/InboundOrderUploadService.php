<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 13:12
 */

namespace common\ecommerce\defacto\inbound\service;


use common\ecommerce\defacto\inbound\repository\InboundRepository;
use common\ecommerce\main\service\SpreadsheetService;
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
        $dtoForCreateInboundOrder->expectedProductQty = 0;
        $dtoForCreateInboundOrder->expectedPlaceQty = 0;
        $dtoForCreateInboundOrder->items = [];

        if (file_exists($dto->pathToPreparedOrderFile)) {
            if (($handle = fopen($dto->pathToPreparedOrderFile, "r")) !== FALSE) {
                $row = 0;
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $row++;
                    $preparedDataFromRow = $this->preparedRowDataFromFile($data);
                    if ($row > 1 && !empty($preparedDataFromRow->productName) && !empty($preparedDataFromRow->productQty) && !empty($preparedDataFromRow->productModel)) {
                        if(isset( $dtoForCreateInboundOrder->items[$preparedDataFromRow->productModel])) {
                            $dtoForCreateInboundOrder->items[$preparedDataFromRow->productModel]->productQty += $preparedDataFromRow->productQty;
                        } else {
                            $dtoForCreateInboundOrder->items[$preparedDataFromRow->productModel] = $preparedDataFromRow;
                        }
                        $dtoForCreateInboundOrder->expectedProductQty += $preparedDataFromRow->productQty;
                    }
                }
            }
        }
        return $dtoForCreateInboundOrder;
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
//        $row->productBarcode = $this->productService->getProductBarcodeByModel($productModel,$this->repository->getClientID());
        $row->expectedPlaceQty = 0;
        return $row;
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