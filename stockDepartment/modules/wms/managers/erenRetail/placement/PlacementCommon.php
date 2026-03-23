<?php

namespace stockDepartment\modules\wms\managers\erenRetail\placement;

class PlacementCommon
{
	static function  makeSortAddress($secondary_address) {
		$sa = explode('-', trim($secondary_address));

		$stage = isset($sa[0]) ? preg_replace('/[^0-9]/', '', $sa[0]) : ''; // этаж
		$row = isset($sa[1]) ? preg_replace('/[^0-9]/', '', $sa[1]) : ''; // ряд
		$rack = isset($sa[2]) ? preg_replace('/[^0-9]/', '', $sa[2]) : ''; // полка
		$level = "";// isset($sa[3]) ? preg_replace('/[^0-9]/', '', $sa[3]) : ''; // уровень
		if (strlen($row) == 1) {
			$row = "0".$row;
		}
		return  intval($stage.$row.$rack.$level);
	}
}