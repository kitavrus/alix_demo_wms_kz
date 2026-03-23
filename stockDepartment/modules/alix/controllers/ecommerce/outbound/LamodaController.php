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

class LamodaController extends \stockDepartment\components\Controller
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

			$dirPath = 'uploads/erenRetail/ecom/lamoda/' . date('Ymd') . '/' . date('Hi');

			BaseFileHelper::createDirectory($dirPath);
			$file = $model->file;
			$fileToPath = $dirPath . '/' . $file->baseName . '.' . $file->extension;
			$file->saveAs($fileToPath);

			$ps = new ProductService();
			$os = new OutboundService();

			if (file_exists($fileToPath)) {
				$inputFileType = \PHPExcel_IOFactory::identify($fileToPath);
				$reader = $inputFileType === 'CSV'
					? (new \PHPExcel_Reader_CSV())
						->setDelimiter(';')
						->setEnclosure('"')
						->setInputEncoding('UTF-8')
					: \PHPExcel_IOFactory::createReader($inputFileType);

				$excel = $reader->load($fileToPath);
				$excel->setActiveSheetIndex(0);
				$sheet = $excel->getActiveSheet();

				$start = 2;
				$orders = [];

				for ($i = $start; $i <= 30000; $i++) {
					$orderNumber = (string)$sheet->getCell('G' . $i)->getValue();
					$orderNumber = trim($orderNumber);
					if (empty($orderNumber)) {
						continue;
					}

					$shippingNameParts = explode(' ', (string)$sheet->getCell('M' . $i)->getValue());
					$firstName = $shippingNameParts[0];
					$lastName = $shippingNameParts[1];
					$customerName = (string)$sheet->getCell('J' . $i)->getValue();
					$email = (string)$sheet->getCell('K' . $i)->getValue();
					$phoneMobile = (string)$sheet->getCell('S' . $i)->getValue();
					$country = (string)$sheet->getCell('W' . $i)->getValue();
					$region = (string)$sheet->getCell('X' . $i)->getValue();
					$city = (string)$sheet->getCell('U' . $i)->getValue();
					$zipCode = (string)$sheet->getCell('V' . $i)->getValue();
					$street = (string)$sheet->getCell('N' . $i)->getValue();
					$paidPrice = (string)$sheet->getCell('AK' . $i)->getValue();

					$lamodaSku = (string)$sheet->getCell('D' . $i)->getValue();
					$itemName = (string)$sheet->getCell('AP' . $i)->getValue();

					$qty = 1;
					$productBarcode = (string)$sheet->getCell('BD' . $i)->getValue();
					$productBarcode = trim($productBarcode);

					$productInfo = $ps->getProductInfoByBarcode($productBarcode);

					if (isset($orders[$orderNumber])) {
						$notNewOrder = true;
						foreach ($orders[$orderNumber]["items"] as $key=>$item) {
							if ($item["guid"] == $productInfo->product->client_product_id) {
								$orders[$orderNumber]["items"][$key]["quantity"] += 1;
								$notNewOrder = false;
							}
						}

						if ($notNewOrder) {
							$orders[$orderNumber]["items"][] = [
								"guid"=> $productInfo->product->client_product_id,
								"quantity"=> $qty,
								"lamoda_sku" => $lamodaSku,
								"item_name" => $itemName,
								"paidPrice" => $paidPrice
							];
						}

					} else {
						$orders[$orderNumber]["order_id"] = $orderNumber;
						$orders[$orderNumber]["firstName"] = $firstName;
						$orders[$orderNumber]["lastName"] = $lastName;
						$orders[$orderNumber]["customerName"] = $customerName;
						$orders[$orderNumber]["email"] = $email;
						$orders[$orderNumber]["phoneMobile"] = $phoneMobile;
						$orders[$orderNumber]["country"] = $country;
						$orders[$orderNumber]["region"] = $region;
						$orders[$orderNumber]["city"] = $city;
						$orders[$orderNumber]["zipCode"] = $zipCode;
						$orders[$orderNumber]["street"] = $street;
						$orders[$orderNumber]["shipmentSource"] = OutboundSource::getLAMODA();
						$orders[$orderNumber]["items"][] = [
							"guid" => $productInfo->product->client_product_id,
							"quantity" => $qty,
							"lamoda_sku" => $lamodaSku,
							"item_name" => $itemName,
							"paidPrice" => $paidPrice
						];
					}
				}
			}
			// VarDumper::dump($orders,10,true);
			// die;

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
}