<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\uploads\service;

use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use stockDepartment\modules\alix\controllers\outboundSeparator\constants\Status;
use stockDepartment\modules\alix\controllers\outboundSeparator\entities\OutboundSeparator;
use stockDepartment\modules\alix\controllers\outboundSeparator\entities\OutboundSeparatorItems;
use stockDepartment\modules\alix\controllers\outboundSeparator\entities\OutboundSeparatorStock;

class UploadService
{
	public function makeOrder($orderNumber,$comments,$pathToFile) {
			$order = OutboundSeparator::findOne(["order_number"=>$orderNumber]);
			if(empty($order)){
				$order = new OutboundSeparator();
				$order->order_number = $orderNumber;
				$order->comments = $comments;
				$order->status = Status::_NEW;
				$order->path_to_file = $pathToFile;
				$order->save(false);
			}
			return $order;
	}

	public function makeOrderItem($orderId,$orderNumber,$box,$productBarcode) {
			$order = OutboundOrder::findOne(["order_number"=>$orderNumber]);
			$orderItem = new OutboundSeparatorItems();
			$orderItem->outbound_separator_id = $orderId;
			$orderItem->order_number = $orderNumber;
			$orderItem->out_box_barcode = $box;
			$orderItem->product_barcode = $productBarcode;
			$orderItem->outbound_id = !empty($order) ? $order->id : 0;
			$orderItem->status = Status::_NEW;
			$orderItem->save(false);
		return $orderItem;
	}

	public function makeOutboundOrdersStock($orderId,$outboundOrderNumber) {
			$order = OutboundOrder::findOne(["order_number"=>$outboundOrderNumber]);
			$stockRows = Stock::findAll(["outbound_order_id"=>$order->id]);
			foreach ($stockRows as $stockRow) {
				$orderStock = new OutboundSeparatorStock();
				$orderStock->outbound_separator_id = $orderId;
				$orderStock->stock_id = $stockRow->id;
				$orderStock->outbound_id = $stockRow->outbound_order_id;
				$orderStock->order_number = $outboundOrderNumber;
				$orderStock->out_box_barcode = $stockRow->box_barcode;
				$orderStock->product_id = $stockRow->product_id;
				$orderStock->product_sku = $stockRow->product_sku;
				$orderStock->product_barcode = $stockRow->product_barcode;
				$orderStock->status = Status::_NEW;
				$orderStock->status_to_out = Status::IN_BOX;
				$orderStock->stock_data = json_encode($stockRow->toArray());
				$orderStock->save(false);
			}

		return null;
	}

	public function setProductToOutFromOrder($orderId,$orderNumber,$box,$productBarcode) {
		$orderItem = OutboundSeparatorStock::findOne([
			"outbound_separator_id"=>$orderId,
			"order_number"=>$orderNumber,
			"out_box_barcode"=>$box,
			"product_barcode"=>$productBarcode,
			"status_to_out"=>Status::IN_BOX,
		]);
		if($orderItem) {
			$orderItem->status_to_out = Status::OUT_BOX;
			$orderItem->save(false);
		} else {
			echo $orderNumber." / ".$box." / ".$productBarcode;
			die("Ошибка нет товара в накладной");
		}
		return $orderItem;
	}
}