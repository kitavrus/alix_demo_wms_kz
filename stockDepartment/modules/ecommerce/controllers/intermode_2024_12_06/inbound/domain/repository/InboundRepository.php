<?php

namespace app\modules\ecommerce\controllers\intermode\inbound\domain\repository;

use app\modules\ecommerce\controllers\intermode\inbound\domain\constants\InboundStatus;
use app\modules\ecommerce\controllers\intermode\inbound\domain\dto\OrderInfoDTO;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundDataMatrix;
use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockInboundStatus;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundItem;
use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStock;
use yii\db\Expression;

class InboundRepository
{
	private $inboundOrderID;

	/**
	 *
	 */
	public function getClientID()
	{
		return 103;
	}

	/**
	 *
	 */
	public function isProductBarcodeExistInOrder($aInboundId, $aProductBarcode)
	{
		return !EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'product_barcode' => $aProductBarcode,
		])->exists();
	}

	/**
	 *
	 */
	public function getOrderInfo($id)
	{
		$order = EcommerceInbound::find()->andWhere([
			"id" => $id,
			"client_id" => $this->getClientID(),
		])->one();

		$items = EcommerceInboundItem::find()
									 ->andWhere([
										 'inbound_id' => $order->id,
									 ])->all();
		$result = new OrderInfoDTO();
		$result->order = $order;
		$result->items = $items;

		return $result;
	}

	/**
	 * @return array|EcommerceInbound|\yii\db\ActiveRecord
	 */
	public function getOrder($id)
	{
		return EcommerceInbound::find()->andWhere([
			"id" => $id,
			"client_id" => $this->getClientID(),
		])->one();
	}

	/**
	 *
	 */
	public function isUsedBox($aInboundId, $aOurBoxBarcode)
	{
		return EcommerceStock::find()
							 ->andWhere(['box_address_barcode' => $aOurBoxBarcode])
							 ->andWhere('inbound_id != :inboundID', [':inboundID' => $aInboundId])
							 ->count();
	}

	/**
	 *
	 */
	public function create($data)
	{
		$orderID = $this->createOrder($data);
		$this->createOrderItems($data, $orderID);

		$this->setInboundOrderID($orderID);
		return $orderID;
	}

	/**
	 *
	 */
	public function isOrderExist($orderNumber)
	{
		return EcommerceInbound::find()->andWhere(['client_id' => $this->getClientID(), 'order_number' => $orderNumber])->exists();
	}

	/**
	 *
	 */
	private function createOrder($data)
	{
		$inboundOrder = new EcommerceInbound();
		$inboundOrder->client_id = $this->getClientID();
		$inboundOrder->order_number = $data->orderNumber;
//        $inboundOrder->supplier_id = $data->supplierId;
//        $inboundOrder->order_type = EcommerceInbound::ORDER_TYPE_INBOUND;
		$inboundOrder->status = InboundStatus::_NEW;
//        $inboundOrder->cargo_status = EcommerceInbound::CARGO_STATUS_NEW;
		$inboundOrder->expected_box_qty = $data->expectedBoxQty;
//        $inboundOrder->accepted_qty = 0;
//        $inboundOrder->accepted_number_places_qty = $data->expectedTotalPlaceQty;
//        $inboundOrder->expected_number_places_qty = 0;
//        $inboundOrder->comments = '?';
		$inboundOrder->save(false);

		return $inboundOrder->id;
	}

	/**
	 *
	 */
	private function createOrderItems($data, $orderId)
	{
		if (empty($data->items)) {
			return;
		}

		foreach ($data->items as $item) {
			$inboundOrderItem = new EcommerceInboundItem();
			$inboundOrderItem->inbound_order_id = $orderId;
			$inboundOrderItem->product_name = $item->productName;
			$inboundOrderItem->product_model = $item->productModel;
			$inboundOrderItem->product_barcode = $item->productModel;
			$inboundOrderItem->expected_qty = $item->expectedProductQty;
			$inboundOrderItem->expected_number_places_qty = $item->expectedPlaceQty;
			$inboundOrderItem->save(false);
		}
	}

	/**
	 *
	 */
	public function isProductModelBarcodeExistInOrder($productModelBarcode, $inboundOrderID)
	{
		return EcommerceInboundItem::find()->andWhere([
			'inbound_order_id' => $inboundOrderID,
			'product_model' => $productModelBarcode,
		])->exists();
	}

	/**
	 *
	 */
	public function isPlusQtyBarcodeInOrder($productBarcode, $inboundOrderID, $qty)
	{
		return EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $inboundOrderID,
			'product_barcode' => $productBarcode,
		])->andWhere('expected_qty >= accepted_qty + ' . (int)$qty . ' AND expected_qty != 0')->exists();
	}

	/**
	 *
	 */
	public function getNewAndInProcessOrder()
	{
		return EcommerceInbound::find()
							   ->andWhere(['client_id' => $this->getClientID()])
							   ->andWhere(['status' => InboundStatus::getNewAndInProcessOrder()])
							   ->asArray()
							   ->all();
	}

	/**
	 *
	 */
//	public function getQtyInOrder($id)
//	{
//		$inboundOrder = EcommerceInbound::find()
//										->select('expected_box_qty, accepted_box_qty, expected_lot_qty, accepted_lot_qty, expected_product_qty, accepted_product_qty')
//										->andWhere(['id' => $id, 'client_id' => $this->getClientID()])
//										->one();
//
//		$dto = new \stdClass();
//		$dto->expected_product_qty = 0;
//		$dto->accepted_product_qty = 0;
//		if ($inboundOrder != null) {
//			$dto->expected_product_qty = $inboundOrder->expected_product_qty;
//			$dto->accepted_product_qty = $inboundOrder->accepted_product_qty;
//		}
//
//		return $dto;
//	}

	/**
	 *
	 */
	public function getQtyModelsInOrder($inboundOrderID, $productModel)
	{
		$inboundOrderItem = EcommerceInboundItem::find()->select('expected_qty, accepted_qty')
												->andWhere(['product_barcode' => $productModel, 'inbound_id' => $inboundOrderID])
												->one();

		$dto = new \stdClass();
		$dto->expected_qty = 0;
		$dto->accepted_qty = 0;

		if ($inboundOrderItem != null) {
			$dto->expected_qty = $inboundOrderItem->expected_qty;
			$dto->accepted_qty = $inboundOrderItem->accepted_qty;
		}
		return $dto;
	}

	/**
	 *
	 */
	public function updateAcceptedQtyItemByProductModelBarcode($inboundId, $productBarcode)
	{
		$inboundItem = EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $inboundId,
			'product_barcode' => $productBarcode,
		])->one();

		if ($inboundItem) {
			$inboundItem->accepted_qty = $this->getScannedProductQtyByModelOrderInStock($inboundId, $productBarcode);
			$inboundItem->save(false);
			return $inboundItem->id;
		}
		return -1;
	}

	/**
	 *
	 */
	public function updateAcceptedQtyItems($inboundId, $productBarcodeWithQtyInBox)
	{
		foreach ($productBarcodeWithQtyInBox as $product) {

			$inboundItems = EcommerceInboundItem::find()->andWhere([
				'inbound_id' => $inboundId,
				'product_barcode' => $product['product_barcode'],
//                'client_box_barcode' =>$clientBoxBarcode,
			])->all();

			foreach ($inboundItems as $inboundItem) {

				$productAcceptedQty = $this->getScannedProductQtyByModelOrderInStock($inboundId, $inboundItem->product_barcode);
				$inboundItem->product_accepted_qty = $productAcceptedQty;

				if ($productAcceptedQty < 1) {
					$inboundItem->status = InboundStatus::_NEW;
				}

				$inboundItem->save(false);
			}
		}

		$inboundItems = EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $inboundId,
		])->all();

		foreach ($inboundItems as $row) {
			if ($row->product_expected_qty == 0 && $row->product_accepted_qty == 0) {
				$row->delete();
			}
		}
	}

	/**
	 *
	 */
	private function getScannedProductQtyByModelOrderInStock($inboundId, $productBarcode)
	{
		return EcommerceStock::find()->andWhere([
			'inbound_id' => $inboundId,
			'product_barcode' => $productBarcode,
//            'client_box_barcode' =>$clientBoxBarcode,
			'status_inbound' => [
				StockInboundStatus::SCANNED,
				StockInboundStatus::OVER_SCANNED
			],
		])->count();
	}

	/**
	 *
	 */
	public function setOrderStatusInProcess($orderId)
	{
		$inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
		if ($inbound) {
			$inbound->status = InboundStatus::SCANNING;
			$inbound->save(false);
		}
	}

	/**
	 *
	 */
	public function setProductBarcodeToItemByProductModel($productBarcode, $inboundId, $productModel)
	{
		$inboundItem = EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $inboundId,
			'product_model' => $productModel,
		])->one();

		if ($inboundItem) {
			$inboundItem->product_barcode = $productBarcode;
			$inboundItem->save(false);
		}
	}

	/**
	 * @return mixed
	 */
	public function getInboundOrderID()
	{
		return $this->inboundOrderID;
	}

	/**
	 * @param mixed $inboundOrderID
	 */
	public function setInboundOrderID($inboundOrderID)
	{
		$this->inboundOrderID = $inboundOrderID;
	}
//////////////////////////////////////////////////////////////////////
/////////////////////////// NEW /////////////////////////////////
//////////////////////////////////////////////////////////////////////
	/**
	 *
	 */
	public function lotQtyInClientBox($aInboundId, $aBoxBarcode)
	{
		return EcommerceInboundItem::find()
								   ->select('count(distinct lot_barcode) as lotQty')
								   ->andWhere([
									   'inbound_id' => $aInboundId,
									   'client_box_barcode' => $aBoxBarcode,
								   ])
								   ->scalar();
	}

	/**
	 *
	 */
	public function productSumQtyInLot($aInboundId, $aBoxBarcode, $aLotBarcode)
	{
		return EcommerceInboundItem::find()
								   ->select('sum(product_accepted_qty) as productAcceptedQty, sum(product_expected_qty) as productExpectedQty')
								   ->andWhere([
									   'inbound_id' => $aInboundId,
									   'client_box_barcode' => $aBoxBarcode,
									   'lot_barcode' => $aLotBarcode,
								   ])
								   ->asArray()
								   ->one();
	}

	/**
	 *
	 */
	public function productSumQtyInClientBox($aInboundId)
	{
		return EcommerceInboundItem::find()
								   ->select('sum(product_accepted_qty) as productAcceptedQty, sum(product_expected_qty) as productExpectedQty')
								   ->andWhere([
									   'inbound_id' => $aInboundId,
//                'client_box_barcode' => $aBoxBarcode,
								   ])
								   ->asArray()
								   ->one();
	}

	/**
	 *
	 */
	public function productSumQtyInOurBox($aInboundId, $aOurBoxBarcode)
	{
		return EcommerceStock::find()
							 ->andWhere([
								 'inbound_id' => $aInboundId,
								 'box_address_barcode' => $aOurBoxBarcode,
							 ])
							 ->count();
	}

	/**
	 *
	 */
	public function productBarcodeQtyInClientBox($aInboundId, $aBoxBarcode, $aProductBarcode)
	{
		return EcommerceInboundItem::find()
								   ->select('product_expected_qty as productExpectedQty, product_accepted_qty as productAcceptedQty')
								   ->andWhere([
									   'inbound_id' => $aInboundId,
									   'client_box_barcode' => $aBoxBarcode,
									   'product_barcode' => $aProductBarcode,
								   ])
								   ->asArray()
								   ->one();
	}

	/**
	 *
	 */
	public function isProductBarcodeExistInBox($aInboundId, $aBoxBarcode, $aProductBarcode)
	{
		return EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'client_box_barcode' => $aBoxBarcode,
			'product_barcode' => $aProductBarcode,
		])->exists();
	}

	/**
	 *
	 */
	public function isLotBarcodeExistInOrder($aInboundId, $aBoxBarcode, $aLotBarcode)
	{
		return EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'client_box_barcode' => $aBoxBarcode,
			'lot_barcode' => $aLotBarcode,
		])->exists();
	}

	/**
	 *
	 */
	public function isClientBarcodeExistInOrder($aInboundId, $aBoxBarcode)
	{
		return EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'client_box_barcode' => $aBoxBarcode,
		])->exists();
	}

	/**
	 *
	 */
	public function isExtraBarcodeInOrder($aInboundId, $aProductBarcode)
	{
		return EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'product_barcode' => $aProductBarcode,
		])->andWhere('product_expected_qty = product_accepted_qty AND product_expected_qty != 0')->exists();
	}

	/**
	 *
	 */
	public function getItemByProductBarcode($aInboundId, $aProductBarcode)
	{
		return EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'product_barcode' => $aProductBarcode,
		])->one();
	}

	/**
	 *
	 */
	public function updateAcceptedQtyItemByProductBarcode($aInboundId, $aProductBarcode)
	{
		$inboundItem = EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'product_barcode' => $aProductBarcode,
		])->one();

		if ($inboundItem) {
			$inboundItem->product_accepted_qty = $this->getScannedProductQtyByOrderInStock($aInboundId, $aProductBarcode);
			$inboundItem->save(false);
			return $inboundItem->id;
		}
		return -1;
	}

	/**
	 *
	 */
	private function getScannedProductQtyByOrderInStock($aInboundId, $aProductBarcode)
	{
		return EcommerceStock::find()->andWhere([
			'inbound_id' => $aInboundId,
			'product_barcode' => $aProductBarcode,
		])->count();
	}

	/**
	 *
	 */
	public function setOrderItemStatusInProcess($aInboundId, $aProductBarcode)
	{
		$inboundItem = EcommerceInboundItem::find()->andWhere([
			'inbound_id' => $aInboundId,
			'product_barcode' => $aProductBarcode,
		])->one();

		if ($inboundItem) {
			$inboundItem->status = InboundStatus::SCANNING;
			$inboundItem->save(false);

		}
	}

	/**
	 *
	 */
	public function updateQtyScannedInOrder($orderId, $acceptedQty)
	{
		$inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();

		if (empty($inbound)) {
			return;
		}
		$inbound->accepted_product_qty = $acceptedQty;

		if (empty($inbound->begin_datetime)) {
			$inbound->begin_datetime = time();
		}

		$inbound->end_datetime = time();
		$inbound->save(false);
	}

	/**
	 *
	 */
	public function getItemsForDiffReportByOrderId($inboundOrderId)
	{
		return EcommerceInboundItem::find()->select('client_box_barcode as clientBoxBarcode, sum(product_expected_qty) as productExpectedQty, sum(product_accepted_qty) as productAcceptedQty,inbound_id as inboundId')
								   ->andWhere(['inbound_id' => $inboundOrderId])
								   ->groupBy('client_box_barcode')
								   ->having('sum(product_expected_qty) != sum(product_accepted_qty)')
								   ->orderBy('client_box_barcode')
								   ->asArray()
								   ->all();
	}

	/**
	 *
	 */
	public function getItemsForDiffReportByClientBoxBarcodeList($aClientBoxBarcodeList)
	{
		$result = [];

		foreach ($aClientBoxBarcodeList as $clientBoxInfo) {

			$itemsList = EcommerceInboundItem::find()->select('inbound_id,product_barcode,lot_barcode,client_box_barcode, product_expected_qty,product_accepted_qty ')
											 ->andWhere(['inbound_id' => $clientBoxInfo['inboundId'],
												 'client_box_barcode' => $clientBoxInfo['clientBoxBarcode']
											 ])
											 ->andWhere('product_expected_qty != product_accepted_qty')
											 ->orderBy('client_box_barcode')
											 ->asArray()
											 ->all();

			$result [] = [
				'clientBoxBarcode' => $clientBoxInfo['clientBoxBarcode'],
				'productExpectedQty' => $clientBoxInfo['productExpectedQty'],
				'productAcceptedQty' => $clientBoxInfo['productAcceptedQty'],
				'inboundId' => $clientBoxInfo['inboundId'],
				'productList' => $itemsList
			];

		}

		return $result;
	}

	/**
	 *
	 */
	public function getItemsByOrderId($inboundOrderId)
	{
		return EcommerceInboundItem::find()->select('*,(product_expected_qty - product_accepted_qty) as order_by')
								   ->andWhere(['inbound_id' => $inboundOrderId])
								   ->orderBy(new Expression('order_by != 0 DESC'))
								   ->all();
	}

	/**
	 *
	 */
	public function setOrderStatusClose($orderId)
	{
		$inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
		if ($inbound) {
			$inbound->status = InboundStatus::DONE;
			$inbound->save(false);
		}
	}

	/**
	 *
	 */
	public function setDateConfirm($orderId)
	{
		$inbound = EcommerceInbound::find()->andWhere(['id' => $orderId])->one();
		if ($inbound) {
			$inbound->date_confirm = time();
			$inbound->save(false);
		}
	}

	/**
	 *
	 */
	public function setOrderItemStatusClose($orderId)
	{
		$inboundItems = EcommerceInboundItem::find()->andWhere(['inbound_id' => $orderId])->all();

		if ($inboundItems) {
			foreach ($inboundItems as $inboundItem) {
				$inboundItem->status = InboundStatus::DONE;
				$inboundItem->save(false);
			}
		}
	}

	/**
	 *
	 */
	public function isClientBoxExistInOtherOrder($aInboundId, $aClientBoxBarcode)
	{
		return EcommerceInboundItem::find()
								   ->andWhere([
									   'client_box_barcode' => $aClientBoxBarcode,
								   ])
								   ->andWhere('inbound_id != :inbound_id', [':inbound_id' => $aInboundId])
								   ->exists();
	}

	/**
	 *
	 */
	public function getOrderFullInfo($id)
	{
		$inboundOrder = EcommerceInbound::find()
										->select('expected_box_qty, accepted_box_qty, expected_lot_qty, accepted_lot_qty, expected_product_qty, accepted_product_qty')
										->andWhere(['id' => $id, 'client_id' => $this->getClientID()])
										->one();

		$dto = new \stdClass();
		$dto->expected_box_qty = 0;
		$dto->accepted_box_qty = 0;
		$dto->expected_product_qty = 0;
		$dto->accepted_product_qty = 0;

		if ($inboundOrder == null) {
			return $dto;
		}

		$itemInfo = $this->getExpectedScannedProduct($id);
		$scannedClientBox = $this->getScannedClientBox($id);

		$dto->expected_box_qty = $inboundOrder->expected_box_qty;
		$dto->accepted_box_qty = $scannedClientBox;
		$dto->expected_product_qty = $itemInfo->totalExpectedProductQty;
		$dto->accepted_product_qty = $itemInfo->totalAcceptedProductQty;

		return $dto;
	}

	/**
	 *
	 */
	public function getScannedClientBox($id)
	{
		return EcommerceStock::find()
							 ->select('COUNT(DISTINCT `client_box_barcode`)')
							 ->andWhere(['inbound_id' => $id])
							 ->scalar();
	}

	/**
	 *
	 */
	public function updateScannedClientBoxInOrder($id)
	{
		$order = EcommerceInbound::find()->andWhere(['id' => $id, 'client_id' => $this->getClientID()])->one();
		$order->accepted_box_qty = $this->getScannedClientBox($id);
		$order->save(false);

		return $order->accepted_box_qty;
	}

	/**
	 *
	 */
	public function getExpectedScannedProduct($id)
	{
		$itemInfo = EcommerceInboundItem::find()
										->select('SUM(product_accepted_qty) as total_accepted_product_qty, SUM(product_expected_qty) as total_expected_product_qty')
										->andWhere(['inbound_id' => $id])
										->asArray()
										->one();

		$result = new \stdClass();
		$result->totalExpectedProductQty = (int)$itemInfo['total_expected_product_qty'];
		$result->totalAcceptedProductQty = (int)$itemInfo['total_accepted_product_qty'];
		return $result;
	}

	/**
	 *
	 */
	public function updateExpectedProductInOrder($id)
	{
		$order = EcommerceInbound::find()->andWhere(['id' => $id, 'client_id' => $this->getClientID()])->one();
		$order->expected_product_qty = $this->getExpectedScannedProduct($id)->totalExpectedProductQty;
		$order->save(false);

		return $order->expected_product_qty;
	}

	/**
	 *
	 */
	public function setOrderStatusComplete($orderId)
	{
		$inbound = EcommerceInbound::find()->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])->one();
		if ($inbound) {
			$inbound->status = InboundStatus::COMPLETE;
			$inbound->save(false);
		}
	}

	/**
	 *
	 */
	public function setOrderItemStatusComplete($orderId)
	{
		$inboundItems = EcommerceInboundItem::find()->andWhere(['inbound_id' => $orderId])->all();

		if ($inboundItems) {
			foreach ($inboundItems as $inboundItem) {
				$inboundItem->status = InboundStatus::COMPLETE;
				$inboundItem->save(false);
			}
		}
	}

	/**
	 *
	 */
	public function getScannedAndExpectedProductInOrder($orderId)
	{
		$productInOrder = EcommerceInboundItem::find()
											  ->select(' SUM(`product_accepted_qty`) as productAcceptedQty, SUM(`product_expected_qty`) as productExpectedQty ')
											  ->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])
											  ->asArray()
											  ->one();
		$result = new \stdClass();
		$result->productAcceptedQty = (int)$productInOrder['productAcceptedQty'];
		$result->productExpectedQty = (int)$productInOrder['productExpectedQty'];
		return $result;
	}

	/**
	 *
	 */
	public function getScannedClientBoxInOrder($orderId)
	{
		$countClientBox = EcommerceStock::find()
										->select('COUNT(DISTINCT `client_box_barcode`) as countClientBox')
										->andWhere(['id' => $orderId, 'client_id' => $this->getClientID()])
										->asArray()
										->scalar();
		return (int)$countClientBox;
	}

	/**
	 *
	 */
	public function getItems($inboundOrderId)
	{
		return EcommerceInboundItem::find()
								   ->andWhere(['inbound_id' => $inboundOrderId])
								   ->all();
	}

	/**
	 *
	 */
	public function getCountProductByClientBox($inboundId, $clientBoxBarcode, $productBarcode)
	{
		return EcommerceStock::find()->andWhere([
			'inbound_id' => $inboundId,
			'client_box_barcode' => $clientBoxBarcode,
			'product_barcode' => $productBarcode,
			'client_id' => $this->getClientID(),
		])->count();
	}

	/**
	 *
	 */
	public function getProductsInClientBox($inboundId, $clientBoxBarcode, $productBarcode)
	{
		return EcommerceStock::find()->andWhere([
			'inbound_id' => $inboundId,
			'client_box_barcode' => $clientBoxBarcode,
			'product_barcode' => $productBarcode,
			'client_id' => $this->getClientID(),
		])->all();
	}

	/**
	 *
	 */
	public function getIncorrectProductBarcode()
	{
		return EcommerceStock::find()
							 ->andWhere('client_product_sku IS NULL OR client_product_sku = "" OR client_product_sku = 0')
							 ->all();
	}

	/**
	 *
	 */
	public function isNotAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix)
	{
		return !EcommerceInboundDataMatrix::find()
										  ->andWhere([
											  "inbound_id" => $inboundId,
											  "product_barcode" => $productBarcode,
											  "data_matrix_code" => $dataMatrix,
											  "status" => EcommerceInboundDataMatrix::NOT_SCANNED,
										  ])
										  ->exists();
	}

	/**
	 *
	 */
	public function getAvailableDataMatrix($inboundId, $productBarcode, $dataMatrix)
	{
		return EcommerceInboundDataMatrix::find()
										 ->andWhere([
											 "inbound_id" => $inboundId,
											 "product_barcode" => $productBarcode,
											 "data_matrix_code" => $dataMatrix,
											 "status" => EcommerceInboundDataMatrix::NOT_SCANNED,
										 ])
										 ->one();
	}

	/**
	 *
	 */
	public function setToNotScannedDataMatrix($dataMatrixIds)
	{
		return EcommerceInboundDataMatrix::updateAll([
			"status" => EcommerceInboundDataMatrix::NOT_SCANNED,
		],[
			"id"=>$dataMatrixIds
		]);
	}
}