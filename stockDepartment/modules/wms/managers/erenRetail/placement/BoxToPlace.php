<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;
use common\modules\stock\models\Stock;
use common\modules\stock\service\ChangeAddressPlaceService;

class BoxToPlace
{
	 static function change($fromBox,$toPlace) {
		Stock::updateAll(
			[
				'secondary_address'=>$toPlace,
				'address_sort_order'=> PlacementCommon::makeSortAddress($toPlace)
			],'primary_address = :pa',[
				':pa'=>$fromBox
			]);
		 ChangeAddressPlaceService::add($fromBox,$toPlace,"успешно");
	}
}