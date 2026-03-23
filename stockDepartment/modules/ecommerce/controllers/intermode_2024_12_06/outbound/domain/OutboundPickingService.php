<?php

namespace app\modules\ecommerce\controllers\intermode\outbound\domain;

use app\modules\ecommerce\controllers\intermode\outbound\domain\mapper\OutboundAPIMapper;
use app\modules\ecommerce\controllers\intermode\outbound\domain\repository\OutboundRepository;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class OutboundPickingService
{
	private $repository;
	private $reservationService;
	private $apiService;
	public function __construct()
	{
		$this->repository = new OutboundRepository();
		$this->reservationService = new OutboundReservationService();
		$this->apiService = new \app\modules\ecommerce\controllers\intermode\outbound\domain\mock\OutboundAPIService();
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
			$response = (new OutboundAPIMapper())->makeByItemAddOrderResponseDTO($this->repository->getOrderInfo($id));
			$this->apiService->sendStatusInWork($response);
			$outboundList[] = $id;
		}

		$beforeReservationSorting = $this->reservationService->beforeReservationSorting($outboundList);

		foreach($beforeReservationSorting as $id) {
			$this->reservationService->run($this->repository->getOrderInfo($id));
		}

		foreach($beforeReservationSorting as $id) {
			$order = $this->repository->getOrderInfo($id);
			if ($order->order->allocated_qty < 1) {
				$response = (new OutboundAPIMapper())->makeByItemAcceptedAddOrderResponseDTO($order);
				$this->apiService->sendStatusOutOfStock($response);
			}
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
			if(!isset($outboundOrderListInfo[$productOnStock['outbound_id']])) {
//				$order = EcommerceOutbound::find()->andWhere(['id' => $productOnStock['outbound_id']])->one();
				$order = $this->repository->getOrderByID($productOnStock['outbound_id']);
				$outboundOrderListInfo[$productOnStock['outbound_id']] = [
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

			$itemInfo = $this->repository->getOrderItemByProductBarcode($productOnStock['outbound_id'],$productOnStock['product_barcode']);

			$orderListForSort [] = [
				'order'=>$outboundOrderListInfo[$productOnStock['outbound_id']],
				'outboundId'=>$productOnStock['outbound_id'],
				'placeAddressSort1'=>$productOnStock['place_address_sort1'],
				'placeAddressBarcode'=>$productOnStock['place_address_barcode'],
				'productBarcode'=>$productOnStock['product_barcode'],
				'boxAddressBarcode'=>$productOnStock['box_address_barcode'],
				'productModel'=>$itemInfo->product_model, //$productOnStock['product_model'],
				'productName'=> $itemInfo->product_name, //$productOnStock['product_name'],
				'productColor'=> $itemInfo->product_color, //$productOnStock['product_color'],
				'productBrand'=> $itemInfo->product_brand, //$productOnStock['product_brand'],
				'productQty'=>$productOnStock['productQty'],
			] ;
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
}