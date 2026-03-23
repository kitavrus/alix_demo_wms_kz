<?php

namespace stockDepartment\modules\other\controllers;


use common\modules\stock\models\Inventory;

use common\modules\client\models\Client;
use common\modules\stock\models\Stock;
use yii\helpers\VarDumper;

class InventoryController extends \stockDepartment\components\Controller
{
	private $inventoryId = 16;
	private $clientId = Client::CLIENT_DEFACTO;
	private $stockStatusAvailabilityYES = Stock::STATUS_AVAILABILITY_YES;

	private $statusInventoryLost = [
		Inventory::STATUS_SCAN_NO,
		Inventory::STATUS_SCAN_PROCESS
	];

	private $statusInventoryYes = [
		Inventory::STATUS_SCAN_YES,
	];

	/*
	* проверяем в ненайденных лотов после инвентори товары которые в плюсах
	* */
	public function actionIndex()
	{
		// /other/inventory/index

		$products = $this->findAllNotScannedLots();

		return $this->render("index", ["dataForVarDumper" => $products]);
	}

	/*
	 * после инвентори  проверяем в ненайденных лотах товары которые в плюсах
	 * */
	public function actionFindInInventoryLostPluses()
	{
		// /other/inventory/find-in-inventory-lost-pluses

		$products = $this->loadPlusesFromFile('/web/add-product/b2b/inventory/22-12-2022/24_12_2022_pluses.xlsx');
		foreach ($products as $k => $barcode) {
			$products[$k]['lcBarcode'] = $this->findOneLcByLotBarcode($barcode['lotBarcode']);
			$products[$k]['isExist'] = $this->findOneLotInLost($barcode['lotBarcode']);
		}

		return $this->render("index", ["dataForVarDumper" => $products]);
	}

	public function actionAddInventoryPlusesLot()
	{
		// /other/inventory/add-inventory-pluses-lot
		die("Все добавлено успешно инвент 16");

		$products = $this->loadPlusesFromFile('/web/add-product/b2b/inventory/22-12-2022/24_12_2022_pluses.xlsx');

		foreach ($products as $k => $barcode) {
			$products[$k]['lcBarcode'] = $this->findOneLcByLotBarcode($barcode['lotBarcode']);
//            $products[$k]['lotBarcode'] = Stock::find()->select('product_barcode')->andWhere(['client_id'=>2,'inbound_client_box'=>$barcode['lcBarcode']])->scalar();
			//$products[$k]['lotBarcode'] = ReturnOrderItemProduct::find()->select('product_barcode')->andWhere(['client_box_barcode'=>$barcode['lcBarcode']])->scalar();
		}

		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
			$stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
			$stock->system_status = 'inventory-plus-20221222';
			$stock->system_status_description = 'это плюсы после инвентаризации 2022 12 22';
//
            if (!$stock->save(false)) {
                echo "NO " . $barcode['lotBarcode'];
            }
//			echo $barcode['lotBarcode']."<br />";
		}

		VarDumper::dump($products, 10, true);
//		return $this->render("index", ["dataForVarDumper" => $products]);
		die;
	}

	public function actionBlockedLostLot()
	{
		// /other/inventory/blocked-lost-lot

		$products = $this->findAllNotScannedLots();

		foreach ( $products as $product) {
			$product->status_availability = Stock::STATUS_AVAILABILITY_BLOCKED;
			$product->system_status = 'inventory-lost-20221222';
			$product->system_status_description = 'это не найденные лоты после инвентаризации 2022 12 22';
//
            if (!$product->save(false)) {
                echo "NO " . $product->id;
            }
		}

		VarDumper::dump($products, 10, true);
//		return $this->render("index", ["dataForVarDumper" => $products]);
		die;
	}

	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
	/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

	private function findAllNotScannedLots()
	{
		return Stock::find()->andWhere([
			'inventory_id' => $this->inventoryId,
			'client_id' => $this->clientId,
			'status_availability' => $this->stockStatusAvailabilityYES,
			'status_inventory' => $this->statusInventoryLost,
		])
		->all();
	}

	private function findOneLcByLotBarcode($lotBarcode) {
		return (string)Stock::find()
							->select('inbound_client_box')
							->andWhere(['client_id' => $this->clientId, 'product_barcode' =>$lotBarcode])->scalar();
	}

	private function findOneLotInLost($lotBarcode) {
		return Stock::find()->andWhere([
			'inventory_id' => $this->inventoryId,
			'client_id' => $this->clientId,
			'status_availability' => $this->stockStatusAvailabilityYES,
			'status_inventory' => $this->statusInventoryLost,
		])
					->andWhere(['product_barcode' => $lotBarcode])
					->asArray()
					->one();
	}

	private function loadPlusesFromFile($pathToFile) {
		$absPathToFile= \Yii::getAlias('@stockDepartment') . $pathToFile;

			$excel = \PHPExcel_IOFactory::load($absPathToFile);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet(1);

			$start = 1;
			for ($i = $start; $i <= 500; $i++) {
				$boxBarcode = $excelActive->getCell('A' . $i)->getValue();
				$lotBarcode = $excelActive->getCell('B' . $i)->getValue();
				$lcBarcode = '-1';

				$boxBarcode = trim($boxBarcode);
				$lotBarcode = trim($lotBarcode);
				$lcBarcode = trim($lcBarcode);

				if (empty($boxBarcode) || empty($lcBarcode) || empty($lotBarcode)) {
					continue;
				}

				$products [] = [
					'boxBarcode' => $boxBarcode,
					'lotBarcode' => $lotBarcode,
					'lcBarcode' => $lcBarcode,
				];
			}

		return $products;
	}

	/*	public function actionAddInventoryPlus()
	{
		// /other/one/add-inventory-plus

		die("actionAddInventoryPlus - die begin");

//        $pathToCSVFile = 'tmp-file/defacto/07-01-2017-inventory/report-plus.csv';
//        $pathToCSVFile = 'tmp-file/defacto/03-11-2017-inventory/add-plus-to-stock.csv';
		$pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/plus-inventory-2018-11-30.csv';
		$rowAndBoxes = [];
		$rowLast = '';
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, ";")) !== false) {
				if (!empty($data[0])) {
					$rowLast = trim($data[0]);
				}

				if (!empty($data[1])) {
					if (!isset($rowAndBoxes[$rowLast][trim($data[1])])) {
						$rowAndBoxes[$rowLast][trim($data[1])] = 1;
					} else {
						$rowAndBoxes[$rowLast][trim($data[1])] += 1;
					}
				}
			}
		}


		foreach ($rowAndBoxes as $box => $products) {
			foreach ($products as $barcode => $qty) {
				for ($i = 1; $i <= $qty; ++$i) {
					$stock = new Stock();
					$stock->client_id = Client::CLIENT_DEFACTO;
					$stock->secondary_address = '';
					$stock->product_barcode = $barcode;
					$stock->primary_address = $box;
					$stock->box_barcode = '';
					$stock->box_size_barcode = '';
					$stock->box_kg = '';
					$stock->box_size_m3 = '';
					$stock->outbound_order_id = '0';
					$stock->outbound_picking_list_id = '0';
					$stock->outbound_picking_list_barcode = '';
					$stock->scan_out_datetime = '';
					$stock->scan_out_employee_id = '0';
					$stock->status = Stock::STATUS_NOT_SET;
					$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
					$stock->system_status = 'inventory-plus-20181130';
					$stock->system_status_description = 'это плюсы после инвентаризации';
					//$stock->save(false);
				}
			}
		}

		VarDumper::dump($rowAndBoxes, 10, true);
		die;
	}

	public function actionAddInventoryPlusV1()
	{
		// /other/one/add-inventory-plus-v1

		die("actionAddInventoryPlus V 1 - die begin");

//        $pathToCSVFile = 'tmp-file/defacto/07-01-2017-inventory/report-plus.csv';
//        $pathToCSVFile = 'tmp-file/defacto/03-11-2017-inventory/add-plus-to-stock.csv';
//        $pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/plus-inventory-2018-11-30.csv';
//        $pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/lost-inventory2018-11-30.csv';
//        $pathToCSVFile = 'tmp-file/defacto/30-11-2018-inventory/in-order-but-in-inventory2018-11-30.csv';
//


		$pathToCSVFile = 'tmp-file/defacto/09-12-2019-inventory/plus-09-12-2019.csv';
		$pathToCSVFile = 'tmp-file/defacto/09-12-2019-inventory/addition-pluses-09-12-2019-4-plus.csv';

		$products = [];
		if (($handle = fopen($pathToCSVFile, "r")) !== false) {
			while (($data = fgetcsv($handle, 70000, ";")) !== false) {
				$products [] = [
					'boxBarcode' => trim($data[0]),
					'lotBarcode' => trim($data[1]),
					'lcBarcode' => trim(''),
				];
			}
		}

		array_shift($products);


		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
			$stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
			$stock->system_status = 'inventory-plus-20191206';
			$stock->system_status_description = 'это плюсы после инвентаризации 4доп товара';
//            if (!$stock->save(false)) {
//                echo "NO " . $barcode['lotBarcode'];
//            }
		}

		VarDumper::dump($products, 10, true);
		die;
	}

	public function actionAddInventoryPlusesReturn()
	{
		// /other/one/add-inventory-pluses-return
		//die("actionAddInventoryPlus V 3 - die begin");

		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/16-02-2022/plus_after_inventory_16_02_2022.xlsx';

		$products = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(1);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 81; $i++) {
				$boxBarcode = $excelActive->getCell('A' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('B' . $i)->getValue();
//                $lotBarcodeOrSkuId = $excelActive->getCell('C' . $i)->getValue();
				$lotBarcodeOrSkuId = "-1";

				$boxBarcode = trim($boxBarcode);
				$lotBarcodeOrSkuId = trim($lotBarcodeOrSkuId);
				$lcBarcode = trim($lcBarcode);

				if (empty($boxBarcode) || empty($lcBarcode) || empty($lotBarcodeOrSkuId)) {
					continue;
				}

				$products [] = [
					'boxBarcode' => $boxBarcode,
					'lotBarcode' => "",
					'lcBarcode' => $lcBarcode,
				];
			}
		}

		foreach ($products as $k => $barcode) {
//            $products[$k]['lcBarcode'] = (string)Stock::find()->select('inbound_client_box')->andWhere(['client_id'=>2,'product_barcode'=>$barcode['lotBarcode']])->scalar();
//            $products[$k]['lotBarcode'] = Stock::find()->select('product_barcode')->andWhere(['client_id'=>2,'inbound_client_box'=>$barcode['lcBarcode']])->scalar();
			$products[$k]['lotBarcode'] = ReturnOrderItemProduct::find()->select('product_barcode')->andWhere(['client_box_barcode' => $barcode['lcBarcode']])->scalar();
		}

		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
			$stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
			$stock->system_status = 'inventory-20220216-lost';
			$stock->system_status_description = 'не нашли после инвентаризации 2022 02 16';
//            if (!$stock->save(false)) {
//                echo "NO " . $barcode['lotBarcode'];
//            }
			echo $barcode['lotBarcode'] . "<br />";
		}

		VarDumper::dump($products, 10, true);
		die;
	}

	public function actionAddInventoryReturn()
	{
		// /other/one/add-inventory-return

		die("other/one/add-inventory-return - die begin");

		$rootPathList = [];
		$rootPathList[] = \Yii::getAlias('@stockDepartment') . '/web/add-product/b2b/inventory/22-02-2021/add-no-fb-return.xlsx';

		$products = [];
		foreach ($rootPathList as $rootPath) {

			$excel = \PHPExcel_IOFactory::load($rootPath);
			$excel->setActiveSheetIndex(0);
			$excelActive = $excel->getActiveSheet();

			$start = 1;
			for ($i = $start; $i <= 76; $i++) {
				$ttn = $excelActive->getCell('A' . $i)->getValue();
				$boxBarcode = $excelActive->getCell('B' . $i)->getValue();
				$lcBarcode = $excelActive->getCell('C' . $i)->getValue();
				$placeAddress = $excelActive->getCell('D' . $i)->getValue();
				$lotBarcode = '';

				$boxBarcode = trim($boxBarcode);
				$lotBarcode = trim($lotBarcode);
				$lcBarcode = trim($lcBarcode);
				$placeAddress = trim($placeAddress);

//                if(empty($boxBarcode) || empty($lotBarcode)) {
//                    continue;
//                }
				$products [] = [
					'boxBarcode' => $boxBarcode,
					'lotBarcode' => $lotBarcode,
					'lcBarcode' => $lcBarcode,
					'placeAddress' => $placeAddress,
				];
			}
		}

		foreach ($products as $k => $barcode) {
			$products[$k]['lotBarcode'] = Stock::find()->select('product_barcode')->andWhere(['client_id' => 2, 'inbound_client_box' => $barcode['lcBarcode']])->scalar();
		}

		foreach ($products as $barcode) {
			$stock = new Stock();
			$stock->client_id = Client::CLIENT_DEFACTO;
//            $stock->secondary_address = '';
			$stock->product_barcode = $barcode['lotBarcode'];
			$stock->primary_address = $barcode['boxBarcode'];
			$stock->secondary_address = $barcode['placeAddress'];
			$stock->inbound_client_box = $barcode['lcBarcode'];
			$stock->box_barcode = '';
			$stock->box_size_barcode = '';
			$stock->box_kg = '';
			$stock->box_size_m3 = '';
			$stock->outbound_order_id = '0';
			$stock->outbound_picking_list_id = '0';
			$stock->outbound_picking_list_barcode = '';
			$stock->scan_out_datetime = '';
			$stock->scan_out_employee_id = '0';
			$stock->status = Stock::STATUS_NOT_SET;
			$stock->status_availability = Stock::STATUS_AVAILABILITY_YES;
//            $stock->system_status = 'inventory-plus-20210222Return';
			$stock->system_status = 'noReturnFB20210222';
			$stock->system_status_description = 'это возвраты которые были размещены по фидбеки по ним не были отправлены';
//            if (!$stock->save(false)) {
//                echo "NO " . $barcode['lotBarcode'];
//            }
		}

		VarDumper::dump($products, 10, true);
		die;
	}*/
}