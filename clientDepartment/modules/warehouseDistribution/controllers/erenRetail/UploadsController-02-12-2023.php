<?php

namespace app\modules\warehouseDistribution\controllers\erenRetail;

use app\modules\warehouseDistribution\models\ErenRetailOutboundForm;
use app\modules\warehouseDistribution\models\ErenRetailInboundForm;

use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use Yii;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use common\components\OutboundManager;

class UploadsController extends \clientDepartment\components\Controller
{
	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionOutboundOrder()
	{
		$model = new ErenRetailOutboundForm();
		$session = Yii::$app->session;
		$client = ClientManager::getClientEmployeeByAuthUser();
		$client_id = $client->client_id;
		$filterWidgetOptionDataRoute = TLHelper::getStoreArrayByClientID($client_id);

		$order = new \stdClass();
		$order->totalQtyRows = 0;
		$order->expectedTotalProductQty = 0;
		$order->expectedTotalPlaceQty = 0;
		$order->items = [];

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$model->file = UploadedFile::getInstance($model, 'file');

			if (!$model->file) {
				$model->addError('file', Yii::t('outbound/messages', 'Please select file for upload'));
				return $this->render('upload-inbound',
					[
						'model' => $model,
						'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
						'previewData' => $order,
					]);
			}

			$dirPath = 'uploads/erenRetail/outbound/' . date('Ymd') . '/' . date('Hi');

			BaseFileHelper::createDirectory($dirPath);
			$file = $model->file;
			$fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
			$file->saveAs($fileToPath);

			if (file_exists($fileToPath)) {
				$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileToPath);
				$excel->setActiveSheetIndex(0);
				$excelActive = $excel->getActiveSheet();

				$start = 2;
				$rowIndex = 0;
				for ($i = $start; $i <= 2000; $i++) {

					$style = (string)$excelActive->getCell('A' . $i)->getValue();
					$color = (string)$excelActive->getCell('B' . $i)->getValue();
					$productName= (string)$excelActive->getCell('C' . $i)->getValue();
					$productBarcode = (string)$excelActive->getCell('D' . $i)->getValue();
					$productSize = (string)$excelActive->getCell('E' . $i)->getValue();
					$productQty = (int)$excelActive->getCell('F' . $i)->getValue();

					if ($productBarcode == null || $style == null || $productQty == null) {
						continue;
					}

					$rowIndex++;
					$row = new \stdClass();
					$row->row = $rowIndex;
					$row->productBarcode = $productBarcode;
					$row->productModel =$productBarcode;
					$row->productName = $productName;
					$row->productColor = $color;
					$row->productSize = $productSize;
					$row->productStyle = $style;
					$row->expectedProductQty = $productQty;
					$row->expectedPlaceQty = 0;

					$order->totalQtyRows += 1;
					$order->expectedTotalProductQty += $row->expectedProductQty;

					if (isset($order->items[$productBarcode])) {
						$order->items[$productBarcode]->expectedProductQty += $row->expectedProductQty;
					} else {
						$order->items[$productBarcode] = $row;
					}
				}
			}
			$session->set('erenRetailOutboundFilePath', $fileToPath);
		}


		return $this->render('upload-outbound',
			[
				'model' => $model,
				'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
				'previewData' => $order,
			]);
	}

	public function actionCreateOutboundOrder()
	{
		$from_point_id = Yii::$app->request->post('from');
		$to_point_id = Yii::$app->request->post('to');
		$session = Yii::$app->session;
		$client = ClientManager::getClientEmployeeByAuthUser();
		$orderCount = OutboundOrder::find()->where(['client_id' => $client->client_id])->count();
		$count = 0;
		$partyNumber = date('Ymd').'-'.$client->client_id.'-'.date('Hms');
		$orderNumber = date('Ymd').'-'.$client->client_id.'-'.date('Hms');
		$tableRows = [];
		$order = new \stdClass();
		$order->totalQtyRows = 0;
		$order->expectedTotalProductQty = 0;
		$order->expectedTotalPlaceQty = 0;
		$order->items = [];


//		VarDumper::dump($client,10,true);
//		VarDumper::dump($from_point_id,10,true);
//		VarDumper::dump($to_point_id,10,true);
//		VarDumper::dump($session->get('erenRetailOutboundFilePath'),10,true);
		if($from_point_id && $to_point_id && file_exists($session->get('erenRetailOutboundFilePath'))){
			$fileToPath = $session->get('erenRetailOutboundFilePath');
//			if (($handle = fopen($fileToPath, "r")) !== FALSE) {
//				$row = 0;
//				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
//					$row++;
//					$data = array_filter($data, 'trim');
//					if ($row > 1) {
//						$tableRows[$row]['brand'] = isset ($data[0]) ? $data[0] : "";
//						$tableRows[$row]['category'] = isset ($data[1]) ? $data[1] : "";
//						$tableRows[$row]['internal_id'] = isset ($data[2]) ? $data[2] : "";
//						$tableRows[$row]['article'] = isset ($data[3]) ? $data[3] : "";
//						$tableRows[$row]['model'] = isset ($data[4]) ? $data[4] : "";
//						$tableRows[$row]['color'] = isset ($data[5]) ? $data[5] : "";
//						$tableRows[$row]['size'] = isset ($data[6]) ? $data[6] : "";
//						$tableRows[$row]['kavala'] = isset ($data[7]) ? $data[7] : "";
//						$tableRows[$row]['expected_qty'] = isset ($data[8]) ? $data[8] : "";
//						$tableRows[$row]['product_barcode'] = isset ($data[9]) ? $data[9] : "";
//					}
//				}
//				fclose($handle);
//			}

			if (file_exists($fileToPath)) {
				$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileToPath);
				$excel->setActiveSheetIndex(0);
				$excelActive = $excel->getActiveSheet();

				$start = 2;
				$rowIndex = 0;
				for ($i = $start; $i <= 2000; $i++) {

					$style = (string)$excelActive->getCell('A' . $i)->getValue();
					$color = (string)$excelActive->getCell('B' . $i)->getValue();
					$productName= (string)$excelActive->getCell('C' . $i)->getValue();
					$productBarcode = (string)$excelActive->getCell('D' . $i)->getValue();
					$productSize = (string)$excelActive->getCell('E' . $i)->getValue();
					$productQty = (int)$excelActive->getCell('F' . $i)->getValue();

					if ($productBarcode == null || $style == null || $productQty == null) {
						continue;
					}

					$rowIndex++;
//					$row = new \stdClass();
//					$row->row = $rowIndex;
//					$row->productBarcode = $productBarcode;
//					$row->productModel =$productBarcode;
//					$row->productName = $productName;
//					$row->productColor = $color;
//					$row->productSize = $productSize;
//					$row->productStyle = $style;
//					$row->expectedProductQty = $productQty;
//					$row->expectedPlaceQty = 0;

					$row = [
						'product_barcode' =>$productBarcode,
						'product_name' => $productName,
						'expected_qty' => $productQty,
					];
//				}

					$order->totalQtyRows += 1;
					$order->expectedTotalProductQty += $row['expected_qty'];

					if (isset($order->items[$productBarcode])) {
						$order->items[$productBarcode]['expected_qty'] += $row['expected_qty'];
					} else {
						$order->items[$productBarcode] = $row;
					}
				}
			}

			if($order->items) {
				$data = [];
				$oManager = new OutboundManager();
				$oManager->initBaseData($client->client_id, $partyNumber, $orderNumber);
//				if($coo =  $oManager->createUpdateConsignmentOutbound()){
//					$data['consignment_outbound_order_id'] = $coo->id;
					$data['parent_order_number'] = $partyNumber;
					$data['order_number'] = $orderNumber;
					$data['from_point_id'] = $from_point_id;
					$data['to_point_id'] = $to_point_id;
					if($oo = $oManager->createUpdateOutbound($data)){
						$oManager->addItems($order->items);
						$oManager->addProducts($tableRows);
						$oManager->createUpdateDeliveryProposalAndOrder();
						$oManager->reservationOnStockByPartyNumber();

						$session->remove('erenRetailOutboundFilePath');
						Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Outbound Order № {0} was successfully created',[$oo->order_number]));
						return $this->redirect('outbound-order');
					}
//				}
			}


		} else {
			$session->remove('erenRetailOutboundFilePath');
			Yii::$app->getSession()->setFlash('error', Yii::t('outbound/messages', 'File upload error. Please try again'));
			return $this->redirect('outbound-order');
		}

	}

	public function actionInboundOrder()
	{
		$model = new ErenRetailInboundForm();
		$session = Yii::$app->session;
		$client = ClientManager::getClientEmployeeByAuthUser();
		$client_id = $client->client_id;
		$filterWidgetOptionDataRoute = TLHelper::getStoreArrayByClientID($client_id);

		$order = new \stdClass();
		$order->totalQtyRows = 0;
		$order->expectedTotalProductQty = 0;
		$order->expectedTotalPlaceQty = 0;
		$order->items = [];

		if ($model->load(Yii::$app->request->post()) && $model->validate()) {
			$model->file = UploadedFile::getInstance($model, 'file');

			if (!$model->file) {
				$model->addError('file', Yii::t('outbound/messages', 'Please select file for upload'));
				return $this->render('upload-inbound',
					[
						'model' => $model,
						'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
						'previewData' => $order,
					]);
			}

			$dirPath = 'uploads/erenRetail/inbound/' . date('Ymd') . '/' . date('Hi');

			BaseFileHelper::createDirectory($dirPath);
			$file = $model->file;
			$fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
			$file->saveAs($fileToPath);

			if (file_exists($fileToPath)) {
				$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileToPath);
				$excel->setActiveSheetIndex(0);
				$excelActive = $excel->getActiveSheet();

				$start = 2;
				$rowIndex = 0;
				for ($i = $start; $i <= 2000; $i++) {

					$style = (string)$excelActive->getCell('A' . $i)->getValue();
					$color = (string)$excelActive->getCell('B' . $i)->getValue();
					$productName= (string)$excelActive->getCell('C' . $i)->getValue();
					$productBarcode = (string)$excelActive->getCell('D' . $i)->getValue();
					$productSize = (string)$excelActive->getCell('E' . $i)->getValue();
					$productQty = (int)$excelActive->getCell('F' . $i)->getValue();

					if ($productBarcode == null || $style == null || $productQty == null) {
						continue;
					}

					$rowIndex++;
					$row = new \stdClass();
					$row->row = $rowIndex;
					$row->productBarcode = $productBarcode;
					$row->productModel =$productBarcode;
					$row->productName = $productName;
					$row->productColor = $color;
					$row->productSize = $productSize;
					$row->productStyle = $style;
					$row->expectedProductQty = $productQty;
					$row->expectedPlaceQty = 0;

					$order->totalQtyRows += 1;
					$order->expectedTotalProductQty += $row->expectedProductQty;

					if (isset($order->items[$productBarcode])) {
						$order->items[$productBarcode]->expectedProductQty += $row->expectedProductQty;
					} else {
						$order->items[$productBarcode] = $row;
					}
				}
			}
			$session->set('erenRetailInboundFilePath', $fileToPath);
		}

		return $this->render('upload-inbound',
			[
				'model' => $model,
				'filterWidgetOptionDataRoute' => $filterWidgetOptionDataRoute,
				'previewData' => $order,
			]);
	}

	public function actionCreateInboundOrder()
	{
		$orderNumber = Yii::$app->request->post('ordernumber');
		$comment = Yii::$app->request->post('comment');

		$session = Yii::$app->session;
		$client = ClientManager::getClientEmployeeByAuthUser();

		if(file_exists($session->get('erenRetailInboundFilePath'))){
			$fileToPath = $session->get('erenRetailInboundFilePath');
			$order = new \stdClass();
			$order->totalQtyRows = 0;
			$order->expectedTotalProductQty = 0;
			$order->expectedTotalPlaceQty = 0;
			$order->items = [];
			if (file_exists($fileToPath)) {
				$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileToPath);
				$excel->setActiveSheetIndex(0);
				$excelActive = $excel->getActiveSheet();

				$start = 2;
				$rowIndex = 0;
				for ($i = $start; $i <= 2000; $i++) {

					$style = (string)$excelActive->getCell('A' . $i)->getValue();
					$color = (string)$excelActive->getCell('B' . $i)->getValue();
					$productName= (string)$excelActive->getCell('C' . $i)->getValue();
					$productBarcode = (string)$excelActive->getCell('D' . $i)->getValue();
					$productSize = (string)$excelActive->getCell('E' . $i)->getValue();
					$productQty = (int)$excelActive->getCell('F' . $i)->getValue();

					if ($productBarcode == null || $style == null || $productQty == null) {
						continue;
					}

					$rowIndex++;
					$row = new \stdClass();
					$row->row = $rowIndex;
					$row->productBarcode = $productBarcode;
					$row->productModel =$style;
					$row->productName = $productName;
					$row->productColor = $color;
					$row->productSize = $productSize;
					$row->productStyle = $style;
					$row->expectedProductQty = $productQty;
					$row->expectedPlaceQty = 0;

					$order->totalQtyRows += 1;
					$order->expectedTotalProductQty += $row->expectedProductQty;

					if (isset($order->items[$productBarcode])) {
						$order->items[$productBarcode]->expectedProductQty += $row->expectedProductQty;
					} else {
						$order->items[$productBarcode] = $row;
					}
				}
			}

			$params = new \stdClass();
			$params->clientId = $client->client_id;

			$dto = new \stdClass();
			$dto->pathToOrderFile = $fileToPath;
			$dto->orderNumber = $orderNumber;
			$dto->supplierId = 1;
			$dto->comment = $comment;
			$dto->order = $order;

			$inboundOrderUploadService = new \common\clientObject\erenRetail\inbound\service\InboundOrderUploadService($params);
			$inboundOrderUploadService->create($dto);

			$session->remove('erenRetailInboundFilePath');
			Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Outbound Order № {0} was successfully created',[$orderNumber]));
			return $this->redirect('inbound-order');

		} else {
			$session->remove('erenRetailInboundFilePath');
			Yii::$app->getSession()->setFlash('error', Yii::t('outbound/messages', 'File upload error. Please try again'));
			return $this->redirect('inbound-order');
		}
	}
}