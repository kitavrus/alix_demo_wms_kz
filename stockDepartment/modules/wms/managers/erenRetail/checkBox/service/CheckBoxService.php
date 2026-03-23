<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 08.11.2019
 * Time: 11:15
 */

namespace stockDepartment\modules\wms\managers\erenRetail\checkBox\service;


use stockDepartment\modules\wms\managers\erenRetail\checkBox\constants\CheckBoxStatus;
use stockDepartment\modules\wms\managers\erenRetail\checkBox\constants\CheckBoxType;
use common\b2b\domains\employee\repository\EmployeeRepository;
use common\components\BarcodeManager;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\Stock;
use yii\helpers\ArrayHelper;

class CheckBoxService
{
    private $repository;

    public function __construct($dto = [])
    {
        $this->repository = new \stockDepartment\modules\wms\managers\erenRetail\checkBox\repository\CheckBoxRepository();
    }

    public function getInventoryKeyList() {
        return ArrayHelper::map($this->repository->getInventoryKeyList(),'id','inventory_key');
    }

    public function getAllInventoryKeyList() {
        return ArrayHelper::map($this->repository->getAllInventoryKeyList(),'id','inventory_key');
    }

    public function getInventoryInfo($id) {
        return $this->repository->getInventoryInfo($id);
    }

    public function employeeBarcode($dto) {  return $dto; }
    public function placeBarcode($dto) {  return $dto; }

    public function inventoryId($dto)
    {
        $inventoryId = $dto->inventoryId;

        $result = new \stdClass();
        $result->qtyExpectedProductInBox = 0;
        $result->qtyScannedProductInBox = 0;
        $result->qtyExpectedProductInInventory = $this->repository->qtyProductInInventory($inventoryId);
        $result->qtyScannedProductInInventory = $this->repository->qtyScannedProductInInventory($inventoryId);

        return $result;
    }

    public function boxBarcode($dto) {

//        if(!$this->repository->isExistBox($dto->inventoryId,$dto->boxBarcode)) {
//            $checkBox = $this->repository->createBox($this->dtoForCreateBox($dto));
//
//            $stockList = $this->repository->getStockByBoxBarcode($dto->boxBarcode);
//            foreach($stockList as $stock) {
//                $this->repository->createStock($this->dtoForCreateStock($dto,$checkBox,$stock));
//            }
//            $checkBox->expected_qty = $this->repository->qtyProductInBox($dto->inventoryId,$dto->boxBarcode,$dto->placeAddress);
//            $checkBox->save(false);
//        }

//        $this->addBoxBarcode($dto);
        $this->addBoxBarcodeBatchInsert($dto);

        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $inventoryId = $dto->inventoryId;

        $this->calculateScannedInBox($inventoryId,$boxBarcode,$placeAddress);
        $this->calculateScannedInInventory($inventoryId);

        $result = new \stdClass();
        $result->qtyExpectedProductInBox = $this->repository->qtyProductInBox($inventoryId,$boxBarcode,$placeAddress);
        $result->qtyScannedProductInBox = $this->repository->qtyScannedProductInBox($inventoryId,$boxBarcode,$placeAddress);
        $result->qtyExpectedProductInInventory = $this->repository->qtyProductInInventory($inventoryId);
        $result->qtyScannedProductInInventory = $this->repository->qtyScannedProductInInventory($inventoryId);
        $result->showProductsInBoxHTML = '';

        return $result;

    }

    public function addBoxBarcode($dto) {

        $this->addBoxBarcodeBatchInsert($dto);

//        if(!$this->repository->isExistBox($dto->inventoryId,$dto->boxBarcode)) {
//            $checkBox = $this->repository->createBox($this->dtoForCreateBox($dto));
//
//            $stockList = $this->repository->getStockByBoxBarcode($dto->boxBarcode);
//            foreach($stockList as $stock) {
//                $this->repository->createStock($this->dtoForCreateStock($dto,$checkBox,$stock));
//            }
//            $checkBox->expected_qty = $this->repository->qtyProductInBox($dto->inventoryId,$dto->boxBarcode,$dto->placeAddress);
//            $checkBox->save(false);
//        }

        return null;
    }

    public function addBoxBarcodeBatchInsert($dto,$inventoryType = CheckBoxType::PART_BY_BOX) {

        if(!$this->repository->isExistBox($dto->inventoryId,$dto->boxBarcode)) {
            $checkBox = $this->repository->createBox($this->dtoForCreateBox($dto));

            $stockList = $this->repository->getStockByBoxBarcode($dto->boxBarcode,$inventoryType);
            $rows = [];
            foreach($stockList as $stock) {
               $rows[] = $this->repository->makeStockByDto($this->dtoForCreateStock($dto,$checkBox,$stock));
            }

            $this->repository->createStockBatchInsert($rows);

            $checkBox->expected_qty = $this->repository->qtyProductInBox($dto->inventoryId,$dto->boxBarcode,$dto->placeAddress);
            $checkBox->save(false);
        }

        return null;
    }


    public function productBarcode($dto)
    {
        $productBarcode = $dto->productBarcode;
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $inventoryId = $dto->inventoryId;
		
		if(BarcodeManager::isBoxLotOrReturnBox($productBarcode,[$placeAddress],$boxBarcode)) {
//			$productBarcode = BarcodeManager::findProductInStockByReturnBarcodeBoxInventory($productBarcode);

			if(BarcodeManager::isBoxOnlyOur($productBarcode)) {
				$productBarcode = Stock::find()->select('product_barcode')->andWhere(['primary_address'=>$productBarcode])->scalar();
			}

			if(BarcodeManager::isDefactoBox($productBarcode)) {
				$productBarcode = Stock::find()->select('product_barcode')->andWhere(['inbound_client_box'=>$productBarcode])->scalar();
			}
		}
		

        $checkBoxStockOne = $this->repository->getProductReadyScan($inventoryId,$productBarcode,$boxBarcode,$placeAddress);
        if($checkBoxStockOne) {
            $checkBoxStockOne->status = CheckBoxStatus::END_SCANNED;
            $checkBoxStockOne->scanned_datetime = time();
            $checkBoxStockOne->save(false);
        }

        $this->calculateScannedInBox($inventoryId,$boxBarcode,$placeAddress);
        $this->calculateScannedInInventory($inventoryId);

        $result = new \stdClass();
        $result->qtyExpectedProductInBox = $this->repository->qtyProductInBox($inventoryId,$boxBarcode,$placeAddress);
        $result->qtyScannedProductInBox = $this->repository->qtyScannedProductInBox($inventoryId,$boxBarcode,$placeAddress);
        $result->qtyExpectedProductInInventory = 0; // $this->repository->qtyProductInInventory($inventoryId);
        $result->qtyScannedProductInInventory = 0; // $this->repository->qtyScannedProductInInventory($inventoryId);
        $result->showProductsInBoxHTML = '';

        return $result;
    }

    public function isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress)
    {
        return $this->repository->isExistProductInBoxForScanning($inventoryId,$productBarcode,$boxBarcode,$placeAddress);
    }

    public function showProductsInBox($dto)
    {
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $inventoryId = $dto->inventoryId;
        return $this->repository->getProductListInBox($inventoryId,$boxBarcode,$placeAddress);
    }

    public function ShowPackedButNotScannedToList($dto)
    {
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $inventoryId = $dto->inventoryId;
        return $this->repository->getProductListInBox($inventoryId,$boxBarcode,$placeAddress);
    }

    public function emptyBox($dto)
    {
        $boxBarcode = $dto->boxBarcode;
        $placeAddress = $dto->placeAddress;
        $inventoryId = $dto->inventoryId;

        $this->repository->emptyBox($inventoryId,$boxBarcode,$placeAddress);

        $this->calculateScannedInBox($inventoryId,$boxBarcode,$placeAddress);
        $this->calculateScannedInInventory($inventoryId);

        $result = new \stdClass();
        $result->qtyExpectedProductInBox = 0;
        $result->qtyScannedProductInBox = 0;
        $result->qtyExpectedProductInInventory = $this->repository->qtyProductInInventory($inventoryId);
        $result->qtyScannedProductInInventory = $this->repository->qtyScannedProductInInventory($inventoryId);
        $result->showProductsInBoxHTML = '';

        return $result;
    }

    public function calculateScannedInBox($inventoryId,$boxBarcode,$placeAddress) {

        $boxInfo = $this->repository->getBoxInfo($inventoryId,$boxBarcode);
        $boxInfo->scanned_qty = $this->repository->qtyScannedProductInBox($inventoryId,$boxBarcode,$placeAddress);
        $boxInfo->save(false);
    }

    public function calculateScannedInInventory($inventoryId) {

        $inventoryInfo = $this->repository->getInventoryInfo($inventoryId);
        $inventoryInfo->expected_product_qty = $this->repository->qtyProductInInventory($inventoryId);
        $inventoryInfo->scanned_product_qty = $this->repository->qtyScannedProductInInventory($inventoryId);

        $inventoryInfo->expected_box_qty = 0;// $this->repository->qtyBoxInInventory($inventoryId);
        $inventoryInfo->scanned_box_qty = 0; // $this->repository->qtyScannedBoxInInventory($inventoryId);

        if(empty($inventoryInfo->begin_datetime)) {
            $inventoryInfo->status = CheckBoxStatus::START_SCANNED;
            $inventoryInfo->begin_datetime = time();
        }
        $inventoryInfo->end_datetime = time();

        $inventoryInfo->save(false);

    }

    private function makeStatusFormDtoForCreateStock($dto,$stock,$checkBox) {

        if($stock['status_availability'] != Stock::STATUS_AVAILABILITY_YES) {
            return CheckBoxStatus::NO_SCANNING;
        }

        return CheckBoxStatus::NEW_;
    }


    public function loadAllBoxForFullInventory($inventoryId,$inventoryType = CheckBoxType::PART_BY_BOX) {

       $allBoxAddress = $this->repository->getAllBoxAddressForFullInventory($inventoryType);
       $this->loadBoxByBoxAddress($inventoryId,$allBoxAddress,$inventoryType);

//        foreach($allBoxAddress as $address) {
//            if($inventoryType == CheckBoxType::PART_BY_BOX && !$this->isExistProductInStockBoxForScanning($address['boxAddress'])) {
//                continue;
//            }
//            $dto = new \stdClass();
//            $dto->employeeBarcode = '01';
//            $dto->inventoryId = $inventoryId;
//            $dto->boxBarcode = $address['boxAddress'];;
//            $dto->productBarcode = '';
//            $dto->placeAddress = $address['placeAddress'];
//            $this->addBoxBarcodeBatchInsert($dto,$inventoryType);
//        }
//        $this->calculateScannedInInventory($inventoryId);
    }

    public function loadAllBoxByRowAddress($inventoryId,$minMaxPlaceAddress,$inventoryType = CheckBoxType::PART_BY_BOX) {

       $allBoxAddress = $this->repository->getAllBoxAddressByRowAddress($minMaxPlaceAddress,$inventoryType);

        $this->loadBoxByBoxAddress($inventoryId,$allBoxAddress,$inventoryType);

//        foreach($allBoxAddress as $address) {
//            if($inventoryType == CheckBoxType::PART_BY_BOX && !$this->isExistProductInStockBoxForScanning($address['boxAddress'])) {
//                continue;
//            }
//            $dto = new \stdClass();
//            $dto->employeeBarcode = '01';
//            $dto->inventoryId = $inventoryId;
//            $dto->boxBarcode = $address['boxAddress'];;
//            $dto->productBarcode = '';
//            $dto->placeAddress = $address['placeAddress'];
//            $this->addBoxBarcodeBatchInsert($dto,$inventoryType);
//        }
//        $this->calculateScannedInInventory($inventoryId);
    }

    public function loadBoxByBoxAddress($aInventoryId,$aBoxAddressList,$aInventoryType = CheckBoxType::PART_BY_BOX) {

        foreach($aBoxAddressList as $address) {
            if($aInventoryType == CheckBoxType::PART_BY_BOX && !$this->isExistProductInStockBoxForScanning($address['boxAddress'])) {
                continue;
            }

            $dto = new \stdClass();
            $dto->employeeBarcode = '01';
            $dto->inventoryId = $aInventoryId;
            $dto->boxBarcode = $address['boxAddress'];;
            $dto->productBarcode = '';
            $dto->placeAddress = $address['placeAddress'];

            $this->addBoxBarcodeBatchInsert($dto,$aInventoryType);
        }
        $this->calculateScannedInInventory($aInventoryId);
    }

    public function isExistProductInStockBoxForScanning($boxBarcode,$placeAddress= null) {

      return $this->repository->getCountAvailableStockByBoxBarcode($boxBarcode,$placeAddress);
    }

    public function deleteBox($inventoryId,$boxBarcode) {

      $this->repository->deleteBox($inventoryId,$boxBarcode);
      $this->calculateScannedInInventory($inventoryId);

      return null;
    }

    public function deleteInventory($inventoryId) {

      $this->repository->deleteInventory($inventoryId);

      return null;
    }

    public function resetRow($inventoryId,$placeAddress) {

        $minMaxPlaceAddress = $this->getMinMaxSecondaryAddress($placeAddress);
        $this->repository->resetRow($inventoryId,$minMaxPlaceAddress);
        $this->calculateScannedInInventory($inventoryId);

        return null;
    }

    public function preLoadProductBarcode($productBarcode,$BoxBarcode,$placeAddress) {
//        $minMaxPlaceAddress = $this->getMinMaxSecondaryAddress($placeAddress);

        $changeAddressPlaceValidation = new \common\b2b\domains\changeAddressPlace\validation\Validation();

        if($changeAddressPlaceValidation->isBoxLotOrReturnBox($productBarcode,[$placeAddress],$BoxBarcode)) {
            $productBarcode = $changeAddressPlaceValidation->findProductInStockByReturnBarcodeBoxInventory($productBarcode,[$placeAddress],$BoxBarcode);
        }
        return $productBarcode;
    }

    /*
     * Get min max secondary_address
     * @param string $secondary_address
     * @return array
     * */
    public function getMinMaxSecondaryAddress($secondary_address)
    {
//        if(RackAddress::checkForExist($secondary_address,2)) {
//            return [$secondary_address];
//        }

        $sa = explode('-',trim($secondary_address));
        $minMax = [];
        if(empty($sa) || !is_array($sa)) {
            return $minMax;
        }

        $rackInRowMin = RackAddress::RACK_MIN; //Полка в ряду минимальное значение
        $rackInRowMax = RackAddress::RACK_MAX*5; //Полка в ряду максимальное значение
        $upperMin = RackAddress::LEVEL_MIN; //Полка в ряду минимальное значение
//            $upperMax = RackAddress::LEVEL_MAX - 6; //Полка в ряду максимальное значение
        $upperMax = RackAddress::LEVEL_MAX - 5; //Полка в ряду максимальное значение
        // 2-10-02-1  // этаж-ряд-полка-уровень
        $stage = self::getFloorNumber($secondary_address);  // preg_replace('/[^0-9]/', '',$sa['0']); // этаж
        $row = self::getRowNumber($secondary_address);   //preg_replace('/[^0-9]/', '',$sa['1']); // ряд
        $rack = self::getRackNumber($secondary_address);  //preg_replace('/[^0-9]/', '',$sa['2']); // полка
        $level = self::getLevelNumber($secondary_address); // preg_replace('/[^0-9]/', '',$sa['3']); // уровень
        // 1-6-06-1
        for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {
            for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                $rack = $i3 < 10 && $i3 > 0 ? '0' . $i3 : $i3;
                $minMax[] = $stage . '-' . $row . '-' . $rack . '-' . $i4;
            }
        }
        $fullRow = 0;
        $row++;
        if ($fullRow) {
            for ($i3 = $rackInRowMin; $i3 <= $rackInRowMax; $i3++) {
                for ($i4 = $upperMin; $i4 <= $upperMax; $i4++) {
                    $rack = $i3 < 10 && $i3 > 0 ? '0' . $i3 : $i3;
                    $minMax[] = $stage . '-' . $row . '-' . $rack . '-' . $i4;
                }
            }
        }

        return $minMax;
    }

    /*
 * Get min max secondary_address
 * @param string $secondary_address
 * @return array
 * */
    public function explodePlaceAddress($placeAddress)
    {
        // 2-10-02-1  // этаж-ряд-полка-уровень
        $placeAddressInfo = new \stdClass();
        $placeAddressInfo->stage = self::getFloorNumber($placeAddress);
        $placeAddressInfo->row = self::getRowNumber($placeAddress);
        $placeAddressInfo->rack = self::getRackNumber($placeAddress);
        $placeAddressInfo->level = self::getLevelNumber($placeAddress);

        return $placeAddressInfo;
    }

    /*
    * Get floor Этаж
    * @param string $secondary_address
    * @return integer floor number
    * */
    public static function getFloorNumber($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));
        $stage = -1;
        if(is_array($sa) && isset($sa['0'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $subject = $sa['0'];
            $stage = preg_replace('/[^0-9]/', '',$subject); // этаж
        }

        return $stage;
    }

    /*
     * Get row Ряд
     * @param string $secondary_address
     * @return integer row number
     * */
    public static function getRowNumber($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));
        $row = -1;
        if(is_array($sa) && isset($sa['1'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $subject = $sa['1'];
            $row = preg_replace('/[^0-9]/','',$subject); // ряд
        }
        return $row;
    }

    /*
     * Get Rack Полка
     * @param string $secondary_address
     * @return integer Level number
     * */
    public static function getRackNumber($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));
        $stage = -1;
        if(is_array($sa) && isset($sa['2'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $subject = $sa['2'];
            $stage = preg_replace('/[^0-9]/', '',$subject); // этаж
        }

        return $stage;
    }

       /*
     * Get Level Уровень
     * @param string $secondary_address
     * @return integer Level number
     * */
    public static function getLevelNumber($secondary_address)
    {
        $sa = explode('-',trim($secondary_address));
        $stage = -1;
        if(is_array($sa) && isset($sa['3'])) {
            // 2-10-02-1  // этаж-ряд-полка-уровень
            $subject = $sa['3'];
            $stage = preg_replace('/[^0-9]/', '',$subject); // уровень
        }

        return $stage;
    }



    private function dtoForCreateBox($dto) {

        $createBox = new \stdClass();
        $createBox->clientId = $this->repository->getClientID();
        $createBox->warehouseId = $this->repository->getWarehouseID();
        $createBox->employeeId = ArrayHelper::getValue(EmployeeRepository::getEmployeeByBarcode($dto->employeeBarcode),'id',0);
        $createBox->inventoryId = $dto->inventoryId;
        $createBox->boxBarcode = $dto->boxBarcode;
        $createBox->placeAddress = $dto->placeAddress;
        $createBox->expectedQty = 0;
        $createBox->scannedQty = 0;

        $placeAddressInfo = $this->explodePlaceAddress($dto->placeAddress);
        $createBox->placeAddressPart1 = $placeAddressInfo->stage;
        $createBox->placeAddressPart2 = $placeAddressInfo->row;
        $createBox->placeAddressPart3 = $placeAddressInfo->rack;
        $createBox->placeAddressPart4 = $placeAddressInfo->level;

        return $createBox;
    }

    private function dtoForCreateStock($dto,$checkBox,$stock) {

        $createStock = new \stdClass();
        $createStock->checkBoxId = $checkBox->id;
        $createStock->stockId = $stock['id'];
        $createStock->clientId = $this->repository->getClientID();
        $createStock->warehouseId = $this->repository->getWarehouseID();

        $createStock->inventoryId = $dto->inventoryId;
        $createStock->boxBarcode = $dto->boxBarcode;
        $createStock->placeAddress = $dto->placeAddress;

        $createStock->stockInboundId = $stock['inbound_order_id'];
        $createStock->stockInboundItemId = $stock['inbound_order_item_id'];
        $createStock->stockOutboundId = $stock['outbound_order_id'];
        $createStock->stockOutboundItemId = $stock['outbound_order_item_id'];
        $createStock->stockStatusAvailability = $stock['status_availability'];
        $createStock->stockClientProductSku = $stock['field_extra1'];

//        $createStock->stockInboundStatus = $stock['status_inbound'];
//        $createStock->stockOutboundStatus = $stock['status_outbound'];
        $createStock->stockConditionType = $stock['condition_type'];

        $createStock->productBarcode = $stock['product_barcode'];
        $createStock->serializedDataStock = serialize($stock);



        $createStock->status = $this->makeStatusFormDtoForCreateStock($dto,$stock,$checkBox);
//        $createStock->status = CheckBoxStatus::NEW_;

//        $createStock->stockTransferId = ArrayHelper::getValue($stock,'transfer_Id');
//        $createStock->stockTransferOutboundBox = ArrayHelper::getValue($stock,'transfer_outbound_box');
//        $createStock->stockStatusTransfer = ArrayHelper::getValue($stock,'status_transfer');

        return $createStock;
    }
	
	public function getAllBoxesFromStockByAddress($address) {
		$result = [];
		foreach ($this->repository->getAllBoxesFromStockByAddress($address) as $data) {
			$result[$data['primary_address']] = $data;
		}
		return $result;
	}
	
}
