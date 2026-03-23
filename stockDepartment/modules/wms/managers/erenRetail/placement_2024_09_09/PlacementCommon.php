<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

class PlacementCommon
{
	static function  makeSortAddress($secondary_address) {
		$sa = explode('-', trim($secondary_address));
		$stage = preg_replace('/[^0-9]/', '', $sa['0']); // этаж
		$row = preg_replace('/[^0-9]/', '', $sa['1']); // ряд
		$rack = preg_replace('/[^0-9]/', '',$sa['2']); // полка
		if (strlen($row) == 1) {
			$row = "0".$row;
		}
		return  $stage.$row.$rack;
	}
}