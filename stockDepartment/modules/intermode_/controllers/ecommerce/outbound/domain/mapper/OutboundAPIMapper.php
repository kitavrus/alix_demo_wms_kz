<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\mapper;

use app\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundStatus;
use app\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderItemResponseDTO;
use app\modules\intermode\controllers\ecommerce\outbound\domain\dto\add_order\AddOrderResponseDTO;
use app\modules\intermode\controllers\ecommerce\outbound\domain\dto\OrderInfoDTO;

class OutboundAPIMapper
{
	/**
	 * @param OrderInfoDTO $orderInfo
	 * @return AddOrderResponseDTO
	 */
	public function makeByItemAddOrderResponseDTO($orderInfo) {
		$r = new AddOrderResponseDTO();
		$r->orderNumber = $orderInfo->order->order_number;
		foreach ($orderInfo->items as $item) {
			for ($i = 1; $i <= $item->expected_qty; $i++) {
				$dtoItem = new AddOrderItemResponseDTO();
				$dtoItem->name = $item->product_name;
				$dtoItem->barcode =  $item->product_barcode;
				$dtoItem->brand = $item->product_brand;
				$dtoItem->color = $item->product_color;
				$dtoItem->quantity = 1;
				$dtoItem->article = $item->product_model;
				$dtoItem->guid = $item->product_sku;
				$r->items[] = $dtoItem;
			}
		}
		return $r;
	}

	/**
	 * @param OrderInfoDTO $orderInfo
	 * @return AddOrderResponseDTO
	 */
	public function makeByItemAddOrderResponseDTONew($orderInfo) {
		$r = new AddOrderResponseDTO();
		$r->orderNumber = $orderInfo->order->order_number;
		foreach ($orderInfo->items as $item) {
			for ($i = 1; $i <= $item->expected_qty; $i++) {
				$dtoItem = new AddOrderItemResponseDTO();
				$dtoItem->name = $item->product_name;
				$dtoItem->barcode =  $item->product_barcode;
				$dtoItem->brand = $item->product_brand;
				$dtoItem->color = $item->product_color;
				$dtoItem->quantity = 1;
				$dtoItem->article = $item->product_model;
				$dtoItem->guid = $item->product_sku;
				$r->items[] = $dtoItem;
			}
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
				$dtoItem->guid = $item->product_sku;
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
			$i->quantity = $stock->status == OutboundStatus::getSCANNED() ? 1 : 0;
			$i->datamatrix = !empty($stock->product_qrcode) ? base64_encode($stock->product_qrcode) : "";
			$i->article = empty($stock->product_model) ? "" : $stock->product_model;
			$i->guid = $stock->product_sku;
			$r->items[] = $i;
		}
		return $r;
	}

}