<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.11.2019
 * Time: 11:15
 */

namespace common\ecommerce\defacto\checkBox\service;


use common\ecommerce\constants\CheckBoxStatus;
use common\ecommerce\defacto\employee\repository\EmployeeRepository;
use yii\helpers\ArrayHelper;

class CheckBoxService
{
    private $repository;

    public function __construct($dto = [])
    {
        $this->repository = new \common\ecommerce\defacto\checkBox\repository\Repository();
    }

    private function getInventoryKey() {
        return '';
    }

    public function inventoryKey($dto){ return $dto;}
    public function title($dto) {  return $dto; }
    public function employeeBarcode($dto) {  return $dto; }
    public function placeBarcode($dto) {  return $dto; }

    public function boxBarcode($dto) {

        if(!$this->repository->isExistBox($dto->title,$dto->boxBarcode)) {
            $checkBox = $this->repository->createBox($this->dtoForCreateBox($dto));

            $stockList = $this->repository->getStockByBoxBarcode($dto->boxBarcode);
            foreach($stockList as $stock) {
                $this->repository->createStock($this->dtoForCreateStock($dto,$checkBox,$stock));
            }
        }

        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $title = $dto->title;
        $inventoryKey = $dto->inventoryKey;

        $result = new \stdClass();
        $result->qtyProductInBox = $this->repository->qtyProductInBox($boxBarcode,$placeAddress,$title,$inventoryKey);

        return $result;

    }

    private function dtoForCreateBox($dto) {

        $createBox = new \stdClass();
        $createBox->clientId = $this->repository->getClientID();
        $createBox->warehouseId = $this->repository->getWarehouseID();
        $createBox->employeeId = ArrayHelper::getValue(EmployeeRepository::getEmployeeByBarcode($dto->employeeBarcode),'id',0);
        $createBox->inventoryKey = $dto->inventoryKey;
        $createBox->title = $dto->title;
        $createBox->boxBarcode = $dto->boxBarcode;
        $createBox->placeAddress = $dto->placeAddress;
        $createBox->expectedQty = 0;
        $createBox->scannedQty = 0;
        return $createBox;
    }

    private function dtoForCreateStock($dto,$checkBox,$stock) {

        $createStock = new \stdClass();
        $createStock->checkBoxId = $checkBox->id;
        $createStock->stockId = $stock['id'];
        $createStock->clientId = $this->repository->getClientID();
        $createStock->warehouseId = $this->repository->getWarehouseID();

        $createStock->inventoryKey = $dto->inventoryKey;
        $createStock->title = $dto->title;
        $createStock->boxBarcode = $dto->boxBarcode;
        $createStock->placeAddress = $dto->placeAddress;

        $createStock->stockInboundId = $stock['inbound_id'];
        $createStock->stockInboundItemId = $stock['inbound_item_id'];
        $createStock->stockOutboundId = $stock['outbound_id'];
        $createStock->stockOutboundItemId = $stock['outbound_item_id'];
        $createStock->stockStatusAvailability = $stock['status_availability'];
        $createStock->stockClientProductSku = $stock['client_product_sku'];

        $createStock->stockInboundStatus = $stock['status_inbound'];
        $createStock->stockOutboundStatus = $stock['status_outbound'];
        $createStock->stockConditionType = $stock['condition_type'];

        $createStock->productBarcode = $stock['product_barcode'];
        $createStock->serializedDataStock = serialize($stock);
        $createStock->status = CheckBoxStatus::NO;

        return $createStock;
    }

    public function productBarcode($dto)
    {
        $productBarcode = $dto->productBarcode;
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $title = $dto->title;
        $inventoryKey = $dto->inventoryKey;
        $this->repository->productScan($productBarcode,$boxBarcode,$placeAddress,$title,$inventoryKey);

        $result = new \stdClass();
        $result->qtyScannedProductInBox = $this->repository->qtyScannedProductInBox($boxBarcode,$placeAddress,$title,$inventoryKey);

        return $result;
    }


    public function isExistProductInBoxForScanning($productBarcode,$boxBarcode,$placeAddress,$title,$inventoryKey = null)
    {
        return $this->repository->isExistProductInBoxForScanning($productBarcode,$boxBarcode,$placeAddress,$title,$inventoryKey);
    }

    public function showProductsInBox($dto)
    {
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $title = $dto->title;
        $inventoryKey = $dto->inventoryKey;
        return $this->repository->getProductListInBox($boxBarcode,$placeAddress,$title,$inventoryKey);
    }

    public function ShowPackedButNotScannedToList($dto)
    {
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $title = $dto->title;
        $inventoryKey = $dto->inventoryKey;
        return $this->repository->getProductListInBox($boxBarcode,$placeAddress,$title,$inventoryKey);
    }

    public function emptyBox($dto)
    {
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $title = $dto->title;
        $inventoryKey = $dto->inventoryKey;
        return $this->repository->emptyBox($boxBarcode,$placeAddress,$title,$inventoryKey);
    }
}