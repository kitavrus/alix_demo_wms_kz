<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 25.09.2017
 * Time: 9:22
 */

namespace common\modules\stock\service;

use common\modules\stock\models\ChangeAddressPlace;

class ChangeAddressPlaceService
{
	public static function isNotExist($fromBarcode,$toBarcode)
	{
		if(!empty($fromBarcode) && empty($toBarcode)) {
			return true;
		}

		return !ChangeAddressPlace::find()->andWhere([
			'from_barcode'=>$fromBarcode,
			'to_barcode'=>$toBarcode,
		])->exists();
	}

	public static function add($fromBarcode,$toBarcode,$message = '')
	{
		if(!empty($fromBarcode) && empty($toBarcode) && empty($message)) {
			return false;
		}

		$cap = new ChangeAddressPlace();
		$cap->from_barcode = $fromBarcode;
		$cap->to_barcode = $toBarcode;
		$cap->message = $message;
		return $cap->save(false);
	}
	
	public static function addWithProduct($fromBarcode,$productBarcode,$toBarcode,$message = '')
	{
		if(!empty($fromBarcode) && empty($toBarcode) && empty($message)) {
			return false;
		}

		$cap = new ChangeAddressPlace();
		$cap->from_barcode = $fromBarcode;
		$cap->product_barcode = $productBarcode;
		$cap->to_barcode = $toBarcode;
		$cap->message = $message;
		return $cap->save(false);
	}
}