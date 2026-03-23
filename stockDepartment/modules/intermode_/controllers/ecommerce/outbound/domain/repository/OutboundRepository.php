<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\repository;


use app\modules\intermode\controllers\ecommerce\stock\domain\constants\StockAvailability;
use app\modules\intermode\controllers\ecommerce\employee\domain\repository\EmployeeRepository;
use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
//use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\StockOutboundStatus;
use app\modules\intermode\controllers\ecommerce\outbound\domain\dto\OrderInfoDTO;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use app\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;
//use app\modules\intermode\controllers\ecommerce\stock\domain\entities\EcommerceStock;
use common\helpers\DateHelper;
use common\modules\stock\models\Stock;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundSource;

class OutboundRepository
{
    public function getClientID()
    {
        return 103;
    }

	public function getOrdersForPrintPickList()
	{
		$query = EcommerceOutbound::find()->andWhere([
			"client_id" => $this->getClientID(),
			'status'=>OutboundStatus::getOrdersForPrintPickList()
		]);

		return new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 20,
			],
			'sort' => ['defaultOrder' => ['created_at' => SORT_ASC]]
		]);
	}

	public function canPrintPickingList($ourOutboundId) {
		return EcommerceOutbound::find()->andWhere([
			'id' =>$ourOutboundId,
			'status' => OutboundStatus::getOrdersForPrintPickList(),
			'client_id' => $this->getClientID()
		])->exists();
	}

    public function getOrderInfo($id)
    {
        $order = EcommerceOutbound::find()->andWhere([
            "id" => $id,
            "client_id" => $this->getClientID(),
        ])->one();

        $items = EcommerceOutboundItem::find()->andWhere(['outbound_id' => $order->id])->all();
        $stocks = Stock::find()
								//->select('product_barcode, product_qrcode, outbound_status')
								->andWhere(['ecom_outbound_id' => $order->id])
//								->groupBy(['product_barcode','product_qrcode'])
//								->asArray()
								->all();

        $result = new OrderInfoDTO();
        $result->order = $order;
        $result->items = $items;
        $result->stocks = $stocks;
        $result->outboundBoxBarcode = Stock::find()
													->select('box_barcode')
													->andWhere(['ecom_outbound_id'=>$id])
													->andWhere('box_barcode  != "" AND box_barcode  != 0')
													->scalar();

        return $result;
    }

	public function getStockBeforePrintPickingList($orderIdList)
	{
		return Stock::find()
							 ->select('created_at, 
							 ecom_outbound_id, 
							 address_sort_order, 
							 product_barcode, 
							 product_id, 
							 primary_address, 
							 secondary_address, 
							 product_model, 
							 product_name, 
							 count(*) as productQty
							 ')
							 ->andWhere(['ecom_outbound_id' => $orderIdList])
							 ->groupBy('product_barcode, primary_address, ecom_outbound_id')
							 ->asArray()
							 ->all();
	}

	public function getOrderByID($id)
	{
		return EcommerceOutbound::find()->andWhere([
			"id" => $id,
			"client_id" => $this->getClientID(),
		])->one();
	}

	public function getPickListByBarcode($pickList)
	{
		$pickList  = trim($pickList);
		$order = EcommerceOutbound::find()->andWhere([
			"order_number" => $pickList,
			"client_id" => $this->getClientID(),
		])->one();
		return [
			'id'=>$order->id,
			'orderNumber'=>$order->order_number,
		];
//		$result = explode('-',$pickList);
//		return [
//			'id'=>ArrayHelper::getValue($result,'0'),
//			'orderNumber'=>ArrayHelper::getValue($result,'1').'-'.ArrayHelper::getValue($result,'2'),
//		];
	}
	
	
	public function changeStatusOfOutOfStock($orderId)
	{
		return EcommerceOutbound::updateAll([
			"status" => OutboundStatus::getOutOfStock(),
		], [
			"id" => $orderId,
		]);
	}
	
	public function isOrderExistByPickingBarcode($id,$orderNumber = "")
	{
		return EcommerceOutbound::find()->andWhere([
//			'client_id' => $this->getClientID(),
			'id' => $id,
//			'order_number' => $orderNumber,
		])->exists();
	}

	public function isOrderReserved($pickList)
	{
		$pikingList = $this->getPickListByBarcode($pickList);

		return Stock::find()
							 ->andWhere(['ecom_outbound_id' => $pikingList['id']])
							 ->exists();
	}
	//
	public function isNotDoneOrder($id,$orderNumber ="")
	{
		return EcommerceOutbound::find()->andWhere([
//			'client_id' => $this->getClientID(),
			'id' => $id,
//			'order_number' => $orderNumber,
			'status' => OutboundStatus::getNotDoneOrders(),
		])->exists();
	}
	
	
	//
	public function isCancelOrder($id)
	{
		return EcommerceOutbound::find()->andWhere([
			'id' => $id,
			'client_CancelReason' => OutboundStatus::getCANCEL(),
		])->exists();
	}
	
		public function canSendByAPI($id)
	{
		return EcommerceOutbound::find()->andWhere([
			'id' => $id,
			'client_ShipmentSource' => OutboundSource::getCRM(),
		])->exists();
	}


	public function getEmployeeByBarcode($barcode)
	{
		return EmployeeRepository::getEmployeeByBarcode($barcode);
//        return Employees::find()->andWhere([
//            'barcode' => $barcode
//        ])->one();
	}

	public function findOrderByPickList($pickList)
	{
		$pikingList = $this->getPickListByBarcode($pickList);
		$outbound = new EcommerceOutbound();
		if ($pikingList) {
			$outbound = EcommerceOutbound::find()->andWhere([
//				'client_id' => $this->getClientID(),
				'id' => $pikingList['id'],
//				'order_number' => $pikingList['orderNumber'],
			])->one();
		}

		return $outbound;
	}

	public function usePackageBarcodeInOtherOrder($pickList,$packageBarcode)
	{
		$pikingList = $this->getPickListByBarcode($pickList);

		return Stock::find()
							 ->andWhere('ecom_outbound_id != :outboundId',[':outboundId'=>$pikingList['id']])
							 ->andWhere(['box_barcode' => $packageBarcode])
							 ->exists();
	}

	public function qtyProductInPackage($pickList,$packageBarcode) {

		$pikingList = $this->getPickListByBarcode($pickList);

		return Stock::find()
							 ->andWhere([
//								 "client_id" => $this->getClientID(),
								 'ecom_outbound_id' => $pikingList['id'],
								 'box_barcode' => $packageBarcode,
							 ])
							 ->count();
	}

	public function showOrderItems($pickList)
	{
		$pikingList = $this->getPickListByBarcode($pickList);

		$items = EcommerceOutboundItem::find()->andWhere([
			'outbound_id' => $pikingList['id'],
		])
									  ->asArray()
									  ->all();

		return $items;
	}

	public function isProductExistInOrder($outboundOrderID,$productBarcode)
	{
		return EcommerceOutboundItem::find()->andWhere([
			'outbound_id' => $outboundOrderID,
//			'product_sku' => $this->getProductSkuIdByBarcode($productBarcode),
//            'product_barcode' => $productBarcode,
            'product_id' => (new ProductService())->getProductIdByBarcode($productBarcode),
		])->exists();
	}

	public function isExtraBarcodeInOrder($outboundId,$productBarcode) {
		return EcommerceOutboundItem::find()->andWhere([
			'outbound_id'=>$outboundId,
//			'product_barcode'=>$productBarcode,
			'product_id' => (new ProductService())->getProductIdByBarcode($productBarcode)
//			'product_sku' => $this->getProductSkuIdByBarcode($productBarcode),
		])
		->andWhere('expected_qty = accepted_qty')->exists();
//        ->andWhere('expected_qty = accepted_qty AND field_extra1 = "" ')->exists();
	}

	// STOCK
	public function makeScannedProduct($dto)
	{
		$stock = $this->makeScannedStock($dto);
		$this->makeScannedItem($dto);
		$this->makeScannedOrder($dto->order->id);
		return $stock;
	}

	/**
	 * @retrun Stock
	 * */
	private function makeScannedStock($dto)
	{
		$stock = Stock::find()->andWhere([
//             'product_barcode' => $dto->productBarcode,
			'product_id' => (new ProductService())->getProductIdByBarcode($dto->productBarcode),
//			'client_product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
			'ecom_outbound_id' => $dto->order->id,
			'status' => OutboundStatus::getReadyForScanning(),
			'client_id' => $this->getClientID(),
		])
		->one();

		if ($stock) {
			$stock->status = Stock::STATUS_OUTBOUND_SCANNED;
			$stock->box_barcode = $dto->packageBarcode;
			$stock->scan_out_employee_id = $dto->employee->id;
			$stock->scan_out_datetime = time();
			$stock->save(false);
		}
		return $stock;
	}
	public function makeScannedStockQRCode($dto)
	{
		/*
		$stock = Stock::find()->andWhere([
			'product_barcode' => $dto->productBarcode,
//			'client_product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
			'ecom_outbound_id' => $dto->order->id,
			'status_outbound' => StockOutboundStatus::SCANNED,
			'client_id' => $this->getClientID(),
			//'product_qrcode' => "",
		])
		->one();
				if ($stock && empty($stock->product_qrcode)) {
			$stock->product_qrcode = $dto->productQRCode;
			$stock->save(false);
		}
		*/
		$stock = Stock::find()->andWhere(['id' => $dto->stockId])->one();
		$stock->product_qrcode = $dto->productQRCode;
		$stock->save(false);
	}
	private function makeScannedItem($dto)
	{
//		$outboundOrderItem = EcommerceOutboundItem::find()->andWhere([
//			'ecom_outbound_id' => $dto->order->id,
//            'product_barcode' => $dto->productBarcode,
////			'product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
//		])->one();
		$outboundOrderItem = $this->getOrderItemByProductBarcode($dto->order->id, $dto->productBarcode);
		if ($outboundOrderItem) {

			if (intval($outboundOrderItem->accepted_qty) < 1) {
				$outboundOrderItem->begin_datetime = time();
				$outboundOrderItem->status = OutboundStatus::getSCANNING();
			}

			$outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode,$dto->order->id);

			if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty ) {
				$outboundOrderItem->status = OutboundStatus::getSCANNED();
			}

			$outboundOrderItem->end_datetime = time();
			$outboundOrderItem->save(false);
		}
	}
	private function makeScannedOrder($orderId)
	{
		$outboundOrder = EcommerceOutbound::find()
										  ->andWhere([
											  'id'=>$orderId,
											  'client_id' => $this->getClientID()
										  ])->one();

		if(intval($outboundOrder->accepted_qty) < 1) {
			$outboundOrder->begin_datetime = time();
			$outboundOrder->status = OutboundStatus::getSCANNING();
		}

		$outboundOrder->accepted_qty = $this->getQtyScanned($orderId);

		if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty ) {
			$outboundOrder->status = OutboundStatus::getSCANNED();
		}

		$outboundOrder->end_datetime = time();
		$outboundOrder->save(false);

	}

	private function getQtyScannedProduct($productBarcode,$orderId) {
		return Stock::find()->andWhere([
//			'product_barcode'=>$productBarcode,
			'product_id' => (new ProductService())->getProductIdByBarcode($productBarcode),
			'ecom_outbound_id' => $orderId,
			'status'=>OutboundStatus::getSCANNED(),
//			'client_id' => $this->getClientID(),
		])->count();
	}

	private function getQtyScanned($orderId) {
		return Stock::find()->andWhere([
			'ecom_outbound_id' => $orderId,
			'status'=>OutboundStatus::getSCANNED(),
//			'client_id' => $this->getClientID(),
		])->count();
	}

	public function emptyPackage($dto) {

		$stocks =  Stock::find()->andWhere([
//			'client_id'=>$this->getClientID(),
			'ecom_outbound_id'=>$dto->order->id,
			'box_barcode'=>$dto->packageBarcode,
			'status'=>OutboundStatus::getSCANNED(),
		])->all();

		foreach ($stocks as $stock) {

			$stock->box_barcode = '';
			$stock->product_qrcode = '';
			$stock->status = OutboundStatus::getPRINTED_PICKING_LIST();
			$stock->save(false);

			$outboundItem = $this->getOrderItemByProductBarcode($dto->order->id,$stock->product_barcode);
//			$outboundItem = EcommerceOutboundItem::find()->andWhere([
//				'outbound_id' => $dto->order->id,
//				'product_barcode' => $stock->product_barcode,
//			])->one();

			if ($outboundItem) {
				$outboundItem->accepted_qty = $this->getQtyScannedProduct($stock->product_barcode, $dto->order->id);
				$outboundItem->save(false);
			}
		}
		$this->makeScannedOrder($dto->order->id);
	}

	public function getOrderItemByProductBarcode($outboundId, $productBarcode) {
			return EcommerceOutboundItem::find()->andWhere([
				'outbound_id' => $outboundId,
//				'product_barcode' => $productBarcode,
				'product_id' => (new ProductService())->getProductIdByBarcode($productBarcode),
			])->one();
	}

	public function isExistsProductQRCode($productQRCode)
	{
		return Stock::find()
							 ->andWhere(['product_qrcode' => $productQRCode])
//							 ->andWhere(['ecom_outbound_id' => $outboundId])
							 ->exists();
	}


	public function packageOrder($orderId)
	{
		$outboundOrder = EcommerceOutbound::find()
										  ->andWhere([
											  'id'=>$orderId,
											  'client_id' => $this->getClientID()
										  ])->one();
		if($outboundOrder) {
			$outboundOrder->status = OutboundStatus::getPACKED();
			$outboundOrder->place_accepted_qty = $this->getQtyBoxesInOrder($orderId);
			$outboundOrder->packing_date = DateHelper::getTimestamp();
			$outboundOrder->save(false);
		}

		return $outboundOrder;
	}

	public function getQtyBoxesInOrder($orderId)
	{
		return Stock::find()
							 ->andWhere([
								 'ecom_outbound_id' => $orderId,
								 'status'=>OutboundStatus::getPrintBoxOnStock()
							 ])
							 ->andWhere(["client_id" => $this->getClientID()])
							 ->groupBy('box_barcode')
							 ->orderBy('box_barcode')
							 ->asArray()
							 ->count();
	}

	public function getOrderByOrderNumber($orderNumber)
	{
		return EcommerceOutbound::find()->andWhere([
			"order_number" => trim($orderNumber),
			"client_id" => $this->getClientID(),
		])->one();
	}
}