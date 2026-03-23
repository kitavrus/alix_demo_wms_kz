<?php

namespace app\modules\intermode\controllers\api\v1\inbound\mapper;

use app\modules\intermode\controllers\api\v1\inbound\constants\StockInboundStatus;
use app\modules\intermode\controllers\api\v1\inbound\dto\add_order\AddOrderItemResponseDTO;
use app\modules\intermode\controllers\api\v1\inbound\dto\add_order\AddOrderResponseDTO;
use app\modules\intermode\controllers\api\v1\inbound\dto\OrderInfoDTO;
use common\modules\inbound\models\InboundOrder;
use app\modules\intermode\controllers\api\v1\inbound\dto\status_order\StatusOrderResponseDTO;

class InboundAPIMapper
{
	/**
	 * @param OrderInfoDTO $orderInfo
	 * @return AddOrderResponseDTO
	 */
	public function makeByItemAddOrderResponseDTO($orderInfo) {
		$r = new AddOrderResponseDTO();
		$r->orderNumber = $orderInfo->order->order_number;
		foreach ($orderInfo->items as $item) {
				$dtoItem = new AddOrderItemResponseDTO();
				$dtoItem->quantity =$item->product_expected_qty;
				$dtoItem->article = $item->product_model;
				$r->items[] = $dtoItem;
		}
		return $r;
	}

	/**
	 * @param OrderInfoDTO $orderInfo
	 * @return AddOrderResponseDTO
	 */
	public function makeByItemAcceptedAddOrderResponseDTO($orderInfo) {
		$r = new AddOrderResponseDTO();
		$r->orderNumber = $orderInfo->order->order_number;
		foreach ($orderInfo->items as $item) {
			for ($i = 1; $i <= $item->expected_qty; $i++) {
				$dtoItem = new AddOrderItemResponseDTO();
				$dtoItem->name = $item->product_name;
				$dtoItem->barcode =  $item->product_barcode;
				$dtoItem->brand = $item->product_brand;
				$dtoItem->color = $item->product_color;
				$dtoItem->quantity =  $item->accepted_qty - $i < 0 ? 0 : 1;
				$dtoItem->article = $item->product_model;
				$r->items[] = $dtoItem;
			}
		}
		return $r;
	}

	/**
	 * @param OrderInfoDTO $orderInfo
	 * @return AddOrderResponseDTO
	 */
	public function makeByStockAddOrderResponseDTO($orderInfo) {
		$r = new AddOrderResponseDTO();
		$r->orderNumber = $orderInfo->order->order_number;
		foreach ($orderInfo->stocks as $stock) {
			$i = new AddOrderItemResponseDTO();
			$i->name = empty($stock->product_name) ? "" : $stock->product_name ;
			$i->barcode =  $stock->product_barcode;
			$i->brand = $stock->product_brand;
			$i->color = $stock->product_color;
			$i->quantity = $stock->status_outbound == StockInboundStatus::SCANNED ? 1 : 0;
			$i->datamatrix = $stock->product_qrcode;
			$i->article = empty($stock->product_model) ? "" : $stock->product_model;
			$r->items[] = $i;
		}
		return $r;
	}

	/**
	 * @param InboundOrder $order
	 * @return StatusOrderResponseDTO
	 */
	public function makeByOrderStatusOrderResponseDTO($order) {
		$r = new StatusOrderResponseDTO();
		$r->orderNumber = $order->order_number;
		$r->wmsId = $order->id;
		return $r;
	}
	/**
	 * @param InboundOrder $order
	 * @param array[] $items
	 * @return StatusOrderResponseDTO
	 */
	public function makeByOrderStatusWithDataOrderResponseDTO($order,$items) {

		$products = [];
		if (empty($items)) {
			$products = [];
		} else {
			foreach ($items as $item) {
//				$datamatrix = [];
//				if (!empty($item["qrcode"])) {
//					$datamatrix = explode("|",$item["qrcode"]);
//					$datamatrix = array_filter($datamatrix);
//				}

				$products [] = [
//					"barcode"=> $item["product_barcode"],
//					"article"=> $item["product_model"],
					"quantity"=> $item["productQty"],
					"guid"=> $item["product_sku"],
//					"datamatrix"=> $datamatrix,
				];
			}
		}

		$r = new StatusOrderResponseDTO();
		$r->orderNumber = $order->order_number;
		$r->wmsId = $order->id;
		$r->items = $products;
		return $r;
	}

}