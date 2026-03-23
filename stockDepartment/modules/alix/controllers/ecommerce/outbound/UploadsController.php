<?php

namespace stockDepartment\modules\intermode\controllers\ecommerce\outbound;

use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\constants\OutboundSource;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\OutboundService;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;
use Yii;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;
use yii\helpers\BaseFileHelper;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\form\UploadOutboundForm;

class UploadsController extends \stockDepartment\components\Controller
{
	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionForm()
	{
		$model = new UploadOutboundForm();
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
						'previewData' => $order,
					]);
			}

			$dirPath = 'uploads/erenRetail/ecom/kaspi/' . date('Ymd') . '/' . date('Hi');

			BaseFileHelper::createDirectory($dirPath);
			$file = $model->file;
			$fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
			$file->saveAs($fileToPath);

			$ps = new ProductService();
			$os = new OutboundService();

			if (file_exists($fileToPath)) {
				$inputFileType = \PHPExcel_IOFactory::identify($fileToPath);

				// Новый
    				if ($inputFileType === 'CSV') {
        		           $reader = new \PHPExcel_Reader_CSV();
       				   $reader->setDelimiter(';');
        			   $reader->setEnclosure('"');
        			   $reader->setInputEncoding('UTF-8');
    			        } else {
				   // Старый
        	          	   $reader = \PHPExcel_IOFactory::createReader($inputFileType);
    				}

    				$excel = $reader->load($fileToPath);
    				$excel->setActiveSheetIndex(0);
    				$excelActive = $excel->getActiveSheet();

				$start = 2;
				$orders = [];

				for ($i = $start; $i <= 300; $i++) {
					$orderNumber = (string)$excelActive->getCell('A' . $i)->getValue();
					$orderNumber = trim($orderNumber);
					if (empty($orderNumber)) {
						continue;
					}
//					$article = (string)$excelActive->getCell('B' . $i)->getValue();
//					$article = trim($article);

					$qty = (int)$excelActive->getCell('C' . $i)->getValue();

					$productBarcode = (string)$excelActive->getCell('D' . $i)->getValue();
					$productBarcode = trim($productBarcode);

					$productInfo = $ps->getProductInfoByBarcode($productBarcode);

					if (isset($orders[$orderNumber])) {
						$orders[$orderNumber]["items"][] = [
							"guid"=> $productInfo->product->client_product_id,
							"quantity"=> $qty,
						];
					} else {
						$orders[$orderNumber]["order_id"] = $orderNumber;
						$orders[$orderNumber]["shipmentSource"] = OutboundSource::getKASPI();
						$orders[$orderNumber]["items"][] = [
							"guid"=> $productInfo->product->client_product_id,
							"quantity"=> $qty,
						];
					}
				}
			}
//			VarDumper::dump($orders,10,true);
//			die;

			foreach ($orders as $request) {
				 $os->addOrder($os->requestToCreateDTO($request));
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

	public function actionCreateInboundOrder()
	{
		$orderNumber = Yii::$app->request->post('ordernumber');
		$comment = Yii::$app->request->post('comment');
		$order_type = Yii::$app->request->post('order_type');

		$session = Yii::$app->session;
//		$client = ClientManager::getClientEmployeeByAuthUser();

		if (file_exists($session->get('erenRetailInboundFilePath'))) {
			$fileToPath = $session->get('erenRetailInboundFilePath');
			$order = new \stdClass();
			$order->totalQtyRows = 0;
			$order->expectedTotalProductQty = 0;
			$order->expectedTotalPlaceQty = 0;
			$order->items = [];

			if (file_exists($fileToPath)) {
				$rowIndex = 0;
				if (($handle = fopen($fileToPath, "r")) !== false) {
					while (($data = fgetcsv($handle, 30000, ";")) !== false) {
						$rowIndex++;
						file_put_contents("log.log",print_r($data,true),FILE_APPEND);

						if ($rowIndex > 1) {
							// $data = array_filter($data, 'trim');
							$productArticle = trim($data[0]);
							$productColor = trim($data[1]);
							$productName = trim($data[2]);
							$productBarcode = trim($data[3]);
							$productSize = trim($data[4]);
							$productQty = (int)$data[5];
							$dataMatrix = "";
							if (isset($data[6])) {
								$dataMatrix = (string)$data[6];
								if ( base64_encode(base64_decode($dataMatrix, true)) === $dataMatrix){
									$dataMatrix = base64_decode($dataMatrix, true);
								}
							}

							$productBrand = "";
							if (isset($data[7])) {
								$productBrand = (string)$data[7];
							}

							$productBarcode = ltrim($productBarcode,"0");
							$row = new \stdClass();
							$row->row = $rowIndex;
							$row->productBarcode = $productBarcode;
							$row->productModel = $productArticle;
							$row->productName = $productName;
							$row->productColor = $productColor;
							$row->productSize = $productSize;
							$row->productBrand= $productBrand;
							$row->productStyle = $productArticle;
							$row->productCategory = "";
							$row->expectedProductQty = $productQty;
							$row->expectedPlaceQty = 0;
							$row->dataMatrix = [];

							$order->totalQtyRows += 1;
							$order->expectedTotalProductQty += $row->expectedProductQty;

							if (isset($order->items[$productBarcode])) {
								$order->items[$productBarcode]->expectedProductQty += $row->expectedProductQty;
								$order->items[$productBarcode]->dataMatrix[] = $dataMatrix;
							} else {
								$order->items[$productBarcode] = $row;
								$order->items[$productBarcode]->dataMatrix[] = $dataMatrix;
							}
						}
					}
				}
				$params = new \stdClass();
				$params->clientId = 103;

				$dto = new \stdClass();
				$dto->pathToOrderFile = $fileToPath;
				$dto->orderNumber = $orderNumber;
				$dto->supplierId = 1;
				$dto->comment = $comment;
				$dto->order_type = $order_type;
				$dto->order = $order;
				$inboundOrderUploadService = new \common\clientObject\erenRetail\inbound\service\InboundOrderUploadService($params);
				$inboundOrderUploadService->create($dto);

				$session->remove('erenRetailInboundFilePath');
				Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Outbound Order № {0} was successfully created', [$orderNumber]));
				return $this->redirect('inbound-order');

			} else {
				$session->remove('erenRetailInboundFilePath');
				Yii::$app->getSession()->setFlash('error', Yii::t('outbound/messages', 'File upload error. Please try again'));
				return $this->redirect('inbound-order');
			}

		}
	}
}
