<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain;

use stockDepartment\modules\intermode\controllers\ecommerce\stock\domain\constants\StockAvailability;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\constants\StockOutboundStatus;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\dto\OrderInfoDTO;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\repository\OutboundReservationRepository;
use common\modules\stock\models\Stock;
use yii\helpers\VarDumper;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutbound;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\entities\EcommerceOutboundItem;

class OutboundReservationService
{
	private $repository;
	public function __construct()
	{
		$this->repository = new OutboundReservationRepository();
	}

	/**
	 * @param OrderInfoDTO $orderInfo
	 * @return void
	 */
	public function run($orderInfo)
	{

//		VarDumper::dump($orderInfo,10,true);
//		die;

		if(isset($orderInfo->order) && (int)$orderInfo->order->allocated_qty < 1) {
			$this->_run($orderInfo);
		}
	}

	public function beforeReservationSorting($outboundIds)
	{
		return $this->repository->beforeReservationSorting($outboundIds);
	}
	/**
	 * @param OrderInfoDTO $orderInfo
	 * @return void
	 */
	private function _run($orderInfo) {
		$allocateQty = 0;
		foreach($orderInfo->items as $item) {
			$item->allocated_qty = 0;
			$item->status = OutboundStatus::getRESERVING();
			$item->save(false);

		//	$stocks = $this->repository->getStocksByProductBarcode(
			$stocks = $this->repository->getStocksByProductSku(
				$orderInfo->order->client_id,
				$item->product_sku,
				$item->expected_qty
			);

//			VarDumper::dump($orderInfo->order->client_id,10,true);
//			VarDumper::dump($item->product_barcode,10,true);
//			VarDumper::dump($item->expected_qty,10,true);
//			VarDumper::dump($stocks,10,true);
//			die;

			if ($stocks) {
				foreach($stocks as $stock) {
					// ORDER ITEM
					$item->allocated_qty +=1;
					$allocateQty++;
					//STOCK
					$stock->ecom_outbound_id =  $orderInfo->order->id;
					$stock->ecom_outbound_items_id = $item->id;
					$stock->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
					$stock->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
					$stock->save(false);
				}
			}

			$item->status = Stock::STATUS_OUTBOUND_PART_RESERVED;
			if( $item->allocated_qty == $item->expected_qty ) {
				$item->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
			}
			$item->save(false);
		}

		$orderInfo->order->allocated_qty = $allocateQty;
		$orderInfo->order->status = Stock::STATUS_OUTBOUND_PART_RESERVED;

		if($orderInfo->order->allocated_qty ==  $orderInfo->order->expected_qty) {
			$orderInfo->order->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
		}
		$orderInfo->order->save(false);
	}

	public function resetByOutboundOrderId($outbound_order_id) {
		$outboundOrder = EcommerceOutbound::findOne($outbound_order_id);

		if (!$outboundOrder) {
			return false;
		}

		EcommerceOutbound::updateAll([
			'accepted_qty' => '0',
			'allocated_qty' => '0',
			'status' => OutboundStatus::getNEW(),
		], ['id' => $outboundOrder->id]);

		EcommerceOutboundItem::updateAll([
			'accepted_qty' => '0',
			'allocated_qty' => '0',
			'status' => OutboundStatus::getNEW()
		], ['outbound_id' => $outboundOrder->id]);

		Stock::updateAll([
			'box_barcode' => '',
			'ecom_outbound_id' => '0',
			'outbound_picking_list_id' => '0',
			'outbound_picking_list_barcode' => '',
			'status' => Stock::STATUS_INBOUND_PLACED,
			'status_availability' => Stock::STATUS_AVAILABILITY_YES,
			'field_extra5' => "Reset by order: ".$outboundOrder->order_number,
		], ['ecom_outbound_id' => $outboundOrder->id]);

		return true;
	}
	
	public function resetAnCancelByOutboundOrderId($outbound_order_id) {
		
		$outboundOrder = EcommerceOutbound::findOne($outbound_order_id);
		
		if (!$outboundOrder) {
			return false;
		}
		
			EcommerceOutbound::updateAll([
				'accepted_qty' => '0',
				'allocated_qty' => '0',
				'status' => OutboundStatus::getCANCEL(),
				], ['id' => $outboundOrder->id]);
				
			EcommerceOutboundItem::updateAll([
				'accepted_qty' => '0',
				'allocated_qty' => '0',
				'status' => OutboundStatus::getCANCEL()
			], ['outbound_id' => $outboundOrder->id]);
			
			Stock::updateAll([
				'box_barcode' => '',
				'ecom_outbound_id' => '0',
				'outbound_picking_list_id' => '0',
				'outbound_picking_list_barcode' => '',
				'status' => Stock::STATUS_INBOUND_PLACED,
				'status_availability' => Stock::STATUS_AVAILABILITY_YES,
				'field_extra5' => "Cancel and reset by order: ".$outboundOrder->order_number,
			], ['ecom_outbound_id' => $outboundOrder->id]);

			return true;
	}
}