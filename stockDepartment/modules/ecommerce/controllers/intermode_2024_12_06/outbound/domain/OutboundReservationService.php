<?php

namespace app\modules\ecommerce\controllers\intermode\outbound\domain;

use app\modules\ecommerce\controllers\intermode\stock\domain\constants\StockAvailability;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\StockOutboundStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\constants\OutboundStatus;
use app\modules\ecommerce\controllers\intermode\outbound\domain\dto\OrderInfoDTO;
use app\modules\ecommerce\controllers\intermode\outbound\domain\repository\OutboundReservationRepository;

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
			$item->status = OutboundStatus::RESERVING;
			$item->save(false);

			$stocks = $this->repository->getStocksByProductBarcode(
				$orderInfo->order->client_id,
				$item->product_barcode,
				$item->expected_qty
			);

			if ($stocks) {
				foreach($stocks as $stock) {
					// ORDER ITEM
					$item->allocated_qty +=1;
					$allocateQty++;
					//STOCK
					$stock->outbound_id =  $orderInfo->order->id;
					$stock->outbound_item_id = $item->id;
					$stock->status_outbound = StockOutboundStatus::FULL_RESERVED;
					$stock->status_availability = StockAvailability::RESERVED;
					$stock->save(false);
				}
			}

			$item->status = OutboundStatus::PART_RESERVED;
			if( $item->allocated_qty == $item->expected_qty ) {
				$item->status = OutboundStatus::FULL_RESERVED;
			}
			$item->save(false);
		}

		$orderInfo->order->allocated_qty = $allocateQty;
		$orderInfo->order->status = OutboundStatus::PART_RESERVED;

		if($orderInfo->order->allocated_qty ==  $orderInfo->order->expected_qty) {
			$orderInfo->order->status = OutboundStatus::FULL_RESERVED;
		}
		$orderInfo->order->save(false);
	}

	public function resetByOutboundOrderId($outbound_order_id) {

	}
}