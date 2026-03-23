<?php

namespace stockDepartment\modules\other\controllers;

use common\api\DeFactoSoapAPI;
use common\b2b\domains\checkBox\entities\CheckBox;
use common\b2b\domains\checkBox\entities\CheckBoxStock;
use common\clientObject\constants\Constants;
use common\clientObject\hyundaiAuto\inbound\service\InboundOrderService;
//use common\clientObject\hyundaiAuto\outbound\service\OutboundService;
//use common\clientObject\hyundaiAuto\outbound\validation\ValidationOutbound;
use common\clientObject\main\outbound\repository\OutboundRepository;
use common\clientObject\main\service\SpreadsheetService;
use common\components\BarcodeManager;
use common\components\DeliveryProposalManager;
use common\components\MailManager;
use common\components\OutboundManager;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\defacto\api\ECommerceAPI;
use common\ecommerce\defacto\api\ECommerceAPINew;
use common\ecommerce\entities\EcommerceStock;
use common\helpers\DateHelper;
use common\managers\base\AllocateManager;
use common\modules\audit\models\OutboundOrderAudit;
use common\modules\audit\models\StockAudit;
use common\modules\client\models\ClientEmployees;
use common\modules\outbound\service\OutboundBoxService;
use common\modules\placementUnit\models\InboundUnitAddress;
use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\modules\returnOrder\models\ReturnOrderItemProduct;
use common\modules\stock\models\Inventory;
use common\modules\stock\models\InventoryRows;
use common\modules\stock\models\RackAddress;
use common\modules\stock\models\StockExtraField;
use common\modules\stock\service\ChangeAddressPlaceService;
use common\modules\user\models\User;
use common\modules\warehouseAddress\repository\RackAddressRepository;
use dektrium\user\helpers\Password;
use stockDepartment\modules\crossDock\models\CrossDockSearch;
use stockDepartment\modules\outbound\models\ScanningForm;
use stockDepartment\modules\report\models\TlDeliveryProposalFormSearch;
use stockDepartment\modules\returnOrder\entities\TmpOrder\ReturnTmpOrder;
use stockDepartment\modules\sheetShipment\repositories\PlaceAddressRepository;
use stockDepartment\modules\wms\managers\defacto\api\CrossDockItemDTO;
use stockDepartment\modules\wms\managers\defacto\api\CrossDockItemService;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use stockDepartment\modules\wms\managers\defacto\api\ProductSerializeDataDTO;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use common\modules\billing\models\TlDeliveryProposalBilling;
use common\modules\city\models\City;
use common\modules\client\models\Client;
use common\modules\crossDock\models\ConsignmentCrossDock;
use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItemProducts;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\returnOrder\models\ReturnOrderItems;
use common\modules\stock\models\Stock;
use common\modules\store\models\Store;
use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\modules\outbound\models\AllocationListForm;

use stockDepartment\modules\report\models\TlDeliveryProposalSearchReportExport;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use Yii;
use yii\validators\EmailValidator;
use yii\web\Response;

use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use function React\Promise\all;

class IntermodeController extends \yii\web\Controller
{

	public function actionUpdateBarcode()
	{
		// other/intermode/update-barcode

		$ps  = new \stockDepartment\modules\intermode\controllers\product\domains\ProductService();
		VarDumper::dump($ps->getGuidIdByBarcode("9330071952542"),10,true);
		$barcode = "9330071952542";
		VarDumper::dump(Product::find()->andWhere(["id"=>ProductBarcodes::find()
														->select("product_id")
														->andWhere([
															'barcode'=>$barcode
														])->orderBy("id DESC")]
		)->orderBy("id DESC")->scalar(),10,true);
		die;
		$items = OutboundOrderItem::find()
								  ->andWhere([
									  "outbound_order_id"=>[
										  78042,77986,77989,77991,77992,77993,77995,77997
									  ],
									  "product_barcode"=> "",
								  ])->all();

		foreach ($items as $item) {

			$productInfo = $ps->getByGuid($item->product_sku);
			$item->product_barcode = array_shift($productInfo->barcodes);
			VarDumper::dump($item->product_barcode,10,true);
			$item->field_extra1 = 1;
			//$item->save(false);
		}
		die("OK");

		return "";
	}

	public function actionAddGsFromFile()
	{ //  other/intermode/add-gs-from-file

		$pathToCSVFile = 'intermode/2025-02-28/qr.csv';
		$content = "";
		foreach(file($pathToCSVFile) as $line) {
				if (strlen($line) < 127) {
					continue;
				}

				$qr = $line;
				$gsCode = "";
				$gs = explode($gsCode,$qr);
				if(count($gs) == 1) {
					$part1 = substr($qr,0,31);
					$part2 = substr($qr,31,6);
					$part3 = substr($qr,37);
					$qr = $part1.$gsCode.$part2.$gsCode.$part3;
				}

				$content .= $qr;
		}

		$filename = "qr-with-gs-".date("Ym").".csv";
		return Yii::$app->response->sendContentAsFile( $content,$filename);
	}
	
	public function actionUpdateInboundType()
	{  // other/intermode/update-inbound-type
		$items = [
			"00TK-001222_05_02_2025",
			"00TK-001546_11_02_2025",
			"00TK-001493_10_02_2025",
			"00TK-001169_04_02_2025",
			"00TK-001284_06_02_2025",
			"00TK-001153_04_02_2025",
			"00TK-000870_29_01_2025",
			"00TK-006187_29_12_2024",
			"00TK-000726_24_01_2025",
			"00TK-000688_23_01_2025",
			"00TK-000214_09_01_2025",
			"00TK-000400_15_01_2025",
			"00TK-000507_17_01_2025",
			"00TK-000398_14_01_2025",
			"00TK-000513_19_01_2025",
			"00TK-000393_18_12_2024",
			"00TK-000252_12_01_2025",
			"00TK-000247_11_01_2025",
			"00TK-000274_13_01_2025",
			"00TK-000290_13_01_2025",
			"00TK-000123_07_01_2025",
			"00TK-000340_14_01_2025",
			"00TK-000253_12_01_2025",
			"00TK-005401_06_12_2024",
			"00TK-004545_12_11_2024",
			"00TK-005946_23_12_2024",
			"00TK-006140_27_12_2024",
			"00TK-000257_12_01_2025",
			"00TK-000255_12_01_2025",
			"00TK-000250_11_01_2025",
			"00TK-005175_03_12_2024",
			"00TK-005955_23_12_2024",
			"00TK-005571_12_12_2024",
			"00TK-005668_15_12_2024",
			"00TK-005642_13_12_2024",
			"00TK-006210_30_12_2024",
			"00TK-005689_16_12_2024",
			"00TK-005743_17_12_2024",
			"00TK-005624_13_12_2024",
			"00TK-006135_27_12_2024"
		];

//		$iOrders = InboundOrder::find()
//							   ->andWhere("id >= 122367 ")
//							   ->orderBy("id DESC")
//							   ->all();
//		foreach ( $iOrders as $in) {
		foreach ( $items as $orderNumber) {
			$iOrder = InboundOrder::find()
								  ->andWhere("order_number LIKE '" . trim($orderNumber) . "'")
								  ->one();
			if($iOrder) {
				echo $iOrder->order_number."<br />";
				$iOrder->order_type = InboundOrder::ORDER_TYPE_RETURN;
				//$iOrder->save(false);
			} else {
				echo "NOT FIND"."<br />";
			}
		}

		VarDumper::dump($items,10,true);

		return "return SUCCESS OK";
	}
	

	// [
	// 	0 => 'Style'
	// 	1 => 'Color'
	// 	2 => 'Description'
	// 	3 => 'UPC'
	// 	4 => 'Size'
	// 	5 => 'QTY'
	// 	6 => 'Datamatrix Code'
	// ] [
	// 	0 => 'PH4012CB8T4'
	// 	1 => 'CB8'
	// 	2 => 'Поло с коротким рукавом'
	// 	3 => '8001047065306'
	// 	4 => 'T4'
	// 	5 => '1'
	// 	6 => 'MDEwNDY2MDMzMzAwMzA2MDIxNVN2ZXBjWnQ1bGtOOh05MUVFMTAdOTJOeFg3dmFPWE9BckthZ2pQWGgrV2xIcU5hbFpNWkdrVEZDZnoxSGVrQXUwPQ=='
	// ]
	public function actionSeparateStoreByQr()
	{ //  other/intermode/separate-store-by-qr

		$items = [];
		$pathToCSVFile = 'intermode/2024-10-21/qr-datamatrix.csv';
        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 5000, ";")) !== FALSE) {
				if(!isset($data["3"])) {
					continue;
				}
				$stock = Stock::find()
//							  ->select("outbound_picking_list_barcode")
							  ->andWhere("product_sku is NULL" )
							  ->andWhere([
//								  "product_barcode" =>$data["3"],
								  "product_model" =>$data["0"],
								  "outbound_order_id" => [
									  74381, 74946, 74957, 74958, 74959, 74963, 74964, 74966, 74967, 75006, 75022,
									  75023, 74632, 74802, 74805, 74806, 74843, 74860, 74861,74862, 74909,
								  ],
							  ])->one();
				$orderNumber = "";
				$productBarcode = $data["3"];
				if ($stock) {
					$stock->product_sku = "y";
					//$stock->save(false);
					$orderNumber = OutboundOrder::findOne($stock->outbound_order_id)->order_number;
					$productBarcode = $stock->product_barcode;
				}

				$items[$orderNumber][] = [
					$data["0"],
					$data["1"],
					$data["2"],
					$data["3"],
					$data["4"],
					$data["5"],
					$data["6"],
					$orderNumber,
				];
//				file_put_contents('separate-store-by-qr-003.csv', implode(";",$items) . "\n", FILE_APPEND);

            }
        }

        foreach ($items as $orderNumber=>$rows) {
			foreach ($rows as $row) {
				file_put_contents('separate-store-by-qr-'.$orderNumber.'.csv', implode(";", $row) . "\n", FILE_APPEND);
			}
		}
		die("return end ok");
		return $this->render('index');
	}

	public function actionAddArticle()
	{
		// other/intermode/add-article
		//die('-START DIE END-');
		$excel = \PHPExcel_IOFactory::load('intermode/2024-10-21/opt-store.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		for ($i = 2; $i <= 25000; $i++) {
			$boxBarcode = $excelActive->getCell('A' . $i)->getValue();
			$orderNumber = $excelActive->getCell('B' . $i)->getValue();
			$storeName = $excelActive->getCell('C' . $i)->getValue();
			$productBarcode = $excelActive->getCell('D' . $i)->getValue();

			$items = [
				'boxBarcode' => $boxBarcode,
				'orderNumber' => $orderNumber,
				'storeName' => $storeName,
				'productBarcode' => $productBarcode,
				'productArticle' => Stock::find()->select("product_model")->andWhere(["product_barcode"=>$productBarcode])->scalar(),
			];
			file_put_contents('intermode-opt-2.csv', implode(";",$items) . "\n", FILE_APPEND);
		}
		die("return end ok");
		return $this->render('index');
	}
	
		public function actionAddProductToStock()
	{  // other/intermode/add-product-to-stock
		return "return srart end";
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-26/stock/NORMAL_OZZE.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$items = [];
		$clientID = 103;
		$lastBox = "";
		for ($i = 2; $i <= 100000; $i++) {
			$box = trim($excelActive->getCell('A' . $i)->getValue());
			$barcode = trim($excelActive->getCell('B' . $i)->getValue());
			$article = trim($excelActive->getCell('C' . $i)->getValue());
			$qty = $excelActive->getCell('D' . $i)->getValue();

			if (empty($article) && empty($barcode)) {
				continue;
			}

			if (!empty($box)) {
				$lastBox = $box;
			}
			$items[$lastBox][] = [
				'barcode' => $barcode,
				'article' => $article,
				'qty' => $qty,
			];
			}

		foreach ($items as $box=>$rows) {
			foreach ($rows as $item) {
				$stock = new Stock();
				$stock->client_id = $clientID;
				$stock->inbound_order_id = 0;
				$stock->product_barcode = $item["barcode"];
				$stock->product_model = $item["article"];
				$stock->product_name = "";
				$stock->primary_address = $box;
				$stock->status = Stock::STATUS_INBOUND_NEW;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_NO;
				$stock->system_status = "normal_ozze_24_11_26";
				//$stock->save(false);
			}
		}
			return "return SUCCESS OK";
		}
		
/**
 * На основе отгрузочной накладной добавляем товары на сток для того чтобы отгрузить накладную без ошибок.
 * */
	public function actionMakeVirtualOutboundOrder()
	{ //  other/intermode/make-virtual-outbound-order

		return "return srart end";
		
		$order = OutboundOrder::findOne(76020);
		$items = OutboundOrderItem::findAll(["outbound_order_id"=>$order->id]);
		$box = "500000000000";
		$palace = "1-1-01-0";
		foreach ($items as $item) {
			for ($i = 0; $i < $item->expected_qty; $i++) {
				$stock = new Stock();
				$stock->client_id = 103;
				$stock->outbound_order_id = $item->outbound_order_id;
				$stock->outbound_order_item_id = $item->id;
				$stock->product_barcode =$item->product_barcode;
				$stock->product_model = "";
				$stock->product_name = "";
				$stock->primary_address = $box;
				$stock->secondary_address = $palace;
				$stock->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
				$stock->status_availability = Stock::STATUS_AVAILABILITY_RESERVED;
				$stock->system_status = "make-virtual-outbound-order";
				// $stock->save(false);
			}
			$item->allocated_qty = $item->expected_qty;
			//$item->save(false);
		}
		$order->allocated_qty = $order->expected_qty;
		$order->accepted_qty = 0;
		$order->status = Stock::STATUS_OUTBOUND_FULL_RESERVED;
		//$order->save(false);
		return "Done";
	}
/**
	 * Заполнить приходную накладную из уже отгруженных накладных
	 * */
	public function actionLoadInboundFromOutbound()
	{ //  other/intermode/load-inbound-from-outbound

		  return "return start end";
		$outboundIDs = [
			76025,	//	00TK-003116_15_03_2025
			76026,	//	00TK-003117_15_03_2025
		];
		$order = InboundOrder::findOne(122535);
		//$inItems = InboundOrderItem::findAll(["inbound_order_id"=>$order->id]);
		$outItems = OutboundOrderItem::find()->andWhere(["outbound_order_id"=>$outboundIDs])->all();
		foreach ($outItems as $outItem) {
			for ($i = 0; $i < $outItem->expected_qty; $i++) {
				$inItem = InboundOrderItem::find()->andWhere([
					"inbound_order_id"=>$order->id,
					"product_barcode"=>$outItem->product_barcode,
				])->one();
				if(empty($inItem)) {
					echo $outItem->product_barcode."<br />";
					continue;
				}

				if($inItem->expected_qty > $inItem->accepted_qty) {
					$inItem->accepted_qty +=1;
					$inItem->allocated_qty +=1;
					//$inItem->save(false);
				}	else  {
					$inItem->accepted_number_places_qty +=1;
					//$inItem->save(false);
				}
			}
		}
		//$order->save(false);
		return "Done";
	}

	public function actionAddPluses()
	{
		 die(" /other/intermode/add-pluses die begin 2");
		// $data = $this->loadPlusesFromFileXlsx("/web/lostDB1703/18032025_pluses.xlsx");
		//$data = $this->loadPlusesFromFileXlsx("/web/lostDB1703/25032025_lost_of_stock_pluses.xlsx");
		// $data = $this->loadPlusesFromFileXlsx("/web/lostDB1703/25032025_lost_after_last_invent_pluses.xlsx");
		$data = $this->loadPlusesFromFileXlsx("/web/add/add_find_after_close_inbound_order.xlsx");

		$productCount = 0;
		$boxCount = 0;
		foreach ($data as $box=>$products) {
			$boxCount++;
			foreach ($products as $product) {
				$inbound_id = isset($product["inbound_id"]) && !empty($product["inbound_id"]) ? $product["inbound_id"] : null;

				$stock = new Stock();
				$stock->client_id = 103;
				$stock->inbound_order_id = $inbound_id;
				$stock->secondary_address = '212-12-1';
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
				$stock->field_extra1 = "addFindAfterCloseInbound";
				// if (!$stock->save(false)) {
				//	echo "NO " . $product['barcode'];
				// }
				$productCount++;
			}
		}

		VarDumper::dump($data, 10, true);
		$r = 'ok ProductCount = '.$productCount;
		$r .= 'ok BoxCount = '.$boxCount;
		$r .= "<br /> -- ok -- <br />";
//        die;

		return $r;
	}

	private function loadPlusesFromFileXlsx($pathToFile) {
		$absPathToFile= \Yii::getAlias('@stockDepartment') . $pathToFile;
//		VarDumper::dump($absPathToFile,10,true);
//die("dfsdfsd");
		$excel = \PHPExcel_IOFactory::load($absPathToFile);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet(0);

		$start = 1;
		$lastBox = "";
		for ($i = $start; $i <= 50000; $i++) {
			$box = $excelActive->getCell('A' . $i)->getValue();
			$barcode = $excelActive->getCell('B' . $i)->getValue();
			$inboundId = $excelActive->getCell('C' . $i)->getValue();

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
				'inbound_id' => $inboundId,
			];
		}

		return $products;
	}

}





