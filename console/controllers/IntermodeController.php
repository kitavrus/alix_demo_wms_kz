<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 8/5/14
 * Time: 6:17 PM
 */

namespace console\controllers;

use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\modules\product\service\ProductService;
use common\modules\stock\models\Stock;
use yii\console\Controller;
use yii\helpers\VarDumper;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\OutboundOrderItem;
use yii\db\Expression;
use stockDepartment\modules\wms\managers\erenRetail\checkBox\entities\CheckBoxStock;
use yii\helpers\ArrayHelper;
use stockDepartment\modules\intermode\controllers\ecommerce\outbound\domain\entities\models\EcommerceOutboundItem;

class IntermodeController extends Controller
{

	public function actionLoadProductsInfoFromApi()
	{
		echo "php yii intermode/load-products-info-from-api start" . "\n";
		//return 0;
		$s = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
//		$filter = new Filter();
//		$filter->barcode = [
//			"5059862199334"
//		];

//		$isOK = $s->getAndSaveMetaData($filter);
		$isOK = $s->getAndSaveMetaData();

		echo $isOK . "\n";

		echo "php yii intermode/load-products-info-from-api end" . "\n";
	}

	public function actionUpdateStockProductInfo()
	{
		echo "php yii intermode/update-stock-product-info start" . "\n";
		return 0;
		$client_id = 103;
		$stockAllQuery = Stock::find()
							  ->select("product_barcode")
							  ->andWhere([
									  //'client_id' => $client_id,
									  // 'outbound_order_id' => 76633,
									  // 'status_availability' => Stock::STATUS_AVAILABILITY_YES,
								  ]
							  )
							  ->andWhere("product_id is NULL or product_id = 0")
							  ->andWhere("status_availability = 2")
							  ->groupBy("product_barcode")
							  ->asArray();

		$s = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
		foreach ($stockAllQuery->batch() as $stocks) {
			//echo ".";
			foreach ($stocks as $stock) {
				$product = $s->getProductInfoByBarcode($stock["product_barcode"]);
				echo $stock["product_barcode"]."\n";
				//continue;
				if (empty($product->product)) {
					//echo"LOST"."\n";
					file_put_contents("update-stock-product-info-lost.csv",$stock["product_barcode"]."\n",FILE_APPEND);
					continue;
				}
				Stock::updateAll(
					[
						"product_id"=>	$product->product->id,
						"product_name"=>	$product->product->name,
						"product_model"=>	$product->product->model,
						"product_sku"=>	$product->product->client_product_id,
						"product_color"=>	$product->product->color,
						"product_brand"=>	$product->product->field_extra1,
						"product_category"=>	$product->product->category,
					],
					[
						"product_barcode"=>$stock["product_barcode"]
					]
				);
			}
		}

		file_put_contents("update-stock-product-info-lost.csv",date(DATE_ISO8601)."\n"."\n",FILE_APPEND);

		echo "php yii intermode/update-stock-product-info end" . "\n";
	}

	public function actionUpdateStockProductInfoM()
	{
		echo "php yii intermode/update-stock-product-info-m 2 start" . "\n";
		return 0;
		$client_id = 103;
		$stockAllQuery = Stock::find()
							  ->select("product_barcode")
							  ->andWhere([
									  'client_id' => $client_id,
									  'status_availability' => Stock::STATUS_AVAILABILITY_YES,
								  ]
							  )
							  ->andWhere("product_id is NULL")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76826,76827,76828,76829,76830,76831,76832,76833,76834,76835,76836,76837))")
			//->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76826))")
			//  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76827))")
			//  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76828))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76829))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76830))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76831))")
			//						  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76832))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76833))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76834))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76835))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76836))")
//							  ->andWhere("product_sku in (SELECT  distinct `product_sku`FROM `outbound_order_items` WHERE `outbound_order_id` IN (76837))")
							  ->groupBy("product_barcode")
							  ->asArray();

		$s = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
		foreach ($stockAllQuery->batch() as $stocks) {
			//echo ".";
			foreach ($stocks as $stock) {
				$product = $s->getProductInfoByBarcode($stock["product_barcode"]);
				echo $stock["product_barcode"]."\n";
				if (empty($product->product)) {
					//echo"LOST"."\n";
					file_put_contents("update-stock-product-info-lost.csv",$stock["product_barcode"]."\n",FILE_APPEND);
					continue;
				}
				Stock::updateAll(
					[
						"product_id"=>	$product->product->id,
						"product_name"=>	$product->product->name,
						"product_model"=>	$product->product->model,
						"product_sku"=>	$product->product->client_product_id,
						"product_color"=>	$product->product->color,
						"product_brand"=>	$product->product->field_extra1,
						"product_category"=>	$product->product->category,
					],
					[
						"product_barcode"=>$stock["product_barcode"]
					]
				);
			}
		}

		file_put_contents("update-stock-product-info-lost.csv",date(DATE_ISO8601)."\n"."\n",FILE_APPEND);

		echo "php yii intermode/update-stock-product-info 2 end" . "\n";
	}

	public function actionUpdateInboundProductInfo()
	{
		echo "php yii intermode/update-inbound-product-info start" . "\n";

		$stockAllQuery = InboundOrderItem::find()
										 ->select("product_barcode")
										 ->andWhere("product_id is NULL")
			// ->andWhere("inbound_order_id in (124618,124625)")
										 ->groupBy("product_barcode")
										 ->asArray();

		$s = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
		foreach ($stockAllQuery->batch() as $stocks) {
			foreach ($stocks as $stock) {
				$product = $s->getProductInfoByBarcode($stock["product_barcode"]);
				echo $stock["product_barcode"]."\n";
				if (empty($product->product)) {
					echo"LOST"."\n";
					continue;
				}
				InboundOrderItem::updateAll(
					[
						"product_id"=>	$product->product->id,
						"product_name"=>	$product->product->name,
						"product_model"=>	$product->product->model,
						"product_sku"=>	$product->product->client_product_id,
						"product_color"=>	$product->product->color,
						"product_brand"=>	$product->product->field_extra1,
						"product_category"=>	$product->product->category,
					],
					[
						"product_barcode"=>$stock["product_barcode"]
					]
				);
			}
		}

		echo "php yii intermode/update-inbound-product-info end" . "\n";
	}


	public function actionUpdateOutboundProductInfo()
	{
		echo "php yii intermode/update-outbound-product-info start" . "\n";

		$stockAllQuery = OutboundOrderItem::find()
										  ->select("product_barcode")
										  ->andWhere("product_id is NULL")
			// ->andWhere("outbound_order_id >= 75451")
			// ->andWhere(["outbound_order_id"=>[76357,76358]])
										  ->groupBy("product_barcode")
										  ->asArray();

		$s = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
		foreach ($stockAllQuery->batch() as $stocks) {
			foreach ($stocks as $stock) {
				$product = $s->getProductInfoByBarcode($stock["product_barcode"]);
				echo $stock["product_barcode"]."\n";
				if (empty($product->product)) {
					echo"LOST"."\n";
					continue;
				}
				OutboundOrderItem::updateAll(
					[
						"product_id"=>	$product->product->id,
						"product_name"=>	$product->product->name,
						"product_model"=>	$product->product->model,
						"product_sku"=>	$product->product->client_product_id,
						"product_color"=>	$product->product->color, // -
						"product_brand"=>	$product->product->field_extra1, // -
						"product_category"=>	$product->product->category, // -
					],
					[
						"product_barcode"=>$stock["product_barcode"]
					]
				);
			}
		}

		echo "php yii intermode/update-outbound-product-info end" . "\n";
	}

	public function actionUpdateOutboundProductInfoV2()
	{
		echo "php yii intermode/update-outbound-product-info-v2 start" . "\n";
		/*
		76826,
		76827,
		76828,
		76829,
		76830,
		76831,
		76832,
		76833,
		76834,
		76835,
		76836,
		76837
		*/

		$stockAllQuery = OutboundOrderItem::find()
										  ->select("product_sku")
										  ->andWhere("product_id is NULL")
										  ->andWhere(["outbound_order_id"=>[
		'78166',
		'78167',
		'78168',
		'78169',
		'78170',
		'78171',
		'78172',
		'78173',
		'78174',
		'78175',
		'78176',
		'78177',
		'78178',
		'78179',
		'78180',
		'78181',
		'78182',
		'78183',
		'78184',
		'78185',
		'78186',
		'78187',
		'78188',
		'78189',
		'78190',
	]])->groupBy("product_sku")
		->asArray();

		$s = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
		foreach ($stockAllQuery->batch() as $stocks) {
			foreach ($stocks as $stock) {
				$product = $s->getByGuid($stock["product_sku"]);
				echo $stock["product_sku"]."\n";
				if (empty($product->product)) {
					echo"LOST"."\n";
					continue;
				}
				OutboundOrderItem::updateAll(
					[
						"product_id"=>	$product->product->id,
						"product_name"=>	$product->product->name,
						"product_model"=>	$product->product->model,
						"product_barcode"=>	 array_values($product->barcodes)[0],
						"product_color"=>	$product->product->color, // -
						"product_brand"=>	$product->product->field_extra1, // -
						"product_category"=>	$product->product->category, // -
					],
					[
						"product_sku"=>$stock["product_sku"],
						"outbound_order_id"=>[
							'78166',
							'78167',
							'78168',
							'78169',
							'78170',
							'78171',
							'78172',
							'78173',
							'78174',
							'78175',
							'78176',
							'78177',
							'78178',
							'78179',
							'78180',
							'78181',
							'78182',
							'78183',
							'78184',
							'78185',
							'78186',
							'78187',
							'78188',
							'78189',
							'78190',
						],
					]
				);
			}
		}

		echo "php yii intermode/update-outbound-product-info end" . "\n";
	}



	public function actionLoadProductsInfo()
	{
		echo "php yii intermode/load-products-info start". "\n";

		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('console/input-files/intermode/products/2024-10-22/products.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$productService = new ProductService();
		$clientID = 103;
		for ($i = 2; $i <= 100000; $i++) {
			$barcode = $excelActive->getCell('A' . $i)->getValue();
			$article = $excelActive->getCell('C' . $i)->getValue();
			$color = $excelActive->getCell('L' . $i)->getValue();
			$brand = $excelActive->getCell('M' . $i)->getValue();
			$category = $excelActive->getCell('N' . $i)->getValue();
			$name = $excelActive->getCell('J' . $i)->getValue();
			$nameModel = $excelActive->getCell('F' . $i)->getValue();
			$size = $excelActive->getCell('H' . $i)->getValue();
			$gender = $excelActive->getCell('O' . $i)->getValue();

			if (empty($article) && empty($barcode)) {
				continue;
			}
			$productInfo = [
				'barcode' => $barcode, // 91207409161 +
				'article' => $article, // NKBQ4567001T75 +
				'color' => $color, // ЧЁРНЫЙ +
				'brand' => $brand, // NIKE
				'category' => $category, // Кроссовки для города +
				'name' => $name, // КРОССОВКИ ДЛЯ ГОРОДА NKBQ4567001T75
				'nameModel' => $nameModel, // NIKE AIR MAX 97
				'size' => $size, // T75 +
				'gender' => $gender, // T75 +
			];

			echo $productInfo['article']."\n";

			if ($productService->isExistsModel($clientID, $productInfo['article'])) {
				continue;
			}
			$createDTO = new \stdClass();
			$createDTO->client_id = $clientID;
			$createDTO->model = $productInfo['article'];
			$createDTO->name = $productInfo['name'];
			$createDTO->color = $productInfo['color'];
			$createDTO->size = $productInfo['size'];
			$createDTO->category = $productInfo['category'];
			$createDTO->gender = $productInfo['gender'];
			$createDTO->field_extra1 = $productInfo['brand'];
			$createDTO->field_extra2 = $productInfo['nameModel'];
			$product = $productService->create($createDTO);
			if ($productService->isExistsBarcode($clientID, $productInfo['barcode'])) {
				continue;
			}
			$productBarcode = new ProductBarcodes();
			$productBarcode->product_id = $product->id;
			$productBarcode->client_id = $clientID;
			$productBarcode->barcode = $productInfo['barcode'];
			$productBarcode->save(false);
		}

		echo "php yii intermode/load-products-info end". "\n";
	}

	public function actionLoadProducts()
	{
		echo "php yii intermode/load-products start". "\n";

		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('console/input-files/intermode/products/2024-10-22/products.xlsx');
		$excel->setActiveSheetIndex(1);
		$excelActive = $excel->getActiveSheet();
		$lastArticle = "";
		$items = [];
		for ($i = 2; $i <= 100000; $i++) {
			$article = $excelActive->getCell('A' . $i)->getValue();
			$barcode = $excelActive->getCell('B' . $i)->getValue();

			if (empty($article) && empty($barcode)) {
				continue;
			}
			if (!empty($article)) {
				$lastArticle = $article;
			}
			if (empty($article)) {
				$article = $lastArticle;
			}
			$items[$article][] = $barcode;
		}

		$productService  = new ProductService();
		$clientID = 103;
		foreach ($items as $article=>$barcodes) {

			if(!$productService->isExistsModel($clientID,$article)) {
				$createDTO = new \stdClass();
				$createDTO->client_id = $clientID;
				$createDTO->model = $article;
				$product = $productService->create($createDTO);
			} else {
				$product = $productService->getProductByModel($article,$clientID);
			}

			foreach ($barcodes as $barcode) {
				if($productService->isExistsBarcode($clientID,$barcode)) {
					continue;
				}
				$productBarcode = new ProductBarcodes();
				$productBarcode->product_id = $product->id;
				$productBarcode->client_id = $clientID;
				$productBarcode->barcode = $barcode;
				$productBarcode->save(false);
				echo $article." / ".$barcode."\n";
			}
		}
		echo "php yii intermode/load-products end". "\n";
	}

	public function actionFindInvalidDm()
	{
		echo "php yii intermode/find-invalid-dm start". "\n";

		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('console/input-files/intermode/2024-11-22/find/f.xlsx');
//		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-22/find/f.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$count = 0;
		$lines = [];
		for ($i = 1; $i <= 100000; $i++) {
			$new_dm = $excelActive->getCell('A' . $i)->getValue();
			$new_dm = trim($new_dm);
			$count++;
			if (empty($new_dm)) {
				continue;
			}
//			if (substr($new_dm, 0, 3) == "MDE") {
//				$new_dm = base64_decode($new_dm, true);
//			}
			$lines[$count]["re_data_matrix"] = $new_dm;
		}
//		VarDumper::dump($lines,10,true);
//		die;
		$delimiter = "";
		$content = "";
		foreach ($lines as $line) {
			//$stock = Stock::find()->andWhere("product_qrcode LIKE '%".substr($line["re_data_matrix"],0,31)."'")->one();
			$qr = substr($line["re_data_matrix"],0,31);
			$stock = Stock::find()->andWhere(['LIKE', 'product_qrcode', $qr."%"])->one();
			echo $qr."\n";
			echo $line["re_data_matrix"]."\n";
			if(empty($stock)) {
				// VarDumper::dump($line,10,true);
				//echo "NOT FIND: ".$line["re_data_matrix"]."\n";
			} else {
				echo "FIND OK: ".$line["re_data_matrix"]."\n";
				$row = [
					$stock->product_qrcode,
				];
				//$content .= implode($row,$delimiter)."\n";
			}
		}
		echo "OK";
		echo "php yii intermode/find-invalid-dm end". "\n";
//		$filename = "FindInvalidDm-"."000001".".csv";
//		return  Yii::$app->response->sendContentAsFile( $content,$filename);
	}

	public function actionAddBarcodeByArticle()
	{  // other/intermode/add-barcode-by-article

		echo "php yii intermode/add-barcode-by-article start". "\n";
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('console/input-files/intermode/2025-01-27/diff.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$orderName = "2025-01-27-barcodes";
		$delimiter = ";";
		$content = "Article;BR1;BR2;BR3;"."\n";
		for ($i = 2; $i <= 100000; $i++) {
			$article =  $excelActive->getCell('A' . $i)->getValue();
			$bItems = [];
			if (empty($article)) {
				continue;
			}
			$bItems[] = $article;


			$barcodes = ProductBarcodes::find()->select('barcode')
									   ->andWhere(['product_id'=>
										   Product::find()->select('id')
												  ->andWhere(['model'=>$article])
										   // ->andWhere(['model'=>"TH0410166T4"])
									   ])
									   ->asArray()
									   ->all();

			if(empty($barcodes)) {
				$barcodes = [];
			}

			foreach ($barcodes as $barcode) {
				$bItems[] = $barcode["barcode"];
			}

			echo $i."-".$article."\n";

			$content = implode($bItems,$delimiter).$delimiter."\n";
			$filename = $orderName.".csv";
			file_put_contents($filename,$content,FILE_APPEND);

		}

	}

	public function actionBlockedProductForCheckBox()
	{  // other/intermode/blocked-product-for-check-box

		echo "php yii intermode/blocked-product-for-check-box start". "\n";


		$productBarcodeNotIn = [
			"8681558491513",
			"4010481379287",
			"8681558491834",
			"8681558491308",
		];

		$checkBoxStocks = CheckBoxStock::find()
									   ->select("stock_id")
									   ->andWhere(["inventory_id"=>3])
									   ->andWhere(["status"=>0])
									   ->asArray();
		foreach ($checkBoxStocks->batch(50) as $checkBoxStock) {
			$stockIDs = ArrayHelper::getColumn($checkBoxStock,"stock_id");
			$stocks = Stock::find()
						   ->andWhere(["id"=>$stockIDs])
						   ->andWhere(['status_availability'=>'2'])
						   ->andWhere(["not in",'product_barcode',$productBarcodeNotIn])
						   ->andWhere(["not LIKE",'field_extra1',"lostDB1703"])
						   ->all();

			foreach ($stocks as $stock) {
				echo $stock->id . "\n";
				file_put_contents("blocked-product-for-check-box_v6.csv",$stock->id.",". "\n",FILE_APPEND);
			}
		}
	}

	public function actionUpdateEcomOutboundProductInfo()
	{
		echo "php yii intermode/update-ecom-outbound-product-info start" . "\n";

		$stockAllQuery = EcommerceOutboundItem::find()
											  ->select("product_barcode")
											  ->andWhere("product_id is NULL")
											  ->groupBy("product_barcode")
											  ->asArray();

		$s = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
		foreach ($stockAllQuery->batch() as $stocks) {
			foreach ($stocks as $stock) {
				$product = $s->getProductInfoByBarcode($stock["product_barcode"]);
				echo $stock["product_barcode"]."\n";
				if (empty($product->product)) {
					echo"LOST"."\n";
					continue;
				}
				EcommerceOutboundItem::updateAll(
					[
						"product_id"=>	$product->product->id,
						"product_name"=>	$product->product->name,
						"product_model"=>	$product->product->model,
						"product_sku"=>	$product->product->client_product_id,
						"product_color"=>	$product->product->color, // -
						"product_brand"=>	$product->product->field_extra1, // -
						//"product_category"=>	$product->product->category, // -
					],
					[
						"product_barcode"=>$stock["product_barcode"]
					]
				);
			}
		}

		echo "php yii intermode/update-ecom-outbound-product-info end" . "\n";
	}


}