<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 8/5/14
 * Time: 6:17 PM
 */

namespace console\controllers;

use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\OutboundBox;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use yii\console\Controller;
use yii\helpers\VarDumper;

class FixController extends Controller
{
	public function actionMergeDuplicateProductInInboundItems()
	{
		// php yii fix/merge-duplicate-product-in-inbound-items
		 die("php yii fix/merge-duplicate-product-in-inbound-items");
		$absPathToFile = "intermode/fix/2025_06_06/inbound.csv";

		if (($handle = fopen($absPathToFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 1000, ";")) !== false) {
				$barcode = $data[0];
				$qty = $data[1];
				$barcode = trim($barcode);
				$qty = trim($qty);

				if (empty($qty) && empty($barcode)) {
					continue;
				}
				if (isset($clientDataByBox[$barcode])) {
					$clientDataByBox[$barcode] += $qty;
				} else {
					$clientDataByBox[$barcode] = $qty;
				}
			}
			$fileLogName = "x";
			file_put_contents($fileLogName . ".csv", print_r($clientDataByBox, true) . ";" . "\n", FILE_APPEND);
		}
		
		foreach ($clientDataByBox as $barcode=>$qty) {
			$items = InboundOrderItem::find()
					->andWhere([
						"inbound_order_id"=>"123032",
						"product_barcode"=>$barcode,
					])
					->all();

			if (empty($items)) {
				file_put_contents($fileLogName . "_p_not_find.csv", $barcode . ";".$qty. ";"."" . ";" . "\n", FILE_APPEND);
			}

			foreach ($items as $item) {
				if ($item->accepted_qty > 0) {
					$item->expected_qty = $qty;
				} else {
					$item->deleted = 1;
				}

			//	$item->save(false);


				file_put_contents($fileLogName . "_p2.csv", $item->product_barcode . ";".$item->expected_qty. ";".$item->accepted_qty . ";" . "\n", FILE_APPEND);
			}
		}
	}
}