<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */
namespace common\ecommerce\defacto\transfer\service;

use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\constants\TransferDefaultValue;
use common\ecommerce\constants\TransferStatus;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceTransfer;
use common\ecommerce\entities\EcommerceTransferItems;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class TransferService
{
    private $repository;
    private $api;

    /**
     * TransferService constructor.
     */
    public function __construct() {
        $this->repository = new \common\ecommerce\defacto\transfer\repository\TransferRepository();
        $this->api = new \common\ecommerce\defacto\transfer\service\TransferAPIService();
    }

    public function getOrdersForPrintPickingList() {
        return $this->repository->getOrdersForPrintPickList();
    }

    public function canPrintPickingList($aTransferId) {
        return $this->repository->canPrintPickingList($aTransferId);
    }


    public function reservationOrdersForPrintPickingList($outboundOrderIds) {

        $outboundList = [];
        foreach($outboundOrderIds as $id) {
            // Если ребята патаются напечатать листы сборки для собраных заказов
            if(!$this->canPrintPickingList($id)) {
                continue;
            }
            $outboundList[] = $id;
        }

//        VarDumper::dump($outboundList,10,true);
//        die;
        $placeAddressSorting = new \common\ecommerce\defacto\transfer\service\ReservationTransferPlaceAddressSortingService();
        $beforeReservationSorting = $placeAddressSorting->beforeReservationSorting($outboundList);

        foreach($beforeReservationSorting as $id) {
            $order = $this->getOrderInfo($id);
            $order->order->print_picking_list_date = time();
            $order->order->save(false);

            $this->runReservation($this->getOrderInfo($id));
        }

        return $placeAddressSorting->beforePrintPickingList($beforeReservationSorting);
    }

    public function runReservation($aTransferInfo) {
        if(isset($aTransferInfo->order) && (int)$aTransferInfo->order->allocated_qty < 1) {
            $outboundReservation = new \common\ecommerce\defacto\transfer\service\TransferReservationService();
            $outboundReservation->run($aTransferInfo);
        }
    }

    public function getOrderInfo($aTransferId = null) {
        return $this->repository->getOrderInfo($aTransferId);
    }


    public function scannedProductBarcode($aDto) {

        $product = $this->repository->makeScannedProduct($aDto);

        $product = $this->repository->getProductInfoInOurBox($aDto->productBarcode,$aDto->lcBarcode,$aDto->pickingListBarcode);
//        $product = $this->repository->getProductInfoInOurBox($aDto->productBarcode,$aDto->ourBoxBarcode,$aDto->pickingListBarcode);
        $result = new \stdClass();
        $result->productExpectedQty = $product->expectedProductQtyInBox;
        $result->productAcceptedQty = $product->scannedProductQtyInBox;

        $printPickListInfo = $this->repository->getPrintPickListInfo($aDto->pickingListBarcode);
        $result->totalExpectedQty = $printPickListInfo->allocated_qty;
        $result->totalAcceptedQty = $printPickListInfo->accepted_qty;

        $qtyProductsInBox = $this->repository->getQtyProductsInBox($aDto->lcBarcode,$aDto->pickingListBarcode);
        $result->qtyProductsInBox = $qtyProductsInBox;

//        $productInfoInBox = $this->repository->getQtyProductsInOurBox($aDto->ourBoxBarcode,$aDto->pickingListBarcode);
        $productInfoInBox = $this->repository->getQtyProductsInOurBox($aDto->lcBarcode,$aDto->pickingListBarcode);
        $result->productExpectedQtyInBox = $productInfoInBox->expectedProductQtyInBox;
        $result->productAcceptedQtyInBox = $productInfoInBox->scannedProductQtyInBox;

        return $result;
    }

    public function scannedPrintPickListBarcode($aDto) {

        $printPickListInfo = $this->repository->getPrintPickListInfo($aDto->pickingListBarcode);

        $result = new \stdClass();
        $result->expectedQty = $printPickListInfo->allocated_qty;
        $result->acceptedQty = $printPickListInfo->accepted_qty;
        return $result;
    }

    public function scannedLcBarcode($aDto) {

//        $productListInOurBox = $this->repository->getProductListInOurBox($aDto->ourBoxBarcode,$aDto->pickingListBarcode);

//        foreach($productListInOurBox as $product) {
//            $dto = new \stdClass();
//            $dto->pickingListBarcode = $aDto->pickingListBarcode;
//            $dto->ourBoxBarcode = $aDto->ourBoxBarcode;
//            $dto->lcBarcode = $aDto->lcBarcode;
//            $dto->productBarcode = $product;
//            //$this->repository->makeScannedProduct($dto); // TODO Закоментировал для обработки старой версии
//        }

        $qtyProductsInBox = $this->repository->getQtyProductsInBox($aDto->lcBarcode,$aDto->pickingListBarcode);
        $result = new \stdClass();
        $result->qtyProductsInBox = $qtyProductsInBox;

        $printPickListInfo = $this->repository->getPrintPickListInfo($aDto->pickingListBarcode);
        $result->totalExpectedQty = $printPickListInfo->allocated_qty;
        $result->totalAcceptedQty = $printPickListInfo->accepted_qty;

        $productInfoInBox = $this->repository->getQtyProductsInOurBox($aDto->ourBoxBarcode,$aDto->pickingListBarcode);
        $result->productExpectedQty = $productInfoInBox->expectedProductQtyInBox;
        $result->productAcceptedQty = $productInfoInBox->scannedProductQtyInBox;

        return $result;
    }

    public function moveAllProductFromOurBox($aDto) {

//        $productListInOurBox = $this->repository->getProductListInOurBox($aDto->ourBoxBarcode,$aDto->pickingListBarcode);

//        foreach($productListInOurBox as $product) {
//            $dto = new \stdClass();
//            $dto->pickingListBarcode = $aDto->pickingListBarcode;
//            $dto->ourBoxBarcode = $aDto->ourBoxBarcode;
//            $dto->lcBarcode = $aDto->lcBarcode;
//            $dto->productBarcode = $product;
            $this->repository->makeScannedProduct($aDto); // TODO Закоментировал для обработки старой версии
//        }

        $qtyProductsInBox = $this->repository->getQtyProductsInBox($aDto->lcBarcode,$aDto->pickingListBarcode);
        $result = new \stdClass();
        $result->qtyProductsInBox = $qtyProductsInBox;

        $printPickListInfo = $this->repository->getPrintPickListInfo($aDto->pickingListBarcode);
        $result->totalExpectedQty = $printPickListInfo->allocated_qty;
        $result->totalAcceptedQty = $printPickListInfo->accepted_qty;

        $productInfoInBox = $this->repository->getQtyProductsInLc($aDto->ourBoxBarcode,$aDto->pickingListBarcode);
        $result->productExpectedQty =  $productInfoInBox->expectedProductQtyInBox;
        $result->productAcceptedQty = $productInfoInBox->scannedProductQtyInBox;

        return $result;
    }

    public function scannedOurBoxBarcode($aDto) {

//        $productInfoInBox = $this->repository->getQtyProductsInOurBox($aDto->ourBoxBarcode,$aDto->pickingListBarcode);

        $result = new \stdClass();
        $result->productExpectedQty = 0; //$productInfoInBox->expectedProductQtyInBox;
        $result->productAcceptedQty = 0; //$productInfoInBox->scannedProductQtyInBox;

        return $result;
    }

    public function showBoxItems($aDto) {
        return $this->repository->showBoxItems($aDto->ourBoxBarcode,$aDto->pickingListBarcode);
    }

    public function showLcBoxItems($aDto) {
        return $this->repository->showLcBoxItems($aDto->lcBarcode,$aDto->pickingListBarcode);
    }

    public function showOrderItems($aDto) {
        return $this->repository->showOrderItems($aDto->pickingListBarcode);
    }

    public function showScannedItems($aDto) {
        return $this->repository->showScannedItems($aDto->pickingListBarcode);
    }

    public function emptyBox($aDto) {

        $this->repository->emptyPackage($aDto->lcBarcode,$aDto->pickingListBarcode);

//        $productInfoInBox = $this->repository->getQtyProductsInOurBox($aDto->ourBoxBarcode,$aDto->pickingListBarcode);
        $productInfoInBox = $this->repository->getQtyProductsInOurBox($aDto->lcBarcode,$aDto->pickingListBarcode);

        $result = new \stdClass();
        $result->productExpectedQty = $productInfoInBox->expectedProductQtyInBox;
        $result->productAcceptedQty = $productInfoInBox->scannedProductQtyInBox;

        $printPickListInfo = $this->repository->getPrintPickListInfo($aDto->pickingListBarcode);
        $result->totalExpectedQty = $printPickListInfo->allocated_qty;
        $result->totalAcceptedQty = $printPickListInfo->accepted_qty;

        $qtyProductsInBox = $this->repository->getQtyProductsInBox($aDto->lcBarcode,$aDto->pickingListBarcode);
        $result->qtyProductsInBox = $qtyProductsInBox;


        return $result;
    }


//    public function GetOutBound($aBatchId) {
//        return $this->api->GetOutBound($aBatchId);
//    }

    public function sendByAPI($aDto) {

        $parsePikingList = $this->repository->parsePikingListBarcode($aDto->pickingListBarcode);
        $orderInfo = $this->repository->getOrderInfo($parsePikingList->transferId);
        $orderInfo->order->packing_date = time();
        $orderInfo->order->save(false);

        $dataForSend = $this->prepareDataForSendByAPI($aDto);
        $result = $this->api->SendOutBoundFeedBack($dataForSend);
        $this->setAPIStatus($result,$aDto);
        $this->MarkBatchForCompleted($result,$aDto);

        file_put_contents('sendByAPI-TEST-response-.log',print_r($result,true));
        return  $result;
    }

    private function prepareDataForSendByAPI($aDto) {

        $parsePikingList = $this->repository->parsePikingListBarcode($aDto->pickingListBarcode);
        $orderInfo = $this->repository->getOrderInfo($parsePikingList->transferId);

        $result = [];
        $waybillNumberList = [];
        foreach ($orderInfo->items as $item) {

            $productInBox = $this->repository->getProductInBoxForSendByAPI($item->transfer_id,$item->id);

            if(empty($productInBox) || !is_array($productInBox) ) {
                continue;
            }

            foreach ($productInBox as $product) {

                $transferOutboundBox = trim($product['transfer_outbound_box']);
                if(!isset($waybillNumberList[$transferOutboundBox])) {
//                    $waybillNumberList[$transferOutboundBox] = $this->makeWaybillNumber($transferOutboundBox).''.$item->transfer_id;
                    $waybillNumberList[$transferOutboundBox] = $this->makeWaybillNumber($transferOutboundBox).''.$item->id;
                }

                $productData = new \stdClass();
                $productData->OutBoundId = $item->client_OutboundId;
                $productData->LcBarcode = trim($product['transfer_outbound_box']);
                $productData->LotOrSingleBarcode = $product['product_barcode'];
                $productData->LotOrSingleQuantity = $product['qtyProductInBox'];
                $productData->WaybillNumber = $waybillNumberList[$transferOutboundBox];// $this->makeWaybillNumber($product['transfer_outbound_box']);
//                $productData->WaybillNumber =  str_replace('0','',$product['transfer_outbound_box']);
//                $productData->WaybillNumber =  $item->transfer_id;
                $result [] = $productData;
            }
        }

        return $result;
    }

    function makeWaybillNumber($boxBarcode) {
         return trim(str_replace('0','',$boxBarcode));
    }

    private function setAPIStatus($aResult,$aDto) {
        $parsePikingList = $this->repository->parsePikingListBarcode($aDto->pickingListBarcode);
        $orderInfo = $this->repository->getOrderInfo($parsePikingList->transferId);

        if (ArrayHelper::getValue($aResult, 'HasError')) {
            $orderInfo->order->api_status = StockAPIStatus::ERROR;
        } else {
            $orderInfo->order->api_status = StockAPIStatus::YES;
            $orderInfo->order->status = TransferStatus::DONE;
        }

        $orderInfo->order->save(false);
    }

    private function MarkBatchForCompleted($aResult,$aDto) {
        $parsePikingList = $this->repository->parsePikingListBarcode($aDto->pickingListBarcode);
        $orderInfo = $this->repository->getOrderInfo($parsePikingList->transferId);

        $result = [];
        if (!ArrayHelper::getValue($aResult, 'HasError')) {
            $result = $this->api->MarkBatchForCompleted($orderInfo->order->client_BatchId);
        }

        return $result;
    }

    public function GetBatchesAPI() {
        return $this->api->GetBatches();
    }

    public function GetOutBoundAPI($batchId) {
        return $this->api->GetOutBound($batchId);
    }

    /**
    * @return array[] $batchIdList
     */
    public function GetBatches() {
        $result = $this->api->GetBatches();
        $lcBarcodeList = $this->prepareGetBatches($result);
        $batchIdList = $this->saveTransfer($lcBarcodeList);
        foreach ($batchIdList as $batchId) {
            $productsList = $this->GetOutBound($batchId);
            $this->saveTransferItem($productsList,$batchId);
        }

        return $batchIdList;
    }

    private function prepareGetBatches($aResponseFromGetBatches) {
        $lcBarcodeList = [];
        if (ArrayHelper::getValue($aResponseFromGetBatches, 'HasError') == false) {
            $dataList = ArrayHelper::getValue($aResponseFromGetBatches, 'Data');
            if (!empty($dataList) && is_array($dataList)) {
                foreach ($dataList as $key => $dataListItem) {
                    if (!empty($dataListItem)) {

                        $LcBarcodes =  ArrayHelper::getValue($dataListItem, 'LcBarcodes.string');
                        
						if(count($LcBarcodes) == 1) {
                            $LcBarcodes = [$LcBarcodes];
                        } else {
                            $LcBarcodes = [ArrayHelper::getValue($dataListItem, 'BatchId')];
						}
						
                        $lcBarcodeList[$key] = new \stdClass();
                        $lcBarcodeList[$key]->BatchId = ArrayHelper::getValue($dataListItem, 'BatchId');
                        $lcBarcodeList[$key]->Status = ArrayHelper::getValue($dataListItem, 'Status');
                        $lcBarcodeList[$key]->LcBarcodes = $LcBarcodes;
                    }
                }
            }
        }

        return $lcBarcodeList;
    }

    private function saveTransfer($lcBarcodeList) {

        $batchIdList = [];
        if (!empty($lcBarcodeList) && is_array($lcBarcodeList)) {
            foreach ($lcBarcodeList as $dto) {
                if (!empty($dto->LcBarcodes) && is_array($dto->LcBarcodes)) {
                    $batchIdList[] = $dto->BatchId;
                    foreach ($dto->LcBarcodes as $box) {
                        $isExistTransfer = EcommerceTransfer::find()->andWhere(['client_BatchId' => $dto->BatchId,'client_LcBarcode' => $box])->exists();
                        if($isExistTransfer) {
                            continue;
                        }
                        $transferNew = new EcommerceTransfer();
                        $transferNew->client_id = 2;
                        $transferNew->client_BatchId = $dto->BatchId;
                        $transferNew->client_Status = $dto->Status;
                        $transferNew->client_LcBarcode = $box;
                        $transferNew->status = TransferStatus::_NEW;
                        $transferNew->api_status = StockAPIStatus::NO;
                        $transferNew->save(false);
                    }

                    $isExistTransfer = EcommerceTransfer::find()->andWhere(['client_BatchId' => $dto->BatchId,'client_LcBarcode' => TransferDefaultValue::MAIN_VIRTUAL_BOX])->exists();
                    if($isExistTransfer) {
                        continue;
                    }
                    $transferNew = new EcommerceTransfer();
                    $transferNew->client_id = 2;
                    $transferNew->client_BatchId = $dto->BatchId;
                    $transferNew->client_Status = $dto->Status;
                    $transferNew->client_LcBarcode = TransferDefaultValue::MAIN_VIRTUAL_BOX;
                    $transferNew->status = TransferStatus::_NEW;
                    $transferNew->api_status = StockAPIStatus::NO;
                    $transferNew->expected_box_qty = count($dto->LcBarcodes);
                    $transferNew->save(false);
                }
            }
        }

        return $batchIdList;
    }

    private function GetOutBound($aBatchId) {
        $result = $this->api->GetOutBound($aBatchId);
        $transferMain = EcommerceTransfer::find()->andWhere(['client_BatchId' => $aBatchId,'client_LcBarcode' => TransferDefaultValue::MAIN_VIRTUAL_BOX])->one();
        $productsList = [];
        if (ArrayHelper::getValue($result, 'HasError') == false) {
            $dataList = ArrayHelper::getValue($result, 'Data.Data.B2COutboundDTO');
			if (count($dataList)==1) {
				$dataList = [$dataList];
			}
            if (!empty($dataList) && is_array($dataList)) {
                foreach ($dataList as $key => $dataListItem) {
                    if (!empty($dataListItem)) {
                        $productsList[$key] = new \stdClass();
                        $productsList[$key]->ourTransferId = $transferMain->id;
                        $productsList[$key]->OutboundId = ArrayHelper::getValue($dataListItem, 'OutboundId');
                        $productsList[$key]->BatchId = ArrayHelper::getValue($dataListItem, 'BatchId');
                        $productsList[$key]->SkuId = ArrayHelper::getValue($dataListItem, 'SkuId');
                        $productsList[$key]->Quantity = ArrayHelper::getValue($dataListItem, 'Quantity');
                        $productsList[$key]->Status = ArrayHelper::getValue($dataListItem, 'Status');
                        $productsList[$key]->ToBusinessUnitId = ArrayHelper::getValue($dataListItem, 'ToBusinessUnitId');
                    }
                }
            }
        }

        return $productsList;
    }

    private function saveTransferItem($productsList,$aBatchId) {

        $transferMain = EcommerceTransfer::find()->andWhere(['client_BatchId' => $aBatchId,'client_LcBarcode' => TransferDefaultValue::MAIN_VIRTUAL_BOX])->one();

        if (!empty($productsList) && is_array($productsList)) {
            foreach ($productsList as $dto) {

                $isExistTransferItem = EcommerceTransferItems::find()->andWhere([
                    'transfer_id' => $dto->ourTransferId,
                    'client_OutboundId' =>  $dto->OutboundId,
                    'client_BatchId' =>  $dto->BatchId,
                    'client_SkuId' =>  $dto->SkuId,
                    'client_Quantity' =>  $dto->Quantity,
                ])->exists();

                if($isExistTransferItem) {
                    continue;
                }
                $transferNew = new EcommerceTransferItems();
                $transferNew->transfer_id = $dto->ourTransferId;
                $transferNew->client_OutboundId = $dto->OutboundId;
                $transferNew->client_BatchId = $dto->BatchId;
                $transferNew->client_SkuId = $dto->SkuId;
                $transferNew->client_Quantity = $dto->Quantity;
                $transferNew->client_Status = $dto->Status;
                $transferNew->product_sku = $dto->SkuId;
                $transferNew->expected_qty = $dto->Quantity;
                $transferNew->status = TransferStatus::_NEW;
                $transferNew->api_status = StockAPIStatus::NO;
                $transferNew->save(false);
            }
        }

        $transferMain->expected_qty = EcommerceTransferItems::find()->select('sum(expected_qty)')->andWhere(['transfer_id'=>$transferMain->id])->scalar();
        $transferMain->save(false);

    }

    public function resetTransferOrder ($aTransferId) {
        $this->repository->resetTransferOrder($aTransferId);
    }
}
