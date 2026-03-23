<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

use common\modules\stock\models\Stock;
use common\modules\stock\service\ChangeAddressPlaceService;

class ProductInBoxToBox
{
	static function change($fromBox, $productBarcode,$toBox) {
		$p  = new PlacementValidation();
		$place = $p->getPlaceByBox($toBox);

		$productStockOne = Stock::find()->andWhere([
			'product_barcode'=>$productBarcode,
			'id'=>$p->getNotEmptyBoxIdsQuery($fromBox)
		])->one();

		$placeAddress = "";
		if(!empty($place)) {
			$placeAddress = $place->secondary_address;
		} else {
			$placeAddress = $productStockOne->secondary_address;
		}

		if($productStockOne) {
			$productStockOne->primary_address = $toBox;
			$productStockOne->secondary_address = $placeAddress;
			$productStockOne->address_sort_order= PlacementCommon::makeSortAddress($placeAddress);
			$productStockOne->save(false);
			ChangeAddressPlaceService::addWithProduct($fromBox,$productBarcode,$toBox,"успешно $productBarcode");
		} else {
			ChangeAddressPlaceService::addWithProduct($fromBox,$productBarcode,$toBox,"товар не найден $productBarcode");
		}
	}

	static function getCountProductInBox($boxBarcode) {
		return  Stock::find()
			->andWhere([
				'primary_address'=>$boxBarcode,
				'status_availability'=>[
					Stock::STATUS_AVAILABILITY_NOT_SET,
					Stock::STATUS_AVAILABILITY_YES,
//					Stock::STATUS_AVAILABILITY_NO
				],
				'status'=>[
					Stock::STATUS_INBOUND_NEW,
					Stock::STATUS_INBOUND_SCANNED,
					Stock::STATUS_INBOUND_OVER_SCANNED,
					Stock::STATUS_INBOUND_CONFIRM,
					Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API,
				]])->count();
				
	}

	static function getProductInBox($boxBarcode) {
		return Stock::find()->select("product_barcode, count(*) as qtyProduct")
			->andWhere([
				'primary_address'=>$boxBarcode,
				'status_availability'=>[
					Stock::STATUS_AVAILABILITY_NOT_SET,
					Stock::STATUS_AVAILABILITY_YES,
//					Stock::STATUS_AVAILABILITY_NO
				],
				'status'=>[
					Stock::STATUS_INBOUND_NEW,
					Stock::STATUS_INBOUND_SCANNED,
					Stock::STATUS_INBOUND_OVER_SCANNED,
					Stock::STATUS_INBOUND_CONFIRM,
					Stock::STATUS_INBOUND_PREPARED_DATA_FOR_API,
				]])
		->groupBy("product_barcode")
		->orderBy(["updated_at"=>SORT_DESC])
		->asArray()
		->all();
	}
}