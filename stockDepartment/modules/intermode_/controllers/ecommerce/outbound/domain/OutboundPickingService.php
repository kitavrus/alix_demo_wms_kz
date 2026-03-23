<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain;

use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use app\modules\intermode\controllers\ecommerce\outbound\domain\mapper\OutboundAPIMapper;
use app\modules\intermode\controllers\ecommerce\outbound\domain\repository\OutboundRepository;
//use common\ecommerce\defacto\outbound\service\ReservationPlaceAddressSortingService;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class OutboundPickingService
{
	private $repository;
	private $reservationService;
	private $apiService;
	public function __construct()
	{
		$this->repository = new OutboundRepository();
		$this->reservationService = new OutboundReservationService();
		//$this->apiService = new \app\modules\intermode\controllers\ecommerce\outbound\domain\mock\OutboundAPIService();
		$this->apiService = new \app\modules\intermode\controllers\ecommerce\outbound\domain\OutboundAPIService();
	}
	/**
	 * @param void
	 * @return ActiveDataProvider
	 */
	public function getOrdersForPrintPickingList() {
		return $this->repository->getOrdersForPrintPickList();
	}

	public function reservationOrdersForPrintPickingList($outboundOrderIds) {

		$outboundList = [];
		foreach($outboundOrderIds as $id) {
			// Если ребята патаются напечатать листы сборки для собраных заказов
			if(!$this->canPrintPickingList($id)) {
				continue;
			}
			
			if ($this->repository->canSendByAPI($id)) {
				$response = (new OutboundAPIMapper())->makeByItemAddOrderResponseDTO($this->repository->getOrderInfo($id));
				$this->apiService->sendStatusInWork($response);
			}
			
			$outboundList[] = $id;
		}

		$beforeReservationSorting = $this->reservationService->beforeReservationSorting($outboundList);

//		VarDumper::dump($beforeReservationSorting,10,true);
//		die;

		foreach($beforeReservationSorting as $id) {
			$order = $this->repository->getOrderInfo($id);
			if ($order->order->allocated_qty < 1) {
				$this->reservationService->run($order);
			}
		}

		foreach($beforeReservationSorting as $id) {
			$order = $this->repository->getOrderInfo($id);
			if ($order->order->allocated_qty < 1) {
				if ($this->repository->canSendByAPI($id)) {
					$response = (new OutboundAPIMapper())->makeByItemAcceptedAddOrderResponseDTO($order);
					$this->apiService->sendStatusOutOfStock($response);
				}
				$this->repository->changeStatusOfOutOfStock($id);
			}
			$order->order->status =  OutboundStatus::getPRINTED_PICKING_LIST();
			$order->order->save(false);
		}

		return $this->beforePrintPickingList($beforeReservationSorting);
	}

	public function canPrintPickingList($outboundId) {
		return $this->repository->canPrintPickingList($outboundId);
	}

	public function beforePrintPickingList($orderIdList)
	{
		$orderListForSort = [];
		$orderOnStock = $this->repository->getStockBeforePrintPickingList($orderIdList);

		$outboundOrderListInfo = [];
		foreach ($orderOnStock as $key=>$productOnStock) {
			if(!isset($outboundOrderListInfo[$productOnStock['ecom_outbound_id']])) {
//				$order = EcommerceOutbound::find()->andWhere(['id' => $productOnStock['ecom_outbound_id']])->one();
				$order = $this->repository->getOrderByID($productOnStock['ecom_outbound_id']);
				$outboundOrderListInfo[$productOnStock['ecom_outbound_id']] = [
					'orderNumber' => $order->order_number,
					'orderID' => $order->id,
					'clientID' => $order->client_id,
//					'showPriority' => $order->client_Priority,
//					'showShippingCity' => $order->client_ShippingCity,
//					'showPackMessage' => $order->client_PackMessage,
//					'showGiftWrappingMessage' => $order->client_GiftWrappingMessage,
					'createdAt' => $order->created_at,
//					'clientShipmentSource' => $order->client_ShipmentSource,
				];
			}

			$itemInfo = $this->repository->getOrderItemByProductBarcode($productOnStock['ecom_outbound_id'],$productOnStock['product_barcode']);

			$orderListForSort [] = [
				'order'=>$outboundOrderListInfo[$productOnStock['ecom_outbound_id']],
				'outboundId'=>$productOnStock['ecom_outbound_id'],
				'placeAddressSort1'=>$productOnStock['address_sort_order'],
				'placeAddressBarcode'=>$productOnStock['secondary_address'],
				'productBarcode'=>$productOnStock['product_barcode'],
				'productId'=>$productOnStock['product_id'],
				'boxAddressBarcode'=>$productOnStock['primary_address'],
				'productModel'=>$itemInfo->product_model, //$productOnStock['product_model'],
				'productName'=> $itemInfo->product_name, //$productOnStock['product_name'],
				'productColor'=> $itemInfo->product_color, //$productOnStock['product_color'],
				'productBrand'=> $itemInfo->product_brand, //$productOnStock['product_brand'],
				'productQty'=>$productOnStock['productQty'],
			] ;
//ecom_outbound_id,
//address_sort_order,
//product_barcode,
//primary_address,
//secondary_address,
//product_model,
//product_name,
//count(*) as productQty
		}

		ArrayHelper::multisort($orderListForSort,['outboundId','placeAddressSort1']);
		$orderListForSort = ArrayHelper::index($orderListForSort,null,'outboundId');
		uasort($orderListForSort,function($a,$b) {
			$aCount = count($a)-1;
			$bCount = count($b)-1;
			return  $a[$aCount]['placeAddressSort1'] < $b[$bCount]['placeAddressSort1'] ? -1 : 1;
		});

		return $orderListForSort;
	}

	public function makeDataForPrintPickingListNoReserved($outboundOrderIds) {

		$placeAddressSorting = new OutboundReservationService();
		$beforeReservationSorting = $placeAddressSorting->beforeReservationSorting($outboundOrderIds);

		return $this->beforePrintPickingList($beforeReservationSorting);
	}
}