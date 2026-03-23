<?php

namespace stockDepartment\modules\alix\controllers\outboundSeparator\uploads\v2;

use app\modules\outbound\outbound;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use stockDepartment\modules\alix\controllers\outboundSeparator\uploads\forms\UploadForm;
use stockDepartment\modules\alix\controllers\outboundSeparator\uploads\service\UploadService;
use stockDepartment\modules\alix\controllers\product\domains\ProductService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use stockDepartment\components\Controller;

class FormController extends Controller
{
	public function actionIndex()
	{
		// /alix/outboundSeparator/uploads/v2/form/index
		$model = new UploadForm();
		$order = new \stdClass();
		$order->totalQtyRows = 0;
		$order->expectedTotalProductQty = 0;
		$order->expectedTotalPlaceQty = 0;
		$order->items = [];

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$model->file = UploadedFile::getInstance($model, 'file');

			if (!$model->file) {
				$model->addError('file', Yii::t('outbound/messages', 'Please select file for upload'));
				return $this->render('form',
					[
						'model' => $model,
						'previewData' => $order,
					]);
			}

			$dirPath = 'uploads/outboundSeparator/' . date('Ymd') . '/' . date('Hi');

			BaseFileHelper::createDirectory($dirPath);
			$file = $model->file;
			$fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
			$file->saveAs($fileToPath);

			if (file_exists($fileToPath)) {
				$rootPath = $fileToPath;
				$excel = \PHPExcel_IOFactory::load($rootPath);
				$excel->setActiveSheetIndex(0);
				$excelActive = $excel->getActiveSheet();

				$start = 3;
				$orders = [];
				$orderNumber = (string)$excelActive->getCell('A1')->getValue();
				$orderNumber = trim($orderNumber);
				for ($i = $start; $i <= 50000; $i++) {
					$productBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
					$qty = (integer)$excelActive->getCell('C' . $i)->getValue();
					$productBarcode = trim($productBarcode);
					if (empty($orderNumber) || empty($qty) || empty($productBarcode)) {
						continue;
					}
					if (!isset($orders[$productBarcode])) {
						$orders[$productBarcode] = $qty;
					} else {
						$orders[$productBarcode] += $qty;
					}
				}
			}

			//VarDumper::dump($orders,10,true);
			$orderId = OutboundOrder::find()->select("id")->andWhere(["order_number" => $orderNumber])->scalar();
			$dataForFindList = [];
			foreach ($orders as $productBarcode => $qty) {
				$productOnStock = Stock::find()
									   ->select("`box_barcode`, `product_barcode`, COUNT(`product_barcode`) as qty")
									   ->andWhere([
										   "outbound_order_id" => $orderId,
										   "product_barcode" => $productBarcode,
									   ])
									   ->groupBy("`box_barcode`, `product_barcode`")
									   ->limit($qty)
									   ->asArray()
									   ->all();
				$qtyTotal = $qty;
				if (!empty($productOnStock)) {
					foreach ($productOnStock as $item) {
							$qtyTotal -= $item["qty"];
							$dataForFindList[$productBarcode]["items"][] = [
								"box_barcode"=>$item["box_barcode"],
								"product_barcode"=>$item["product_barcode"],
								"qty"=>$item["qty"],
							];
					}
					$dataForFindList[$productBarcode]["expected_qty"] = $qty;
					$dataForFindList[$productBarcode]["diff_qty"] = $qtyTotal;
				} else {
					$dataForFindList[$productBarcode]["items"][] = [
						"box_barcode"=>"",
						"product_barcode"=>$productBarcode,
						"qty"=>$qty,
					];
					$dataForFindList[$productBarcode]["expected_qty"] = $qty;
					$dataForFindList[$productBarcode]["diff_qty"] = $qty;
				}
			}
//			VarDumper::dump($dataForFindList,10,true);
//			die;
			$contentValidate = "Шк Товара по накладной;Кол-во по накладной;Разница;Короб;Шк Товара;Кол-во;"."\n";
			$delimiter = ";";
			foreach ($dataForFindList as $prodctBercode=>$itemData) {
				$row = [
					$prodctBercode,
					$itemData["expected_qty"],
					$itemData["diff_qty"],
				];

				$contentValidate .= implode($row, $delimiter) . "\n";
				foreach ($itemData["items"] as $item) {
					$row = [
						" ",
						" ",
						" ",
						$item["box_barcode"],
						$item["product_barcode"],
						$item["qty"],
					];

					$contentValidate .= implode($row, $delimiter) . "\n";
				}
			}

			$contentUpload = "Номер накладной;Короб;Шк Товара;Кол-во;"."\n";
			foreach ($dataForFindList as $prodctBercode=>$itemData) {
				foreach ($itemData["items"] as $item) {
					$row = [
						$orderNumber,
						$item["box_barcode"],
						$item["product_barcode"],
						$item["qty"],
					];

					$contentUpload .= implode($row, $delimiter) . "\n";
				}
			}

		//	$filename = $orderNumber . "-validate.csv";
			// Yii::$app->response->sendContentAsFile($contentValidate, $filename);
			$filename = $orderNumber . "-upload.csv";
			return Yii::$app->response->sendContentAsFile($contentUpload, $filename);
		}

		return $this->render('form',
			[
				'model' => $model,
				'previewData' => $order,
			]);
	}
}