<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 9:34
 */

namespace common\ecommerce\defacto\returnOutbound\service;

use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\ReturnOutbound;
use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use common\ecommerce\defacto\returnOutbound\repository\ReturnRepository;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;

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

    public function scanOrderNumber($dto)
    {
        if ($this->returnRepository->isOrderExistByAny($dto->orderNumber)) {
            $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
            return $this->returnRepository->getOrderInfo($returnOrder->id);
        }

        if ($this->outboundRepository->isOrderExistByAny($dto->orderNumber)) {
            $outboundOrder = $this->outboundRepository->getOrderByAny($dto->orderNumber);
            $outboundOrderInfo = $this->outboundRepository->getOrderInfo($outboundOrder->id);
            $returnOrder = $this->createFromOutboundOrder($outboundOrderInfo);
            return $this->returnRepository->getOrderInfo($returnOrder->id);
        }

        $returnOrder = $this->create($dto->orderNumber);

        return $this->returnRepository->getOrderInfo($returnOrder->id);
    }

    public function scanBoxBarcode($dto) {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
        $qtyInbox = $this->returnRepository->getQtyInBox($returnOrder->id,$dto->boxBarcode);
        return $qtyInbox;
    }

    public function scanProductBarcode($dto)
    {
        $returnOrder = $this->returnRepository->getOrderByAny($dto->orderNumber);
        if(!$this->returnRepository->isOrderItemExist($returnOrder->id,$dto->productBarcode) && empty($returnOrder->outbound_id)) {
            $this->createItem($returnOrder->id,$dto->productBarcode);
        }

        $orderItem = $this->returnRepository->getOrderItemByProductBarcode($returnOrder->id,$dto->productBarcode);

        if(empty($returnOrder->outbound_id)) {
            $orderItem->expected_qty += 1;
        }

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
		$dtoForCreateStock->apiStatus  = StockAPIStatus::YES;
		
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

        $productsReadyForSendByAPI = $this->returnRepository->getProductsReadyForSendByAPI($returnOrderId);
        $productsReadyList = $this->prepareProductsReadyForSendByAPI($productsReadyForSendByAPI);

        $returnAPIService = new ReturnAPIService();

        $result = [];
        foreach ($productsReadyList as $product) {
            $result[] =  $returnAPIService->send($product,$returnOrderId);
        }

        return $result;
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



    private function prepareProductsReadyForSendByAPI($productsReadyForSendByAPI) {
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

}