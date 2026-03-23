<?php

namespace app\modules\wms\controllers\erenRetail;

use common\components\BarcodeManager;
use common\modules\outbound\models\ConsignmentOutboundOrder;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use common\modules\employees\models\Employees;
use common\modules\outbound\models\OutboundOrder;
use common\modules\outbound\models\OutboundOrderItem;
use common\modules\outbound\models\OutboundPickingLists;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use common\modules\transportLogistics\models\TlDeliveryProposalOrders;
use stockDepartment\modules\outbound\models\BeginEndPickListForm;
use stockDepartment\modules\outbound\models\OutboundOrderSearch;
use stockDepartment\modules\outbound\models\OutboundPickingListSearch;
use stockDepartment\modules\wms\managers\erenRetail\outbound_data_matrix\OutboundDataMatrixForm;
use stockDepartment\modules\wms\managers\erenRetail\outbound_data_matrix\OutboundDataMatrixService;
use Yii;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\outbound\models\OutboundPickListForm;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use common\helpers\DateHelper;
use stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix;
use stockDepartment\modules\wms\models\erenRetail\InboundDataMatrix2;

class ReOutboundDataMatrixController extends Controller
{

	public function actionMakeInboundFileV3()
	{
		// http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/make-inbound-file-v3

		$lines = Stock::find()->select("DISTINCT (box_barcode) ")->andWhere([
			"outbound_order_id"=>76281,
			"system_status"=>'2025-04-28v1',
		])->asArray()
		->column();

		$InBoxLines = [];
		foreach ($lines as $line) {
			$inBox = Stock::find()->andWhere([
				"box_barcode"=>$line,
			])->asArray()
			->all();

			foreach ($inBox as $inBoxValue) {
				$InBoxLines[] = [
					"product_model"=>$inBoxValue["product_model"],
					"product_barcode"=>$inBoxValue["product_barcode"],
					"product_qrcode"=>$inBoxValue["product_qrcode"],
				];
			 }
		}


//		VarDumper::dump($InBoxLines,10,true);
		//VarDumper::dump($lines,10,true);
//		die;
		$orderName = "00TK-004847_16_04_2025";
		$orderName = "00TK-004903_17_04_2025";
		$orderName = "00TK-004571_11_04_2025";
		$delimiter = ";";
		$content = "Style;Color;Description;UPC;Size;QTY;DM;"."\n";
		foreach ($InBoxLines as $line) {
			$dataMatrix = $line["product_qrcode"];
			if (empty($dataMatrix)) {
				continue;
			}
			$dataMatrix = base64_encode($dataMatrix);

			$row = [
				$line["product_model"], // Style
				"-", // Color
				"-", // Description
				$line["product_barcode"], // UPC
				"-", // Size
				"1", // QTY
				$dataMatrix, // DM
			];
			$content .= implode($row,$delimiter).$delimiter."\n";
		}

		$filename = "inbound-with-data-matrix-".$orderName.".csv";
		return Yii::$app->response->sendContentAsFile( $content,$filename);
	}

	public function actionIndexV3()
	{
		// http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/index-v3
		// die(" - STOP - ");
		$lines = [];
		$count = 0;
//		if (($handle = fopen("ReOutboundDataMatrix/2025-04-24/data-matrix-00TK-004847_16_04_2025.csv", "r")) !== FALSE) {
//		if (($handle = fopen("ReOutboundDataMatrix/2025-04-24/data-matrix-00TK-004903_v2.csv", "r")) !== FALSE) {
		if (($handle = fopen("ReOutboundDataMatrix/2025-04-28/data-matrix-00TK-004571_11_04_2025.csv", "r")) !== FALSE) {			
			while (($data = fgetcsv($handle, 100000, "|")) !== FALSE) {
				$count++;
				if (!isset($data['5'])) {
					continue;
				}
				$dm = trim($data['5']);
				if (!empty($dm)) {
					$lines[] = [
						"stock_id"=>trim($data['4']),
						"dm"=>$dm,
					];
				}
			}
		}

		// VarDumper::dump($lines,10,true);
		// die;
		$systemStatus = "2025-04-28v1";
		foreach ($lines as $line) {
			$stock = Stock::find()->andWhere(["id"=>$line["stock_id"]])->one();
			if(empty($stock)) {
				VarDumper::dump($line,10,true);
				echo "not find <br />";
			} else {
				if (!empty($line["dm"])) {
					$stock->system_status = $systemStatus;
					$stock->system_status_description = $stock->product_qrcode;
					$stock->product_qrcode = $line["dm"];
					$stock->save(false);
					echo "stock_id: ".$stock->outbound_order_id." / ".$line["stock_id"]."<br />";
					//echo "stock_id re_data_matrix_orig: ".$line["re_data_matrix_orig"]."<br />";
				}

				$stock = Stock::find()->andWhere(["id"=>$stock->id])->one();

				$gsCode = "";
				$gs = explode($gsCode,$stock->product_qrcode);
				if(count($gs) == 1) {
					$qr = $stock->product_qrcode;
					$part1 = substr($qr,0,31);
					$part2 = substr($qr,31,6);
					$part3 = substr($qr,37);
					$toSave = $part1.$gsCode.$part2.$gsCode.$part3;
					$stock->product_qrcode = $toSave;
					$stock->save(false);
					echo "toSave:".$stock->id." DM: ".$toSave."<br />";
					//echo "toSave re_data_matrix_orig:".$line["re_data_matrix_orig"]."<br />";
				}
			}
		}

		return "ok";
	}
	
	public function actionIndexV2()
	{
		// http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/index-v2

		//$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-10-31/from_f.xlsx');
		// $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-01/from_f.xlsx');
		// $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-07/from_f.xlsx');
		// $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-08/from_f.xlsx');
		// $excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-08/from_f2.xlsx');
		//$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-20/from_f.xlsx');
		//$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-20/4433.xlsx');
		//$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-21/from_f.xlsx');
		//$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-22/from_f.xlsx');
	//	$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2025-02-11/from_f.xlsx');
		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2025-02-11/02-11/from_f.xlsx');
		
		
		// 75711,75712, 75713, 75714, 75715
		
		
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$count = 0;
		$lines = [];
		$systemStatus = "2025-02-11_v2";
		for ($i = 1; $i <= 100000; $i++) {
			$stock_id = $excelActive->getCell('E' . $i)->getValue();
			$new_dm = $excelActive->getCell('F' . $i)->getValue();

			$stock_id = trim($stock_id);
			$new_dm = trim($new_dm);
			if (empty($new_dm)) {
				continue;
			}
			$count++;

			if (substr($new_dm, 0, 3) == "MDE") {
				$new_dm = base64_decode($new_dm, true);
			}

			$lines[$count]["re_data_matrix"] = $new_dm;
			$lines[$count]["stock_id"] = $stock_id;
		}
		
VarDumper::dump($lines,10,true);
die;
		foreach ($lines as $line) {
			$stock = Stock::find()->andWhere(["id"=>$line["stock_id"]])->one();

			if(empty($stock)) {
				VarDumper::dump($line,10,true);
				echo "not find <br />";
			} else {
				if (!empty($line["re_data_matrix"])) {
					$stock->system_status = $systemStatus;
					//$stock->system_status = "2024_11_08";
					$stock->system_status_description = $stock->product_qrcode;
					$stock->product_qrcode = $line["re_data_matrix"];
					$stock->save(false);
					echo "stock_id: ".$stock->outbound_order_id." / ".$line["stock_id"]."<br />";
					//echo "stock_id re_data_matrix_orig: ".$line["re_data_matrix_orig"]."<br />";
				}

				$stock = Stock::find()->andWhere(["id"=>$stock->id])->one();

				$gsCode = "";
				$gs = explode($gsCode,$stock->product_qrcode);
				if(count($gs) == 1) {
					$qr = $stock->product_qrcode;
					$part1 = substr($qr,0,31);
					$part2 = substr($qr,31,6);
					$part3 = substr($qr,37);
					$toSave = $part1.$gsCode.$part2.$gsCode.$part3;
					$stock->product_qrcode = $toSave;
					$stock->save(false);
					echo "toSave:".$stock->id." DM: ".$toSave."<br />";
					//echo "toSave re_data_matrix_orig:".$line["re_data_matrix_orig"]."<br />";
				}
			}
		}

		return "ok";
	}
	
	
	public function actionFindInvalidDm()
	{
		// http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/find-invalid-dm

		$excel = \PhpOffice\PhpSpreadsheet\IOFactory::load('ReOutboundDataMatrix/2024-11-22/find/f.xlsx');
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
		//VarDumper::dump($lines,10,true);
		//die;
		//VarDumper::dump($lines,10,true);
		//die;
		$delimiter = "";
		$content = "";
		foreach ($lines as $line) {
				$qr = substr($line["re_data_matrix"],0,31);
				//$stock = Stock::find()->andWhere("product_qrcode LIKE '%".substr($line["re_data_matrix"],0,31)."'")->one();
				$stock = Stock::find()->andWhere(['LIKE', 'product_qrcode', $qr."%"])->one();
				
			if(empty($stock)) {
				VarDumper::dump($line,10,true);
			} else {
				$row = [
					$stock->product_qrcode,
				];
				$content .= implode($row,$delimiter)."\n";
			}
		}
		echo "OK";
//		$filename = "FindInvalidDm-"."000001".".csv";
//		return  Yii::$app->response->sendContentAsFile( $content,$filename);
	}
	
	
	
		public function actionIndex()
	{

        // http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/index
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-060700.csv'; // 72689 1010
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-030732.csv'; // 72687 1011
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-110701-from-bota.csv'; // 72692 1008
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-100733-from-bota.csv'; // 72691 1001
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-040745-from-bota.csv';  // 72688 1024
		// $pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-130756-from-bota.csv';  // 72693 1041
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-150709-from-bota.csv';  // 72694 1041
		// $pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240710-103-090716-from-bota.csv';  // 72726 1041
		// $pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240710-103-100753-from-bota.csv';  // 72727 1041
//		$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-030732-from-bota.csv';  // 72687	20240709-103-030732
		 return "NO";

		$list = [
			// "74632"=> 'ReOutboundDataMatrix/2024-10-29/data-matrix-00TK-002907_24_09_2024.csv',
			//"74861"=> 'ReOutboundDataMatrix/2024-10-29/data-matrix-00TK-003280_02_10_2024.csv',
			//"74946"=> 'ReOutboundDataMatrix/2024-10-29/data-matrix-00TK-003554_10_10_2024.csv',
			
			//"74802"=> 'ReOutboundDataMatrix/2024-10-29/data-matrix-00TK-003276_02_10_2024_new.csv',
			//"74958"=> 'ReOutboundDataMatrix/2024-10-29/data-matrix-00TK-003556_10_10_2024_new.csv',
			//"74909"=> 'ReOutboundDataMatrix/2024-10-29/data-matrix-00TK-003569_10_10_2024_new_30.csv',
			//"74802"=> 'ReOutboundDataMatrix/2024-10-29/update_2/data-matrix-00TK-003276_02_10_2024.csv',
			// "74861"=> 'ReOutboundDataMatrix/2024-10-29/update_2/data-matrix-00TK-003280_02_10_2024.csv',
			// "74946"=> 'ReOutboundDataMatrix/2024-10-29/update_2/data-matrix-00TK-003554_10_10_2024.csv',
			// "74957"=> 'ReOutboundDataMatrix/2024-10-29/update_2/data-matrix-00TK-003555_10_10_2024.csv',
			// "74958"=> 'ReOutboundDataMatrix/2024-10-29/update_2/data-matrix-00TK-003556_10_10_2024.csv',
			"74909"=> 'ReOutboundDataMatrix/2024-10-29/update_2/data-matrix-00TK-003569_10_10_2024.csv',
		];

		$lines_ = [];
		$count = 0;
		if (($handle = fopen("ReOutboundDataMatrix/2024-10-29/update_2/CSV.csv", "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 100000, ";")) !== FALSE) {
				$count++;
				if($count == 1) {
					continue;
				}
				$lines_[$count]["sku"] = trim($data['0'],'"');
				$lines_[$count]["barcode"] =   trim($data['1'],'"');
				$lines_[$count]["data_matrix"] = base64_decode($data['2'], true);
				$lines_[$count]["data_matrix_orig"] = $data['2'];
			}
		}
		
			//	VarDumper::dump($lines_,10,true);

		foreach ($list as $outboundId=>$pathToCSVFile) {
			$lines = [];
			$count = 0;
			if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 100000, "	")) !== FALSE) {
					$count++;

					if(empty($data['4'])) {
						continue;
					}

					$lines[$count]["data_matrix"] = $data['3'];
					$lines[$count]["stock_id"] =  $data['4'];
					$lines[$count]["re_data_matrix"] = "";
					$lines[$count]["re_data_matrix_orig"] = "";
					if(!empty($data['5'])) {
						$lines[$count]["re_data_matrix"] = base64_decode($data['5'], true);
						$lines[$count]["re_data_matrix_orig"] = $data['5'];
					}
					
					//echo "stock_id =:> ".$lines[$count]["stock_id"]."<br />";
					
					foreach ($lines_ as $key=>$item) {
						
						//if ($lines[$count]["stock_id"] == "4502086") {
						//	echo "data_matrix1: ".substr($item["data_matrix_orig"],0,57)."<br />";
						//	echo "data_matrix2: ".substr($lines[$count]["data_matrix"],0,57)."<br />";
						//}
						
						//if(substr($item["data_matrix_orig"],0,26) == substr($lines[$count]["data_matrix"],0,26)) {
						if(substr($item["data_matrix_orig"],0,31) == substr($lines[$count]["data_matrix"],0,31)) {
							$lines[$count]["re_data_matrix"] = base64_decode($item["data_matrix_orig"], true);
							echo "data_matrix: ".$lines[$count]["re_data_matrix"]."<br />";
						}
					}
				}
			}
//die;
//			$outboundId = $outboundId;
			foreach ($lines as $line) {
				$stock = Stock::find()->andWhere([
					"id"=>$line["stock_id"],
				])->one();

				if(empty($stock)) {
					VarDumper::dump($line,10,true);
				} else {
					if (!empty($line["re_data_matrix"])) {
						$stock->system_status = 5;
						$stock->product_qrcode = $line["re_data_matrix"];
						$stock->system_status_description = $line["data_matrix"];
						//$stock->save(false);
						echo "stock_id: ".$stock->outbound_order_id." / ".$line["stock_id"]."<br />";
						//echo "stock_id re_data_matrix_orig: ".$line["re_data_matrix_orig"]."<br />";
					}
					
					$stock = Stock::find()->andWhere(["id"=>$stock->id])->one();
					
					$gsCode = "";
					$gs = explode($gsCode,$stock->product_qrcode);
					if(count($gs) == 1) {
						$qr = $stock->product_qrcode;
						$part1 = substr($qr,0,31);
						$part2 = substr($qr,31,6);
						$part3 = substr($qr,37);
						$toSave = $part1.$gsCode.$part2.$gsCode.$part3;
						$stock->product_qrcode = $toSave;
						//$stock->save(false);
						echo "toSave:".$stock->id." DM: ".$toSave."<br />";
						//echo "toSave re_data_matrix_orig:".$line["re_data_matrix_orig"]."<br />";
					}
				}
			}
		}

		return "ok";
	}
	
	
	
	public function actionIndex_2024_10_29()
	{

// http://wms.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/index
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-060700.csv'; // 72689 1010
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-030732.csv'; // 72687 1011
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-110701-from-bota.csv'; // 72692 1008
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-100733-from-bota.csv'; // 72691 1001
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-040745-from-bota.csv';  // 72688 1024
		// $pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-130756-from-bota.csv';  // 72693 1041
		//$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-150709-from-bota.csv';  // 72694 1041
		// $pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240710-103-090716-from-bota.csv';  // 72726 1041
		// $pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240710-103-100753-from-bota.csv';  // 72727 1041
		$pathToCSVFile = 'ReOutboundDataMatrix/data-matrix-20240709-103-030732-from-bota.csv';  // 72687	20240709-103-030732
		return "NO";
		
		
		$lines = [];
		$count = 0;
        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, "|")) !== FALSE) {
				$count++;
				
				//VarDumper::dump($pathToCSVFile,10,true);
				//VarDumper::dump("",10,true);
				//VarDumper::dump($data,10,true);
				//die;
				
				$lines[$count]["order_number"] = $data['0'];
				$lines[$count]["box"] = $data['1'];
				$lines[$count]["product_barcode"] = $data['2'];
				$lines[$count]["data_matrix"] = $data['3'];
				$lines[$count]["re_data_matrix"] = "";
				if(!empty($data['4'])) {
					$dataMatrix = $data['4'];
					if ( base64_encode(base64_decode($dataMatrix, true)) === $dataMatrix){
						$dataMatrix = base64_decode($dataMatrix, true);
					}
					$lines[$count]["re_data_matrix"] = $dataMatrix;
				}

            }
        }
		
        $outboundId = 72687;
		foreach ($lines as $line) {
			$stock = Stock::find()->andWhere([
				"outbound_order_id"=>$outboundId,
				"outbound_picking_list_barcode"=>$line["order_number"],
				"box_barcode"=>$line["box"],
				"product_barcode"=>$line["product_barcode"],
				"product_qrcode"=>$line["data_matrix"],
			])->one();
			if(empty($stock)) {
				VarDumper::dump($line,10,true);
			} else {
				if (!empty($line["re_data_matrix"])) {
					$stock->system_status = 2;
					$stock->product_qrcode = $line["re_data_matrix"];
					$stock->system_status_description = $line["data_matrix"];
					//$stock->save(false);
				}
				$gsCode = "";
				$gs = explode($gsCode,$stock->product_qrcode);
				if(count($gs) == 1) {
					$qr = $stock->product_qrcode;
					$part1 = substr($qr,0,31);
					$part2 = substr($qr,31,6);
					$part3 = substr($qr,37);
					$toSave = $part1.$gsCode.$part2.$gsCode.$part3;
					$stock->product_qrcode = $toSave;
					//$stock->save(false);
					//echo "toSave:".$toSave."<br />";
				}
			}
		}
		return "ok";
	}
	
	
	public function actionAddGs()
	{
		// http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/add-gs
		  return "NO-add-gs";
		// 1017 73180
		// 1096 73181
		// 1079 73182
		// 1030 73183
		// 1078 73184
		// 1006 73185
		//$outboundId = [73180,73181,73182,73183,73184,73185]; // 1017
		//$outboundId = [73635]; // 1017
		//$outboundId = [74172, 74173, 74174,74175,74176, 74196, 74197, 74246]; // 1017
		$outboundId = [		
75711,75712, 75713, 75714, 75715
		]; // 1017
		$lines = Stock::find()->andWhere([
			"outbound_order_id" => $outboundId,
		])->all();
		$gsCode = "";
		foreach ($lines as $line) {
			$gs = explode($gsCode, $line->product_qrcode);
			if (count($gs) == 1) {
				$qr = $line->product_qrcode;
				$part1 = substr($qr, 0, 31);
				$part2 = substr($qr, 31, 6);
				$part3 = substr($qr, 37);
				$toSave = $part1 . $gsCode . $part2 . $gsCode . $part3;
				$line->product_qrcode = $toSave;
				$line->save(false);
				echo "toSave:".$toSave."<br />";
			}
		}
		return "ok";
	}
	
	public function actionDataMatrixOutboundForBota()
	{
		// http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/data-matrix-outbound-for-bota

		 // 73636	Intermode	20240809-103-140831
         // 73575	Intermode	20240807-103-120858
		 // 73598	Intermode	20240808-103-080814
		 // 73599	Intermode	20240808-103-090810
		 // 73600	Intermode	20240808-103-110820
		 
		// 74172	Intermode	RPT	00TK-001967_02_09_2024 — 1
		// 74173	Intermode	RPT	00TK-001967_02_09_2024 — 2
		// 74174	Intermode	RPT	00TK-001967_02_09_2024 — 3
		// 74175	Intermode	RPT	00TK-001967_02_09_2024 — 4
		// 74176	Intermode	RPT	00TK-001967_02_09_2024 — 5 (1)
		// 74196	Intermode	RPT	00TK-001967_02_09_2024 — 6
		// 74197	Intermode	RPT	00TK-001967_02_09_2024 — 7
		// 74246	Intermode	RPT	00TK-001967_02_09_2024 — 8 нак

		$lines = Stock::find()
					  ->select("product_qrcode")
					  ->andWhere([
						"outbound_order_id" => [
			75133,
			75109,
			75110,
			75111,
			75112,
			75113,
			75114,
			75115,
			75116,
			75117,
			75118,
			75232,

						],
					  ])->asArray()->all();
					  
		$orderName = "2024-11-22";
		$delimiter = " ";
		$content = "";
		foreach ($lines as $line) {
			$row = [
				$line["product_qrcode"],
			];
			$content .= implode($row,$delimiter)."\n";
		}

		$filename = "outbound-with-data-matrix-".$orderName.".csv";
		return  Yii::$app->response->sendContentAsFile( $content,$filename);
	}

	public function actionDataMatrixInboundForBota()
	{
		// http://wms.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/data-matrix-inbound-for-bota

		$lines = InboundDataMatrix::find()
					  ->select("data_matrix_code")
//					  ->andWhere([
//						  "outbound_order_id" => [
//							  72727, 72726, 72725, 72694, 72693, 72692, 72691, 72689, 72688, 72687
//						  ],
//					  ])
					  ->asArray()->all();
		$orderName = "2024-08-02";
		$delimiter = " ";
		$content = "";
		foreach ($lines as $line) {
			$row = [
				$line["data_matrix_code"],
			];
			$content .= implode($row,$delimiter)."\n";
		}

		$filename = "outbound-with-data-matrix-in-".$orderName.".csv";
		return  Yii::$app->response->sendContentAsFile( $content,$filename);
	}

	public function actionMakeInboundFile()
	{
		// http://wms.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/make-inbound-file
		//$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/CSV1.csv';
		//$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/CSV2.csv'; // 20240712-103-040734
		//$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/CSV3.csv'; // 20240712-103-090706
		// $pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/CSV4.csv'; // 20240712-103-110725
		// $pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/CSV5.csv'; // 20240712-103-140728 отправил по почте
		//$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/CSV6.csv'; // 20240713-103-090757
//		$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/00TK-000769_19_07_2024_1-1187.csv'; // 20240713-103-090757
//		$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/00TK-000809_25_07_2024_1-1455.csv'; // 20240808-103-080814
//		$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/00TK-000809_25_07_2024_2_1415.csv'; // 20240808-103-090810
		// $pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/00TK-000809_25_07_2024_3_1404.csv'; // 20240808-103-110820
		$pathToCSVFile = 'ReOutboundDataMatrix/make-inbound-file/done/CSV-389.csv'; // 20240808-103-110820
		$orderName = "20240812";
		$lines = [];
		$count = -1;
		if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 5000, ";")) !== FALSE) {
				$count++;
				if ($count == 0) {
					continue;
				}
				if (!isset($data['2'])) {
					continue;
				}
				//VarDumper::dump($data,10,true);
				$lines[$count]["sku"] = $data['0'];
				$lines[$count]["gtin"] = $data['1'];
				$lines[$count]["data_matrix"] = $data['2'];
			}
		}
		$delimiter = ";";
		$content = "Style;Color;Description;UPC;Size;QTY;DM;"."\n";
		foreach ($lines as $line) {
//				$dataMatrix = mb_convert_encoding($line["data_matrix"],"UTF-8");
			$dataMatrix = $line["data_matrix"];
			if ( base64_encode(base64_decode($dataMatrix, true)) === $dataMatrix){
				$dataMatrix = base64_decode($dataMatrix, true);
			}

			$row = [
				$line["sku"], // Style
				"-", // Color
				"-", // Description
				$line["gtin"], // UPC
				"-", // Size
				"1", // QTY
				$line["data_matrix"], // DM
				//$dataMatrix, // DM
			];
			$content .= implode($row,$delimiter).$delimiter."\n";
		}

		$filename = "inbound-with-data-matrix-".$orderName.".csv";
		return Yii::$app->response->sendContentAsFile( $content,$filename);
	}

	public function actionQrWithErrors()
	{
		// http://wms.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/qr-with-errors
		$pathToCSVFile = 'ReOutboundDataMatrix/qr-with-errors/6306_09_08.csv'; //
		$orderName = "6306_09_08-with-fix.csv";
		$badFileLines = $this->readOriginalFile($pathToCSVFile);

		$l1 = $this->readOriginalFile('ReOutboundDataMatrix/qr-with-errors/Lacoste-2113.csv');
		$l2 = $this->readOriginalFile('ReOutboundDataMatrix/qr-with-errors/Lacoste-1070.csv');
		$l3 = $this->readOriginalFile('ReOutboundDataMatrix/qr-with-errors/Lacoste-5721.csv');
		$good = array_merge($l1,$l2,$l3);
//		$t = "0105012123800318213QhMwLWcFpZuY91kzf0928fgwVSE1aFTpAlfQLtI2wI42Zmi=8fgwVSE1aFTpAlfQLtI2wI42Zmi=8fgwVSE1aFTpAlfQLtI2wI42Zmi=8fgw";

		$content ="";
		$delimiter = "	";
		$isOk = false;
		foreach ($badFileLines as $bQR) {
			foreach ($good as $gQR) {
				if (strtoupper($gQR) == strtoupper($bQR)) {
					$row = [
						$gQR
					];
					$content .= implode($row,$delimiter)."\n";
					$isOk = true;
				}
			}
			if(!$isOk) {
				file_put_contents("actionQrWithErrors.csv",$bQR."\n",FILE_APPEND);
			}
			$isOk = false;
		}

//		VarDumper::dump(array_search($t,$good),10,true);
//		VarDumper::dump($good,10,true);
//		die;

//		return Yii::$app->response->sendContentAsFile( $content,$orderName);
	}

	private function readOriginalFile($pathToCSVFile) {
		$lines = [];
		if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 5000, "	")) !== FALSE) {
				if (!isset($data['0'])) {
					continue;
				}
				$lines[]= $data['0'];
			}
		}
		return $lines;
	}

	public function actionDataMatrixOutboundForBotaWithReplace()
	{
		// http://wms.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/data-matrix-outbound-for-bota-with-replace

		$lines = Stock::find()
					  ->select("product_qrcode")
					  ->andWhere([
						  "outbound_order_id" => [
							  //72727, 72726, 72725, 72694, 72693, 72692, 72691, 72689, 72688, 72687
							  //73185,
							  //73180,
							  73635, // 20240809-103-140846
						  ],
					  ])->asArray()->all();
		$orderName = "20240809-103-140846";
		$delimiter = " ";
		$content = "";
		$goodQRList = $this->readOriginalFile('ReOutboundDataMatrix/qr/qr-intermode.csv');

		foreach ($lines as $line) {
			foreach ($goodQRList as $gQR) {
				if (strtoupper($gQR) == strtoupper($line["product_qrcode"])) {
					$row = [
						$gQR
					];
					$content .= implode($row, $delimiter) . "\n";
				}
//				$row = [
//					$line["product_qrcode"],
//				];
//				$content .= implode($row,$delimiter)."\n";
			}
		}

		$filename = "outbound-with-data-matrix-".$orderName.".csv";
		return  Yii::$app->response->sendContentAsFile( $content,$filename);
	}

public function actionReplaceInvalidQrCode()
	{

// http://wms.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/replace-invalid-qr-code
		$pathToCSVFile = 'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-1.csv';
		return "NO";
		$list = [
			'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-1.csv',
			'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-2.csv',
			'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-3.csv',
			'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-4.csv',
			'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-5.csv',
			'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-6.csv',
			'ReOutboundDataMatrix/2024-09-16/data-matrix-00TK-001967_02_09_2024-7.csv',
		];

		foreach ($list as  $pathToCSVFile) {
			$lines = [];
			$count = 0;
			if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 10000, "	")) !== FALSE) {
					$count++;

					$lines[$count]["order_number"] = $data['0'];
					$lines[$count]["box"] = $data['1'];
					$lines[$count]["product_barcode"] = $data['2'];
					$lines[$count]["data_matrix"] = $data['3'];
					$lines[$count]["stock_id"] = $data['4'];
					$lines[$count]["re_data_matrix"] = "";
					if(!empty($data['5'])) {
						$dataMatrix = $data['5'];
						if ( base64_encode(base64_decode($dataMatrix, true)) === $dataMatrix){
							$dataMatrix = base64_decode($dataMatrix, true);
						}
						$lines[$count]["re_data_matrix"] = $dataMatrix;
					}
				}
			}

//			VarDumper::dump($lines,10,true);
//			die;
//			$outboundId = 72687;
			foreach ($lines as $line) {
				if(empty($line["re_data_matrix"])) {
					continue;
				}
				$stock = Stock::find()->andWhere(["id"=>$line["stock_id"]])->one();
				if(empty($stock)) {
					VarDumper::dump($line,10,true);
				} else {
					if (!empty($line["re_data_matrix"])) {
						$stock->system_status = 2;
						$stock->product_qrcode = $line["re_data_matrix"];
						$stock->system_status_description = $line["data_matrix"];
						//$stock->save(false);
					}
					$gsCode = "";
					$gs = explode($gsCode,$stock->product_qrcode);
					if(count($gs) == 1) {
						$qr = $stock->product_qrcode;
						$part1 = substr($qr,0,31);
						$part2 = substr($qr,31,6);
						$part3 = substr($qr,37);
						$toSave = $part1.$gsCode.$part2.$gsCode.$part3;
						$stock->product_qrcode = $toSave;
						//$stock->save(false);
						
						echo "toSave:".$toSave."<br />";
					}
				}
			}
		}

		return "ok";
	}
	
	
		public function actionMergeInboundToOutbound()
	{
		// http://intermode-kz.nmdx.kz/wms/erenRetail/re-outbound-data-matrix/merge-inbound-to-outbound
		  die("-DIE- new");
		  $map = [
		/*  "115109"=>"75556",
		"115110"=>"75557",
		"115111"=>"75559",
		"115112"=>"75607",
		"115113"=>"75608",
		"115114"=>"75612",
		"115115"=>"75613",
		"115116"=>"75615",
		"115117"=>"75635",
		*/
		115104=> "75611", // 00674
115103=> "75614", // 00678
115102=> "75610", // 00673
115101=> "75609", // 00671
115100=> "75561", // 00668
115099=> "75558", // 00665

		  ];
		  
		  	foreach ($map as $inboundID=>$outbounID) {
				
						$lines = InboundDataMatrix2::find()
								  ->andWhere([
									  "inbound_id" =>  $inboundID,
								  ])
								  ->asArray()->all();
								  
					foreach ($lines as $line) {

						$stock = Stock::find()->andWhere([
							"outbound_order_id" => $outbounID, 
							"product_barcode" => $line["product_barcode"],
						])->andWhere("system_status != 2")->one();
						if (empty($stock)) {
							VarDumper::dump($line, 10, true);
						} else {
							$stock->system_status = 2;
							$stock->product_qrcode = $line["data_matrix_code"];
							//$stock->system_status_description = $line["data_matrix"];
							//$stock->save(false);
						}
					}
			}
		return "OK 4";
	}
}

