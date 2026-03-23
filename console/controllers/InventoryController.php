<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 8/5/14
 * Time: 6:17 PM
 */

namespace console\controllers;

use common\modules\outbound\models\OutboundBox;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use yii\console\Controller;
use yii\helpers\VarDumper;

class InventoryController extends Controller
{
	public function actionCompareByRow()
	{
		// php yii inventory/compare-by-row
		// die("php yii inventory/compare-by-row");

		//$l = "ground";
		$l = "4";
		$inventory_id = 20;
		$vers = "2";
		$pref = "_v".$vers;
		$absPathToFile = "intermode/2025-12-11/level_".$l."/v".$vers."/auditor.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 2;

		$products = [
			'шк товара',
			'их кол-во товара',
			'наш адрес',
			'наш короб',
			'наше кол-во товара в коробе',
			'общее кол-во товара в коробе',
		];

		$fileLogName = "intermode/2025-12-11/level_".$l."/v".$vers."/result_stock-level-".$l."-compare";
		$clientDataByBox = [];
		$clientTotalQty = 0;
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $products) . ";" . "\n", FILE_APPEND);
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('D' . $i)->getValue();
			$qty = $excelActive->getCell('E' . $i)->getValue();

			$box = trim($box);
			$barcode = ltrim(trim($barcode),"0");
			$qty = (integer)trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}

			//file_put_contents($fileLogName . $pref . ".csv", implode(";", [$box,$barcode,$qty]) . ";" . "\n", FILE_APPEND);
			// file_put_contents($fileLogName . $pref . "-print-r.csv", print_r($clientDataByBox,true) . "\n", FILE_APPEND);

			$clientTotalQty += $qty;
			if (isset($clientDataByBox[$barcode])) {
				$clientDataByBox[$barcode]["total_qty"] += $qty;
				if (isset($clientDataByBox[$barcode]["boxes"][$box])) {
					$clientDataByBox[$barcode]["boxes"][$box] += $qty;
				} else {
					$clientDataByBox[$barcode]["boxes"][$box] = $qty;
				}
			} else {
				$clientDataByBox[$barcode]["total_qty"] = $qty;
				$clientDataByBox[$barcode]["boxes"][$box] = $qty;
			}
		}

		file_put_contents($fileLogName. $pref . "_their.csv", print_r($clientDataByBox, true) . ";" . "\n", FILE_APPEND);
		//	die;
		$theirTotal = 0;
		$totalBarcode = 0;
		$theirData = [];
		foreach ($clientDataByBox as $barcode=>$data) {
			$totalBarcode++;
			$theirTotal += $data["total_qty"];
			//$theirData[(string)$barcode] = $data["total_qty"];
			$theirData[ltrim(trim($barcode),"0")] = $data["total_qty"];
		}
		file_put_contents($fileLogName. $pref . "_their.csv", print_r($theirData, true) . ";" . "\n", FILE_APPEND);
		//die;
		echo $theirTotal."\n";
		echo $totalBarcode."\n";
		echo "\n";
		echo "\n";
		// die;
		$ourTotal = 0;
		$totalBarcode = 0;
		$ourData = [];
		$scannedProductOnStocks = Stock::find()
									   ->select("product_barcode, COUNT(product_barcode) as qty")
									   ->andWhere([
										   "inventory_id" => $inventory_id,
										   "status_inventory" => 2,
									   ])
									   ->andWhere("secondary_address LIKE '".$l."-%'")
			//->andWhere("(`secondary_address` NOT LIKE '1-%' AND `secondary_address` NOT LIKE '2-%' AND `secondary_address` NOT LIKE '3-%' AND `secondary_address` NOT LIKE '4-%')")
			//->andWhere("product_barcode != '8681558491834'")
									   ->groupBy("product_barcode")
									   ->orderBy("address_sort_order")
									   ->asArray()
									   ->all();
		foreach ($scannedProductOnStocks as  $data) {
			$totalBarcode++;
			$ourTotal += $data["qty"];
			$ourData[ltrim(trim($data["product_barcode"]),"0")] = $data["qty"];
		}

		file_put_contents($fileLogName. $pref . "_our.csv", print_r($scannedProductOnStocks, true) . ";" . "\n", FILE_APPEND);
		file_put_contents($fileLogName. $pref . "_our.csv", print_r($ourData, true) . ";" . "\n", FILE_APPEND);

		$diffList1 = array_diff_assoc($theirData,$ourData);
		$diffList2 = array_diff_assoc($ourData,$theirData);
		//$diffList = array_merge($diffList1,$diffList2);
		$diffList = [];
		foreach ($diffList1 as  $product_barcode=>$qty) {
			$diffList[$product_barcode] = $qty;
		}
		foreach ($diffList2 as  $product_barcode=>$qty) {
			$diffList[$product_barcode] = $qty;
		}

		file_put_contents($fileLogName. $pref . "_diffwwwww1.csv", print_r($diffList1, true) . ";" . "\n", FILE_APPEND);
		file_put_contents($fileLogName. $pref . "_diffwwwww2.csv", print_r($diffList2, true) . ";" . "\n", FILE_APPEND);
		file_put_contents($fileLogName. $pref . "_diffwwwww4.csv", print_r($diffList, true) . ";" . "\n", FILE_APPEND);

		echo "\n";
		echo $ourTotal."\n";
		echo $totalBarcode."\n";
		//die;

		file_put_contents($fileLogName. $pref . "_diff.csv", print_r($diffList, true) . ";" . "\n", FILE_APPEND);
//die;
		foreach ($diffList as  $product_barcode=>$_) {
			$barcodeTo = $product_barcode;
			$theirQty = isset($theirData[$barcodeTo]) ? $theirData[$barcodeTo] : 0;
			$ourQty = isset($ourData[$barcodeTo]) ? $ourData[$barcodeTo] : 0;
			$productRow = [
				$barcodeTo,
				$theirQty,
				" ",
				" ",
				$ourQty,
			];
			file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

			$productInBox = Stock::find()
								 ->select("product_barcode, COUNT(product_barcode) as qty, primary_address, secondary_address")
								 ->andWhere([
									 "inventory_id" => $inventory_id,
									 "status_inventory" => 2,
									 //"product_barcode" => $product_barcode,
								 ])
								 ->andWhere("product_barcode LIKE '%".$product_barcode."%'")
								 ->andWhere("secondary_address LIKE '".$l."-%'")
				//->andWhere("(`secondary_address` NOT LIKE '1-%' AND `secondary_address` NOT LIKE '2-%' AND `secondary_address` NOT LIKE '3-%' AND `secondary_address` NOT LIKE '4-%')")
								 ->groupBy("product_barcode,primary_address, secondary_address")
								 ->orderBy("address_sort_order")
								 ->asArray()
								 ->all();
			if ($productInBox) {
				foreach ($productInBox as $product) {

					$countInBox = Stock::find()
									   ->andWhere([
										   "inventory_id" => $inventory_id,
										   "status_inventory" => 2,
										   "secondary_address" => $product["secondary_address"],
										   "primary_address" => $product["primary_address"],
									   ])
									   ->count();

					$productRow = [
						$product["product_barcode"],
						" ",
						$product["secondary_address"],
						$product["primary_address"],
						$product["qty"],
						$countInBox,
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			}

			if(isset($clientDataByBox[$product_barcode])) {
				foreach ($clientDataByBox[$barcodeTo]["boxes"] as $box=>$qtyInBox) {
					$productRow  = [
						$barcodeTo,
						$qtyInBox,
						$box,
						" ",
						" ",
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			}

			$productRow  = [
				" ",
				" ",
				" ",
				" ",
				" ",
			];
			file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
		}

		$productRow  = [
			$theirTotal,
			$ourTotal,
		];
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

	}

	public function actionCompareByRowOut()
	{
		// php yii inventory/compare-by-row-out
		// die("php yii inventory/compare-by-row");
		//$l = "ground";
//		$inventory_id = 20;
		$l = "out";
		$vers = "4";
		$pref = "_v".$vers;
		$absPathToFile = "intermode/2025-12-11/level_".$l."/v".$vers."/auditor.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$absPathToFile2 = "intermode/2025-12-11/level_".$l."/v".$vers."/text.xlsx";
		$excel2 = \PHPExcel_IOFactory::load($absPathToFile2);
		$excel2->setActiveSheetIndex(0);
		$excelActive2 = $excel2->getActiveSheet(0);
		$start = 2;
		$outOrders = [];
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive2->getCell('A' . $i)->getValue();
			$qty = $excelActive2->getCell('B' . $i)->getValue();

			$barcode = ltrim(trim($barcode),"0");
			$qty = (integer)trim($qty);

			if (empty($qty) && empty($barcode)) {
				continue;
			}
			if (isset($outOrders[$barcode])) {
				$outOrders[$barcode] += $qty;
			} else {
				$outOrders[$barcode] = $qty;
			}
		}


		$products = [
			'шк товара',
			'их кол-во товара',
			'наш адрес',
			'наш короб',
			'наше кол-во товара в коробе',
		];

		$fileLogName = "intermode/2025-12-11/level_".$l."/v".$vers."/result_stock-level-".$l."-compare";
		$clientDataByBox = [];
		$clientTotalQty = 0;
		$clientStoreArray = TLHelper::getStoreArrayByClientID();

		file_put_contents($fileLogName . $pref . ".csv", implode(";", $products) . ";" . "\n", FILE_APPEND);
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('C' . $i)->getValue();
			$qty = $excelActive->getCell('D' . $i)->getValue();

			$box = trim($box);
			$barcode = ltrim(trim($barcode),"0");
			$qty = (integer)trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}
			$clientTotalQty += $qty;
			if (isset($clientDataByBox[$barcode])) {
				$clientDataByBox[$barcode]["total_qty"] += $qty;
				if (isset($clientDataByBox[$barcode]["boxes"][$box])) {
					$clientDataByBox[$barcode]["boxes"][$box] += $qty;
				} else {
					$clientDataByBox[$barcode]["boxes"][$box] = $qty;
				}
			} else {
				$clientDataByBox[$barcode]["total_qty"] = $qty;
				$clientDataByBox[$barcode]["boxes"][$box] = $qty;
			}
		}

		file_put_contents($fileLogName. $pref . "_their.csv", print_r($clientDataByBox, true) . ";" . "\n", FILE_APPEND);
		//	die;
		$theirTotal = 0;
		$totalBarcode = 0;
		$theirData = [];
		foreach ($clientDataByBox as $barcode=>$data) {
			$totalBarcode++;
			$theirTotal += $data["total_qty"];
			$theirData[$barcode] = $data["total_qty"];
		}
		file_put_contents($fileLogName. $pref . "_their.csv", print_r($theirData, true) . ";" . "\n", FILE_APPEND);
		echo $theirTotal."\n";
		echo $totalBarcode."\n";
		echo "\n";
		echo "\n";
		$ourTotal = 0;
		$totalBarcode = 0;
		$ourData = [];
		$scannedProductOnStocks = Stock::find()
									   ->select("product_barcode, COUNT(product_barcode) as qty")
									   ->andWhere([
										   "outbound_order_id" => [
											   /*
   77933,
   77940,
   77941,
   77942,
   77979,
   77980,
   78010,
   78011,
   78012,
   78013,
   78014,
   78015,
   75452,
   */
											   //76819,
											   75451,
											   76267,
											   76280,
											   77268,
											   77267,
											   77266,
											   77265,
											   77264,
											   77246,
											   76989,
											   76990,
											   76991,
											   76992,
											   76993,
											   76994,
											   76995,
											   76996,
											   77015,
											   77977,
											   77978,



										   ],
									   ])
										->andWhere("(box_barcode != '400000059518' AND box_barcode != '400000060740')")
									   ->groupBy("product_barcode")
									   ->asArray()
									   ->all();
		foreach ($scannedProductOnStocks as  $data) {
			$totalBarcode++;
			$ourTotal += $data["qty"];
			$ourData[$data["product_barcode"]] = $data["qty"];
			//if (isset($outOrders[$data["product_barcode"]])) {
			//	$ourData[$data["product_barcode"]] += $outOrders[$data["product_barcode"]];
			//}
		}

		foreach ($outOrders as  $bar=>$q) {
			$totalBarcode++;
			$ourTotal += $q;
			if (isset($ourData[$bar])) {
				$ourData[$bar] += $q;
			} else {
				$ourData[$bar] = $q;
			}
		}



		file_put_contents($fileLogName. $pref . "_our.csv", print_r($scannedProductOnStocks, true) . ";" . "\n", FILE_APPEND);
		file_put_contents($fileLogName. $pref . "_our.csv", print_r($ourData, true) . ";" . "\n", FILE_APPEND);

		$diffList1 = array_diff_assoc($theirData,$ourData);
		$diffList2 = array_diff_assoc($ourData,$theirData);
		// $diffList = array_merge($diffList1,$diffList2);
		$diffList = [];
		foreach ($diffList1 as  $product_barcode=>$qty) {
			$diffList[$product_barcode] = $qty;
		}
		foreach ($diffList2 as  $product_barcode=>$qty) {
			$diffList[$product_barcode] = $qty;
		}

		echo "\n";
		echo $ourTotal."\n";
		echo $totalBarcode."\n";

		file_put_contents($fileLogName. $pref . "_diff.csv", print_r($diffList, true) . ";" . "\n", FILE_APPEND);
//die;
		foreach ($diffList as  $product_barcode=>$_) {
			$barcodeTo = $product_barcode;
			$theirQty = isset($theirData[$barcodeTo]) ? $theirData[$barcodeTo] : 0;
			$ourQty = isset($ourData[$barcodeTo]) ? $ourData[$barcodeTo] : 0;
			$productRow = [
				$barcodeTo,
				$theirQty,
				" ",
				" ",
				$ourQty,
			];
			file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

			$productInBox = Stock::find()
								 ->select("product_barcode, COUNT(product_barcode) as qty, box_barcode, outbound_order_id")
								 ->andWhere([
									 "outbound_order_id" => [
										 /*
	77933,
	77940,
	77941,
	77942,
	77979,
	77980,
	78010,
	78011,
	78012,
	78013,
	78014,
	78015,
	75452,
	*/

										 //76819,
										 75451,
										 76267,
										 76280,
										 77268,
										 77267,
										 77266,
										 77265,
										 77264,
										 77246,
										 76989,
										 76990,
										 76991,
										 76992,
										 76993,
										 76994,
										 76995,
										 76996,
										 77015,
										 77977,
										 77978,
									 ],
									 "product_barcode" => $product_barcode,
								 ])
								 ->andWhere("(box_barcode != '400000059518' AND box_barcode != '400000060740')")
								 ->groupBy("box_barcode")
								 ->asArray()
								 ->all();
			if ($productInBox) {
				foreach ($productInBox as $product) {
					$outboundOrder = OutboundOrder::findOne($product["outbound_order_id"]);
					$productRow = [
						$product["product_barcode"],
						" ",
						$clientStoreArray[$outboundOrder->to_point_id]. " / ".$outboundOrder->order_number,
						$product["box_barcode"],
						$product["qty"]
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			}

			if(isset($clientDataByBox[$product_barcode])) {
				foreach ($clientDataByBox[$barcodeTo]["boxes"] as $box=>$qtyInBox) {
					$productRow  = [
						$barcodeTo,
						$qtyInBox,
						$box,
						" ",
						" ",
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			}

			$productRow  = [
				" ",
				" ",
				" ",
				" ",
				" ",
			];
			file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
		}

		$productRow  = [
			$theirTotal,
			$ourTotal,
		];
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
	}


	public function actionCompareByRowDamage()
	{
		// php yii inventory/compare-by-row-damage
		// die("php yii inventory/compare-by-row");

		$l = "damage";
		$vers = "1";
		$pref = "_v".$vers;
		$absPathToFile = "intermode/2025-12-11/level_".$l."/v".$vers."/auditor.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 2;

		$products = [
			'шк товара',
			'их короб',
			'их кол-во товара',
			'наш короб',
			'наше кол-во товара',
		];

		$fileLogName = "intermode/2025-12-11/level_".$l."/v".$vers."/result_stock-level-".$l."-compare";
		$clientDataByBox = [];
		$clientTotalQty = 0;
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $products) . ";" . "\n", FILE_APPEND);
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('C' . $i)->getValue();
			$qty = $excelActive->getCell('D' . $i)->getValue();

			$box = trim($box);
			$barcode = ltrim(trim($barcode),"0");
			$qty = (integer)trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}

			$clientTotalQty += $qty;
			if (isset($clientDataByBox[$box])) {
				if (isset($clientDataByBox[$box][$barcode])) {
					$clientDataByBox[$box][$barcode] += $qty;
				} else {
					$clientDataByBox[$box][$barcode] = $qty;
				}
			} else {
				$clientDataByBox[$box][$barcode] = $qty;
			}
		}

		file_put_contents($fileLogName. $pref . "_their.csv", print_r($clientDataByBox, true) . ";" . "\n", FILE_APPEND);

		//START
		$absPathToFile = "intermode/2025-12-11/level_".$l."/v".$vers."/our_stock.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$ourDataByBox = [];
		$ourTotalQty = 0;
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('C' . $i)->getValue();
			$qty = $excelActive->getCell('D' . $i)->getValue();

			$box = trim($box);
			$barcode = ltrim(trim($barcode),"0");
			$qty = (integer)trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}

			$ourTotalQty += $qty;
			if (isset($ourDataByBox[$box])) {
				if (isset($ourDataByBox[$box][$barcode])) {
					$ourDataByBox[$box][$barcode] += $qty;
				} else {
					$ourDataByBox[$box][$barcode] = $qty;
				}
			} else {
				$ourDataByBox[$box][$barcode] = $qty;
			}
		}

		file_put_contents($fileLogName. $pref . "_our.csv", print_r($ourDataByBox, true) . ";" . "\n", FILE_APPEND);
		//FINISH

		$resultData = [];
		foreach ($clientDataByBox as $zbox=>$items) {
			$clientBox = OutboundBox::findOne(["client_box"=>$zbox]);
			$our_box = "-1";
			if ($clientBox) {
				$our_box = $clientBox->our_box;
			} else {
				echo $zbox."\n";
				file_put_contents($fileLogName. $pref . "_not_mapped.csv", print_r($zbox, true) . ";" . "\n", FILE_APPEND);
			}

			if (!isset($ourDataByBox[$our_box])) {
				file_put_contents($fileLogName. $pref . "_not_mapped.csv", print_r([$our_box,$zbox], true) . ";" . "\n", FILE_APPEND);
				continue;
			}

			foreach ($items as $barcode=>$qty) {
				if (!isset($ourDataByBox[$our_box][$barcode])) {
					$productRow = [
						$barcode,
						$zbox,
						$qty,
						$our_box,
						0,
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					continue;
				}
				if (isset($ourDataByBox[$our_box][$barcode])) {
					if ($ourDataByBox[$our_box][$barcode] != $qty ) {
						$productRow = [
							$barcode,
							$zbox,
							$qty,
							$our_box,
							$ourDataByBox[$our_box][$barcode],
						];
						file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
						continue;
					}
				}
			}
		}

		foreach ($ourDataByBox as $obox=>$items) {
			$cBox = OutboundBox::findOne(["our_box"=>$obox]);
			$c_box = "-1";
			if ($cBox) {
				$c_box = $cBox->client_box;
			} else {
				echo $obox."\n";
				file_put_contents($fileLogName. $pref . "_not_mapped_our.csv", print_r($obox, true) . ";" . "\n", FILE_APPEND);
			}

			if (!isset($clientDataByBox[$c_box])) {
				file_put_contents($fileLogName. $pref . "_not_mapped_our.csv", print_r([$our_box,$obox], true) . ";" . "\n", FILE_APPEND);
				continue;
			}

			foreach ($items as $barcode=>$qty) {
				if (!isset($clientDataByBox[$c_box][$barcode])) {
					$productRow = [
						$barcode,
						$c_box,
						0,
						$obox,
						$qty,
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					continue;
				}

				if (isset($clientDataByBox[$c_box][$barcode])) {
					if ($clientDataByBox[$c_box][$barcode] != $qty ) {
						$productRow = [
							$barcode,
							$c_box,
							$clientDataByBox[$c_box][$barcode],
							$obox,
							$qty,
						];
						file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
						continue;
					}
				}
			}
		}

		return 0;
	}

	public function actionCompareOurAndOtherInventoryCompanyOut()
	{
		// php yii inventory/compare-our-and-other-inventory-company-v3
		// die("php yii inventory/compare-our-and-other-inventory-company-v3");
//		$absPathToFile = "intermode/2025-05-27/4/level_v1.xlsx";
//		$absPathToFile = "intermode/2025-05-27/out/v2.xlsx";
		$absPathToFile = "intermode/2025-05-27/out/v9.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 2;
		$pref = "_v1";
		$products = [
			'шк товара',
			'их кол-во товара',
			'наш адрес',
			'наш короб',
			'наше кол-во товара в коробе',
		];

		$fileLogName = "intermode/2025-05-27/out/result/v9/stock-out-compare";
		$clientDataByBox = [];
		$clientTotalQty = 0;
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $products) . ";" . "\n", FILE_APPEND);
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('D' . $i)->getValue();
			$qty = $excelActive->getCell('F' . $i)->getValue();

			$box = trim($box);
			$barcode = trim($barcode);
			$qty = trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}
			$clientTotalQty += $qty;
			if (isset($clientDataByBox[$barcode])) {
				$clientDataByBox[$barcode]["total_qty"] += $qty;
				$clientDataByBox[$barcode]["boxes"][$box] += $qty;
			} else {
				$clientDataByBox[$barcode]["total_qty"] = $qty;
				$clientDataByBox[$barcode]["boxes"][$box] = $qty;
			}
		}

		file_put_contents($fileLogName. $pref . "_their.csv", print_r($clientDataByBox, true) . ";" . "\n", FILE_APPEND);
		//	die;

		$scannedProductOnStocks = Stock::find()
									   ->select("product_barcode, COUNT(product_barcode) as qty")
									   ->andWhere([
										   "outbound_order_id" => [
											   77933,
											   77940,
											   77941,
											   77942,
											   77977,
											   77978,
											   77979,
											   77980,
											   78010,
											   78011,
											   78012,
											   78013,
											   78014,
											   78015,
										   ],
									   ])
									   ->groupBy("product_barcode")
									   ->asArray()
									   ->all();
		file_put_contents($fileLogName. $pref . "_our.csv", print_r($scannedProductOnStocks, true) . ";" . "\n", FILE_APPEND);

		$ourTotalQty = 0;
		$clientStoreArray = TLHelper::getStoreArrayByClientID();
		foreach ($scannedProductOnStocks as  $product) {
			$ourTotalQty += $product["qty"];

			if(isset($clientDataByBox[$product["product_barcode"]])) {
				if($clientDataByBox[$product["product_barcode"]]["total_qty"] != $product["qty"]) {
//					$diff = $clientDataByBox[$product["product_barcode"]]["total_qty"] - $product["qty"];
//					if ($diff == $product["qty"]) {
//						continue;
//					}

					echo $product["product_barcode"],"\n";
					$productRow  = [
						$product["product_barcode"],
						$clientDataByBox[$product["product_barcode"]]["total_qty"],
						" ",
						" ",
						$product["qty"],
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					$productInBox = $this->getProductInOutBox($product["product_barcode"]);

					foreach ($productInBox as $product) {
						$outboundOrder = OutboundOrder::findOne($product["outbound_order_id"]);
						$productRow  = [
							$product["product_barcode"],
							" ",
							$clientStoreArray[$outboundOrder->to_point_id]. " / ".$outboundOrder->order_number,
							$product["box_barcode"],
							$product["qty"]
						];
						file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					}

					foreach ($clientDataByBox[$product["product_barcode"]]["boxes"] as $box=>$qtyInBox) {
						$productRow  = [
							$product["product_barcode"],
							$qtyInBox,
							$box,
							" ",
							" ",
						];
						file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					}

					$productRow  = [
						" ",
						" ",
						" ",
						" ",
						" ",
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			} else {
				echo $product["product_barcode"],"\n";


				$productRow  = [
					$product["product_barcode"],
					"0",
					"empty",
					"empty",
					"empty",
				];
				file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

				$productInBox = $this->getProductInOutBox($product["product_barcode"]);
				foreach ($productInBox as $product) {
					$outboundOrder = OutboundOrder::findOne($product["outbound_order_id"]);
					$productRow  = [
						$product["product_barcode"],
						" ",
						$clientStoreArray[$outboundOrder->to_point_id]. " / ".$outboundOrder->order_number,
						$product["box_barcode"],
						$product["qty"]
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
				$productRow  = [
					" ",
					" ",
					" ",
					" ",
					" ",
				];
				file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
			}
		}

		$productRow  = [
			$clientTotalQty,
			$ourTotalQty,
		];
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

	}


	public function actionCompareByRowGround()
	{
		// php yii inventory/compare-by-row-ground
		// die("php yii inventory/compare-by-row");
		$l = "ground";
		$absPathToFile = "intermode/2025-05-27/ground/v92.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 2;
		$pref = "_v9";
		$products = [
			'шк товара',
			'их кол-во товара',
			'наш адрес',
			'наш короб',
			'наше кол-во товара в коробе',
			'общее кол-во товара в коробе',
		];

		$fileLogName = "intermode/2025-05-27/".$l."/result/v9/stock-level-".$l."-compare";
		$clientDataByBox = [];
		$clientTotalQty = 0;
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $products) . ";" . "\n", FILE_APPEND);
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('D' . $i)->getValue();
			$qty = $excelActive->getCell('F' . $i)->getValue();

			$box = trim($box);
			$barcode = trim($barcode);
			$qty = trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}
			$clientTotalQty += $qty;
			if (isset($clientDataByBox[$barcode])) {
				$clientDataByBox[$barcode]["total_qty"] += $qty;
				$clientDataByBox[$barcode]["boxes"][$box] += $qty;
			} else {
				$clientDataByBox[$barcode]["total_qty"] = $qty;
				$clientDataByBox[$barcode]["boxes"][$box] = $qty;
			}
		}

		file_put_contents($fileLogName. $pref . "_their.csv", print_r($clientDataByBox, true) . ";" . "\n", FILE_APPEND);
	//	die;
		$theirTotal = 0;
		$totalBarcode = 0;
		$theirData = [];
		foreach ($clientDataByBox as $barcode=>$data) {
			$totalBarcode++;
			$theirTotal += $data["total_qty"];
			$theirData[$barcode] = $data["total_qty"];
		}
		file_put_contents($fileLogName. $pref . "_their.csv", print_r($theirData, true) . ";" . "\n", FILE_APPEND);
		echo $theirTotal."\n";
		echo $totalBarcode."\n";
		echo "\n";
		echo "\n";
		$ourTotal = 0;
		$totalBarcode = 0;
		$ourData = [];

		$scannedProductOnStocks = Stock::find()
									   ->select("product_barcode, COUNT(product_barcode) as qty")
									   ->andWhere([
										   "inventory_id" => 19,
										   "status_inventory" => 2,
									   ])
//									   ->andWhere("`inbound_order_id` != '122748'")
									   ->andWhere("(`secondary_address` NOT LIKE '1-%' AND `secondary_address` NOT LIKE '2-%' AND `secondary_address` NOT LIKE '3-%' AND `secondary_address` NOT LIKE '4-%')")
									   ->groupBy("product_barcode")
									   ->orderBy("address_sort_order")
									   ->asArray()
									   ->all();
		foreach ($scannedProductOnStocks as  $data) {
			$totalBarcode++;
			$ourTotal += $data["qty"];
			$ourData[$data["product_barcode"]] = $data["qty"];
		}

		file_put_contents($fileLogName. $pref . "_our.csv", print_r($scannedProductOnStocks, true) . ";" . "\n", FILE_APPEND);
		file_put_contents($fileLogName. $pref . "_our.csv", print_r($ourData, true) . ";" . "\n", FILE_APPEND);

		$diffList1 = array_diff_assoc($theirData,$ourData);
		$diffList2 = array_diff_assoc($ourData,$theirData);
		$diffList = array_merge($diffList1,$diffList2);

		echo "\n";
		echo $ourTotal."\n";
		echo $totalBarcode."\n";

		file_put_contents($fileLogName. $pref . "_diff.csv", print_r($diffList, true) . ";" . "\n", FILE_APPEND);
//die;
		foreach ($diffList as  $product_barcode=>$_) {
			$barcodeTo = $product_barcode;
			$theirQty = isset($theirData[$barcodeTo]) ? $theirData[$barcodeTo] : 0;
			$ourQty = isset($ourData[$barcodeTo]) ? $ourData[$barcodeTo] : 0;
			$productRow = [
				$barcodeTo,
				$theirQty,
				" ",
				" ",
				$ourQty,
			];
			file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

			$productInBox = Stock::find()
								 ->select("product_barcode, COUNT(product_barcode) as qty, primary_address, secondary_address")
								 ->andWhere([
									 "inventory_id" => 19,
									 "status_inventory" => 2,
									 "product_barcode" => $product_barcode,
								 ])
//								 ->andWhere("`inbound_order_id` != '122748'")
								 ->andWhere("(`secondary_address` NOT LIKE '1-%' AND `secondary_address` NOT LIKE '2-%' AND `secondary_address` NOT LIKE '3-%' AND `secondary_address` NOT LIKE '4-%')")
								 ->groupBy("product_barcode,primary_address, secondary_address")
								 ->orderBy("address_sort_order")
								 ->asArray()
								 ->all();
			if ($productInBox) {
				foreach ($productInBox as $product) {

					$countInBox = Stock::find()
									   ->andWhere([
										   "inventory_id" => 19,
										   "status_inventory" => 2,
										   "secondary_address" => $product["secondary_address"],
										   "primary_address" => $product["primary_address"],
									   ])
									   ->count();

					$productRow = [
						$product["product_barcode"],
						" ",
						$product["secondary_address"],
						$product["primary_address"],
						$product["qty"],
						$countInBox
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			}

			if(isset($clientDataByBox[$product_barcode])) {
				foreach ($clientDataByBox[$barcodeTo]["boxes"] as $box=>$qtyInBox) {
					$productRow  = [
						$barcodeTo,
						$qtyInBox,
						$box,
						" ",
						" ",
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			}

			$productRow  = [
				" ",
				" ",
				" ",
				" ",
				" ",
			];
			file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
		}

		$productRow  = [
			$theirTotal,
			$ourTotal,
		];
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

	}


	public function actionCompareOurAndOtherInventoryCompany()
	{
		// php yii inventory/compare-our-and-other-inventory-company
		// die("php yii inventory/compare-our-and-other-inventory-company");
		$absPathToFile = "intermode/2025-05-27/2/v9.xlsx";
//		$absPathToFile = "intermode/2025-05-27/ground/v9.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 2;
		$pref = "_v1";
		$products = [
			'шк товара',
			'их кол-во товара',
			'наш адрес',
			'наш короб',
			'наше кол-во товара в коробе',
		];

		$fileLogName = "intermode/2025-12-11/1/result/v1/stock-level-2-compare";
//		$fileLogName = "intermode/2025-05-27/ground/result/v9/stock-level-ground-compare";
		$clientDataByBox = [];
		$clientTotalQty = 0;
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $products) . ";" . "\n", FILE_APPEND);
		for ($i = $start; $i <= 99000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('D' . $i)->getValue();
			$qty = $excelActive->getCell('E' . $i)->getValue();

			$box = trim($box);
			$barcode = trim($barcode);
			$qty = trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}
			$clientTotalQty += $qty;
			if (isset($clientDataByBox[$barcode])) {
				$clientDataByBox[$barcode]["total_qty"] += $qty;
				$clientDataByBox[$barcode]["boxes"][$box] += $qty;
			} else {
				$clientDataByBox[$barcode]["total_qty"] = $qty;
				$clientDataByBox[$barcode]["boxes"][$box] = $qty;
			}
		}

		file_put_contents($fileLogName. $pref . "_their.csv", print_r($clientDataByBox, true) . ";" . "\n", FILE_APPEND);
	//	die;

//		$scannedBoxes= Stock::find()
//									   ->select("primary_address,secondary_address")
//									   ->andWhere([
//										   "inventory_id" => 19,
//										   "status_inventory" => 2,
//									   ])
//									   ->andWhere("(secondary_address LIKE '4-%')")
//									   ->groupBy("primary_address,secondary_address")
//									   ->asArray()
//									   ->all();
//
//		foreach ($scannedBoxes as $boxItem) {
//			$ex = OutboundBox::find()->andWhere(["our_box"=>$boxItem["primary_address"]])->one();
//			if (!$ex) {
//				echo $boxItem["primary_address"]."\n";
//				file_put_contents($fileLogName."_" . $pref . "_not_find_box.csv", $boxItem["primary_address"] . ";".$boxItem["secondary_address"] . ";" . "\n", FILE_APPEND);
//			}
//		}
//		die;

		$excludeProductBarcode = [
			"8681558491834", //9154шт(5 шт лишний)
			"8681558491308", //-(2шт лишний)
			"8681558491513", //-735 шт
			"4010481379287", //-468 шт
		];

		$scannedProductOnStocks = Stock::find()
									  ->select("product_barcode, COUNT(product_barcode) as qty")
									  ->andWhere([
										  "inventory_id" => 20,
										  "status_inventory" => 2,
									  ])
									  ->andWhere("secondary_address LIKE '1-%'")
//									  ->andWhere(["not in","product_barcode",$excludeProductBarcode])
//									   ->andWhere("(`secondary_address` NOT LIKE '1-%' AND `secondary_address` NOT LIKE '2-%' AND `secondary_address` NOT LIKE '3-%' AND `secondary_address` NOT LIKE '4-%')")
//			->andWhere("`inbound_order_id` != '122748'")
									   ->groupBy("product_barcode")
									  ->orderBy("address_sort_order")
									  ->asArray()
									  ->all();
		file_put_contents($fileLogName. $pref . "_our.csv", print_r($scannedProductOnStocks, true) . ";" . "\n", FILE_APPEND);

		$ourTotalQty = 0;
		foreach ($scannedProductOnStocks as  $product) {
			$ourTotalQty += $product["qty"];

			if(isset($clientDataByBox[$product["product_barcode"]])) {
				if($clientDataByBox[$product["product_barcode"]]["total_qty"] != $product["qty"]) {
					$diff = $clientDataByBox[$product["product_barcode"]]["total_qty"] - $product["qty"];
					if ($diff == $product["qty"]) {
						continue;
					}

					echo $product["product_barcode"],"\n";
					$productRow  = [
						$product["product_barcode"],
						$clientDataByBox[$product["product_barcode"]]["total_qty"],
						" ",
						" ",
						$product["qty"],
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					$productInBox = $this->getProductInBox($product["product_barcode"]);
					foreach ($productInBox as $product) {
						$productRow  = [
							$product["product_barcode"],
							" ",
							$product["secondary_address"],
							$product["primary_address"],
							$product["qty"]
						];
						file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					}

					foreach ($clientDataByBox[$product["product_barcode"]]["boxes"] as $box=>$qtyInBox) {
						$productRow  = [
							$product["product_barcode"],
							$qtyInBox,
							$box,
							" ",
							" ",
						];
						file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					}

					$productRow  = [
						" ",
						" ",
						" ",
						" ",
						" ",
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
				}
			} else {
					echo $product["product_barcode"],"\n";
					$productRow  = [
						$product["product_barcode"],
						"0",
						"empty",
						"empty",
						"empty",
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

					$productInBox = $this->getProductInBox($product["product_barcode"]);
					foreach ($productInBox as $product) {
						$productRow  = [
							$product["product_barcode"],
							" ",
							$product["secondary_address"],
							$product["primary_address"],
							$product["qty"]
						];
						file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
					}
					$productRow  = [
						" ",
						" ",
						" ",
						" ",
						" ",
					];
					file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);
			}
		}

		$productRow  = [
			$clientTotalQty,
			$ourTotalQty,
		];
		file_put_contents($fileLogName . $pref . ".csv", implode(";", $productRow) . ";" . "\n", FILE_APPEND);

	}


	private function getProductInBox($product_barcode) {

		$excludeProductBarcode = [
			"8681558491834", //9154шт(5 шт лишний)
			"8681558491308", //-(2шт лишний)
			"8681558491513", //-735 шт
			"4010481379287", //-468 шт
		];

		return Stock::find()
									   ->select("product_barcode, COUNT(product_barcode) as qty, primary_address, secondary_address")
									   ->andWhere([
										   "inventory_id" => 20,
										   "status_inventory" => 2,
										   "product_barcode" => $product_barcode,
									   ])
//									   ->andWhere("secondary_address LIKE '4-%'")
//									   ->andWhere(["not in","product_barcode",$excludeProductBarcode])
										->andWhere("(`secondary_address` NOT LIKE '1-%' AND `secondary_address` NOT LIKE '2-%' AND `secondary_address` NOT LIKE '3-%' AND `secondary_address` NOT LIKE '4-%')")
//										->andWhere("`inbound_order_id` != '122748'")
										->groupBy("product_barcode,primary_address, secondary_address")
									   ->orderBy("address_sort_order")
									   ->asArray()
									   ->all();
	}

	private function getProductInOutBox($product_barcode) {
		return Stock::find()
									   ->select("product_barcode, COUNT(product_barcode) as qty, box_barcode, outbound_order_id")
									   ->andWhere([
										   "outbound_order_id" => [
											   77933,
											   77940,
											   77941,
											   77942,
											   77977,
											   77978,
											   77979,
											   77980,
											   78010,
											   78011,
											   78012,
											   78013,
											   78014,
											   78015,
										   ],
										   "product_barcode" => $product_barcode,
									   ])
									   ->groupBy("box_barcode")
									   ->asArray()
									   ->all();
	}

	public function actionCompareOurAndOtherInventoryCompany_OLD()
	{
		// php yii inventory/compare-our-and-other-inventory-company
		// die("php yii inventory/compare-our-and-other-inventory-company");

		// $absPathToFile = "intermode/2025-05-27/stock-level-1.xlsx";
	//	$absPathToFile = "intermode/2025-05-27/stock-level-1_v2.xlsx";
		$absPathToFile = "intermode/2025-05-27/stock-level-1_v4.xlsx";
		$absPathToFile = "intermode/2025-05-27/stock-level-1_v5.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 1;
		$kpi_value_no = "no";
		$kpi_value_yes = "yes";
		$pref = "_v5";
		$products = [
			'product_barcode',
			'other_box',
			'other_product_qty',
			'our_box_barcode',
			'our_place_barcode',
			'our_product_qty',
			'diff_qty',
		];

		$excludeProductBarcode = [
			"8681558491834", //9154шт(5 шт лишний)
			"8681558491308", //-(2шт лишний)
			"8681558491513", //-735 шт
			"4010481379287", //-468 шт
		];

		$boxToOurBox = [];
		$clientDataByBox = [];
		$boxToBox = [];
		file_put_contents("stock-level-1-result".$pref.".csv",implode(";",$products).";"."\n",FILE_APPEND);
		for ($i = $start; $i <= 50000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$box = $excelActive->getCell('D' . $i)->getValue();
			$qty = $excelActive->getCell('F' . $i)->getValue();

			$box = trim($box);
			$barcode = trim($barcode);
			$qty = trim($qty);

			if (empty($box) && empty($barcode)) {
				continue;
			}

			$clientBox = OutboundBox::findOne(["client_box"=>$box,"created_user_id"=>1]);
			if (!$clientBox) {
				echo $box."\n";
				$boxToOurBox[$box] = $box;
			} else {
				$primaryAddress = $clientBox->our_box;
				$boxToBox[$box] = $primaryAddress;
			}

			if (isset($clientDataByBox[$box][$barcode])) {
				$clientDataByBox[$box][$barcode] += $qty;
			} else {
				$clientDataByBox[$box][$barcode] = $qty;
			}
		}
		file_put_contents("stock-level-1-result-map-box".$pref.".csv",implode("\n",$boxToBox).";"."\n",FILE_APPEND);
		file_put_contents("stock-level-1-result-full-log".$pref.".csv",print_r($clientDataByBox,true).";"."\n",FILE_APPEND);
//die;
		foreach ($clientDataByBox as $box=>$products) {
			$ourBoxBarcode = "empty";
			$ourPlaceBarcode = "empty";
			$ourProductQty = 0;
			$primaryAddress = isset($boxToBox[$box]) ? $boxToBox[$box] : "-1";

			foreach ($products as $productBarcode=>$productQt) {
				if ($primaryAddress == "-1") {
					$productRow = [
						$productBarcode, // 'barcode' =>
						$box, // 'box' =>
						$productQt, // 'box' =>
						$ourBoxBarcode, // 'our_box_barcode' =>
						$ourPlaceBarcode, // 'our_place_barcode' =>
						$ourProductQty, // 'stock_id' =>
						$productQt,
					];
					file_put_contents("stock-level-1-result".$pref.".csv",implode(";",$productRow).";"."\n",FILE_APPEND);
					continue;
				} else {
					$scannedProductOnStock = Stock::find()
												   ->select("product_barcode, primary_address, COUNT(product_barcode) as qty,secondary_address")
												   ->andWhere([
													   "inventory_id"=>19,
													   "status_inventory"=>2,
													   "product_barcode"=>$productBarcode,
													   "primary_address"=>$primaryAddress,
													   "kpi_value"=>$kpi_value_no,
												   ])
												   ->andWhere("secondary_address LIKE '1-%'")
												   ->groupBy("product_barcode, primary_address, secondary_address")
												   ->asArray()
												   ->one();
				}

				if ($scannedProductOnStock) {
						Stock::updateAll([
							"kpi_value"=>$kpi_value_yes,
						],[
							"product_barcode"=>$productBarcode,
							"primary_address"=>$scannedProductOnStock['primary_address'],
							"secondary_address"=>$scannedProductOnStock['secondary_address'],
							"status_inventory"=>2,
							"inventory_id"=>19,
						]);

					$productRow = [
						$productBarcode, // 'barcode' =>
						$box, // 'box' =>
						$productQt, // 'box' =>
						$scannedProductOnStock['primary_address'], // 'our_box_barcode' =>
						$scannedProductOnStock['secondary_address'], // 'our_place_barcode' =>
						$scannedProductOnStock["qty"], // 'stock_id' =>
						$productQt-$scannedProductOnStock["qty"],
					];
					file_put_contents("stock-level-1-result".$pref.".csv",implode(";",$productRow).";"."\n",FILE_APPEND);
				} else {
					$productRow = [
						$productBarcode, // 'barcode' =>
						$box, // 'box' =>
						$productQt, // 'box' =>
						"empty", // 'our_box_barcode' =>
						"empty", // 'our_place_barcode' =>
						"empty", // 'our_place_barcode' =>
						$productQt, // 'stock_id' =>
					];
					file_put_contents("stock-level-1-result".$pref.".csv",implode(";",$productRow).";"."\n",FILE_APPEND);
//					file_put_contents("stock-level-1-result-diff".$pref.".csv",implode(";",$productRow).";"."\n",FILE_APPEND);
				}
			}
		}

		echo "\n";
		echo "\n";
		file_put_contents("stock-level-1-result-map-box".$pref.".csv",implode("\n",$boxToBox).";"."\n",FILE_APPEND);
		file_put_contents("stock-level-1-result-not-find-box".$pref.".csv",implode("\n",$boxToOurBox).";"."\n",FILE_APPEND);
		// Это плюсовые товары которых нет в сканере внешней компании

		$scannedProductOnStockItems = Stock::find()->andWhere([
			"inventory_id"=>19,
			"status_inventory"=>2,
			"kpi_value"=>$kpi_value_no,
		])
	  ->andWhere("secondary_address LIKE '1-%'")
	  ->andWhere(["not in","product_barcode",$excludeProductBarcode])
	  ->all();
//
		foreach ($scannedProductOnStockItems as $item) {
			$ourBoxBarcode = $item->secondary_address;
			$ourPlaceBarcode = $item->primary_address;
			$ourProductBarcode = $item->product_barcode;
			$products = [
				 $ourProductBarcode,  // 'barcode' =>
				 "empty", // 'box' =>
				 "0", // 'box' =>
				 $ourBoxBarcode, // 'our_box_barcode' =>
				 $ourPlaceBarcode, // 'our_place_barcode' =>
				 1, // 'our_place_barcode' =>
				 1, // 'stock_id' =>
			];
			file_put_contents("stock-level-1-result-diff".$pref.".csv",implode(";",$products).";"."\n",FILE_APPEND);
		}
	}

	public function actionCheckPlusesInInvent()
	{
		// php yii inventory/check-pluses-in-invent
		//die("php yii inventory/check-pluses-in-invent");
		// $data = $this->loadPlusesFromFileXlsx("intermode/2025-05-27/add/pluses_inbound.xlsx");
		$data = $this->loadPlusesFromFileXlsx("intermode/2025-05-27/add/pluses_return.xlsx");

		foreach ($data as $box=>$products) {
			foreach ($products as $product) {
				$barcode = $product["barcode"];
				$findProductOnStock = Stock::find()->andWhere([
					"inventory_id"=>19,
					"status_inventory"=>1,
					"product_barcode"=>$barcode,
				])
				->one();

				if($findProductOnStock) {
					echo "YES " . $barcode." / ". $findProductOnStock->inventory_secondary_address." / ". $findProductOnStock->inventory_primary_address."\n";
				}
			}
		}
	}


	public function actionAddPlusesToInvent()
	{
		// php yii inventory/add-pluses-to-invent
		// die("php yii inventory/add-pluses-to-invent");
		// $absPathToFile = "intermode/2025-05-27/add/blocked_now.xlsx";
		// $absPathToFile = "intermode/2025-05-27/add/pluses.xlsx";
		// $absPathToFile = "intermode/2025-05-27/add/pluses_inbound.xlsx";
//		$absPathToFile = "intermode/2025-05-27/add/to_mosсow_to_stock.xlsx";
//		$absPathToFile = "intermode/2025-05-27/add/return_plus_v2.xlsx";
		$absPathToFile = "intermode/2025-12-11/add/v1.xlsx";
		$absPathToFile = "intermode/2025-12-11/add/mask_v2.xlsx";
		$data = $this->loadPlusesFromFileXlsx($absPathToFile);

//			print_r($data);
//			VarDumper::dump($data,10,true);
//			die("Стоп");
		$productCount = 0;
		$boxCount = 0;
		foreach ($data as $box=>$products) {
			$boxCount++;
			foreach ($products as $product) {
				$stock = new Stock();
				$stock->client_id = 103;
				$stock->inbound_order_id = 0;

				$stock->inventory_id = 0;
				$stock->status_inventory = 0;
				$stock->inventory_primary_address = "";
				$stock->inventory_secondary_address =  '';

				$stock->secondary_address = '100-1-01';
				$stock->product_barcode = $product["barcode"];
				$stock->primary_address = $box;
				$stock->inbound_client_box = "";
				$stock->box_barcode = '';
				$stock->box_size_barcode = '';
				$stock->box_kg = '';
				$stock->box_size_m3 = '';
				$stock->outbound_order_id = '0';
				$stock->outbound_picking_list_id = '0';
				$stock->outbound_picking_list_barcode = '';
				$stock->scan_out_datetime = '';
				$stock->scan_out_employee_id = '0';
				$stock->status = Stock::STATUS_INBOUND_CONFIRM;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
				$stock->condition_type = Stock::CONDITION_TYPE_UNDAMAGED;
				$stock->field_extra1 = "Plus20251211";
				$stock->field_extra2 = "v2Mask";
//				$stock->field_extra1 = "PlusDamage20251211";
				//if (!$stock->save(false)) {
				//	echo "NO " . $product['barcode'];
				//}
				$productCount++;
			}
		}

		print_r($data);
		$r = 'ok ProductCount = '.$productCount."\n";
		$r .= 'ok BoxCount = '.$boxCount."\n";
		echo $r;
		die;

		return 0;
	}

	public function actionComparePluses()
	{
		// php yii inventory/compare-pluses
		// die("php yii inventory/compare-pluses");
		$absPathToFile = "intermode/2025-05-27/add/return_plus_v2.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 1;
		$products1 = [];
		$products11 = [];
		for ($i = $start; $i <= 50000; $i++) {
			$box = $excelActive->getCell('A' . $i)->getValue();
			$barcode = $excelActive->getCell('B' . $i)->getValue();

			$box = trim($box);
			$barcode = trim($barcode);

			if (empty($box) && empty($barcode)) {
				continue;
			}
			$products1[$box][] = [
				'barcode' => $barcode,
			];

			if (isset($products11[$barcode])) {
				$products11[$barcode] += 1;
			} else {
				$products11[$barcode] = 1;
			}
		}

		$absPathToFile = "intermode/2025-05-27/add/return_plus_v2_az.xlsx";
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 1;
		$products2 = [];
		for ($i = $start; $i <= 50000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$qty = $excelActive->getCell('C' . $i)->getValue();

			$barcode = trim($barcode);
			$qty = trim($qty);

			if (empty($qty) && empty($barcode)) {
				continue;
			}

			if (isset($products[$barcode])) {
				$products2[$barcode] += $qty;
			} else {
				$products2[$barcode] = $qty;
			}
		}

		$diffList1 = array_diff_assoc($products11,$products2);
		$diffList2 = array_diff_assoc($products2,$products11);
		$diffList = array_merge($diffList1,$diffList2);

		echo count($products1)."\n";
		file_put_contents("ccc.log",print_r($diffList,true),FILE_APPEND);

	}


	private function loadPlusesFromFileXlsx($pathToFile) {
		$absPathToFile =  $pathToFile;
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 1;
		$lastBox = "";
		for ($i = $start; $i <= 50000; $i++) {
			$box = $excelActive->getCell('A' . $i)->getValue();
			$barcode = $excelActive->getCell('B' . $i)->getValue();

			$box = trim($box);
			$barcode = trim($barcode);

			if (empty($box) && empty($barcode)) {
				continue;
			}
			if (!empty($box)) {
				$lastBox = $box;
			}

			$products[$lastBox][] = [
				'barcode' => $barcode,
			];
		}

		return $products;
	}
}