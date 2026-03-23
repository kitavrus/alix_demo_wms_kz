<?php
namespace app\modules\intermode\controllers\ecommerce\stock\domain;


use app\modules\intermode\controllers\ecommerce\stock\domain\constants\StockAPIStatus;
use app\modules\intermode\controllers\ecommerce\inbound\domain\entities\EcommerceInboundItem;
use app\modules\intermode\controllers\ecommerce\stock\domain\entities\EcommerceStock;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class StockService
{
    private $repository;
	private $api;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->repository = new \app\modules\intermode\controllers\ecommerce\stock\domain\repository\StockRepository();
		$this->api = new \app\modules\intermode\controllers\ecommerce\stock\domain\StockAPIService();
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
    public function moveProductFromBoxToBox($fromBoxBarcode,$productBarcode,$toBoxBarcode)
    {
        $this->repository->moveProductFromBoxToBox($fromBoxBarcode,$productBarcode,$toBoxBarcode);
    }

      //
    public function moveAllProductsFromBoxToBox($fromBoxBarcode,$toBoxBarcode)
    {
        $this->repository->moveAllProductsFromBoxToBox($fromBoxBarcode,$toBoxBarcode);
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

    public function getItemsForDiffReportByOrderIdOnlyBox($inboundOrderId,$boxBarcode,$productBarcode) {
        return $this->repository->getItemsForDiffReportByOrderIdOnlyBox($inboundOrderId,$boxBarcode,$productBarcode);
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


    public function SendInventorySnapshot() {
        $queryForSendInventorySnapshot = $this->repository->getRemainsForSendInventorySnapshot();
        $oneSnapshot = $queryForSendInventorySnapshot->all();
        
		file_put_contents('SendInventorySnapshotCSV-'.date('Y-m-d').'.csv',"\n"."\n"."\n",FILE_APPEND);
		
        foreach($oneSnapshot as $row) {
            $rowToSave = $row['product_barcode'].';';
            $rowToSave .= $row['box_address_barcode'].';';
            $rowToSave .= $row['place_address_barcode'].';';
            $rowToSave .= $row['client_product_sku'].';';
            $rowToSave .= $row['productQty'].';';
            file_put_contents('SendInventorySnapshotCSV-'.date('Y-m-d').'.csv',$rowToSave."\n",FILE_APPEND);
        }

        $this->api->SendInventorySnapshot($oneSnapshot);

//        foreach ($queryForSendInventorySnapshot->batch() as $oneSnapshot) {
//            file_put_contents('queryForSendInventorySnapshot'.date('Y-m-d').'.log',print_r($oneSnapshot,true)."\n",FILE_APPEND);
//            $this->api->SendInventorySnapshot($oneSnapshot);
//        }
        return true;
    }
	
   /**
     *
     * */
//    public function updateProductSku($aProductBarcode = null)
//    {
//        $allInStock = EcommerceStock::find()->select('product_barcode')
//            ->andFilterWhere(['product_barcode'=>$aProductBarcode])
//            ->andWhere('client_product_sku IS NULL OR client_product_sku = "" OR client_product_sku = 0')
//            ->groupBy('product_barcode')
//            ->column();
//
//        $apiService = new MasterDataAPIService();
//        foreach($allInStock as $k=>$ProductBarcode) {
//            $masterDataResult = $apiService->GetMasterData('','',$ProductBarcode);
//
//            EcommerceStock::updateAll([
//                'client_product_sku'=>ArrayHelper::getValue($masterDataResult,'Data.SkuId')
//            ],[
//                'product_barcode'=>$ProductBarcode
//            ]);
//
//            EcommerceInboundItem::updateAll([
//                'client_product_sku'=>ArrayHelper::getValue($masterDataResult,'Data.SkuId')
//            ],[
//                'product_barcode'=>$ProductBarcode
//            ]);
//        }
//    }


	public function getDataForInboundAPI($inboundOrderId) {
		return $this->repository->getDataForInboundAPI($inboundOrderId);
	}

	public function updateInboundDataMatrixId($stockId,$datamatrixId,$datamatrix) {
		return $this->repository->setInboundDataMatrixId($stockId,$datamatrixId,$datamatrix);
	}

	public function checkExistDataMatrixByStockId($stockId,$datamatrixId,$datamatrix) {
		return $this->repository->checkExistDataMatrixByStockId($stockId,$datamatrixId,$datamatrix);
	}

	public function getStockRemains() {
		return $this->repository->getStockRemains();
	}
}