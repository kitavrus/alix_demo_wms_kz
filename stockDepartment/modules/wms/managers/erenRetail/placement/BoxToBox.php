<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

use common\modules\stock\models\Stock;
use common\modules\stock\service\ChangeAddressPlaceService;

class BoxToBox
{
	static function change($fromBox,$toBox) {


		// Если у коробки есть адрес
		$place = (new PlacementValidation())->getPlaceByBox($toBox);
		if ($place) {
			Stock::updateAll(
				[
					'primary_address'=>$toBox,
					'secondary_address'=>$place->secondary_address,
					'address_sort_order'=> PlacementCommon::makeSortAddress($place->secondary_address)
				],'primary_address = :pa',[
				':pa'=> $fromBox
			]);
		} else {
			// Если у короба нет адреса это пустой короб.
			$place = (new PlacementValidation())->getPlaceByBox($fromBox);
			Stock::updateAll(
				[
					'primary_address'=>$toBox,
					'secondary_address'=>$place->secondary_address,
					'address_sort_order'=> PlacementCommon::makeSortAddress($place->secondary_address)
				],'primary_address = :pa',[
				':pa'=> $fromBox
			]);
		}


		ChangeAddressPlaceService::add($fromBox,$toBox,"успешно");
	}

}