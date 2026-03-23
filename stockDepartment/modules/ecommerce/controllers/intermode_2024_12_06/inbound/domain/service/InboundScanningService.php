<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.09.2017
 * Time: 10:02
 */

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\service;

use common\ecommerce\constants\InboundStatus;
use common\ecommerce\entities\EcommerceInboundItem;
use common\overloads\ArrayHelper;
use yii\helpers\VarDumper;

class InboundScanningService
{
    private $inboundRepository;
    private $stockService;
    private $productService;
    private $apiService;
    private $dto;

    /**
     * ServiceInbound constructor.
     * @param $dto array | \stdClass
     */
    public function __construct($dto = []) {
        $this->inboundRepository = new \common\ecommerce\intermode\inbound\repository\InboundRepository();
        $this->stockService = new \common\ecommerce\intermode\stock\service\Service();
        $this->productService = new \common\modules\product\service\ProductService();
        $this->apiService = new \common\ecommerce\intermode\inbound\service\InboundAPIService();

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
        return $this->inboundRepository->getQtyModelsInOrder($this->dto->orderNumberId,$this->dto->productBarcode);
    }
    //
//    public function cleanTransportedBox() {
//        $this->stockService->cleanTransportedBox($this->dto->transportedBoxBarcode,$this->dto->orderNumberId);
//        $this->inboundRepository->updateAcceptedQtyItems($this->dto->orderNumberId);
//        $this->inboundRepository->updateQtyScannedInOrder($this->dto->orderNumberId,$this->stockService->getScannedQtyByOrderInStock($this->dto->orderNumberId));
//    }
//    //
//    public function addScannedProductToStock($dto)
//    {
//        // TODO For this create mapper
//        $dtoForCreateStock = new \stdClass();
//        $dtoForCreateStock->clientId = $this->inboundRepository->getClientID();
//        $dtoForCreateStock->inboundId  = $dto->orderNumberId;
//        $dtoForCreateStock->productBarcode  = $dto->productBarcode;
//        $dtoForCreateStock->conditionType  = $dto->conditionType;
//        $dtoForCreateStock->boxAddressBarcode  = $dto->transportedBoxBarcode;
//        $dtoForCreateStock->statusInbound  = $this->stockService->getStatusInboundScanned();
//        $dtoForCreateStock->statusAvailability  = $this->stockService->getStatusAvailabilityNO();
//        $dtoForCreateStock->scanInDatetime  = $this->stockService->makeScanInboundDatetime();
//        $dtoForCreateStock->inboundItemId  = $this->inboundRepository->getItemByProductBarcode($dto->orderNumberId,$dto->productBarcode);
//
//        if((int)$dto->productQty < 1) {
//            $dto->productQty = 1;
//        }
//
//        for($i = 1; $i<= $dto->productQty; $i++ ) {
//
//            $this->stockService->create($dtoForCreateStock);
//            $dto->clientId = $this->inboundRepository->getClientID();
//
////            $placementUnitService = new \common\modules\placementUnit\service\Service($dto);
////            $placementUnitService->createFlow($dto, $this->stockService->getStockId());
//        }
//
//        $this->inboundRepository->updateAcceptedQtyItemByProductBarcode($dto->orderNumberId,$dto->productBarcode);
//        $this->inboundRepository->updateQtyScannedInOrder($dto->orderNumberId,$this->stockService->getScannedQtyByOrderInStock($dto->orderNumberId));
//        $this->inboundRepository->setOrderStatusInProcess($dto->orderNumberId);
//        $this->inboundRepository->setOrderItemStatusInProcess($dto->orderNumberId,$dto->productBarcode);
//    }
    //
    public function isEmptyProductBarcodeByModel()
    {
        $productService = new \common\modules\product\service\ProductService();
        return $productService->isEmptyProductBarcodeByModel($this->inboundRepository->getClientID(),$this->dto->productModel);
    }
    //
//    public function getQtyByBoxBarcodeInOrder() {
//        return $this->stockService->getQtyByBoxBarcodeInOrder($this->dto->transportedBoxBarcode,$this->dto->orderNumberId);
////        $placementUnitService = new \common\modules\placementUnit\service\Service();
////        return $placementUnitService->getQtyInUnitByBarcodeInOrder($this->dto->transportedBoxBarcode,$this->dto->orderNumberId);
//    }
//    //
//    public function getOrderItems() {
//        return $this->inboundRepository->getItemsByOrderId($this->dto->orderNumberId);
//    }
    //
    public function getItemsForDiffReportByOrderId($orderNumberId) {
        $clientBoxBarcodeList = $this->inboundRepository->getItemsForDiffReportByOrderId($orderNumberId);
        if(!empty($clientBoxBarcodeList) ) {
            return $this->inboundRepository->getItemsForDiffReportByClientBoxBarcodeList($clientBoxBarcodeList);
        }

        return [];
    }
    //
//    public function closeOrder()
//    {
//        $this->inboundRepository->setOrderStatusClose($this->dto->orderNumberId);
//        $this->inboundRepository->setDateConfirm($this->dto->orderNumberId);
//        $this->inboundRepository->setOrderItemStatusClose($this->dto->orderNumberId);
//
//        $this->stockService->setStatusNewAndAvailableYes($this->dto->orderNumberId);
//    }

    /////////////////////////////////////////////////////////////////
    /////////////////////////// NEW /////////////////////////////////
    /////////////////////////////////////////////////////////////////

    public function lotQtyInClientBox($aInboundId,$aBoxBarcode) {
        return $this->inboundRepository->lotQtyInClientBox($aInboundId,$aBoxBarcode);
    }

//    public function productSumQtyInLot($aInboundId,$aBoxBarcode,$aLotBarcode) {
//        return $this->inboundRepository->productSumQtyInLot($aInboundId,$aBoxBarcode,$aLotBarcode);
//    }

    public function productSumQtyInClientBox($aInboundId,$aBoxBarcode) {
        return $this->inboundRepository->productSumQtyInClientBox($aInboundId,$aBoxBarcode);
    }

    public function productSumQtyInOurBox($aInboundId,$aOurBoxBarcode) {
        return $this->inboundRepository->productSumQtyInOurBox($aInboundId,$aOurBoxBarcode);
    }

    public function productBarcodeQtyInClientBox($aInboundId,$aBoxBarcode,$aProductBarcode) {
        return $this->inboundRepository->productBarcodeQtyInClientBox($aInboundId,$aBoxBarcode,$aProductBarcode);
    }

//    public function isLotBarcodeExistInOrder($aInboundId,$aBoxBarcode,$aLotBarcode) {
//        return $this->inboundRepository->isLotBarcodeExistInOrder($aInboundId,$aBoxBarcode,$aLotBarcode);
//    }

    public function scanClientBoxBarcode()
    {
//        $lotQtyInClientBox = $this->lotQtyInClientBox($this->dto->orderNumberId,$this->dto->clientBoxBarcode);
        $productSumQtyInClientBox = $this->productSumQtyInClientBox($this->dto->orderNumberId,$this->dto->clientBoxBarcode);
		
		$this->inboundRepository->updateScannedClientBoxInOrder($this->dto->orderNumberId);
		$this->inboundRepository->updateExpectedProductInOrder($this->dto->orderNumberId);

        return [
            'productAcceptedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productAcceptedQty'),
            'productExpectedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productExpectedQty'),
        ];
    }

    public function scanOurBoxBarcode()
    {
        $productSumQtyInClientBox = $this->productSumQtyInOurBox($this->dto->orderNumberId,$this->dto->ourBoxBarcode);

        return [
            'productAcceptedQty'=> $productSumQtyInClientBox,
//            'productExpectedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productExpectedQty'),
        ];
    }

//    public function scanLotBarcode()
//    {
////        $lotQtyInClientBox = $this->lotQtyInClientBox($this->dto->orderNumberId,$this->dto->clientBoxBarcode);
//        $productSumQtyInClientBox = $this->productSumQtyInLot($this->dto->orderNumberId,$this->dto->clientBoxBarcode,$this->dto->lotBarcode);
//
//        return [
////            'lotQtyInClientBox'=>$lotQtyInClientBox,
//            'productAcceptedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productAcceptedQty'),
//            'productExpectedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productExpectedQty'),
//        ];
//    }


    //
    public function scanProductBarcode()
    {
        if ($this->dto->addExtraProduct != 0) {

            $extraInboundItem = $this->inboundRepository->getItemByProductBarcode($this->dto->orderNumberId, $this->dto->clientBoxBarcode, $this->dto->productBarcode);
            if (!$extraInboundItem)
            {
                $extraInboundItem = EcommerceInboundItem::find()->andWhere([
                    'inbound_id'=>$this->dto->orderNumberId,
                    'client_box_barcode'=>$this->dto->clientBoxBarcode,
                    'product_barcode'=>$this->dto->productBarcode,
                    'product_expected_qty'=>0,
                    'client_inbound_id'=>0,
                    'client_lot_sku'=>0,
                    'lot_barcode'=>'',
                ])->one();
            }


            if($extraInboundItem) {
                $extraInboundItem->product_accepted_qty += 1;
            } else {
                $extraInboundItem = new EcommerceInboundItem();
                $extraInboundItem->inbound_id = $this->dto->orderNumberId;
                $extraInboundItem->client_box_barcode = $this->dto->clientBoxBarcode;
                $extraInboundItem->product_barcode = $this->dto->productBarcode;
                $extraInboundItem->status = InboundStatus::SCANNING;
                $extraInboundItem->product_expected_qty = 0;
                $extraInboundItem->product_accepted_qty = 1;
                $extraInboundItem->client_inbound_id = 0;
                $extraInboundItem->client_lot_sku = 0;
                $extraInboundItem->lot_barcode = '';
            }

            $extraInboundItem->save(false);
        }

        // TODO For this create mapper
        $dtoForCreateStock = new \stdClass();
        $dtoForCreateStock->clientId = $this->inboundRepository->getClientID();
        $dtoForCreateStock->inboundId  = $this->dto->orderNumberId;
        $dtoForCreateStock->productBarcode  = $this->dto->productBarcode;
        $dtoForCreateStock->conditionType  = $this->dto->conditionType;
        $dtoForCreateStock->clientBoxBarcode  = $this->dto->clientBoxBarcode;
//        $dtoForCreateStock->lotBarcode  = $this->dto->lotBarcode;
        $dtoForCreateStock->boxAddressBarcode  = $this->dto->ourBoxBarcode;
        $dtoForCreateStock->statusInbound  = $this->stockService->getStatusInboundScanned();
        $dtoForCreateStock->statusAvailability  = $this->stockService->getStatusAvailabilityNO();
        $dtoForCreateStock->scanInDatetime  = $this->stockService->makeScanInboundDatetime();

        $inboundItem = $this->inboundRepository->getItemByProductBarcode($this->dto->orderNumberId, $this->dto->clientBoxBarcode, $this->dto->productBarcode);
        if($inboundItem) {
            $dtoForCreateStock->inboundItemId  = $inboundItem->id;
            $dtoForCreateStock->clientInboundId  = $inboundItem->client_inbound_id;
            $dtoForCreateStock->clientLotSku  = $inboundItem->client_lot_sku;
        }

        if((int)$this->dto->productQty < 1) {
            $this->dto->productQty = 1;
        }

        for($i = 1; $i<= $this->dto->productQty; $i++ ) {
            $this->stockService->create($dtoForCreateStock);
            $this->dto->clientId = $this->inboundRepository->getClientID();
        }

        $this->inboundRepository->updateAcceptedQtyItemByProductBarcode($this->dto->orderNumberId, $this->dto->clientBoxBarcode, $this->dto->productBarcode);
        $this->inboundRepository->updateQtyScannedInOrder($this->dto->orderNumberId,$this->stockService->getScannedQtyByOrderInStock($this->dto->orderNumberId));
        $this->inboundRepository->setOrderStatusInProcess($this->dto->orderNumberId);
        $this->inboundRepository->setOrderItemStatusInProcess($this->dto->orderNumberId, $this->dto->clientBoxBarcode, $this->dto->productBarcode);
		$this->inboundRepository->updateScannedClientBoxInOrder($this->dto->orderNumberId);

        $productSumQtyInClientBox = $this->productSumQtyInClientBox($this->dto->orderNumberId,$this->dto->clientBoxBarcode);
        $productSumQtyInOurBox = $this->productSumQtyInOurBox($this->dto->orderNumberId,$this->dto->ourBoxBarcode);

        return [
            'InClientBoxProductAcceptedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productAcceptedQty'),
            'InClientBoxProductExpectedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productExpectedQty'),
            'InOurBoxProductAcceptedQty'=> $productSumQtyInOurBox,
        ];
    }

    //
    public function getOrderItems() {
        return $this->inboundRepository->getItemsByOrderId($this->dto->orderNumberId);
    }


    public function closeOrder()
    {
//        $this->inboundRepository->setOrderStatusClose($this->dto->orderNumberId);
//        $this->inboundRepository->setDateConfirm($this->dto->orderNumberId);
//        $this->inboundRepository->setOrderItemStatusClose($this->dto->orderNumberId);
//
        $this->stockService->setStatusNewAndAvailableYes($this->dto->orderNumberId);

        // send by API
        $this->sendByAPI($this->dto->orderNumberId);
//        $dataForSendByAPI = $this->stockService->getDataForSendByAPI($this->dto->orderNumberId);
//        foreach($dataForSendByAPI as $row) {
//            $response  = $this->apiService->send($row, $this->dto->orderNumberId);
//            if($response['HasError'] == false) {
//
//            }
//        }

//        VarDumper::dump($dataForSendByAPI,10,true);
//        die;
        return;
    }

    private function sendByAPI($InboundID,$clientBoxBarcode = '') {
        // send by API
//        $dataForSendByAPI = $this->stockService->getDataForSendByAPI($InboundID);
        $resultBoxList =  $this->stockService->boxReadyToSendByInboundAPI($InboundID,$clientBoxBarcode);
        foreach($resultBoxList as $boxBarcode) {
            $aB2CInBoundFeedBack = $this->stockService->getDataForSendByApiByBox($InboundID, $boxBarcode);
            $response  = $this->apiService->send($aB2CInBoundFeedBack,$InboundID);
            $StockIds = [];
            foreach($aB2CInBoundFeedBack as $row) {
                $StockIds = ArrayHelper::merge($StockIds,explode(',',$row['ids'])) ;
            }
            if($response['HasError'] == false) {
                $this->stockService->setStockApiStatusYes($InboundID,$StockIds);
            } else {
                $this->stockService->setStockApiStatusError($InboundID,$StockIds);
            }
           //echo print_r($aB2CInBoundFeedBack,true)."\n";
        }
    }

    private function sendByAPI_OLD($InboundID) {
        // send by API
        $dataForSendByAPI = $this->stockService->getDataForSendByAPI($InboundID);
        foreach($dataForSendByAPI as $row) {
//            VarDumper::dump($row,10,true);
           echo print_r($row,true)."\n";
//            die;
//            $response['HasError'] = false;
            $response  = $this->apiService->send($row,$InboundID);
            $StockIds = explode(',',$row['ids']);

            if($response['HasError'] == false) {
                $this->stockService->setStockApiStatusYes($InboundID,$StockIds);
            } else {
                $this->stockService->setStockApiStatusError($InboundID,$StockIds);
            }
//            die('-END-');
        }
    }


    public function cleanOurBox() {

        $productBarcodeWithQtyInBox = $this->stockService->cleanOurBox($this->dto->ourBoxBarcode,$this->dto->orderNumberId);
        $this->inboundRepository->updateAcceptedQtyItems($this->dto->orderNumberId,$this->dto->clientBoxBarcode,$productBarcodeWithQtyInBox);
        $this->inboundRepository->updateQtyScannedInOrder($this->dto->orderNumberId,$this->stockService->getScannedQtyByOrderInStock($this->dto->orderNumberId));

        $productSumQtyInClientBox = $this->productSumQtyInClientBox($this->dto->orderNumberId,$this->dto->clientBoxBarcode);
        $productSumQtyInOurBox = $this->productSumQtyInOurBox($this->dto->orderNumberId,$this->dto->ourBoxBarcode);

        return [
            'InClientBoxProductAcceptedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productAcceptedQty'),
            'InClientBoxProductExpectedQty'=> ArrayHelper::getValue($productSumQtyInClientBox,'productExpectedQty'),
            'InOurBoxProductAcceptedQty'=> $productSumQtyInOurBox,
        ];
    }

    public function getQtyByBoxBarcodeInOrder() {
        return $this->stockService->getQtyByBoxBarcodeInOrder($this->dto->ourBoxBarcode,$this->dto->orderNumberId);
//        $placementUnitService = new \common\modules\placementUnit\service\Service();
//        return $placementUnitService->getQtyInUnitByBarcodeInOrder($this->dto->transportedBoxBarcode,$this->dto->orderNumberId);
    }
	
	
	public function done()
	{
        $this->inboundRepository->setOrderStatusComplete($this->dto->orderNumberId);
        $this->inboundRepository->setDateConfirm($this->dto->orderNumberId);
        $this->inboundRepository->setOrderItemStatusComplete($this->dto->orderNumberId);
        $this->inboundRepository->deleteZeroScannedBox($this->dto->orderNumberId);
	}

	public function checkOrder()
	{
		$productProblem = new \stdClass();
		$productProblem->problemProductList = $this->getProblemProductList();

		$incorrectProductBarcodeList = $this->inboundRepository->getIncorrectProductBarcode();
		$productProblem->incorrectProductBarcode = [];
		if(!empty($incorrectProductBarcodeList)) {
			foreach ($incorrectProductBarcodeList as $stockItem)
			$productProblem->incorrectProductBarcode [] = $this->makeAddressProblemList(
				$stockItem->client_box_barcode,
				$stockItem->product_barcode,
				$stockItem->box_address_barcode,
				$stockItem->place_address_barcode
			);
		}

		return $productProblem;
	}

	public function getProblemProductList()
	{
		$inboundItemList = $this->inboundRepository->getItems($this->dto->orderNumberId);

		$productProblemList = [];

		foreach($inboundItemList as $rowItem) {

			$count = $this->inboundRepository->getCountProductByClientBox($rowItem->inbound_id,$rowItem->client_box_barcode,$rowItem->product_barcode);

			if($count == $rowItem->product_accepted_qty) {
				continue;
			}

			$onStockList = $this->inboundRepository->getProductsInClientBox($rowItem->inbound_id,$rowItem->client_box_barcode,$rowItem->product_barcode);

			if(empty($onStockList)) {
				$productProblemList[] = $this->makeAddressProblemList($rowItem->client_box_barcode,$rowItem->product_barcode);
			} else {
				foreach ($onStockList as $stockItem) {
					$productProblemList[] =  $this->makeAddressProblemList(
						$rowItem->client_box_barcode,
						$rowItem->product_barcode,
						$stockItem->box_address_barcode,
						$stockItem->place_address_barcode
					);
				}
			}
		}

		return $productProblemList;
	}

	private function makeAddressProblemList($clientBoxBarcode,$productBarcode,$boxAddressBarcode = '',$placeAddressBarcode = '') {

    	$std = new \stdClass();
		$std->clientBoxBarcode = $clientBoxBarcode;
		$std->productBarcode = $productBarcode;
		$std->boxAddressBarcode = $boxAddressBarcode;
		$std->placeAddressBarcode = $placeAddressBarcode;

		return $std;
	}
}