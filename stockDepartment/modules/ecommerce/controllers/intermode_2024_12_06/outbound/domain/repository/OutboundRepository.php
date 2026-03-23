<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 27.07.2017
 * Time: 8:14
 */

namespace app\modules\ecommerce\controllers\intermode\outbound\domain\repository;


use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockAvailability;
use app\modules\ecommerce\controllers\intermode\employee\domain\repository\EmployeeRepository;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\StockOutboundStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\dto\OrderInfoDTO;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutbound;
use app\modules\ecommerce\controllers\intermode\outbound\domain\entities\EcommerceOutboundItem;
use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStock;
use common\helpers\DateHelper;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

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
        $stocks = EcommerceStock::find()
								//->select('product_barcode, product_qrcode, outbound_status')
								->andWhere(['outbound_id' => $order->id])
//								->groupBy(['product_barcode','product_qrcode'])
//								->asArray()
								->all();

        $result = new OrderInfoDTO();
        $result->order = $order;
        $result->items = $items;
        $result->stocks = $stocks;
        $result->outboundBoxBarcode = EcommerceStock::find()
													->select('outbound_box')
													->andWhere(['outbound_id'=>$id])
													->andWhere('outbound_box  != "" AND outbound_box  != 0')
													->scalar();

        return $result;
    }

	public function getStockBeforePrintPickingList($orderIdList)
	{
		return EcommerceStock::find()
							 ->select('created_at, outbound_id, place_address_sort1, product_barcode, box_address_barcode, place_address_barcode, product_model, product_name, count(*) as productQty')
							 ->andWhere(['outbound_id' => $orderIdList])
							 ->groupBy('product_barcode, box_address_barcode,outbound_id')
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

		return EcommerceStock::find()
							 ->andWhere(['outbound_id' => $pikingList['id']])
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

		return EcommerceStock::find()
							 ->andWhere('outbound_id != :outboundId',[':outboundId'=>$pikingList['id']])
							 ->andWhere(['outbound_box' => $packageBarcode])
							 ->exists();
	}

	public function qtyProductInPackage($pickList,$packageBarcode) {

		$pikingList = $this->getPickListByBarcode($pickList);

		return EcommerceStock::find()
							 ->andWhere([
//								 "client_id" => $this->getClientID(),
								 'outbound_id' => $pikingList['id'],
								 'outbound_box' => $packageBarcode,
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
            'product_barcode' => $productBarcode,
		])->exists();
	}

	public function isExtraBarcodeInOrder($outboundId,$productBarcode) {
		return EcommerceOutboundItem::find()->andWhere([
			'outbound_id'=>$outboundId,
			'product_barcode'=>$productBarcode,
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
	 * @retrun EcommerceStock
	 * */
	private function makeScannedStock($dto)
	{
		$stock = EcommerceStock::find()->andWhere([
             'product_barcode' => $dto->productBarcode,
//			'client_product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
			'outbound_id' => $dto->order->id,
			'status_outbound' => StockOutboundStatus::getReadyForScanning(),
			'client_id' => $this->getClientID(),
		])
		->one();

		if ($stock) {
			$stock->status_outbound = StockOutboundStatus::SCANNED;
			$stock->outbound_box = $dto->packageBarcode;
			$stock->scan_out_employee_id = $dto->employee->id;
			$stock->scan_out_datetime = time();
			$stock->save(false);
		}
		return $stock;
	}
	public function makeScannedStockQRCode($dto)
	{
		/*
		$stock = EcommerceStock::find()->andWhere([
			'product_barcode' => $dto->productBarcode,
//			'client_product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
			'outbound_id' => $dto->order->id,
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
		$stock = EcommerceStock::find()->andWhere(['id' => $dto->stockId])->one();
		$stock->product_qrcode = $dto->productQRCode;
		$stock->save(false);
	}
	private function makeScannedItem($dto)
	{
//		$outboundOrderItem = EcommerceOutboundItem::find()->andWhere([
//			'outbound_id' => $dto->order->id,
//            'product_barcode' => $dto->productBarcode,
////			'product_sku' => $this->getProductSkuIdByBarcode($dto->productBarcode),
//		])->one();
		$outboundOrderItem = $this->getOrderItemByProductBarcode($dto->order->id, $dto->productBarcode);
		if ($outboundOrderItem) {

			if (intval($outboundOrderItem->accepted_qty) < 1) {
				$outboundOrderItem->begin_datetime = time();
				$outboundOrderItem->status = OutboundStatus::SCANNING;
			}

			$outboundOrderItem->accepted_qty = $this->getQtyScannedProduct($dto->productBarcode,$dto->order->id);

			if ($outboundOrderItem->accepted_qty == $outboundOrderItem->expected_qty || $outboundOrderItem->accepted_qty == $outboundOrderItem->allocated_qty ) {
				$outboundOrderItem->status = OutboundStatus::SCANNED;
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
			$outboundOrder->status = OutboundStatus::SCANNING;
		}

		$outboundOrder->accepted_qty = $this->getQtyScanned($orderId);

		if ($outboundOrder->accepted_qty == $outboundOrder->expected_qty || $outboundOrder->accepted_qty == $outboundOrder->allocated_qty ) {
			$outboundOrder->status = OutboundStatus::SCANNED;
		}

		$outboundOrder->end_datetime = time();
		$outboundOrder->save(false);
	}

	private function getQtyScannedProduct($productBarcode,$orderId) {
		return EcommerceStock::find()->andWhere([
			'product_barcode'=>$productBarcode,
			'outbound_id' => $orderId,
			'status_outbound'=>StockOutboundStatus::SCANNED,
//			'client_id' => $this->getClientID(),
		])->count();
	}

	private function getQtyScanned($orderId) {
		return EcommerceStock::find()->andWhere([
			'outbound_id' => $orderId,
			'status_outbound'=>StockOutboundStatus::SCANNED,
//			'client_id' => $this->getClientID(),
		])->count();
	}

	public function emptyPackage($dto) {

		$stocks =  EcommerceStock::find()->andWhere([
//			'client_id'=>$this->getClientID(),
			'outbound_id'=>$dto->order->id,
			'outbound_box'=>$dto->packageBarcode,
			'status_outbound'=>StockOutboundStatus::SCANNED,
		])->all();

		foreach ($stocks as $stock) {

			$stock->outbound_box = '';
			$stock->product_qrcode = '';
			$stock->status_outbound = StockOutboundStatus::PRINTED_PICKING_LIST;
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
				'product_barcode' => $productBarcode,
			])->one();
	}

	public function isExistsProductQRCode($productQRCode)
	{
		return EcommerceStock::find()
							 ->andWhere(['product_qrcode' => $productQRCode])
//							 ->andWhere(['outbound_id' => $outboundId])
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
			$outboundOrder->status = OutboundStatus::PRINT_BOX_LABEL;
			$outboundOrder->place_accepted_qty = $this->getQtyBoxesInOrder($orderId);
			$outboundOrder->packing_date = DateHelper::getTimestamp();
			$outboundOrder->save(false);
		}

		return $outboundOrder;
	}

	public function getQtyBoxesInOrder($orderId)
	{
		return EcommerceStock::find()
							 ->andWhere([
								 'outbound_id' => $orderId,
								 'status'=>StockOutboundStatus::getPrintBoxOnStock()
							 ])
							 ->andWhere(["client_id" => $this->getClientID()])
							 ->groupBy('outbound_box')
							 ->orderBy('outbound_box')
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