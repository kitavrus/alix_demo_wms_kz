<?php

namespace app\modules\intermode\controllers\ecommerce\outbound\domain\constants;

use common\modules\stock\models\Stock;
use yii\helpers\ArrayHelper;

class OutboundStatus
{
	const OUT_OF_STOCK = 45; // Нет на складе
	const REPEAT_ORDER = 46; // Повторный заказ

	public static function getReadyForScanning()
	{
		return [
			Stock::STATUS_NOT_SET,
			Stock::STATUS_OUTBOUND_NEW,
			Stock::STATUS_OUTBOUND_PART_RESERVED,
			Stock::STATUS_OUTBOUND_FULL_RESERVED,
			Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
		];
	}
	
public static function getRepeatOrder()
{
	return self::REPEAT_ORDER;
}

public static function getOutOfStock()
{
	return self::OUT_OF_STOCK;
}

public static function getCANCEL() {
	return Stock::STATUS_OUTBOUND_CANCEL;
}

public static function getPACKED() {
	return Stock::STATUS_OUTBOUND_PACKED;
}

public static function getPRINTED_PICKING_LIST() {
	return Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST;
}

public static function getSCANNING() {
	return Stock::STATUS_OUTBOUND_SCANNING;
}

public static function getSCANNED() {
	return Stock::STATUS_OUTBOUND_SCANNED;
}
public static function getNEW() {
	return Stock::STATUS_OUTBOUND_NEW;
}

public static function getRESERVING() {
	return Stock::STATUS_OUTBOUND_RESERVING;
}

	public static function getPrintBoxOnStock() {
		return [
			Stock::STATUS_OUTBOUND_SCANNED,
			Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
			Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
		];
	}

	public static function getOrdersForPrintPickList()
	{
		return [
			Stock::STATUS_OUTBOUND_NEW,
			Stock::STATUS_OUTBOUND_FULL_RESERVED,
			Stock::STATUS_OUTBOUND_PART_RESERVED,
			Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
		];
	}

	public static function getNotDoneOrders()
	{
		return [
			Stock::STATUS_OUTBOUND_NEW,
			Stock::STATUS_OUTBOUND_FULL_RESERVED,
			Stock::STATUS_OUTBOUND_PART_RESERVED,
			Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
			Stock::STATUS_OUTBOUND_SCANNING,
			Stock::STATUS_OUTBOUND_SCANNED,
		];
	}

	/**
	 * @param string $lang
	 * @return array Массив с статусами.
	 */
	public static function getAll($lang = null)
	{
		$statusArray = [
			Stock::STATUS_NOT_SET => \Yii::t('stock/titles', 'Not set'),
			Stock::STATUS_OUTBOUND_NEW => \Yii::t('stock/titles', 'Outbound new'),
			Stock::STATUS_OUTBOUND_CANCEL => \Yii::t('stock/titles', 'Canceled by client'),
			Stock::STATUS_OUTBOUND_RESERVING => \Yii::t('stock/titles', 'Process reserving'),
			Stock::STATUS_OUTBOUND_FULL_RESERVED => \Yii::t('stock/titles', 'Full reserved'),//разные
			Stock::STATUS_OUTBOUND_PART_RESERVED => \Yii::t('stock/titles', 'Part reserved'),//разные
			Stock::STATUS_OUTBOUND_SCANNING => \Yii::t('stock/titles', 'Scanning (outbound)'),//один
			Stock::STATUS_OUTBOUND_SCANNED => \Yii::t('stock/titles', 'Scanned (outbound)'),//один
			Stock::STATUS_OUTBOUND_PACKED => \Yii::t('stock/titles', 'Packed'),
			Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL => \Yii::t('stock/titles', 'Print box label'), //выделить ярким цветом  напечатали
			Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL => \Yii::t('stock/titles', 'Printing box label'), //выделить ярким цветом  печатаются
			self::OUT_OF_STOCK => \Yii::t('stock/titles', 'Out of stock'),
			self::REPEAT_ORDER => \Yii::t('stock/titles', 'Repeat order'),
			Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST => \Yii::t('stock/titles', 'Распечатали лист сборки')
		];
		
		return $statusArray;
	}

	/**
	 * @param int $status
	 * @param string $lang
	 * @return string Читабельный статус поста.
	 */
	public static function getValue($status = null, $lang = null)
	{
		return ArrayHelper::getValue(self::getAll($lang), $status);
	}

	/*
	  * Высчитывает разницу между датами в рабочих днях
	  * @param mixed $start дата нaчала
	  * @param mixed $end дата конца
	  * @return int
	  **/
	public static function getStockGridColor($status)
	{
		$class = 'color-tan';
		switch ($status) {
			case Stock::STATUS_OUTBOUND_NEW: //#FFA54F
				$class = 'color-dodger-blue';
				break;
			case Stock::STATUS_OUTBOUND_FULL_RESERVED: //#FFA500
				$class = 'color-khaki';
				break;
			case Stock::STATUS_OUTBOUND_PACKED: //#FFA500
				$class = 'color-dark-olive-green';
				break;
			case Stock::STATUS_OUTBOUND_RESERVING: //#FFF68F
				$class = 'color-khaki';
				break;
			case Stock::STATUS_OUTBOUND_PART_RESERVED: //#CAFF70
				$class = 'color-add-route';
				break;
			case Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST: //#87CEFA
				$class = 'color-light-sky-blue';
				break;
			case Stock::STATUS_OUTBOUND_SCANNING: //#1E90FF
				$class = 'color-dodger-blue';
				break;
			case Stock::STATUS_OUTBOUND_SCANNED: //#FFFFE0
				$class = 'color-add-car';
				break;
			case Stock::STATUS_OUTBOUND_CANCEL://#C6E2FF
				$class = 'color-light-yellow';
			//$class = 'color-dark-olive-green';
				break;
			case self::OUT_OF_STOCK://#C6E2FF
				//$class = 'color-slate-gray';
				$class = 'color-indian-red';

				break;
			default:
				$class = '';
				break;
		}

		return $class;
	}
}