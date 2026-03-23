<?php

namespace stockDepartment\modules\alix\controllers\outboundSeparator\uploads;

use stockDepartment\modules\alix\controllers\outboundSeparator\uploads\forms\UploadForm;
use stockDepartment\modules\alix\controllers\outboundSeparator\uploads\service\UploadService;
use stockDepartment\modules\alix\controllers\product\domains\ProductService;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use stockDepartment\components\Controller;

class FormController extends Controller
{
	public function actionIndex()
	{
		// /alix/outboundSeparator/uploads/form/index
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

			$ps = new ProductService();

			if (file_exists($fileToPath)) {
				$rootPath = $fileToPath;
				$excel = \PHPExcel_IOFactory::load($rootPath);
				$excel->setActiveSheetIndex(0);
				$excelActive = $excel->getActiveSheet();

				$start = 2;
				$orders = [];

				for ($i = $start; $i <= 50000; $i++) {
					$orderNumber = (string)$excelActive->getCell('A' . $i)->getValue();
					$boxBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
					$productBarcode = (string)$excelActive->getCell('C' . $i)->getValue();
					$orderNumber = trim($orderNumber);
					$boxBarcode = trim($boxBarcode);
					$productBarcode = trim($productBarcode);
					if (empty($orderNumber) || empty($boxBarcode) || empty($productBarcode)) {
						continue;
					}

					$orders[$orderNumber][$boxBarcode][] = $productBarcode;
				}
			}

//			VarDumper::dump($orders,10,true);
		//	die;
			$us = new UploadService();

			//$orderNumbers = array_keys($orders);
			//$os->makeStockItem()
//			VarDumper::dump($orderNumbers,10,true);
			//die;

			$sOrder = $us->makeOrder($model->order_name,$model->comments,$fileToPath);
			foreach ($orders as $orderNumber=>$boxes) {
				$us->makeOutboundOrdersStock($sOrder->id,$orderNumber);
				foreach ($boxes as $box=>$items) {
					foreach ($items as $productBarcode) {
						$us->makeOrderItem($sOrder->id,$orderNumber,$box,$productBarcode);
						$us->setProductToOutFromOrder($sOrder->id,$orderNumber,$box,$productBarcode);
					}
				}
			}

			Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Outbound Order № {0} was successfully created', [count($orders)]));
			return $this->redirect('form');
		}

		return $this->render('form',
			[
				'model' => $model,
				'previewData' => $order,
			]);
	}
}