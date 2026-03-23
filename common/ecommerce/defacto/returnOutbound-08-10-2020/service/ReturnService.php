<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 9:34
 */

namespace common\ecommerce\defacto\returnOutbound\service;


use common\components\BarcodeManager;
use common\ecommerce\constants\ReturnOutbound;
use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use common\ecommerce\defacto\returnOutbound\repository\ReturnRepository;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class ReturnService
{
    private $outboundRepository;
    private $returnRepository;
    private $stockService;

    /**
     * ReturnService constructor.
     */
    public function __construct()
    {
        $this->outboundRepository = new OutboundRepository();
        $this->returnRepository = new ReturnRepository();
        $this->stockService = new Service();
    }

    public function preLoadOrderNumber($dto)
    {
//        return null;
        if(!$this->returnRepository->isOrderExistByAny($dto->orderNumber)) {
            $returnAPIService = new ReturnAPIService();
            $result = $returnAPIService->GetShipmentForReturn($dto->orderNumber, 'Shipment');
//            $result = $returnAPIService->GetShipmentForReturn($dto->orderNumber, 'CargoReturnCode');

            $dto = new \stdClass();
            $dto->ExternalShipmentId = ArrayHelper::getValue($result, 'Data.ExternalShipmentId', '');
            $dto->ExternalOrderId = ArrayHelper::getValue($result, 'Data.ExternalOrderId', '');
            $dto->OrderSource = ArrayHelper::getValue($result, 'Data.OrderSource', '');
            $dto->IsRefundable = (int)ArrayHelper::getValue($result, 'Data.IsRefundable', '');
            $dto->CargoReturnCode = @ArrayHelper::getValue($result, 'Data.CargoReturnCode', '');
            $dto->RefundableMessage = @ArrayHelper::getValue($result, 'Data.RefundableMessage', '');

            $items = ArrayHelper::getValue($result, 'Data.Items.B2CShipmentReturnItemResult', []);
            $dto->items = (count($items) == 1) ? [$items] : $items;


            if (!$this->returnRepository->isExistOrderFromAPI($dto->ExternalShipmentId, $dto->ExternalOrderId)) {
                $newReturn = $this->createByDTO($dto);
                $totalQty = 0;
                foreach ($dto->items as $dtoItem) {
                    $this->createItemByDTO($newReturn->id, $dtoItem);
                    $totalQty += $dtoItem->SalesQuantity-$dtoItem->ReturnedQuantity;
                }
                $newReturn->expected_qty = $totalQty;
                $newReturn->save(false);
            }
            //////////////////////
            $returnOrder = $this->returnRepository->getOrderByAny($dto->ExternalShipmentId);
            $outbound = $this->outboundRepository->getOrderByAny($returnOrder->order_number);
            if($outbound) {
                $returnOrder->customer_name = $outbound->customer_name;
                $returnOrder->city =  $outbound->city;
                $returnOrder->customer_address = $outbound->customer_address;
                $returnOrder->client_ReferenceNumber = $outbound->client_ReferenceNumber;
//                 $returnOrder->client_CargoCompany = $outbound->client_CargoCompany;
                $returnOrder->outbound_box = $this->returnRepository->getOutboundBox($outbound->id);
                $returnOrder->outbound_id = $outbound->id;
                $returnOrder->save(false);
            }
        }

        return null;
    }

    public function scanOrderNumber($dto)
    {
        if ($this->returnRepository->isOrderExistByAny($dto->orderNumber)) {
            $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
            $returnOrder->return_reason = $dto->returnReason;
            $returnOrder->save(false);

//             $outbound = $this->outboundRepository->getOrderByAny($returnOrder->order_number);
//             if($outbound) {
//                 $returnOrder->customer_name = $outbound->customer_name;
//                 $returnOrder->city =  $outbound->city;
//                 $returnOrder->customer_address = $outbound->customer_address;
//                 $returnOrder->client_ReferenceNumber = $outbound->client_ReferenceNumber;
////                 $returnOrder->client_CargoCompany = $outbound->client_CargoCompany;
//                 $returnOrder->outbound_box =  $this->returnRepository->getOutboundBox($outbound->id);
//                 $returnOrder->outbound_id =  $outbound->id;
//                 $returnOrder->save(false);
//             }

            return $this->returnRepository->getOrderInfo($returnOrder->id);
        }

//        if ($this->outboundRepository->isOrderExistByAny($dto->orderNumber)) {
//            $outboundOrder = $this->outboundRepository->getOrderByAny($dto->orderNumber);
//            $outboundOrderInfo = $this->outboundRepository->getOrderInfo($outboundOrder->id);
//            $returnOrder = $this->createFromOutboundOrder($outboundOrderInfo);
//            return $this->returnRepository->getOrderInfo($returnOrder->id);
//        }

//        $returnOrder = $this->create($dto->orderNumber);

//        return $this->returnRepository->getOrderInfo($returnOrder->id);
    }

    public function scanBoxBarcode($dto) {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
        $qtyInbox = $this->returnRepository->getQtyInBox($returnOrder->id,$dto->boxBarcode);
        return $qtyInbox;
    }

    public function scanProductBarcode($dto)
    {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
//        if(!$this->returnRepository->isOrderItemExist($returnOrder->id,$dto->productBarcode) && empty($returnOrder->outbound_id)) {
//            $this->createItem($returnOrder->id,$dto->productBarcode);
//        }

        $orderItem = $this->returnRepository->getOrderItemByProductBarcode($returnOrder->id,$dto->productBarcode);

//        if(empty($returnOrder->outbound_id)) {
//            $orderItem->expected_qty += 1;
//        }

        $orderItem->accepted_qty += 1;
        $returnOrder->accepted_qty += 1;

        if(empty($orderItem->begin_datetime)) {
            $orderItem->begin_datetime = time();
        }

        if(empty($returnOrder->begin_datetime)) {
            $returnOrder->begin_datetime = time();
        }
        $orderItem->end_datetime = time();
        $returnOrder->end_datetime = time();

//        if(!empty($returnOrder->expected_qty) && $returnOrder->expected_qty == $returnOrder->accepted_qty) {
//            $returnOrder->end_datetime = time();
//        }
//        if(!empty($orderItem->expected_qty) && $orderItem->expected_qty == $orderItem->accepted_qty) {
//            $orderItem->end_datetime = time();
//        }

        $returnOrder->save(false);
        $orderItem->save(false);

        $conditionType =  StockConditionType::UNDAMAGED;
        switch($dto->returnProcess) {
            case ReturnOutbound::FIRS_QUALITY : $conditionType =  StockConditionType::UNDAMAGED;
                break;
            case ReturnOutbound::DONATION : $conditionType =  StockConditionType::FULL_DAMAGED;
                break;
        }

        $dtoForCreateStock = new \stdClass();
        $dtoForCreateStock->clientId = $this->returnRepository->getClientID();
        $dtoForCreateStock->returnId  = $returnOrder->id;
        $dtoForCreateStock->returnItemId  = $orderItem->id;
        $dtoForCreateStock->productBarcode  = $dto->productBarcode;
        $dtoForCreateStock->boxAddressBarcode  = $dto->boxBarcode;
        $dtoForCreateStock->conditionType  = $conditionType;
        $dtoForCreateStock->statusReturn  = ReturnOutboundStatus::SCANNING;
        $dtoForCreateStock->statusAvailability  = StockAvailability::NO;
        $dtoForCreateStock->scanInDatetime  = time();
//        $dtoForCreateStock->apiStatus  = StockAPIStatus::YES;
        $dtoForCreateStock->apiStatus  = StockAPIStatus::NO;
        $this->stockService->create($dtoForCreateStock);

        return $this->returnRepository->getOrderInfo($returnOrder->id);
    }

    public function showBoxItems($dto) {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
        return $this->returnRepository->getItemsInBox($returnOrder->id,$dto->boxBarcode);
    }

    public function showOrderItems($dto) {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
        return $this->returnRepository->getItemsInOrder($returnOrder->id);
    }

    public function emptyBox($dto)
    {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
        $allProductsInBox = $this->returnRepository->getItemsInBox($returnOrder->id,$dto->boxBarcode);

        $this->returnRepository->emptyBoxOnStock($returnOrder->id,$dto->boxBarcode);

        $this->recalculateScannedProductOnOrderItem($returnOrder->id,$allProductsInBox);
        $this->recalculateScannedProductOnOrder($returnOrder);

        return $this->returnRepository->getOrderInfo($returnOrder->id);
    }

    public function recalculateScannedProductOnOrderItem($returnId,$allProductsInBox)
    {
        foreach($allProductsInBox as $product) {
            $item = $this->returnRepository->getOrderItemByProductBarcode($returnId,$product['product_barcode']);
            $item->accepted_qty = $this->returnRepository->getScannedProductOnStock($returnId,$product['product_barcode']);
            $item->save(false);
        }
    }

    public function recalculateScannedProductOnOrder($returnOrder)
    {
        $returnOrder->accepted_qty = $this->returnRepository->getAllScannedProductOnStock($returnOrder->id);
        $returnOrder->save(false);
    }

    public function createFromOutboundOrder($outboundOrderInfo) {

        $newReturn = new EcommerceReturn();
        $newReturn->client_id = $outboundOrderInfo->order->client_id;
        $newReturn->outbound_id = $outboundOrderInfo->order->id;
        $newReturn->status = ReturnOutboundStatus::_NEW;
        $newReturn->order_number = $outboundOrderInfo->order->order_number;
        $newReturn->expected_qty = $outboundOrderInfo->order->accepted_qty;
        $newReturn->accepted_qty = 0;
        $newReturn->customer_name = $outboundOrderInfo->order->customer_name;
        $newReturn->city = $outboundOrderInfo->order->city;
        $newReturn->customer_address = $outboundOrderInfo->order->customer_address;
        $newReturn->client_ReferenceNumber = $outboundOrderInfo->order->client_ReferenceNumber;
        $newReturn->save(false);

        foreach($outboundOrderInfo->items as $item) {
            $newItemReturn = new EcommerceReturnItem();
            $newItemReturn->return_id = $newReturn->id;
            $newItemReturn->status = ReturnOutboundStatus::_NEW;
            $newItemReturn->expected_qty = $item->accepted_qty;
            $newItemReturn->accepted_qty = 0;
            $newItemReturn->product_barcode = $item->product_barcode;
            $newItemReturn->save(false);
        }

        return $newReturn;
    }

    public function create($orderNumber) {

        $newReturn = new EcommerceReturn();
        $newReturn->client_id =  $this->returnRepository->getClientID();
        $newReturn->status = ReturnOutboundStatus::_NEW;
        $newReturn->order_number = $orderNumber;
        $newReturn->expected_qty = 0;
        $newReturn->accepted_qty = 0;
        $newReturn->customer_name = '';
        $newReturn->city = '';
        $newReturn->customer_address = '';
        $newReturn->client_ReferenceNumber = '';
        $newReturn->save(false);

        return $newReturn;
    }

    public function createItem($returnId,$productBarcode) {

        $newItemReturn = new EcommerceReturnItem();
        $newItemReturn->return_id = $returnId;
        $newItemReturn->status = ReturnOutboundStatus::_NEW;
        $newItemReturn->expected_qty = 0;
        $newItemReturn->accepted_qty = 0;
        $newItemReturn->product_barcode = $productBarcode;
        $newItemReturn->save(false);

        return $newItemReturn;
    }

    public function sendByAPI($returnOrderId) {

        $stockService = new Service();
        $orderInfo = $this->returnRepository->getOrderInfo($returnOrderId);
        foreach($orderInfo->items as $item) {
            $stockService->updateProductSku($item->product_barcode);
        }

        $productsReadyForSendByAPI = $this->returnRepository->getProductsReadyForSendByAPI($returnOrderId);
        $productsReadyList = $this->prepareProductsReadyForSendByAPI($productsReadyForSendByAPI);

        $returnAPIService = new ReturnAPIService();
        return $returnAPIService->send($productsReadyList,$returnOrderId);
    }

    public function completeOrder($dto)
    {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
        $apiResponse =  $this->sendByAPI($returnOrder->id);
        $this->returnRepository->complete($returnOrder->id);

        $result = new \stdClass();
        $result->apiResponse = $apiResponse;

        return $result;
    }



    public function prepareProductsReadyForSendByAPI($productsReadyForSendByAPI) {

        $B2CReturnShipmentItems = [];
        foreach($productsReadyForSendByAPI->items as $item) {
            $B2CReturnShipmentItems [] = [
                'SkuId'=> $item['client_product_sku'],
                'Quantity'=> $item['qtyProduct'],
                'ReturnReasonCode'=> $productsReadyForSendByAPI->order->return_reason,
                'ReturnReasonProcessCode'=> ( new ReturnOutbound())->convertConditionTypeToAPIValue($item['condition_type']),
            ];
        }

        $result = [
            'ExternalShipmentId'=>$productsReadyForSendByAPI->order->order_number, //'OMC-8186455',
            'UniqueNumber'=>BarcodeManager::generateUuid(), // ? ,
            'CargoReturnCode'=> null, // ? ,
            'RefundUser'=> 'Kayrat', // ? ,
            'RefundUserId'=> '1', // ? ,
            'items'=>$B2CReturnShipmentItems
        ];

        return $result;
    }

    private function prepareProductsReadyForSendByAPI_OLD($productsReadyForSendByAPI) {
        $result = [];


        foreach ($productsReadyForSendByAPI->items as $product) {
            $result[] = [
                'ExternalShipmentId' => $productsReadyForSendByAPI->order->order_number, //'OMC-8186455',
                'SkuBarcode' => $product['product_barcode'], //'2430007688586',
                'Quantity' => $product['qtyProduct'], //'1',
                'ReturnProcess' => ( new ReturnOutbound())->convertConditionTypeToAPIValue($product['condition_type']), //'FirsQuality or Donation',
                'ReturnReason' => '?',
            ];
        }

        return $result;
    }


    public function createByDTO($aDto) {

        $newReturn = new EcommerceReturn();
        $newReturn->client_id = $this->returnRepository->getClientID();
        $newReturn->status = ReturnOutboundStatus::_NEW;
        $newReturn->order_number = $aDto->ExternalShipmentId;
        $newReturn->client_ExternalOrderId = $aDto->ExternalOrderId;
        $newReturn->client_ExternalShipmentId = $aDto->ExternalShipmentId;
        $newReturn->client_OrderSource = $aDto->OrderSource;
        $newReturn->client_CargoReturnCode = $aDto->CargoReturnCode;
        $newReturn->client_IsRefundable = $aDto->IsRefundable;
        $newReturn->client_RefundableMessage = $aDto->RefundableMessage;
        $newReturn->expected_qty = 0;
        $newReturn->accepted_qty = 0;
        $newReturn->customer_name = '';
        $newReturn->city = '';
        $newReturn->customer_address = '';
        $newReturn->client_ReferenceNumber = '';
        $newReturn->save(false);

        return $newReturn;
    }

    public function createItemByDTO($returnId,$aDtoItem) {

        $productBarcodeList = explode(',',$aDtoItem->Barcode);

        $newItemReturn = new EcommerceReturnItem();
        $newItemReturn->return_id = $returnId;
        $newItemReturn->status = ReturnOutboundStatus::_NEW;
        $newItemReturn->client_SkuId = $aDtoItem->SkuId;
        $newItemReturn->client_ImageUrl = @ArrayHelper::getValue($aDtoItem,'ImageUrl','');
        $newItemReturn->client_UnitPrice = $aDtoItem->UnitPrice;
        $newItemReturn->client_UnitDiscount = $aDtoItem->UnitDiscount;
        $newItemReturn->client_SalesQuantity = $aDtoItem->SalesQuantity;
        $newItemReturn->client_ReturnedQuantity = $aDtoItem->ReturnedQuantity;
        $newItemReturn->product_barcode = '';
        $newItemReturn->product_barcode1 = @ArrayHelper::getValue($productBarcodeList,'0','');
        $newItemReturn->product_barcode2 = @ArrayHelper::getValue($productBarcodeList,'1','');
        $newItemReturn->product_barcode3 = @ArrayHelper::getValue($productBarcodeList,'2','');
        $newItemReturn->product_barcode4 = @ArrayHelper::getValue($productBarcodeList,'3','');
        $newItemReturn->expected_qty = $aDtoItem->SalesQuantity-$aDtoItem->ReturnedQuantity;
        $newItemReturn->accepted_qty = 0;
        $newItemReturn->save(false);

        return $newItemReturn;
    }

    public function saveByDTOFromAPI($aDto) {
//            $this->returnRepository->ser
    }

    public function searchByOrderNumber($aOrderNumber) {

    }
}