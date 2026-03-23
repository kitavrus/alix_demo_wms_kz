<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 29.07.2017
 * Time: 9:16
 */
namespace common\ecommerce\defacto\outbound\service;

use common\ecommerce\constants\CancelByClientOutboundStatus;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\defacto\outbound\repository\CancelOutboundByClientRepository;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use common\ecommerce\entities\EcommerceCancelByClientOutbound;
use common\ecommerce\entities\EcommerceCancelByClientOutboundItems;
use common\ecommerce\entities\EcommerceStock;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use Yii;

class CancelOutboundByClientService
{
    private $repository;
    private $cancelByRepository;
    private $api;
    private $service;

    public function __construct()
    {
        $this->repository = new OutboundRepository();
        $this->cancelByRepository = new CancelOutboundByClientRepository();
        $this->api = new \common\ecommerce\defacto\outbound\service\OutboundAPIService();
        $this->service = new \common\ecommerce\defacto\outbound\service\OutboundService();
    }

    public function orderNumber($dtoForm) {
        $orderInfo = $this->service->getOrderInfoByOrderNumber($dtoForm->outboundOrderNumber);
//        VarDumper::dump($dtoForm,10,true);
        if(!$this->cancelByRepository->isOrderExist($dtoForm->outboundOrderNumber)) {
            $newOutboundByClient = $this->addOutboundOrder($orderInfo,$dtoForm);
            $this->addOutboundOrderItem($orderInfo,$newOutboundByClient);
        }
    }

    private function addOutboundOrder($aOrderInfo,$aDtoForm) {

        $clientOutboundByClient = new EcommerceCancelByClientOutbound();
        $clientOutboundByClient->client_id = $aOrderInfo->order->client_id;
        $clientOutboundByClient->outbound_id = $aOrderInfo->order->id;
        $clientOutboundByClient->cancel_key = $aDtoForm->cancelKey;
        $clientOutboundByClient->order_number = $aOrderInfo->order->order_number;
        $clientOutboundByClient->outbound_box = $aOrderInfo->outboundBoxBarcode;
        $clientOutboundByClient->client_OrderSource = $aOrderInfo->order->client_ShipmentSource;
        $clientOutboundByClient->status = CancelByClientOutboundStatus::_NEW;
        $clientOutboundByClient->api_status = StockAPIStatus::NO;
        $clientOutboundByClient->expected_qty = $aOrderInfo->order->expected_qty;
        $clientOutboundByClient->accepted_qty = $aOrderInfo->order->accepted_qty;
        $clientOutboundByClient->save(false);

        return $clientOutboundByClient;
    }

    private function addOutboundOrderItem($aOrderInfo,$newOutboundByClient) {

        $stockRepository = new \common\ecommerce\defacto\stock\repository\Repository();

        foreach($aOrderInfo->items as $item) {
            $excludeStockIds = [];
            for ($i = 0; $i < $item->accepted_qty; $i++) {

                $stockItem = $stockRepository->getStockItemByOutboundOrderProduct($item->outbound_id,$item->id,$item->product_barcode,$excludeStockIds);
                $excludeStockIds [] = $stockItem->id;

                $clientOutboundByClientItem = new EcommerceCancelByClientOutboundItems();
                $clientOutboundByClientItem->cancel_by_client_outbound_id = $newOutboundByClient->id;
                $clientOutboundByClientItem->outbound_id = $item->outbound_id;
                $clientOutboundByClientItem->outbound_item_id = $item->id;
                $clientOutboundByClientItem->product_barcode = $item->product_barcode;
                $clientOutboundByClientItem->client_SkuId = $item->product_sku;
                $clientOutboundByClientItem->status = CancelByClientOutboundStatus::_NEW;

                $clientOutboundByClientItem->stock_id = $stockItem->id;
                $clientOutboundByClientItem->old_box_address = $stockItem->box_address_barcode;
                $clientOutboundByClientItem->old_place_address = $stockItem->place_address_barcode;
                $clientOutboundByClientItem->save(false);
            }
        }
    }

    public function boxAddress($dtoForm) {
        $cancelBy = $this->cancelByRepository->getOrderByOrderNumber($dtoForm->outboundOrderNumber);
        if($cancelBy) {
            $this->cancelByRepository->addBoxAddressToOrder($cancelBy->id,$dtoForm->boxAddress);
        }
    }

    public function showOrderItems($dtoForm)
    {
        $aOrderNumber = $dtoForm->outboundOrderNumber;
        return $this->cancelByRepository->getOrderItems($aOrderNumber);
    }

    public function showAllOrderItems($dtoForm)
    {
        $aCancelKey = $dtoForm->cancelKey;
        return $this->cancelByRepository->getAllOrderItems($aCancelKey);
    }

    public function emptyBox($dtoForm)
    {
        $cancelKey = $dtoForm->cancelKey;
        $boxAddress = $dtoForm->boxAddress;

        $orderList = $this->cancelByRepository->getAllOrderByCancelKey($cancelKey);
        foreach($orderList as $order) {
            $this->cancelByRepository->emptyBox($order->id,$boxAddress);
        }

        return;
    }

    public function cancel($dtoForm)
    {
        $cancelKey = $dtoForm->cancelKey;
        $orderList = $this->cancelByRepository->getAllOrderByCancelKey($cancelKey);
        foreach($orderList as $order) {

            if($order->status == CancelByClientOutboundStatus::DONE) {
                continue;
            }

            $items = $this->cancelByRepository->getItemsById($order->id);
            foreach($items as $item) {
                EcommerceStock::updateAll(['box_address_barcode' => $order->new_box_address], ['id' => $item->stock_id]);
                $item->status = CancelByClientOutboundStatus::DONE;
                $item->save(false);
            }
            //$this->service->resetByOutboundOrderId($order->outbound_id);
            $this->service->CancelShipment($order->outbound_id,\common\ecommerce\constants\OutboundCancelStatus::CUSTOMER_REQUESTS_CANCELLATION);

            $orderInfo = $this->service->getOrderInfo($order->outbound_id);
            $order->api_status = $orderInfo->order->api_status;
            $order->status = CancelByClientOutboundStatus::DONE;
            $order->save(false);
        }
    }
}