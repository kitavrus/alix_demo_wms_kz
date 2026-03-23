<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 19.10.2017
 * Time: 12:39
 */

namespace stockDepartment\modules\stock\service\RestoreProduct;


use common\components\BarcodeManager;
use common\modules\stock\models\Stock;
use Yii;

class RestoreProductService
{
	/**
	 *
	 */
	public function doAvailableProduct($id)
	{
		$asDatetimeFormat = 'php:d.m.Y H:i:s';
		$s = Stock::findOne($id);
		$s->status_availability = Stock::STATUS_AVAILABILITY_YES;
		$s->field_extra5 = "Восстановил сотрудник склада " . Yii::$app->formatter->asDatetime(time(), $asDatetimeFormat);
		$s->save(false);
		return $s;
	}

	/**
	 *
	 */
	public function doBlockedProduct($id)
	{
		$asDatetimeFormat = 'php:d.m.Y H:i:s';
		$s = Stock::findOne($id);
		$s->status_availability =  Stock::STATUS_AVAILABILITY_BLOCKED;
		$s->field_extra5 =  "Заблокировал сотрудник склада ". Yii::$app->formatter->asDatetime(time(), $asDatetimeFormat);
		$s->save(false);
		return $s;
	}
}