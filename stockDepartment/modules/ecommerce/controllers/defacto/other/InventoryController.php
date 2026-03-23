<?php

namespace app\modules\ecommerce\controllers\defacto\other;

use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\constants\TransferStatus;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceTransfer;
use common\ecommerce\entities\EcommerceTransferItems;
use PHPExcel_IOFactory;
use stdClass;
use stockDepartment\components\Controller;
use Yii;
use yii\helpers\VarDumper;
use common\modules\client\models\Client;
use common\ecommerce\entities\EcommerceInventory;

// ecommerce/defacto/ecom-other/index
class InventoryController extends Controller
{
	private $inventoryId = 2;
	private $clientId = Client::CLIENT_DEFACTO;
	private $stockStatusAvailabilityYES = StockAvailability::YES;

	private $statusInventoryLost = [
		EcommerceInventory::STATUS_SCAN_NO,
		EcommerceInventory::STATUS_SCAN_PROCESS
	];

	private $statusInventoryYes = [
		EcommerceInventory::STATUS_SCAN_YES,
	];

	public function actionValidatePluses() {
		// /ecommerce/defacto/other/inventory/validate-pluses

		//$data = $this->loadPlusesFromFile("/web/inventory/ecommerce/2024-12-07/not-damage.xlsx");
		$data = $this->loadPlusesFromFile("/web/inventory/ecommerce/2024-12-07/ecom-not-damage-v2.xlsx");
//		$data = $this->loadPlusesFromFile("/web/inventory/ecommerce/2024-12-07/damage-merge.xlsx");
// VarDumper::dump($data,10,true);
//
	//	die;
		$delimiter = ";";
		$content = "";
		$row = [
			"Короб",
			"Шк товара",
			"inventory_box_address_barcode",
			"inventory_place_address_barcode",
			"box_address_barcode",
			"place_address_barcode",
			"stock_id",
		];
		$content .= implode($row,$delimiter)."\n";
		
		$productsNotScanned = $this->findAllNotScannedLots();
		$boxProductStockID = [];
		foreach ($data as $box=>$products) {
			foreach ($products as $product) {
//				echo $box."<br />";
//				echo $product["barcode"]."<br />";
//				echo "<br />";
				foreach ($productsNotScanned as $i=>$stock) {
					if ($product["barcode"] == $stock->product_barcode) {
						//$key = $box."".$product["barcode"]."". $stock->id."";
						//$key = $stock->id;
						//if(!isset($boxProductStockID[$key])) {
						//	$boxProductStockID[$key] = $stock->product_barcode;
							//unset($productsNotScanned[$i]);
						//} else {
					//		continue;
					//	}

						$row = [
							$box,
							$stock->product_barcode,
							$stock->inventory_box_address_barcode,
							$stock->inventory_place_address_barcode,
							$stock->box_address_barcode,
							$stock->place_address_barcode,
							$stock->id,
						];
						$content .= implode($row,$delimiter)."\n";
						break;
					}
				}
			}
		}

		$filename = "find-not-damage-ecom-v2".".csv";
//		$filename = "find-damage-ecom".".csv";
		return  Yii::$app->response->sendContentAsFile( $content,$filename);


//		$products = $this->findAllNotScannedLots();
//		foreach ($products as $product) {
//			echo $product->product_barcode."<br />";
//			echo $product->box_address_barcode."<br />";
//			echo $product->place_address_barcode."<br />";
//			echo $product->status_inventory."<br />";
//			echo $product->inventory_box_address_barcode."<br />";
//			echo $product->inventory_place_address_barcode."<br />";
//			echo "-----"."<br />";
//		}
// VarDumper::dump($products,10,true);


		die;
}

	private function findAllNotScannedLots()
	{
		return EcommerceStock::find()->andWhere([
			'inventory_id' => $this->inventoryId,
			'client_id' => $this->clientId,
			'status_availability' => $this->stockStatusAvailabilityYES,
			'status_inventory' => $this->statusInventoryLost,
		])
			->orderBy([
				'address_sort_order'=>SORT_DESC,
			])
			->all();
	}

	public function actionAddInventoryPlusV1()
	{
		die("/ecommerce/defacto/ecom-other/add-inventory-plus-v1 die begin");

		$rootPath = Yii::getAlias('@stockDepartment') . '/web/inventory/ecommerce/08-12-2020/end-version-pluses.xlsx';
		$excel = PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 1;
		$productInBoxList = [];
		for ($i = $start; $i <= 1465; $i++) {
			$boxBarcode = $excelActive->getCell('A' . $i)->getValue(); // Коробка
			$productBarcode = $excelActive->getCell('B' . $i)->getValue(); // Полка

			$productBarcode = trim($productBarcode);
			$boxBarcode = trim($boxBarcode);

			if(empty($productBarcode) && empty($boxBarcode)) {
				continue;
			}

			$productInBoxList [$boxBarcode][] = [
				'productBarcode' => $productBarcode,
				'boxBarcode' => $boxBarcode,
			];

		}

//        VarDumper::dump($productInBoxList, 10, true);
//        die;

//        foreach ($productInBoxList as $boxBarcode=>$productInBoxItems) {
//            foreach ($productInBoxItems as $barcode) {
//                $productInMinus = EcommerceStock::find()
//                    ->andWhere(['inventory_id' => 1])
//                    ->andWhere(['status_inventory' => 1])
//                    ->andWhere(['box_address_barcode' => '0-inventory-0'])
//                    ->andWhere(['status_availability' => 2])
//                    ->andWhere(['product_barcode' => $barcode['productBarcode']])
//                    ->one();
//                if($productInMinus) {
//                    echo $barcode['productBarcode']."<br />";
//                }
//            }
//        }
//        die;

		$productCount = 0;
		foreach ($productInBoxList as $boxBarcode=>$productInBoxItems) {
			foreach ($productInBoxItems as $barcode) {

				$dtoForCreateStock = new stdClass();
				$dtoForCreateStock->clientId = 2;
				$dtoForCreateStock->inboundId = 0;
				$dtoForCreateStock->productBarcode = $barcode['productBarcode'];
				$dtoForCreateStock->conditionType = StockConditionType::UNDAMAGED;
				$dtoForCreateStock->clientBoxBarcode = '';
				$dtoForCreateStock->boxAddressBarcode = $barcode['boxBarcode'];
				$dtoForCreateStock->statusInbound = 0;
				$dtoForCreateStock->statusAvailability = StockAvailability::YES;
				$dtoForCreateStock->scanInDatetime = 0;
				$dtoForCreateStock->productName = "PlusInventory20241208";
				$dtoForCreateStock->apiStatus = StockAPIStatus::YES;

				$stockService = new Service();
				//$stockService->create($dtoForCreateStock);
				$productCount++;
			}
		}

//        VarDumper::dump($products, 10, true);
//        die;
		return 'ok ProductCount = '.$productCount;
	}

	private function loadPlusesFromFile($pathToFile) {
		$absPathToFile= \Yii::getAlias('@stockDepartment') . $pathToFile;

		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(1);

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

	public function actionAddPluses()
	{
		 die(" /ecommerce/defacto/other/inventory/add-pluses die begin");

		$data = $this->loadPlusesFromFile("/web/inventory/ecommerce/2024-12-08/result/stock-b2c-plusess.xlsx");

		$productCount = 0;
		foreach ($data as $box=>$products) {
			foreach ($products as $product) {
				$dtoForCreateStock = new stdClass();
				$dtoForCreateStock->clientId = 2;
				$dtoForCreateStock->inboundId = 0;
				$dtoForCreateStock->productBarcode = $product["barcode"];
				$dtoForCreateStock->conditionType = StockConditionType::UNDAMAGED;
				$dtoForCreateStock->clientBoxBarcode = '';
				$dtoForCreateStock->boxAddressBarcode = $box;
				$dtoForCreateStock->statusInbound = 0;
				$dtoForCreateStock->statusAvailability = StockAvailability::YES;
				$dtoForCreateStock->scanInDatetime = 0;
				$dtoForCreateStock->productName = "PlusInventory20241208";
				$dtoForCreateStock->apiStatus = StockAPIStatus::YES;

				$stockService = new Service();
				//$stockService->create($dtoForCreateStock);
				$productCount++;
			}
		}

        VarDumper::dump($data, 10, true);
//        die;
		return 'ok ProductCount = '.$productCount;
	}

	public function actionAddScannedInventory()
	{
		//die("/ecommerce/defacto/ecom-other/add-scanned-inventory die begin");

//		$transferRepository = new \common\ecommerce\defacto\transfer\repository\TransferRepository();
		$boxBarcodes = [
			"2430017694236"=>"2430017787087",
			"2430017694240"=>"2430017787086",
			"2430017694241"=>"2430017787085",
			"2430017694246"=>"2430017787084",
			"2430017694249"=>"2430017787083",
			"2430017694251"=>"2430017787082",
			"2430017694252"=>"2430017787081",
			"2430017694253"=>"2430017787080",
			"2430017694254"=>"2430017787079",
			"2430017694272"=>"2430017787078",
			"2430017694276"=>"2430017787077",
			"2430017694278"=>"2430017787076",
			"2430017694279"=>"2430017787075",
			"2430017694281"=>"2430017787074",
			"2430017694289"=>"2430017787073",
			"2430017694292"=>"2430017787072",
			"2430017694293"=>"2430017787071",
			"2430017694295"=>"2430017787070",
			"2430017694299"=>"2430017787069",
			"2430017694302"=>"2430017787068",
			"2430017694306"=>"2430017787067",
			"2430017694310"=>"2430017787066",
			"2430017694312"=>"2430017787065",
			"2430017694313"=>"2430017787064",
			"2430017694315"=>"2430017787063",
			"2430017694316"=>"2430017787062",
			"2430017694317"=>"2430017787061",
			"2430017694318"=>"2430017787060",
			"2430017694319"=>"2430017787059",
			"2430017694320"=>"2430017787058",
			"2430017694322"=>"2430017787057",
			"2430017694323"=>"2430017787056",
			"2430017699977"=>"2430017787055",
			"2430017699978"=>"2430017787054",
			"2430017699979"=>"2430017787053",
			"2430017699980"=>"2430017787052",
			"2430017699981"=>"2430017787051",
			"2430017699982"=>"2430017787050",
			"2430017699988"=>"2430017787049",
			"2430017699989"=>"2430017787048",
			"2430017700006"=>"2430017787047",
			"2430017700009"=>"2430017787046",
			"2430017700010"=>"2430017787045",
		];

		$transfers = [
//			'11321'=> '11321-19323467', // +
			'11323'=> '11323-19323468',
		];

		foreach ($transfers as $id=>$transferPickingList) {
			$stockList = EcommerceStock::find()->andWhere(['transfer_id' => $id])->all();

			foreach ($stockList as $stock) {
//				$stock->status_transfer = StockTransferStatus::SCANNED;
				$stock->transfer_outbound_box = isset($boxBarcodes[$stock->box_address_barcode]) ? $boxBarcodes[$stock->box_address_barcode] : $stock->box_address_barcode;
				$stock->scan_out_datetime = time();
				$stock->note_message1 = "OLD_BOX:".$stock->box_address_barcode;
//				$stock->save(false);
			}
		}

		return '-OK-';

		$transfers = [
			'11321'=> '11321-19323467', // +
			'11323'=> '11323-19323468',
		];

		foreach ($transfers as $id=>$transferPickingList) {
			$stockList = EcommerceStock::find()->andWhere(['transfer_id' => $id])->all();

			foreach ($stockList as $stock) {
				$stock->status_transfer = StockTransferStatus::SCANNED;
				$stock->transfer_outbound_box = $stock->box_address_barcode;
				$stock->scan_out_datetime = time();
//				$stock->save(false);
			}

			$transferList = EcommerceTransfer::find()->andWhere(['id' => $id])->all();

			foreach ($transferList as $transfer) {
				$transfer->accepted_qty = $transfer->expected_qty;
				$transfer->status = TransferStatus::SCANNED;;
//				$transfer->save(false);
			}

			$transferItemList = EcommerceTransferItems::find()->andWhere(['transfer_id' => $id])->all();

			foreach ($transferItemList as $transferItem) {

				$transferItem->accepted_qty = $transferItem->expected_qty;
				$transferItem->status = TransferStatus::SCANNED;;
//				$transferItem->save(false);
			}

		}

//		$dto = new \stdClass();
//		$dto->pickingListBarcode = $this->pickingListBarcode;
//		$dto->ourBoxBarcode = $this->ourBoxBarcode;
//		$dto->lcBarcode = $this->lcBarcode;
//		$dto->productBarcode = $this->productBarcode;



//		$rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/06-01-2022/B2Cplus.xlsx';
//		$excel = PHPExcel_IOFactory::load($rootPath);
//		$excel->setActiveSheetIndex(0);
//		$excelActive = $excel->getActiveSheet();
//
//		$start = 1;
//		$productInBoxList = [];
//		for ($i = $start; $i <= 4586; $i++) {
//
//			$box_barcode = $excelActive->getCell('A' . $i)->getValue();
//			$box_barcode = trim($box_barcode);
//
//			$product_barcode = $excelActive->getCell('B' . $i)->getValue();
//			$product_barcode = trim($product_barcode);
//
//			$productInBoxList [] = [
//				'box_barcode' => $box_barcode,
//				'product_barcode' => $product_barcode,
//			];
//		}

//		foreach ($productInBoxList as $product) {
//
//			$stockService = new Service();
//
//			$dtoForCreateStock = new \stdClass();
//			$dtoForCreateStock->clientId = 2;
//			$dtoForCreateStock->inboundId  = 0;
//			$dtoForCreateStock->productBarcode  = $product['product_barcode'];
//			$dtoForCreateStock->conditionType  = StockConditionType::UNDAMAGED;
//			$dtoForCreateStock->clientBoxBarcode  = '';
//			$dtoForCreateStock->boxAddressBarcode  = $product['box_barcode'];
//			$dtoForCreateStock->placeAddressBarcode  = '00-00-00-00';
//			$dtoForCreateStock->statusInbound = $stockService->getStatusInboundScanned();
//			$dtoForCreateStock->statusAvailability = StockAvailability::YES;
//			$dtoForCreateStock->scanInDatetime  = $stockService->makeScanInboundDatetime();
//
////			$stockService->create($dtoForCreateStock);
////			$stock = $stockService->create($dtoForCreateStock);
////			$stock->client_product_sku = EcommerceStock::find()
////														->select('client_product_sku')
////														->andWhere([
////															'product_barcode' => $product['product_barcode'],
////														])->andWhere(
////					'client_product_sku != "" OR client_product_sku is NOT NULL'
////				)
////														->scalar();
////			$stock->save(false);
//
////			$stockService->updateProductSku($dtoForCreateStock->productBarcode);
//
//		}

		return '-OK-';
	}

}