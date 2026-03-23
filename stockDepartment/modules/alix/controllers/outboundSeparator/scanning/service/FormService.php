<?php
namespace stockDepartment\modules\alix\controllers\outboundSeparator\scanning\service;

use stockDepartment\modules\alix\controllers\outboundSeparator\entities\OutboundSeparator;
use stockDepartment\modules\alix\controllers\outboundSeparator\entities\OutboundSeparatorStock;

class FormService
{
	const STATUS_NEW = "_new";
	const STATUS_SCANNED = "scanned";
	const STATUS_IN_BOX = "in_box";
	const STATUS_OUT_BOX = "out_box";

	public function getActiveOutboundSeparator() {
		$items = [];
		foreach (OutboundSeparator::findAll(["status"=>self::STATUS_NEW]) as $item) {
			$items[$item->id] = $item->order_number." ( ".$item->comments." ) ";
		}
		return $items;
	}

	public function getInfoByOrder($orderId) {

		$countNotScanned = OutboundSeparatorStock::find()->select("count(product_barcode)")
									  ->andWhere([
										  "outbound_separator_id"=>$orderId,
										  "status_to_out"=>self::STATUS_OUT_BOX,
										  "status"=>self::STATUS_NEW,
									  ])
									  ->scalar();
		$countScanned = OutboundSeparatorStock::find()->select("count(product_barcode)")
									  ->andWhere([
										  "outbound_separator_id"=>$orderId,
										  "status_to_out"=>self::STATUS_OUT_BOX,
										  "status"=>self::STATUS_SCANNED,
									  ])
									  ->scalar();

		return [
			"countNotScanned"=>$countNotScanned,
			"countScanned"=>$countScanned,
		];
	}

	public function getInfoByInBoxBarcode($orderId,$inBoxBarcode) {
		$countScanned = OutboundSeparatorStock::find()->select("count(product_barcode)")
											  ->andWhere([
												  "outbound_separator_id"=>$orderId,
												  "in_box_barcode"=>$inBoxBarcode,
												  "status_to_out"=>self::STATUS_OUT_BOX,
												  "status"=>self::STATUS_SCANNED,
											  ])
											  ->scalar();

		return [
			"countScanned"=>$countScanned,
		];
	}

	public function getInfoByOutBoxBarcode($orderId,$outBoxBarcode) {
		$data = OutboundSeparatorStock::find()->select("order_number, out_box_barcode, product_barcode, status_to_out, status")
									  ->andWhere([
										  "outbound_separator_id"=>$orderId,
										  "out_box_barcode"=>$outBoxBarcode,
									  ])
									  ->asArray()
									  ->all();
		$result = [
			"total_in_box"=>count($data), // Должно быть в коробе
			"out_box_scanned"=>0, // Отсканированно
			"out_box_not_scanned"=>0, // Не отсканированны
			"items"=>[
				"in_box"=>[],
				"out_box"=>[],
			],
		];
		foreach ($data as $item) {
			if ($item["status_to_out"] == self::STATUS_OUT_BOX && $item["status"] == self::STATUS_SCANNED) {
				$result["out_box_scanned"] += 1;
			}

			if ($item["status_to_out"] == self::STATUS_OUT_BOX && $item["status"] == self::STATUS_NEW ) {
				$result["out_box_not_scanned"] += 1;
			}

			if ($item["status_to_out"] == self::STATUS_OUT_BOX) {
				$result["items"]["out_box"][] = $item;
			}

			if ($item["status_to_out"] == self::STATUS_IN_BOX) {
				$result["items"]["in_box"][] = $item;
			}
		}

		return $result;
	}

	public function scannedProductOnStock($orderId,$productBarcode,$outBoxBarcode,$inBoxBarcode) {

		$stock = OutboundSeparatorStock::find()
							  ->andWhere([
								  "outbound_separator_id"=>$orderId,
								  "product_barcode"=>$productBarcode,
								  "out_box_barcode"=>$outBoxBarcode,
								  "status_to_out"=>self::STATUS_OUT_BOX,
								  "status"=>self::STATUS_NEW,
							  ])
							  ->one();
		if($stock) {
			$stock->status = self::STATUS_SCANNED;
			$stock->in_box_barcode = $inBoxBarcode;
			$stock->save(false);
		}
	}

	public function canScannedProduct($orderId,$product_barcode,$outBoxBarcode) {
		return !OutboundSeparatorStock::find()
									  ->andWhere([
										  "outbound_separator_id"=>$orderId,
										  "product_barcode"=>$product_barcode,
										  "out_box_barcode"=>$outBoxBarcode,
										  "status_to_out"=>self::STATUS_OUT_BOX,
										  "status"=>self::STATUS_NEW,
									  ])
									  ->one();
	}
}