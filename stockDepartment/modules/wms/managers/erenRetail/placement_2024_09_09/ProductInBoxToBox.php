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
}