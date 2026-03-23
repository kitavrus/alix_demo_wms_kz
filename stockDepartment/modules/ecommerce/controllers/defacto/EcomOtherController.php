<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\components\BarcodeManager;
use common\ecommerce\constants\OutboundCancelStatus;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\constants\ReturnOutbound;
use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\constants\ReturnReason;
use common\ecommerce\constants\StockAPIStatus;
use common\ecommerce\constants\StockAvailability;
use common\ecommerce\constants\StockConditionType;
use common\ecommerce\constants\StockOutboundStatus;
use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\constants\TransferStatus;
use common\ecommerce\defacto\api\ECommerceAPI2;
use common\ecommerce\defacto\api\ECommerceAPINew;
use common\ecommerce\defacto\barcodeManager\service\BarcodeService;
use common\ecommerce\defacto\barcodeManager\service\MasterDataAPIService;
use common\ecommerce\defacto\inbound\repository\InboundRepository;
use common\ecommerce\defacto\inbound\service\InboundAPIService;
use common\ecommerce\defacto\inbound\service\InboundAPIService2;
use common\ecommerce\defacto\inbound\service\InboundOrderService;
use common\ecommerce\defacto\other\ParseTransferFile;
use common\ecommerce\defacto\outbound\repository\OutboundRepository;
use common\ecommerce\defacto\outbound\service\OutboundListService;
use common\ecommerce\defacto\outbound\service\OutboundService;
use common\ecommerce\defacto\outbound\service\ReservationPlaceAddressSortingService;
use common\ecommerce\defacto\returnOutbound\repository\ReturnRepository;
use common\ecommerce\defacto\returnOutbound\service\ReturnAPIService;
use common\ecommerce\defacto\returnOutbound\service\ReturnService;
use common\ecommerce\defacto\stock\forms\StockAdjustmentForm;
use common\ecommerce\defacto\stock\repository\Repository;
use common\ecommerce\defacto\stock\service\Service;
use common\ecommerce\defacto\transfer\repository\TransferRepository;
use common\ecommerce\defacto\transfer\service\TransferService;
use common\ecommerce\defacto\transfer\service\TransferServiceV2;
use common\ecommerce\entities\EcommerceApiInboundLog;
use common\ecommerce\entities\EcommerceApiOutboundLog;
use common\ecommerce\entities\EcommerceChangeAddressPlace;
use common\ecommerce\entities\EcommerceInboundItem;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceOutboundList;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceTransfer;
use common\ecommerce\entities\EcommerceTransferItems;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\outbound\models\OutboundBox;
use common\modules\outbound\service\OutboundBoxService;
use common\modules\returnOrder\models\ReturnOrder;
use common\modules\stock\models\Stock;
use DateTime;
use DateTimeZone;
use PHPExcel_IOFactory;
use stdClass;
use stockDepartment\components\Controller;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

// ecommerce/defacto/ecom-other/index
class EcomOtherController extends Controller
{
	public function actionTransferV2() {
		$service = new  TransferServiceV2();
		$result = $service->GetBatches();
		VarDumper::dump($result,10,true);
		die;
	}

	public function actionMoveStockRequest()
	{
		//  ecommerce/defacto/ecom-other/move-stock-request
		die("-actionMoveStockRequest-");
		$apiManager = new ECommerceAPINew();
		$fileName = "actionMoveStockRequest-2024-11-27";
		$list = [
			[ // block-2024-11-27
				"barcode"=>8683524412867,
				"qty"=>1,
				"from"=> "Stock",
				"to"=>"Damage",
			],
//			[ // un-block-20240923
//				"barcode"=>8682865015577,
//				"qty"=>4,
//				"from"=> "Damage",
//				"to"=>"Stock",
//			]	,
		];

		//die("dddd");
		foreach ($list as $item) {
			$items = $apiManager->makeMoveStockRequest($item["barcode"],$item["qty"],$item["from"],$item["to"]);
			$result = $apiManager->MoveStock($items);
			$result = "";
			VarDumper::dump($result,10,true);
			usleep(10000);
			file_put_contents($fileName."-log.csv",print_r($result,true).";"."\n",FILE_APPEND);
			file_put_contents($fileName.".csv",$item.";"."\n",FILE_APPEND);
			//VarDumper::dump($result,10,true);
		}


//		$items = $apiManager->makeMoveStockRequest("8683524558954",1);
		//$items = $apiManager->makeMoveStockRequest("8682864238526",1);
		//$result = $apiManager->MoveStock($items);
		//VarDumper::dump($result,10,true);
		die;
	}

	public function actionStockAdjustment()
	{
		//  ecommerce/defacto/ecom-other/stock-adjustment
		die("-actionStockAdjustment-");
		$apiManager = new \common\ecommerce\defacto\stock\service\StockAdjustmentService();
		$fileName = "actionStockAdjustment-2024-11-29";
		$list = [
			[
				"barcode"=>8683524091925,
				"qty"=>1,
				"operator"=> "+",
			],
			[
				"barcode"=>8683524328656,
				"qty"=>1,
				"operator"=> "+",
			],
			[
				"barcode"=>8683523315350,
				"qty"=>1,
				"operator"=> "+",
			],
			[
				"barcode"=>8683523150807,
				"qty"=>1,
				"operator"=> "+",
			],
			[
				"barcode"=>8683522660604,
				"qty"=>1,
				"operator"=> "+",
			],
			[
				"barcode"=>8683524127402,
				"qty"=>1,
				"operator"=> "+",
			],
			[
				"barcode"=>8683524403032,
				"qty"=>1,
				"operator"=> "+",
			],
			[
				"barcode"=>8683523825927,
				"qty"=>1,
				"operator"=> "+",
			],
		];

		//die("dddd");
		foreach ($list as $item) {
			// $result = $apiManager->StockAdjustment($item["barcode"],$item["qty"],$item["operator"]);
			// VarDumper::dump($result,10,true);
			usleep(10000);
			file_put_contents($fileName."-log.csv",print_r($result,true).";"."\n",FILE_APPEND);
			file_put_contents($fileName.".csv",implode(";",$item).";"."\n",FILE_APPEND);
			//VarDumper::dump($result,10,true);
		}
		die;
	}

	public function actionUnBlockFromFileDefacto()
	{
		die('-ecom-other/un-block-from-file-defacto-');
		// ecommerce/defacto/ecom-other/un-block-from-file-defacto
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "  ecommerce/defacto/ecom-other/un-block-from-file-defacto";

		$excel = PHPExcel_IOFactory::load('defacto/unblocked/b2c/2024-09-25/block-unblock.xlsx');
		$excel = PHPExcel_IOFactory::load('defacto/unblocked/b2c/2024-09-27/block-unblock.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$start = 1;

		$fileName = "un-block-from-file-defacto-2024-09-27";
		$noteMessage1 = 'un-block-20240927';
		$apiManager = new ECommerceAPINew();
		for ($i = $start; $i <= 100000; $i++) {
			$productBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
			$boxBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
			$qty = (integer)$excelActive->getCell('C' . $i)->getValue();
			$action = (string)$excelActive->getCell('D' . $i)->getValue();

			$productBarcode = trim($productBarcode);
			$boxBarcode = trim($boxBarcode);

			if ($productBarcode == null || $qty < 1  ||  $action != "активировать")  {
				continue;
			}

			$products = EcommerceStock::find()
									  ->andWhere([
										  'product_barcode'=>$productBarcode,
										  'box_address_barcode'=>$boxBarcode,
									  ])
									  ->andWhere('(status_availability = 2 and  condition_type = 3) OR ( status_availability = 4 and condition_type = 3)')
									  ->limit($qty)
									  ->all();

			if(empty($products)) {

				$products = EcommerceStock::find()
										  ->andWhere([
											  'product_barcode'=>$productBarcode,
											  'box_address_barcode'=>$boxBarcode,
											  'condition_type'=>StockConditionType::UNDAMAGED,
											  'status_availability'=>[
												  StockAvailability::YES,
											  ]
										  ])
										  ->limit($qty)
										  ->all();



				file_put_contents($fileName."-not-find.csv",$productBarcode.";".$boxBarcode.";".$qty.";".count($products).";"."\n",FILE_APPEND);
			} else {
				foreach ($products as $product) {
					file_put_contents($fileName.".csv",$productBarcode.";".$boxBarcode.";".$qty.";".$product->id.";"."\n",FILE_APPEND);
					$product->status_availability = StockAvailability::YES;
					$product->condition_type = StockConditionType::UNDAMAGED;
					$product->note_message2 = $noteMessage1;
					//$product->save(false);
					$result = "";
					//$items = $apiManager->makeMoveStockRequest($productBarcode,1,"Damage","Stock");
					//$result = $apiManager->MoveStock($items);
//				VarDumper::dump($result,10,true);
					usleep(9000);
					//file_put_contents($fileName."-log.csv",print_r($result,true).";"."\n",FILE_APPEND);
				}
			}
		}
		return $this->render('test');// '<br />-END-TEST<br />';
	}

	public function actionBlockFromFileDefacto()
	{
		die('-block-from-file-defacto-');
		// ecommerce/defacto/ecom-other/block-from-file-defacto
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo " ecommerce/defacto/ecom-other/block-from-file-defacto";

		$excel = PHPExcel_IOFactory::load('defacto/unblocked/b2c/2024-09-25/block-unblock.xlsx');
		$excel = PHPExcel_IOFactory::load('defacto/unblocked/b2c/2024-09-27/block-unblock.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();
		$start = 1;
		$fileName = "block-from-file-defacto-2024-09-27";
		$noteMessage1 = 'block-20240927';
		$apiManager = new ECommerceAPINew();
		for ($i = $start; $i <= 100000; $i++) {
			$productBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
			$boxBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
			$qty = (integer)$excelActive->getCell('C' . $i)->getValue();
			$action = (string)$excelActive->getCell('D' . $i)->getValue();

			$productBarcode = trim($productBarcode);
			$boxBarcode = trim($boxBarcode);

			if ($productBarcode == null || $qty < 1  ||  $action == "активировать")  {
				continue;
			}

			$products = EcommerceStock::find()
									  ->andWhere([
										  'product_barcode'=>$productBarcode,
										  'box_address_barcode'=>$boxBarcode,
										  'condition_type'=>StockConditionType::UNDAMAGED,
										  'status_availability'=>[
											  StockAvailability::YES,
										  ]])
									  ->limit($qty)
									  ->all();
			if(empty($products)) {

				$products = EcommerceStock::find()
										  ->andWhere([
											  'product_barcode'=>$productBarcode,
											  'box_address_barcode'=>$boxBarcode,
											  'condition_type'=>StockConditionType::UNDAMAGED,
											  'status_availability'=>[
												  StockAvailability::YES,
											  ]
										  ])
										  ->limit($qty)
										  ->all();



				file_put_contents($fileName."-not-find.csv",$productBarcode.";".$boxBarcode.";".$qty.";".count($products).";"."\n",FILE_APPEND);
			} else {
				foreach ($products as $product) {
					file_put_contents($fileName.".csv",$productBarcode.";".$boxBarcode.";".$qty.";"."\n",FILE_APPEND);
					$product->status_availability = StockAvailability::BLOCKED;
					$product->condition_type = StockConditionType::FULL_DAMAGED;
					$product->note_message2 = $noteMessage1;
					//$product->save(false);
					$result = "";
					// $fromLocation = ", $toLocation =
					//$items = $apiManager->makeMoveStockRequest($productBarcode,1,"Stock","Damage");
					//$result = $apiManager->MoveStock($items);
					usleep(9000);

					//file_put_contents($fileName."-log.csv",print_r($result,true).";"."\n",FILE_APPEND);
				}
			}
		}
		return $this->render('test');// '<br />-END-TEST<br />';
	}

	public function actionBlock()
	{
		die('-actionBlock-');
		// ecommerce/defacto/ecom-other/block
		echo "<br />";
		echo "<br />";
		echo "<br />";
		echo "<br />";

//		$excel = PHPExcel_IOFactory::load('defacto/block/b2c/29-08-2024/DAMAGE-B2C.xlsx');
		$excel = PHPExcel_IOFactory::load('defacto/block/b2c/29-08-2024/damage-qr-code.xlsx');
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 1;
		$fileName = "damage-b2c-qr-code";
		//$noteMessage1 = 'blocked-damage-20240829';
		$noteMessage1 = 'blocked-damage-qr-20240829';
		for ($i = $start; $i <= 100000; $i++) {
			$productBarcode = $excelActive->getCell('A' . $i)->getValue();
			if ($productBarcode == null) continue;

			$product = EcommerceStock::find()
									 ->andWhere([
										 'product_barcode'=>$productBarcode,
										 'status_availability'=>StockAvailability::YES,
									 ])
									 ->andWhere('note_message1 is NULL')
									->limit()
									 ->all();

			if(empty($product)) {
				VarDumper::dump($productBarcode,10,true);
				 file_put_contents($fileName."-not-find.csv",$productBarcode.";"."\n",FILE_APPEND);
			} else {
				VarDumper::dump($productBarcode,10,true);
				file_put_contents($fileName."-blocked.csv",$productBarcode.";"."\n",FILE_APPEND);
				$product->status_availability = StockAvailability::BLOCKED;
				$product->condition_type = StockConditionType::FULL_DAMAGED;
				$product->note_message1 = $noteMessage1;
				//$product->save(false);
			}
		}


		return $this->render('test');// '<br />-END-TEST<br />';
	}



//    public function actionReturnReport()
//    {
//        //die('ecommerce/defacto/ecom-other/return-report DIE');
//
//        $allReport = EcommerceReturn::find()->asArray()->all();
//        $file = 'return-full-report-'.date('Y-m-d').'-result.xlsx';
//        foreach ($allReport as $return) {
//            $allProducts = EcommerceReturnItem::find()->andWhere(['return_id'=>$return['id']])->asArray()->all();
//            foreach ($allProducts as $product) {
//                $row = $return['order_number'].';'.$product['product_barcode'].';'.$product['accepted_qty'].';'."\n";
//                file_put_contents($file,$row,FILE_APPEND);
//            }
//        }
//
//        return \Yii::$app->response->sendFile($file);
//
//        die('ok - ReturnReport - END');
//    }

    public function actionIndex()
    {

        echo (new DateTime('now', new DateTimeZone('Asia/Almaty')))->format('Y-m-d H:i:s P');
        // ecommerce/defacto/ecom-other/index
        die("ecommerce/defacto/ecom-other/index");
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $response = [];
        foreach ([1,2,3,4,5,6] as $orderNumberId) {
            $dto = new stdClass();
            $dto->orderNumberId = $orderNumberId;
            $dto->ourBoxBarcode = '';
            $dto->clientBoxBarcode = '';
            $dto->conditionType = '';
            $dto->productBarcode = '';
            $dto->productQty = 0;


            $inboundOrderService = new InboundOrderService($dto);
            //$inboundOrderService->closeOrder();
//            echo '-Order ID : '.$dto->orderNumberId."\n";
        }


//        VarDumper::dump(count($reportFrom),10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        VarDumper::dump($reportFrom,10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump($response,10,true);
//        VarDumper::dump($result,10,true);
        echo "<br />";

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionIndex10()
    {
//        die('actionIndex');
        // ecommerce/defacto/ecom-other/index10

//        $api = new \common\ecommerce\defacto\api\ECommerceAPI();
//        $ShortCode = ['J9495AZAA'];// 'J9495AZAA';
//        $LotOrSingleSkuIds = '';//['224051206'];//['224051206'];
//        $LotOrSingleBarcodes = ''; // ['2300005992309'];
        //$api->GetSkuInfo($ShortCode,$LotOrSingleSkuIds,$LotOrSingleBarcodes);
//        $MasterDataAPIService = new \common\ecommerce\defacto\barcodeManager\service\MasterDataAPIService();
//        $result = $MasterDataAPIService->GetMasterData($ShortCode,$LotOrSingleSkuIds,$LotOrSingleBarcodes);


        //$api->GetMasterData($ShortCode,$LotOrSingleSkuIds,$LotOrSingleBarcodes);

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $response = [];
//        $stockService = new \common\ecommerce\defacto\stock\service\Service();
//        $response = $stockService->SendInventorySnapshot();


//        VarDumper::dump(count($reportFrom),10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        VarDumper::dump($reportFrom,10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump($response,10,true);
//        VarDumper::dump($result,10,true);
        echo "<br />";

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionIndex9()
    {
        //die('actionIndex');
        // ecommerce/defacto/ecom-other/index9

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $orderList = [
            'OMC-32633966',
            'OMC-32633965',
        ];

        $result = [];
        foreach($orderList as $outboundOrderNumber) {
            $outbound = EcommerceOutbound::find()->andWhere(['order_number'=>$outboundOrderNumber])->one();
            $logList = EcommerceApiOutboundLog::find()->andWhere(['our_outbound_id'=>$outbound->id])->orderBy(['id'=>SORT_ASC])->all();

            foreach($logList as $log) {
                $strToFile  = $log->method_name." : ".$outboundOrderNumber."\n"."\n";
                $strToFile .= print_r(unserialize($log->request_data),true)."\n"."\n";
                $strToFile .= print_r(unserialize($log->response_data),true)."\n"."\n";
                file_put_contents('API-ERROR-LOG.txt',$strToFile,FILE_APPEND);
            }
        }


//        VarDumper::dump(count($reportFrom),10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        VarDumper::dump($reportFrom,10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump(count($result),10,true);
//        VarDumper::dump($result,10,true);
        echo "<br />";

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionIndex8()
    {
        die('actionIndex');
        // ecommerce/defacto/ecom-other/index
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $excel = PHPExcel_IOFactory::load('tmp-file/e-commerce/Orders.xlsx');
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 1000; $i++) {
            $id = $excelActive->getCell('B' . $i)->getValue();
            if ($id == null) continue;

            $outbound = EcommerceOutbound::find()->andWhere(['order_number'=>$id])->asArray()->one();
            echo $id."<br />";
            $reportFrom[] = $outbound;
        }

        $result = [];
        foreach($reportFrom as $outbound) {
//            echo $outbound."<br />";
            $service = new OutboundService();

            if($outbound['expected_qty'] !=  $outbound['accepted_qty']) {
                $result[] = $outbound;
            }
            echo $outbound['id']."<br />";
            // $result[] = $service->SendShipmentFeedback($outbound['id']);
        }


        VarDumper::dump(count($reportFrom),10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        VarDumper::dump($reportFrom,10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump(count($result),10,true);
//        VarDumper::dump($result,10,true);
        echo "<br />";

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionIndex7()
    {
//        die('actionIndex');
        // ecommerce/defacto/ecom-other/index
        $path = '';
//        $service = new OutboundService();
//        $cargoLabel = $service->GetCargoLabel(734);
//        $path =  $service->saveCargoLabel($cargoLabel);

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo $path;


        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionIndex6()
    {
        die('actionIndex');
        // ecommerce/defacto/ecom-other/index
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $excel = PHPExcel_IOFactory::load('tmp-file/e-commerce/Orders.xlsx');
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 1000; $i++) {
            $id = $excelActive->getCell('B' . $i)->getValue();
            if ($id == null) continue;

            $outbound = EcommerceOutbound::find()->andWhere(['order_number'=>$id])->asArray()->one();
                        echo $id."<br />";
            $reportFrom[] = $outbound;
        }

        $result = [];
        foreach($reportFrom as $outbound) {
//            echo $outbound."<br />";
            $service = new OutboundService();

            if($outbound['expected_qty'] !=  $outbound['accepted_qty']) {
                $result[] = $outbound;
            }
            echo $outbound['id']."<br />";
           // $result[] = $service->SendShipmentFeedback($outbound['id']);
        }


        VarDumper::dump(count($reportFrom),10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        VarDumper::dump($reportFrom,10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump(count($result),10,true);
//        VarDumper::dump($result,10,true);
        echo "<br />";

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionIndex5()
    {
        die('actionIndex5');
        // ecommerce/defacto/ecom-other/index
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $excel = PHPExcel_IOFactory::load('tmp-file/e-commerce/Orders.xlsx');
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 1000; $i++) {
            $id = $excelActive->getCell('B' . $i)->getValue();
            if ($id == null) continue;

            $outbound = EcommerceOutbound::find()->andWhere(['order_number'=>$id])->asArray()->one();
            $reportFrom[] = $outbound;
        }

        $result = [];
        foreach($reportFrom as $outbound) {
//            echo $outbound."<br />";
            $service = new OutboundService();

            if($outbound['expected_qty'] !=  $outbound['accepted_qty']) {
                $result[] = $outbound;
            }
            echo $outbound['id']."<br />";
           // $result[] = $service->SendShipmentFeedback($outbound['id']);
        }


        VarDumper::dump(count($reportFrom),10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        VarDumper::dump($reportFrom,10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump(count($result),10,true);
//        VarDumper::dump($result,10,true);
        echo "<br />";

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionResetByOutboundOrderId()
    {
        die(' ecommerce/defacto/ecom-other/reset-by-outbound-order-id ');
        // ecommerce/defacto/ecom-other/reset-by-outbound-order-id
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";

        $service = new OutboundService();
        $outboundID = '64528'; // OMC-8531765
      //  $service->resetByOutboundOrderId($outboundID);
		echo "<br />";
       //$resp =  $service->SendAcceptedShipments($outboundID);
//       $resp =  $service->SendShipmentFeedback($outboundID);
//		       VarDumper::dump($resp,10,true);
        echo "<br />";
        die();
       // $service->saveWaybillDocument($outboundID);

//        $orderList = [
//          '7'=>'OMC-8196867',
//          '3'=>'OMC-8176705',
//          '4'=>'OMC-8186455',
//          '5'=>'OMC-8186673',
//        ];
//
//         $orderLog = EcommerceApiOutboundLog::find()->andWhere(['id'=>59])->one();
//
//        VarDumper::dump($orderLog,10,true);
//        echo "<br />";

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function actionIndex3()
    {
        die('actionIndex3');
        // ecommerce/defacto/ecom-other/index
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

//        $orderList = [
//          '7'=>'OMC-8196867',
//          '3'=>'OMC-8176705',
//          '4'=>'OMC-8186455',
//          '5'=>'OMC-8186673',
//        ];
//
//        $placeAddressSorting = new ReservationPlaceAddressSortingService();
//        $beforeReservationSorting = $placeAddressSorting->beforeReservationSorting(array_keys($orderList));
//        VarDumper::dump($orderList,10,true);
//        echo "<br />:::";
//        VarDumper::dump($beforeReservationSorting,10,true);
//
//        $beforeReservationSorting = $placeAddressSorting->beforePrintPickingList(array_keys($orderList));
//        echo "<br />";
//        VarDumper::dump($beforeReservationSorting,10,true);

        $orderOnStock = EcommerceStock::find();
        foreach($orderOnStock->each() as $productOnStock) {
            $productOnStock->place_address_sort1 = ReservationPlaceAddressSortingService::makePlaceAddressSort1($productOnStock->place_address_barcode);
            $productOnStock->save(false);
        }

        return $this->render('test');// '<br />-END-TEST<br />';
    }
  //
    public function actionIndex2()
    {
        die('actionIndex2');
        // ecommerce/defacto/ecom-other/index
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $orderList = [
          '3'=>'OMC-8176705',
          '4'=>'OMC-8186455',
          '5'=>'OMC-8186673',
          '7'=>'OMC-8196867',
        ];

//        EcommerceOutboundItem::find()->andWhere(['outbound_id'=>])->all();
        $orderListForSort = [];
        $orderOnStock = EcommerceStock::find()->andWhere(['outbound_id'=>array_keys($orderList)])->all();
        foreach ($orderOnStock as $key=>$productOnStock) {
            echo $productOnStock->place_address_barcode.' =  '.$this->preparePlaceAddressBarcode($productOnStock->place_address_barcode)."<br />";
//            $orderListForSort [$this->preparePlaceAddressBarcode($productOnStock->place_address_barcode)] =  $productOnStock->outbound_id;
            $orderListForSort [] = [
                'outboundId'=>$productOnStock->outbound_id,
                'placeAddress'=>$productOnStock->place_address_sort1,
                'placeAddressBarcode'=>$productOnStock->place_address_barcode
            ] ;
        }

        VarDumper::dump($orderListForSort,10,true);
        ArrayHelper::multisort($orderListForSort,['outboundId','placeAddress']);
        $orderListForSort = ArrayHelper::index($orderListForSort,null,'outboundId');
        uasort($orderListForSort,function($a,$b) {
             $aCount = count($a)-1;
             $bCount = count($b)-1;
            return  $a[$aCount]['placeAddress'] < $b[$bCount]['placeAddress'] ? -1 : 1;
        });
//      ArrayHelper::multisort($orderListForSort,['placeAddress']);
        echo "<br />";
        echo "<br />";
        VarDumper::dump($orderListForSort,10,true);
//        VarDumper::dump(,10,true);

        $orderOnStock = EcommerceStock::find();//->all();
        foreach($orderOnStock->each() as $productOnStock) {
            $productOnStock->place_address_sort1 = $this->makePlaceAddressSort1_old($productOnStock->place_address_barcode);
            $productOnStock->save(false);
        }

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    public function makePlaceAddressSort1_old($addressBarcode) {
        $addressBarcodeList = explode('-',$addressBarcode);
        if(empty($addressBarcodeList) || !is_array($addressBarcodeList) || !isset($addressBarcodeList[1])) {
            return 999999;
        }
        $addressBarcodeResult = $addressBarcodeList[1].$addressBarcodeList[2];
        return $addressBarcodeResult;
    }

//    public function preparePlaceAddressBarcode($addressBarcode) {
//        $barcodeService = new BarcodeService();
//        $addressBarcode  = $barcodeService->subStr($addressBarcode,1,strlen($addressBarcode));
//        $addressBarcode = ltrim($addressBarcode,'4-');
//        $addressBarcodeList = explode('-',$addressBarcode);
//        $addressBarcodeResult = intval($addressBarcodeList[1]).$addressBarcodeList[2];
//        foreach($addressBarcodeList as $key=>$value) {
//            $addressBarcodeResult .= ''
//        }

//        return $addressBarcodeResult;
//        return str_replace('-','',$addressBarcode);
//    }

    public function actionIndex1()
    {
        die('actionIndex1');
        // ecommerce/defacto/ecom-other/index
//        $titleList = [
//            '11-11-2019',
//            '08-11-2019-1',
//            '08-11-2019',
//            '07-11-2019',
//            '06-11-2019'
//        ];
//
//        foreach($titleList as $title)
//        {
//            $outboundList = EcommerceOutboundList::find()->andWhere(['list_title' => $title])->all();
//            foreach ($outboundList as $listRow)
//            {
//                echo $listRow->client_order_number."<br />";
//                $order = EcommerceOutbound::find()->andWhere(['order_number' => $listRow->client_order_number])->one();
//                $order->status = OutboundStatus::DONE;
//                $order->date_left_warehouse = $listRow->updated_at;
//                //$order->save(false);
//            }
//        }

//        die;
//        $dto =  new \stdClass();
//        $dto->title = '11-11-2019';
//        $dto->title = '08-11-2019-1';
//        $dto->title = '08-11-2019';
//        $dto->title = '07-11-2019';
//        $dto->title = '06-11-2019';
//        $service = new OutboundListService();
//        $orderForPrintList = $service->printList($dto);
//
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($orderForPrintList,10,true);
//        die("DIE");

        return $this->render('test');// '<br />-END-TEST<br />';
    }

    private function parsTtnFiles($path) {

        $excel = PHPExcel_IOFactory::load($path);
//        $excel = \PHPExcel_IOFactory::load($rootPath . '01052019/31-05-2019.xlsx');
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 1;
        for ($i = $start; $i <= 1000; $i++) {
            $box = (string)$excelActive->getCell('A' . $i)->getValue();
            $qty = (string)$excelActive->getCell('B' . $i)->getValue();
            if ($box == null) continue;
            $reportFrom[] = [
                'box'=>$box,
                'qty'=>$qty,
            ];
        }

        return $reportFrom;
    }

    public function actionChangeStockStatusByOutboundOrder() {
        die('ecommerce/defacto/ecom-other/change-stock-status-by-outbound-order DIE');

        $stockList = EcommerceStock::find()
            ->andWhere('outbound_id > 0')
            ->all();

        foreach($stockList as $stock) {
            if(empty($stock->outbound_box)) {
                $stock->status_outbound = StockOutboundStatus::PRINTED_PICKING_LIST;
                $stock->save(false);
            }
        }

        return $this->render('test');
    }

    public function actionTransfer()
    {
        //die('ecommerce/defacto/ecom-other/transfer DIE');
        // ecommerce/defacto/ecom-other/transfer
        $rootPath = 'tmp-file/defacto/b2c-b2b/';
        $boxs = $this->parsTtnFiles($rootPath . 'From E-commerce Warehouse to DC Warehouse.xlsx');
        $i = 0 ;
        foreach ($boxs as $boxRow) {
            $box = $boxRow['box'];
            $qty = $boxRow['qty'];

//            $products = EcommerceStock::find()->andWhere(['box_address_barcode'=>$box,'status_availability'=>StockAvailability::YES])->all();
            $products = EcommerceStock::find()->andWhere(['box_address_barcode'=>$box,'note_message1'=>'transfer-19022020-b2c-to-b2b'])->all();

            foreach ($products as $product) {
                //$product->note_message1 = 'transfer-19022020-b2c-to-b2b';
                //$product->status_availability = StockAvailability::NO;
                //$product->save(false);
//                $row = $i++.';'.$box.';'.$qty.';'.$product->product_barcode.';'.$product->box_address_barcode.';'.$product->place_address_barcode.';'."\n"; //
                $row = $product->place_address_barcode.';'.$product->box_address_barcode.';'.$product->product_barcode.';'."\n";
                file_put_contents('From-E-commerce-Warehouse-to-DC-Warehouse-result-2.csv',$row,FILE_APPEND);
                echo $row."<br />";
            }
            $row = ''."\n";
            file_put_contents('From-E-commerce-Warehouse-to-DC-Warehouse-result-2.csv',$row,FILE_APPEND);
        }
        VarDumper::dump($boxs,10,true);
        die;
    }

    public function actionFindSaleProductByFile()
    {
        //die('ecommerce/defacto/ecom-other/find-sale-product-by-file DIE');
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        $resultFileName = 'nomadex-stock-result-10-03-2020.xlsx';
        //$resultFileName = 'yeni-sku-result-10-03-2020.xlsx';
        $resultFileName = 'SKU-result-16-03-2020.xlsx';
        $rowToFile = "Row number".';'. "product SKU".';'. "product Barcode".';'. "Product sold Qty".';'."\n";
//        $rowToFile = "Row Labels".';'. "Count of client_product_sku".';'. "Product sold Qty".';'. "Product SKU".';'."\n";
        file_put_contents($resultFileName,$rowToFile);

//        $rootPath = 'tmp-file/defacto/b2c-b2b/10-03-2020/nomadex-stock.xlsx';
        $rootPath = 'tmp-file/defacto/b2c-b2b/16-03-2020/sku.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 1;
        for ($i = $start; $i <= 11595; $i++) {
            $productSku = (string)$excelActive->getCell('A' . $i)->getValue();
//            $numberRow = (string)$excelActive->getCell('B' . $i)->getValue();
            if ($productSku == null) continue;
            $reportFrom[] = [
                'numberRow'=>$i,
//                'numberRow'=>$numberRow,
                'productSku'=>$productSku,
//                'productBarcode'=>$productSku,
            ];
        }

        foreach($reportFrom as $item) {

          $result = EcommerceOutboundItem::find()
                ->select('product_sku, product_name, product_model, product_barcode, id, sum(accepted_qty) as sumAcceptedQty')
//                ->andWhere(['product_barcode'=>$item['productBarcode']])
                ->andWhere(['product_sku'=>$item['productSku']])
                ->groupBy('product_barcode')
                ->asArray()
                ->one();

//            $rowToFile = $item['productBarcode'].';'. $item['numberRow'].';'. (int)$result['sumAcceptedQty'].';'. (int)$result['product_sku'].';'."\n";
            $rowToFile = $item['numberRow'].';'. $item['productSku'].';'. $result['product_barcode'].';'. (int)$result['sumAcceptedQty'].';'."\n";
            file_put_contents($resultFileName,$rowToFile,FILE_APPEND);
        }


        return $this->render('test');
    }

    public function actionPrepareTransfer()
    {
//        die('ecommerce/defacto/ecom-other/prepare-transfer DIE');
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

//        $rootPath = 'tmp-file/defacto/b2c-b2b/v2/Product-b2c-to-b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/12-03-2020/1303/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/17-03-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/19-03-2020/b2b.xlsx';
        //$rootPath = 'tmp-file/defacto/b2c-b2b/20-03-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/27-03-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/31-03-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/01-04-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/02-04-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/03-04-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/07-04-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/14-04-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/15-04-2020/b2b.xlsx';
//        $rootPath = 'tmp-file/defacto/b2c-b2b/16-04-2020/b2b.xlsx';
        $rootPath = 'tmp-file/defacto/b2c-b2b/05-05-2020/b2b.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 51465; $i++) {
            $placeAddress =             (string)$excelActive->getCell('A' . $i)->getValue();
            $ourBoxBarcode =            (string)$excelActive->getCell('B' . $i)->getValue();
            $productBarcodeExpected =   (string)$excelActive->getCell('C' . $i)->getValue();
            $sku =                      (string)$excelActive->getCell('D' . $i)->getValue();
            $season =                   (string)$excelActive->getCell('E' . $i)->getValue();
            $rowNumber =                (string)$excelActive->getCell('F' . $i)->getValue();
            $lcBarcode =                (string)$excelActive->getCell('G' . $i)->getValue();
            $productBarcodeScanned =    (string)$excelActive->getCell('H' . $i)->getValue();
            $reportFrom[] = [
                'placeAddress'=>trim($placeAddress),
                'ourBoxBarcode'=>trim($ourBoxBarcode),
                'productBarcodeExpected'=>trim($productBarcodeExpected),
                'lcBarcode'=>trim($lcBarcode),
                'productBarcodeScanned'=>trim($productBarcodeScanned),
                'sku'=>trim($sku),
                'season'=>trim($season),
                'rowNumber'=>trim($rowNumber),
            ];
        }

        $firstStep = [];
        $productBarcodeExpectedListInfo = [];
        foreach($reportFrom as $item) {
            $firstStep[$item['ourBoxBarcode']][] = $item;
        }

        $secondStep = [];
        foreach($firstStep as $ourBoxBarcode=>$items) {

            $productBarcodeExpectedList = [];
            $productBarcodeScannedList = [];
            $placeAddress = '';
            $ourBoxBarcode = '';
            $lcBarcode = '';

            foreach ($items as $keyItem=>$item) {
                if ($keyItem == 0) {
                    $placeAddress =  $item['placeAddress'];
                    $ourBoxBarcode = $item['ourBoxBarcode'];
                    $lcBarcode = $item['lcBarcode'];
                }

                $productBarcodeExpectedList [] = $item['productBarcodeExpected'];
                $productBarcodeScannedList [] = $item['productBarcodeScanned'];

                $productBarcodeExpectedListInfo[$item['productBarcodeExpected']] = [
                    'sku'=>$item['sku'],
                    'season'=>$item['season'],
                ];
            }
            sort($productBarcodeExpectedList);
            sort($productBarcodeScannedList);

            foreach($productBarcodeExpectedList as $k1=>$productBarcodeExpected) {
                foreach($productBarcodeScannedList as $k2=>$productBarcodeScanned) {
                    if($productBarcodeExpected == $productBarcodeScanned) {

                        $secondStep[$ourBoxBarcode][] = [
                            'placeAddress' => $placeAddress,
                            'ourBoxBarcode' => $ourBoxBarcode,
                            'productBarcodeExpected' => $productBarcodeExpected,
                            'lcBarcode' => $lcBarcode,
                            'productBarcodeScanned' =>$productBarcodeExpected,
                            'problem'=>0,
                        ];

                        unset($productBarcodeExpectedList[$k1]);
                        unset($productBarcodeScannedList[$k2]);
                        break;
                    }
                }
            }

            sort($productBarcodeExpectedList);
            sort($productBarcodeScannedList);

            foreach($productBarcodeExpectedList as $k1=>$productBarcodeExpected) {
                $secondStep[$ourBoxBarcode][] = [
                    'placeAddress' => $placeAddress,
                    'ourBoxBarcode' => $ourBoxBarcode,
                    'productBarcodeExpected' => $productBarcodeExpected,
                    'lcBarcode' => $lcBarcode,
                    'productBarcodeScanned' =>$productBarcodeScannedList[$k1],
                    'problem'=>1,
                ];
            }
        }
        $productQty = 0;

        $rowToFile = "Our Box Barcode".';'
            ."Place Address".';'
            ."Our Box Barcode".';'
            ."Product Barcode Expected".';'
            ."LC Barcode".';'
            ."Product Barcode Scanned".';'
            ."Problem".';'
            ."Problem Type".';'
            ."Product SKU".';'
            ."Product SEASON".';'
            ."Product Qty".';'
            ."\n";

        $resultFileName = 'Product-b2c-to-b2b-05-05-2020-result.xlsx';
        file_put_contents($resultFileName,$rowToFile);

        foreach($secondStep as $ourBoxBarcode=>$items) {

            $lcBarcode = '';
            foreach ($items as $item) {
                if(empty($lcBarcode) && !empty($item['lcBarcode'])) {
                    $lcBarcode = $item['lcBarcode'];
                }

                $problem = isset($item['problem']) && !empty($item['problem']) ? 'Yes' : 'No';

                $problemType = '';
                if(empty($item['productBarcodeExpected']) && !empty($item['productBarcodeScanned'])) {
                    $problemType = 'plus';
                } elseif(!empty($item['productBarcodeExpected']) && empty($item['productBarcodeScanned'])) {
                    $problemType = 'minus';
                } elseif($item['productBarcodeExpected'] != $item['productBarcodeScanned']) {
                    $problemType = 'diff';
                }

                $sku = '';
                $season = '';
                if($item['productBarcodeExpected'] == $item['productBarcodeScanned']) {
                    $sku = $productBarcodeExpectedListInfo[$item['productBarcodeExpected']]['sku'];
                    $season = $productBarcodeExpectedListInfo[$item['productBarcodeExpected']]['season'];
                } elseif(isset($item['productBarcodeExpected']) && !empty($item['productBarcodeExpected'])) {  // Expected
                    $sku = $productBarcodeExpectedListInfo[$item['productBarcodeExpected']]['sku'];
                    $season = $productBarcodeExpectedListInfo[$item['productBarcodeExpected']]['season'];
                }elseif(isset($item['productBarcodeScanned']) && !empty($item['productBarcodeScanned'])) { // Scanned

                    $sku = 'no-sku';
                    $season = 'no-season';
                    if(isset($productBarcodeExpectedListInfo[$item['productBarcodeScanned']]['sku'])) {
                        $sku = $productBarcodeExpectedListInfo[$item['productBarcodeScanned']]['sku'];
                        $season = $productBarcodeExpectedListInfo[$item['productBarcodeScanned']]['season'];
                    }
                }


                if (!empty($item['placeAddress']) && !empty($item['ourBoxBarcode'])) {
                    $rowToFile = $ourBoxBarcode.';'
                        .$item['placeAddress'].';'
                        .$item['ourBoxBarcode'].';'
                        .$item['productBarcodeExpected'].';'
                        .$lcBarcode.';'
                        .$item['productBarcodeScanned'].';'
                        .$problem.';'
                        .$problemType.';'
                        .$sku.';'
                        .$season.';'
                        .++$productQty.';'
                        ."\n";

                    file_put_contents($resultFileName,$rowToFile,FILE_APPEND);
                }
            }

            file_put_contents($resultFileName,"\n",FILE_APPEND);
        }

        return $this->render('test');
    }



    public function actionTransfer2()
    {
        //die('ecommerce/defacto/ecom-other/transfer2 DIE');
        // ecommerce/defacto/ecom-other/transfer2
        $rootPath = 'tmp-file/defacto/b2c-b2b';
        $path = $rootPath.'/12-03-2020/movement-report-from-B2C-TO-B2B.xlsx';
        $excel = PHPExcel_IOFactory::load($path);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 1;
        for ($i = $start; $i <= 31053; $i++) {
            $productBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $productQty = (string)$excelActive->getCell('B' . $i)->getValue();
            $boxBarcode = (string)$excelActive->getCell('C' . $i)->getValue();
            $placeBarcode = (string)$excelActive->getCell('D' . $i)->getValue();
            $productSku = (string)$excelActive->getCell('E' . $i)->getValue();
            $season = (string)$excelActive->getCell('F' . $i)->getValue();

            if ($productBarcode == null) continue;
            $reportFrom[] = [
                'productBarcode'=>$productBarcode,
                'productQty'=>$productQty,
                'boxBarcode'=>$boxBarcode,
                'placeBarcode'=>$placeBarcode,
                'productSku'=>$productSku,
                'season'=>$season,
            ];
        }

        $boxes = [];
        foreach ($reportFrom as $row) {
            $boxes[$row['boxBarcode']][] = $row;
        }

        $i = 0 ;
        foreach ($boxes as $box=>$boxItems) {
            foreach ($boxItems as $boxRow) {
                $productBarcode = $boxRow['productBarcode'];
                $productQty = $boxRow['productQty'];
                $boxBarcode = $boxRow['boxBarcode'];
                $placeBarcode = $boxRow['placeBarcode'];
                $productSku = $boxRow['productSku'];
                $season = $boxRow['season'];

                $products = EcommerceStock::find()->andWhere([
                    'box_address_barcode'=>$boxBarcode,
                    'place_address_barcode'=>$placeBarcode,
                    'product_barcode'=>$productBarcode,
                    'status_availability'=>StockAvailability::YES,
//                    'api_status'=>StockAPIStatus::YES,
                ])
                 ->limit($productQty)
                 ->all();

                foreach ($products as $product) {
                    $product->note_message1 = 'transfer-12032020-b2c-to-b2b';
                    $product->status_availability = StockAvailability::NO;
    //                $product->save(false);
    //                $row = $i++.';'.$box.';'.$qty.';'.$product->product_barcode.';'.$product->box_address_barcode.';'.$product->place_address_barcode.';'."\n"; //
                    $row = $product->place_address_barcode.';'.$product->box_address_barcode.';'.$product->product_barcode.';'.$productSku.';'.$season.';'.$i++.';'."\n";
                    file_put_contents('transfer-12032020-b2c-to-b2b-result.csv',$row,FILE_APPEND);
//                    echo $row."<br />";
                }
            }
            $row = ''."\n";
            file_put_contents('transfer-12032020-b2c-to-b2b-result.csv',$row,FILE_APPEND);
        }
        //VarDumper::dump($reportFrom,10,true);
        die('ok');
    }

    public function actionAddSku()
    {
        die('ecommerce/defacto/ecom-other/add-sku DIE');

        $rootPath = 'tmp-file/defacto/b2c-b2b';
        $path = $rootPath.'/17-03-2020/b2c-to-b2b-updated.xlsx';
        $excel = PHPExcel_IOFactory::load($path);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 1;
        for ($i = $start; $i <= 3436; $i++) {
            $ourBoxBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $placeBarcode = (string)$excelActive->getCell('B' . $i)->getValue();
            $ourBoxBarcode = (string)$excelActive->getCell('C' . $i)->getValue();
            $productBarcodeExpected = (string)$excelActive->getCell('D' . $i)->getValue();
            $lcBarcode = (string)$excelActive->getCell('E' . $i)->getValue();
            $productBarcodeScanned = (string)$excelActive->getCell('F' . $i)->getValue();
            $problem = (string)$excelActive->getCell('G' . $i)->getValue();
            $problemType = (string)$excelActive->getCell('H' . $i)->getValue();

            if ($productBarcodeExpected == null && $productBarcodeScanned == null) continue;

            $reportFrom[] = [
                'ourBoxBarcode'=>$ourBoxBarcode,
                'placeBarcode'=>$placeBarcode,
                'productBarcodeExpected'=>$productBarcodeExpected,
                'lcBarcode'=>$lcBarcode,
                'productBarcodeScanned'=>$productBarcodeScanned,
                'problem'=>$problem,
                'problemType'=>$problemType,
            ];
        }

        $boxes = [];
        foreach ($reportFrom as $row) {
            $boxes[$row['ourBoxBarcode']][] = $row;
        }

        $i = 0 ;
        foreach ($boxes as $box=>$boxItems) {
            foreach ($boxItems as $boxRow) {

                $productSku = EcommerceInboundItem::find()->select('client_product_sku')
                                ->andWhere(['product_barcode' => $boxRow['productBarcodeScanned']])
                                ->andWhere('client_product_sku != ""')
                                ->scalar();

                $rowToFile = $boxRow['ourBoxBarcode'] . ';'
                    . $boxRow['placeBarcode'] . ';'
                    . $boxRow['ourBoxBarcode'] . ';'
                    . $boxRow['productBarcodeExpected'] . ';'
                    . $boxRow['lcBarcode'] . ';'
                    . $boxRow['productBarcodeScanned'] . ';'
                    . $boxRow['problem'] . ';'
                    . $boxRow['problemType'] . ';'
                    . $productSku . ';'
                    . "\n";

                file_put_contents('b2c-to-b2b-updated-with-sku.csv', $rowToFile, FILE_APPEND);
            }
            $row = ''."\n";
            file_put_contents('b2c-to-b2b-updated-with-sku.csv',$row,FILE_APPEND);
        }

        return 'OK';
    }

    public function actionReturnSendByApi() {
        die('ecommerce/defacto/ecom-other/return-send-by-api DIE');

        $allReport = EcommerceReturn::find()
//                    ->andWhere('order_number != "OMC-9353613"')
                    ->andWhere('accepted_qty > 0 and status = 1   ')
                    ->all();

//        VarDumper::dump($allReport,10,true);
//        die;
        $result = '';
        foreach ($allReport as $return) {
            $service = new ReturnService();
//            $result =  $service->sendByAPI($return->id);
//            $return->status = ReturnOutboundStatus::DONE;
//            $return->save(false);
//            VarDumper::dump($return->id,10,true);
//            VarDumper::dump($result,10,true);
        }
    }


    public function actionResendReturnOrder() {
        die('/ecommerce/defacto/ecom-other/resend-return-order DIE');

        $outboundIdList = [
            'OMC-28179336', // --
        ];

        $result = [];
        foreach($outboundIdList as $outboundNumber) {
            $service = new ReturnService();
            $repository = new ReturnRepository();

            $orderInfo = $repository->getOrderByOrderNumber($outboundNumber);
            $result[$outboundNumber] = $orderInfo->id;

            EcommerceStock::updateAll(['api_status' => StockAPIStatus::NO], ['return_id' => $orderInfo->id,'client_id' => 2,'api_status' => [StockAPIStatus::YES,StockAPIStatus::ERROR]]);
            $result[$outboundNumber] = $service->sendByAPI($orderInfo->id);
        }

        VarDumper::dump($result,10,true);
        return 'OK';
//        BarcodeService::createLcBoxForOurBox("xxxx");
    }

    public function actionResendReturnOrderOnlyFeedback() {
        die('/ecommerce/defacto/ecom-other/resend-return-order-only-feedback DIE');

        $outboundIdList = [
			//'OMC-78511242',
			'OMC-78531840',
			'OMC-78746055',
			'OMC-78431427',
			'OMC-78228846',
			'OMC-78504290',
			'OMC-78440540',
			'OMC-78677150',
			'OMC-78466700',
			'OMC-78509608',
			'OMC-78532272',
			'OMC-78191637',
        ];

        $outboundIdList = array_unique($outboundIdList);
        $result = [];
        foreach($outboundIdList as $outboundNumber) {

            $extract = [
                'OMC-29169097',
                'OMC-27399654'
            ];

            if(in_array($outboundNumber,$extract)) {
                //continue;
            }

            $service = new ReturnService();
            $repository = new OutboundRepository();

            $orderInfo = $repository->getOrderByOrderNumber($outboundNumber); //  $orderInfo = ;
            $result[$outboundNumber] = $orderInfo ? $orderInfo->id : 0; //            $result[$outboundNumber] = $repository->getOrderInfo($orderInfo->id);
            $result[$outboundNumber] = $repository->getOrderInfo($orderInfo->id);
            //$result[$outboundNumber] = $service->sendOutboundByAPI($repository->getOrderInfo($orderInfo->id));
        }

        VarDumper::dump($result,10,true);
        return 'OK';
//        BarcodeService::createLcBoxForOurBox("xxxx");
    }

    public function actionAddSkuIdToProduct() {
//        die('ecommerce/defacto/ecom-other/add-sku-id-to-product DIE');

        $rootPath = 'tmp-file/one/15-02-2021';
        $path = $rootPath.'/Book1.xlsx';
        $excel = PHPExcel_IOFactory::load($path);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 1;
        for ($i = $start; $i <= 44; $i++) {
            $productBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $productSku = EcommerceStock::find()->select('client_product_sku')->andWhere(['product_barcode'=>trim($productBarcode)])->scalar();
            $rowToFile = $productBarcode . ';'
                . $productSku . ';'
                . "\n";

            file_put_contents('add-sku-id-to-product.csv', $rowToFile, FILE_APPEND);
        }
        return $this->render('test');
    }

    public function actionInboundDiff($id = -1)
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        //die('ecommerce/defacto/ecom-other/inbound-diff DIE');
//        $inboundId = 89;
        $inboundId = $id;
        $inboundItemList = EcommerceInboundItem::find()
                          ->andWhere(['inbound_id'=>$inboundId])
                          ->all();
        $isSuccess = 1;
        foreach($inboundItemList as $rowItem) {
           $count = EcommerceStock::find()->andWhere([
               'inbound_id'=>$rowItem->inbound_id,
               'client_box_barcode'=>$rowItem->client_box_barcode,
               'product_barcode'=>$rowItem->product_barcode,
           ])->count();


            $row =  $rowItem->client_box_barcode.';'.$rowItem->product_barcode.";".$rowItem->product_expected_qty.';'.$rowItem->product_accepted_qty.';'.$count."\n";//"<br />";
            //echo $row."<br />";
            file_put_contents("inboundItemList-".$inboundId.".csv",$row,FILE_APPEND);

            if($count != $rowItem->product_accepted_qty) {
                $isSuccess = 0;
               $onStockList = EcommerceStock::find()->andWhere([
                    'inbound_id'=>$rowItem->inbound_id,
                    'client_box_barcode'=>$rowItem->client_box_barcode,
                    'product_barcode'=>$rowItem->product_barcode,
                ])->all();

                if(empty($onStockList)) {
                    $row =  $rowItem->client_box_barcode.';'.$rowItem->product_barcode.";".'-'.';'.'-'."\n";//"<br />";
                    echo $row."<br />";
                    file_put_contents("inboundItemList-full-".$inboundId.".csv",$row,FILE_APPEND);
                } else {
                    foreach ($onStockList as $stockITem) {
                        $row =  $rowItem->client_box_barcode.';'.$rowItem->product_barcode.";".$stockITem->box_address_barcode.';'.$stockITem->place_address_barcode."\n";//"<br />";
                        echo $row."<br />";
                        file_put_contents("inboundItemList-full-".$inboundId.".csv",$row,FILE_APPEND);
                    }
                }
            }
        }

        if($isSuccess) {
            echo "<h1>Все хорошо, расхождений нет!</h1> ".$inboundId." <br />";
        }

        return $this->render('test');
    }

    public function actionInboundDiff2()
    {
        // проверяем есть ли одинаковые короба в разных накладных
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        //die('ecommerce/defacto/ecom-other/inbound-diff2 DIE');
        $inboundId1 = 53;

        $inboundItemList1 = EcommerceInboundItem::find()
            ->andWhere(['inbound_id'=>$inboundId1])
            ->all();

        $inboundId2 = 54;
//        $inboundItemList2 = EcommerceInboundItem::find()
//            ->andWhere(['inbound_id'=>$inboundId2])
//            ->all();
        $isSuccess = 1;
        foreach($inboundItemList1 as $rowItem1) {

            $inboundItem = EcommerceInboundItem::find()
                ->andWhere(['inbound_id' => $inboundId2, 'client_box_barcode' => $rowItem1->client_box_barcode])
                ->one();

            if ($inboundItem) {
                $row = $rowItem1->client_box_barcode . ';' . $rowItem1->product_barcode . ";" . $rowItem1->product_expected_qty . ';' . $rowItem1->product_accepted_qty . "\n";//"<br />";
                echo $row . "<br />";
                file_put_contents("inboundItemList-" . $inboundId1 . ".csv", $row, FILE_APPEND);
                $isSuccess = 0;
            }
        }

        if($isSuccess) {
            echo "<h1>Все хорошо, расхождений нет!</h1>"."<br />";
        }

        return $this->render('test');
    }

    public function actionInboundDiff3()
    {
        // проверяем есть ли одинаковые короба в разных накладных
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        //die('ecommerce/defacto/ecom-other/inbound-diff2 DIE');
        $inboundId1 = 53;

        $inboundItemList = EcommerceInboundItem::find()
            ->andWhere(['inbound_id'=>$inboundId1])
            ->andWhere('product_accepted_qty != product_expected_qty')
            ->all();
        $isSuccess = 0;
        foreach($inboundItemList as $rowItem) {

            Stock::find()->andWhere([''])->all();

            if ($rowItem) {
                $row = $rowItem->client_box_barcode . ';' . $rowItem->product_barcode . ";" . $rowItem->product_expected_qty . ';' . $rowItem->product_accepted_qty . "\n";//"<br />";
                echo $row . "<br />";
                file_put_contents("inboundItemList-" . $inboundId1 . ".csv", $row, FILE_APPEND);
                $isSuccess = 0;
            }
        }

        if($isSuccess) {
            echo "<h1>Все хорошо, расхождений нет!</h1>"."<br />";
        }

        return $this->render('test');
    }


    //
    public function actionMoveReport()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        //die('ecommerce/defacto/ecom-other/move-report DIE');


        $rootPath = 'tmp-file/defacto/b2c-b2b/08-04-2020/LCBarcode.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $lcBarcodeList = [];
        $start = 1;
        for ($i = $start; $i <= 1670; $i++) {
            $lcBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $lcBarcodeList[trim($lcBarcode)] = trim($lcBarcode);
        }

        $rootPath = 'tmp-file/defacto/b2c-b2b/15-04-2020/Product-b2c-to-b2b-15-04-2020-result.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 51462; $i++) {

            $lcBarcode = (string)$excelActive->getCell('E' . $i)->getValue();
            $productBarcodeScanned = (string)$excelActive->getCell('F' . $i)->getValue();
            $sku = (string)$excelActive->getCell('I' . $i)->getValue();

            $reportFrom[] = [
                'lcBarcode'=>trim($lcBarcode),
                'productBarcodeScanned'=>trim($productBarcodeScanned),
                'sku'=>trim($sku),
            ];
        }

        $firstStep = [];
        foreach($reportFrom as $item) {
            $firstStep[$item['lcBarcode']][] = $item;
        }

        $secondStep = [];
        $result = [];
        foreach($firstStep as $ourBoxBarcode=>$items) {
            foreach ($items as $k=>$item) {
                if(!isset($secondStep[$item['productBarcodeScanned']])) {
                    $secondStep[$item['productBarcodeScanned']] = $item;
                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] = 1;
                } else {
                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] += 1;
                }
            }

            if(isset($lcBarcodeList[$ourBoxBarcode])) {
                $result[$ourBoxBarcode] = $secondStep;
            }
            $secondStep = [];
        }

        foreach ($lcBarcodeList as $lc) {
            if(!isset($result[$lc])) {
                //echo $lc."<br />";
            }
        }

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $row = 'LC Barcodes	Scanned'.';'.'Barcodes (EAN Codes)'.';'.'SkuID'.';'.'Quantity'.';';
        $fileName = 'result-file-transfer16-04-2020.csv';
        file_put_contents($fileName,$row."\n",FILE_APPEND);
        foreach($result as $items) {
            foreach($items as $item) {
                if(empty($item['productBarcodeScanned'])) {
                    continue;
                }
                $row = $item['lcBarcode'].';'.$item['productBarcodeScanned'].';'.$item['sku'].';'.$item['productBarcodeScannedQty'].';';
                file_put_contents($fileName,$row."\n",FILE_APPEND);
            }
        }

        //VarDumper::dump($result, 10, true);

        die(' END OK ');
    }
    //
    public function actionTestNewVersionInbound()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        //die('ecommerce/defacto/ecom-other/test-new-version-inbound DIE');

        $stockService = new Service();
        $inboundRepository = new InboundRepository();
        $InboundID = 9;
        $clientBoxBarcode = '';

        $resultBoxList = $stockService->boxReadyToSendByInboundAPI($InboundID, $clientBoxBarcode);
//        VarDumper::dump($resultBoxList,10,true);
        $boxQtyInfoList = [];
        foreach ($resultBoxList as $boxBarcode) {

           $productsByClientBoxBarcodeResult = $inboundRepository->getCountProductsByClientBoxBarcode($boxBarcode,$InboundID);
            $aB2CInBoundFeedBack = $stockService->getDataForSendByApiByBox($InboundID, $boxBarcode);
//            VarDumper::dump($aB2CInBoundFeedBack,10,true);

            if(!empty($productsByClientBoxBarcodeResult) && isset($productsByClientBoxBarcodeResult['0'])) {
                $expected = ArrayHelper::getValue($productsByClientBoxBarcodeResult,'0.productExpectedQty');
                $accepted = ArrayHelper::getValue($productsByClientBoxBarcodeResult,'0.productAcceptedQty');
                // высчитываем максимальное значение которые мы можем отправить по апи в расхождениях на один короб
                $maxQtyOverScan = floor(((($expected / 100) * 25) + $expected)-1);
                $doSeparate = $accepted > $maxQtyOverScan ? 'Y' : 'N';
                $doMoveToOtherLC = $accepted - $maxQtyOverScan;

                $boxQtyInfoList [$boxBarcode] = [
                    'productExpectedQty'=>$expected,
                    'productAcceptedQty'=>$accepted,
                    'maxQtyOverScan'=>$maxQtyOverScan,
                    'separate'=>$doSeparate,
                    'doMoveToOtherLC'=>$doMoveToOtherLC,
                ];
            }
        }

        VarDumper::dump($boxQtyInfoList,10,true);
        die;
    }
    // Нужно взять шк товаров из одного файли и понять это ошибочные шк или нет
    public function actionIndex13()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        die('ecommerce/defacto/ecom-other/index13 DIE');


        $rootPath = 'tmp-file/defacto/b2c-b2b/08-04-2020/problem-product-barcode.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $productBarcodeList = [];
        $start = 1;
        for ($i = $start; $i <= 3355; $i++) {
            $lcBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $productBarcodeList[trim($lcBarcode)] = trim($lcBarcode);
        }

        $rootPath = 'tmp-file/defacto/b2c-b2b/08-04-2020/Product-b2c-to-b2b-08-04-2020-result.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
//        for ($i = $start; $i <= 51408; $i++) {
        for ($i = $start; $i <= 510; $i++) {

            $lcBarcode = (string)$excelActive->getCell('E' . $i)->getValue();
            $productBarcodeExpected = (string)$excelActive->getCell('D' . $i)->getValue();
            $productBarcodeScanned = (string)$excelActive->getCell('F' . $i)->getValue();
            $sku = (string)$excelActive->getCell('I' . $i)->getValue();
            $problem = (string)$excelActive->getCell('G' . $i)->getValue();
            $problemType = (string)$excelActive->getCell('H' . $i)->getValue();

            $reportFrom[] = [
                'lcBarcode'=>trim($lcBarcode),
                'productBarcodeExpected'=>trim($productBarcodeExpected),
                'productBarcodeScanned'=>trim($productBarcodeScanned),
                'problem'=>trim($problem),
                'problemType'=>trim($problemType),
                'sku'=>trim($sku),
            ];
        }

        $firstStep = [];
        foreach($reportFrom as $item) {
            if(!empty($item['lcBarcode'])) {
                $firstStep[$item['lcBarcode']][] = $item;
            }
        }

//        $secondStep = [];
//        $result = [];
//        foreach($firstStep as $ourBoxBarcode=>$items) {
//            foreach ($items as $k=>$item) {
//                if(!isset($secondStep[$item['productBarcodeScanned']])) {
//                    $secondStep[$item['productBarcodeScanned']] = $item;
//                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] = 1;
//                } else {
//                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] += 1;
//                }
//            }
//
//            if(isset($productBarcodeList[$ourBoxBarcode])) {
//                $result[$ourBoxBarcode] = $secondStep;
//            }
//            $secondStep = [];
//        }
//
//        foreach ($productBarcodeList as $lc) {
//            if(!isset($result[$lc])) {
//                //echo $lc."<br />";
//            }
//        }

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

//        $row = 'LC Barcodes	Scanned'.';'.'Barcodes (EAN Codes)'.';'.'SkuID'.';'.'Quantity'.';';
//        $fileName = 'result-file-transfer5.csv';
//        file_put_contents($fileName,$row."\n",FILE_APPEND);
//        foreach($result as $items) {
//            foreach($items as $item) {
//                if(empty($item['productBarcodeScanned'])) {
//                    continue;
//                }
//                $row = $item['lcBarcode'].';'.$item['productBarcodeScanned'].';'.$item['sku'].';'.$item['productBarcodeScannedQty'].';';
//                file_put_contents($fileName,$row."\n",FILE_APPEND);
//            }
//        }
//
        VarDumper::dump($firstStep, 10, true);
//        VarDumper::dump($result, 10, true);

        die(' END OK ');

    }
    //
    public function actionIndex14()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        die('ecommerce/defacto/ecom-other/index14 DIE');


        $rootPath = 'tmp-file/defacto/b2c-b2b/21-04-2020/product-list.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $productList = [];
        $start = 1;
        for ($i = $start; $i <= 7756; $i++) {
            $productBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $productQty = (string)$excelActive->getCell('B' . $i)->getValue();

            $productList[trim($productBarcode)] = [
//            $productList[] = [
                'productBarcode'=>trim($productBarcode),
                'productQty'=>trim($productQty),
            ];
        }

//        file_put_contents("actionIndex14.log",print_r($productList,true));
//        VarDumper::dump($productList,10,true);
//        die("OK");

        $rootPath = 'tmp-file/defacto/b2c-b2b/21-04-2020/Product-b2c-to-b2b-16-04-2020-result.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $reportFrom = [];
        $start = 2;
        for ($i = $start; $i <= 51461; $i++) {
            $lcBarcode = (string)$excelActive->getCell('E' . $i)->getValue();
            $productBarcodeScanned = (string)$excelActive->getCell('F' . $i)->getValue();
            $sku = (string)$excelActive->getCell('I' . $i)->getValue();

            $reportFrom[] = [
                'lcBarcode'=>trim($lcBarcode),
                'productBarcodeScanned'=>trim($productBarcodeScanned),
                'sku'=>trim($sku),
            ];
        }

        $firstStep = [];
        foreach($reportFrom as $item) {
            $firstStep[$item['lcBarcode']][] = $item;
        }

        $secondStep = [];
        $result = [];
        foreach($firstStep as $ourBoxBarcode=>$items) {
            foreach ($items as $k=>$item) {
                if(!isset($secondStep[$item['productBarcodeScanned']])) {
                    $secondStep[$item['productBarcodeScanned']] = $item;
                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] = 1;
                } else {
                    $secondStep[$item['productBarcodeScanned']]['productBarcodeScannedQty'] += 1;
                }
            }

//            if(isset($lcBarcodeList[$ourBoxBarcode])) {
                $result[$ourBoxBarcode] = $secondStep;
//            }
            $secondStep = [];
        }

        $fileName = 'actionIndex14-result-21-04-2020-1.csv';
        foreach($productList as $productExist) {
            foreach ($result as $ourBoxBarcode => $productsItems) {
                foreach ($productsItems as $productBarcode => $productInfo) {
                    if (empty($productInfo['productBarcodeScanned'])) {
                        continue;
                    }

                    if($productExist['productBarcode'] == $productInfo['productBarcodeScanned']  && $productExist['productQty'] == $productInfo['productBarcodeScannedQty']) {
                        $row = $ourBoxBarcode.';'.$productInfo['productBarcodeScanned'].';'.$productInfo['sku'].';'.$productInfo['productBarcodeScannedQty'].';';
                        file_put_contents($fileName,$row."\n",FILE_APPEND);
                        continue;
                    }
                }
            }
        }

        //VarDumper::dump($result, 10, true);

        die(' END OK ');
    }
    //
    public function actionTest() {
        die('ecommerce/defacto/ecom-other/test DIE');

        $repository = new Repository();
        $queryForSendInventorySnapshot = $repository->getRemainsForSendInventorySnapshot();
        $oneSnapshot = $queryForSendInventorySnapshot->all();

        foreach($oneSnapshot as &$row) {

            $lcBarcode = BarcodeService::getLcBarcodeByOurBarcode($row['box_address_barcode']);

            if(empty($lcBarcode)) {
               $lcBarcode = BarcodeService::createLcBoxForOurBox($row['box_address_barcode']);
                usleep(500000);
            }

            $row['box_address_barcode'] = $lcBarcode;

            $rowToSave = $row['product_barcode'].';';
            $rowToSave .= $row['box_address_barcode'].';';
            $rowToSave .= $row['place_address_barcode'].';';
            $rowToSave .= $row['client_product_sku'].';';
            $rowToSave .= $row['productQty'].';';
//            $rowToSave .= BarcodeService::getLcBarcodeByOurBarcode($row['box_address_barcode']);

            file_put_contents('SendInventorySnapshotCSV-TEST-02-'.date('Y-m-d').'.csv',$rowToSave."\n",FILE_APPEND);
        }

        return $this->render('test');
    }

    public function actionResendShipmentOrder() {
//        die('/ecommerce/defacto/ecom-other/resend-shipment-order DIE');

        $outboundIdList = [
			"OMC-34187369",
			"OMC-34592779",
			"OMC-34593345",
			"OMC-34604854",
			"OMC-35118350",
			"OMC-36299030",
			"OMC-40400880",
			"OMC-40677849",
			"OMC-41306376",
        ];

        $outboundIdList = array_unique($outboundIdList);

        $result = [];
        foreach($outboundIdList as $outboundNumber) {
            $service = new OutboundService();
            $orderInfo = $service->getOrderInfoByOrderNumber($outboundNumber);
            $result[$outboundNumber] = $orderInfo->order->id;

//            $result[$outboundNumber]['items'] = $orderInfo->items;
			//$result[$outboundNumber] = $service->SendShipmentFeedback($orderInfo->order->id);
        }

        VarDumper::dump($result,10,true);
        return 'OK';
//        BarcodeService::createLcBoxForOurBox("xxxx");
    }

    public function actionResendShipmentOrderOutboundList() {

        die('/ecommerce/defacto/ecom-other/resend-shipment-order-outbound-list DIE');

        $title = '10-02-2021';

        $outboundIdList =  EcommerceOutboundList::find()
            ->select('client_order_number')
            ->andWhere([
                'list_title'=>$title,
            ])
            ->orderBy(['id'=>SORT_DESC])
            ->column();


        $result = [];
        foreach($outboundIdList as $outboundNumber) {
            $service = new OutboundService();
            $orderInfo = $service->getOrderInfoByOrderNumber($outboundNumber);
            $result[$outboundNumber] = $orderInfo->order->id;

//             $apiStatusYes = EcommerceOutbound::find()
//                    ->andWhere(['id'=>$orderInfo->order->id])
//                    ->andWhere('api_status = :apiStatusYes ',[':apiStatusYes'=>StockAPIStatus::YES])
//                    ->exists();
//            if($apiStatusYes) {
//                continue;
//            }
            //$result[$outboundNumber] = $service->SendShipmentFeedback($orderInfo->order->id);
        }

        VarDumper::dump($result,10,true);
        echo "<br />";
        echo "<br />";
        echo "COUNT : ";
        VarDumper::dump(count($result),10,true);
        return 'OK';
    }

    public function actionTestGetBatches() {
        die('/ecommerce/defacto/ecom-other/test-get-batches DIE');

        $service = new TransferService();
        $result = [];
        $result = $service->GetBatchesAPI();
        $result = EcommerceApiOutboundLog::findOne(141744);
        if($result) {

            $result = unserialize($result->response_data);

            $lcBarcodeList = [];
            if (ArrayHelper::getValue($result, 'HasError') == false) {
                $dataList = ArrayHelper::getValue($result, 'Data');
                if (!empty($dataList) && is_array($dataList)) {
                    foreach ($dataList as $key => $dataListItem) {
                        if (!empty($dataListItem)) {
                            $lcBarcodeList[$key] = new stdClass();
                            $lcBarcodeList[$key]->BatchId = ArrayHelper::getValue($dataListItem, 'BatchId');
                            $lcBarcodeList[$key]->Status = ArrayHelper::getValue($dataListItem, 'Status');
                            $lcBarcodeList[$key]->LcBarcodes = ArrayHelper::getValue($dataListItem, 'LcBarcodes.string');
                        }
                    }
                }
            }

//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump($lcBarcodeList,10,true);
//            echo "<br />";
//            echo "<br />";
//            VarDumper::dump($dataList,10,true);
            $mainVirtualBox = '10virtual-box00';
            $batchIdList = [];
            if (!empty($lcBarcodeList) && is_array($lcBarcodeList)) {
                foreach ($lcBarcodeList as $dto) {
                    if (!empty($dto->LcBarcodes) && is_array($dto->LcBarcodes)) {
                        $batchIdList[] = $dto->BatchId;
                        foreach ($dto->LcBarcodes as $box) {
                            $isExistTransfer = EcommerceTransfer::find()->andWhere(['client_BatchId' => $dto->BatchId,'client_LcBarcode' => $box])->exists();
                            if($isExistTransfer) {
                                continue;
                            }
                            $transferNew = new EcommerceTransfer();
                            $transferNew->client_id = 2;
                            $transferNew->client_BatchId = $dto->BatchId;
                            $transferNew->client_Status = $dto->Status;
                            $transferNew->client_LcBarcode = $box;
                            $transferNew->status = TransferStatus::_NEW;
                            $transferNew->api_status = StockAPIStatus::NO;
                            $transferNew->save(false);
                        }

                        $isExistTransfer = EcommerceTransfer::find()->andWhere(['client_BatchId' => $dto->BatchId,'client_LcBarcode' => $mainVirtualBox])->exists();
                        if($isExistTransfer) {
                            continue;
                        }
                        $transferNew = new EcommerceTransfer();
                        $transferNew->client_BatchId = $dto->BatchId;
                        $transferNew->client_Status = $dto->Status;
                        $transferNew->client_LcBarcode = $mainVirtualBox;
                        $transferNew->status = TransferStatus::_NEW;
                        $transferNew->api_status = StockAPIStatus::NO;
                        $transferNew->save(false);
                    }
                }
            }


            echo "<br />";
            echo "<br />";
            VarDumper::dump($result, 10, true);

            foreach ($batchIdList as $batchId) {
                $result = $service->GetOutBoundAPI($batchId);
                $transferMain = EcommerceTransfer::find()->andWhere(['client_BatchId' => $batchId,'client_LcBarcode' => $mainVirtualBox])->one();
                $productsList = [];
                if (ArrayHelper::getValue($result, 'HasError') == false) {
                    $dataList = ArrayHelper::getValue($result, 'Data.Data.B2COutboundDTO');
                    if (!empty($dataList) && is_array($dataList)) {
                        foreach ($dataList as $key => $dataListItem) {
                            if (!empty($dataListItem)) {
                                $productsList[$key] = new stdClass();
                                $productsList[$key]->ourTransferId = $transferMain->id;
                                $productsList[$key]->OutboundId = ArrayHelper::getValue($dataListItem, 'OutboundId');
                                $productsList[$key]->BatchId = ArrayHelper::getValue($dataListItem, 'BatchId');
                                $productsList[$key]->SkuId = ArrayHelper::getValue($dataListItem, 'SkuId');
                                $productsList[$key]->Quantity = ArrayHelper::getValue($dataListItem, 'Quantity');
                                $productsList[$key]->Status = ArrayHelper::getValue($dataListItem, 'Status');
                                $productsList[$key]->ToBusinessUnitId = ArrayHelper::getValue($dataListItem, 'ToBusinessUnitId');
                            }
                        }
                    }
                }

                if (!empty($productsList) && is_array($productsList)) {
                    foreach ($productsList as $dto) {

                            $isExistTransferItem = EcommerceTransferItems::find()->andWhere([
                                                'transfer_id' => $dto->ourTransferId,
                                                'client_OutboundId' =>  $dto->OutboundId,
                                                'client_BatchId' =>  $dto->BatchId,
                                                'client_SkuId' =>  $dto->SkuId,
                                                'client_Quantity' =>  $dto->Quantity,
                                                ])->exists();
                            if($isExistTransferItem) {
                                continue;
                            }
                            $transferNew = new EcommerceTransferItems();
                            $transferNew->transfer_id = $dto->ourTransferId;
                            $transferNew->client_OutboundId = $dto->OutboundId;
                            $transferNew->client_BatchId = $dto->BatchId;
                            $transferNew->client_SkuId = $dto->SkuId;
                            $transferNew->client_Quantity = $dto->Quantity;
                            $transferNew->client_Status = $dto->Status;
                            $transferNew->product_sku = $dto->SkuId;
                            $transferNew->expected_qty = $dto->Quantity;
                            $transferNew->status = TransferStatus::_NEW;
                            $transferNew->api_status = StockAPIStatus::NO;
                            $transferNew->save(false);
                    }
                }

                $transferMain->expected_qty = EcommerceTransferItems::find()->select('sum(expected_qty)')->andWhere(['transfer_id'=>$transferMain])->scalar();
                $transferMain->save(false);

            }
        }

        VarDumper::dump($productsList,10,true);

        return 'OK';
    }



    //
    public function actionMoveReport2()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        die('ecommerce/defacto/ecom-other/move-report2 DIE');
//
        $rootPath = 'tmp-file/defacto/b2c-b2b/05-05-2020/LCBarcode.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $lcBarcodeList = [];
        $start = 1;
        for ($i = $start; $i <= 1670; $i++) {
            $lcBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $lcBarcodeList[trim($lcBarcode)] = trim($lcBarcode);
        }

        $parseService = new ParseTransferFile();
        $pathToResultFile = '05-05-2020/Product-b2c-to-b2b-05-05-2020-result.xlsx';
        $firstStep = $parseService->parseFileResult($pathToResultFile);
        $result = $parseService->calculateProductInBox($firstStep);
        file_put_contents("actionMoveReport2",print_r($result,true)."\n");

        $parseService->makeCerenFormat($result,$lcBarcodeList);
        $parseService->makeOurFormat($firstStep,$lcBarcodeList);

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        //VarDumper::dump($result, 10, true);

        die(' END OK ');
    }

    public function actionMoveReport3()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        die('ecommerce/defacto/ecom-other/move-report3 DIE');

        $rootPath = 'tmp-file/defacto/b2c-b2b/05-05-2020/Serdar.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $lcBarcodeList = [];
        $start = 1;
        for ($i = $start; $i <= 4118; $i++) {
            $lcBarcode = (string)$excelActive->getCell('A' . $i)->getValue();
            $barcodeEANCodes = (string)$excelActive->getCell('B' . $i)->getValue();
            $SkuID = (string)$excelActive->getCell('C' . $i)->getValue();
            $Quantity = (string)$excelActive->getCell('D' . $i)->getValue();
            $lcBarcodeList[] = [
              'LCBarcodeScanned'=>trim($lcBarcode),
              'BarcodeEANCodes'=>trim($barcodeEANCodes),
              'SkuID'=>trim($SkuID),
              'Quantity'=>trim($Quantity),
            ];
        }

//        file_put_contents("actionMoveReport5",print_r($lcBarcodeList,true)."\n");
//        die;
        $parseService = new ParseTransferFile();
        $pathToResultFile = '05-05-2020/Product-b2c-to-b2b-05-05-2020-result.xlsx';
        $firstStep = $parseService->parseFileResult($pathToResultFile);

        $row = 'LC Barcodes	Scanned'.';'.'Barcodes (EAN Codes)'.';'.'SkuID'.';'.'Quantity'.';'.'Nomadex Box'.';';
        $fileName = 'AddOutBoxFormat-05-05-2020-v10.csv';
        file_put_contents($fileName,$row."\n");

        $lcBarcodeListCopy = $lcBarcodeList;

        foreach($lcBarcodeList as $k=>$lcBarcodeListItem) {
            foreach ($firstStep as $lcBox => $items) {
                foreach ($items as $productBarcode => $item) {
                    if($lcBarcodeListItem['LCBarcodeScanned'] == $lcBox && $lcBarcodeListItem['BarcodeEANCodes'] == $item['productBarcodeScanned'] ) {
                        $row = $lcBarcodeListItem['LCBarcodeScanned'] . ';' . $lcBarcodeListItem['BarcodeEANCodes'] . ';' . $lcBarcodeListItem['SkuID'] . ';'. $lcBarcodeListItem['Quantity'] . ';' . $item['ourBoxBarcode'] . ';';
                        file_put_contents($fileName, $row . "\n", FILE_APPEND);
                        unset($lcBarcodeListCopy[$k]);
                        break;
                    }
                }
            }
        }

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump($lcBarcodeListCopy, 10, true);
        die(' END OK ');
    }


    public function actionReservationTransfer()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        die('ecommerce/defacto/ecom-other/reservation-transfer DIE');

        $parseService = new ParseTransferFile();
        $pathToResultFile = '05-05-2020/Product-b2c-to-b2b-05-05-2020-result.xlsx';
        $firstStep = $parseService->parseFileResult($pathToResultFile);


        $rowToFile = "Our Box Barcode".';'
            ."Place Address".';'
            ."Our Box Barcode".';'
            ."Product Barcode Expected".';'
            ."LC Barcode".';'
            ."Product Barcode Scanned".';'
            ."Problem".';'
            ."Problem Type".';'
            ."Product SKU".';'
            ."Product SEASON".';'
            ."Product Qty".';'
            ."Найден как доступный?".';'
            ."Зарезервирован Да/Нет?".';'
            ."\n";

        $fileName = 'transfer-08052020-b2c-to-b2b-result-v10.csv';
        file_put_contents($fileName,$rowToFile);

        $i = 0;
        foreach ($firstStep as $lcBox => $items) {
            foreach ($items as $productBarcode => $item) {
               $one = EcommerceStock::find()->andWhere([
                   'box_address_barcode'=>trim($item['ourBoxBarcode']),
                   'place_address_barcode'=>trim($item['placeAddress']),
                   'product_barcode'=>trim($item['productBarcodeScanned']),
                   'status_availability'=>StockAvailability::YES,
               ])->one();

                $row = '';

                if($one) {
                    $one->note_message2 = 'transfer2';
                    $one->status_availability = StockAvailability::BLOCKED;
                    $one->save(false);
                    continue;

                } else {
                    $row = $item['ourBoxBarcode'].';'.
                           $item['placeAddress'].';'.
                           $item['ourBoxBarcode'].';'.
                           $item['lcBarcode'].';'.
                           $item['productBarcodeExpected'].';'.
                           $item['productBarcodeScanned'].';'.
                           $item['problem'].';'.
                           $item['problemType'].';'.
                           $item['sku'].';'.
                           $item['productSeason'].';'.
                           ++$i.';'.
                           'NOT-FIND'.';';
                }

                $one = EcommerceStock::find()->andWhere([
                            'box_address_barcode'=>trim($item['ourBoxBarcode']),
                            'place_address_barcode'=>trim($item['placeAddress']),
                            'product_barcode'=>trim($item['productBarcodeScanned']),
                            'status_availability'=>StockAvailability::RESERVED,
                       ])->one();

                if($one) {
                    $one->note_message2 = 'transfer2';
                    $one->save(false);
                    $row .= 'RESERVED'.";";
                } else {
                    $row .= 'NO-RESERVED'.";";
                }

                file_put_contents($fileName,$row."\n",FILE_APPEND);
            }
        }

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
//        VarDumper::dump([], 10, true);
        die(' END OK ');
    }

    public function actionTestTransferReserv()
    {
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
        die('ecommerce/defacto/ecom-other/test-transfer-reserv DIE');
//
        $tr = new TransferRepository();
        //$tr->resetTransferOrder(6544);
        die('-6544-');


        $orderInfo = $tr->getOrderInfo(855);

//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";

        EcommerceStock::updateAll(['transfer_box_check_step'=>''],['transfer_box_check_step'=>$orderInfo->order->id]);

        $result = [];
        foreach($orderInfo->boxItems as $ourBoxBarcode) {
            $availableProductInBox = EcommerceStock::find()
                ->select('place_address_barcode, box_address_barcode, product_barcode, count(product_barcode) as productQty, product_season_full ')
                ->andWhere([
                    'client_id' => 2,
                    'box_address_barcode' => $ourBoxBarcode,
                    'status_availability' => StockAvailability::YES,
                    'condition_type' => StockConditionType::UNDAMAGED,
                ])
                ->groupBy('product_barcode, box_address_barcode, place_address_barcode')
                ->asArray()
                ->all();

            $item = [];
            $item['isProblemBox'] = 'NO';
            $item['expectedBoxBarcode'] = $ourBoxBarcode;
            $item['readyProductQtyToTransfer'] = 0;
            $item['readyProductQtyToMoveOtherBox'] = 0;
            $item['sorting'] = 0;
            $item['availableProductInBox'] = $availableProductInBox;

            $result[] = $item;
        }


        foreach($result as $i=>$availableProductInBox) {
            foreach ($availableProductInBox['availableProductInBox'] as $i2 => $product) {
                $expectedProductInfo = EcommerceTransferItems::find()->andWhere(['product_barcode' => $product['product_barcode'],'transfer_id' => $orderInfo->order->id])->asArray()->one();

                $result[$i]['availableProductInBox'][$i2]['isExistProductBarcode'] = 'NO';
                $result[$i]['availableProductInBox'][$i2]['expectedProductQty'] = 0;
                $result[$i]['availableProductInBox'][$i2]['sorting'] = 0;

                if(!empty($expectedProductInfo)) {
                    $result[$i]['readyProductQtyToTransfer'] += $product['productQty'];
                    $result[$i]['availableProductInBox'][$i2]['isExistProductBarcode'] = 'YES';
                    $result[$i]['availableProductInBox'][$i2]['expectedProductQty'] = $expectedProductInfo['expected_qty'];

                    EcommerceStock::updateAll([
                        'transfer_box_check_step'=>$orderInfo->order->id
                    ],[
                            'client_id' => 2,
                            'box_address_barcode' => $availableProductInBox['expectedBoxBarcode'],
                            'status_availability' => StockAvailability::YES,
                            'condition_type' => StockConditionType::UNDAMAGED,
                    ]);

                } else {
                    $result[$i]['sorting'] += 1;
                    $result[$i]['readyProductQtyToMoveOtherBox'] += $product['productQty'];
                    $result[$i]['isProblemBox'] = 'YES';
                }
            }
        }

        $fileName = 'test-transfer-reserv.csv';
        $rowHeader = 'Place_address_barcode'.';'.
            'Box_address_barcode'.';'.
            'Product_barcode'.';'.
            'ProductQty'.';'.
            'Product_season_full'.';'.
            'isExistProductBarcode'.';'.
            'ExpectedProductQty'.';'.
            'Sorting'.';'.
            'isProblemBox'.';';

        file_put_contents($fileName,$rowHeader."\n");
        ArrayHelper::multisort($result,'sorting');
        $forPDF = [];
        foreach($result as $i=>$availableProductInBox) {

            if(empty($availableProductInBox['availableProductInBox'])) {
//                $row = $availableProductInBox['expectedBoxBarcode'].';';
//                file_put_contents($fileName,$row."\n",FILE_APPEND);
                continue;
            }

            ArrayHelper::multisort($availableProductInBox['availableProductInBox'],'expectedProductQty');

            foreach ($availableProductInBox['availableProductInBox'] as $i2 => $product) {

                if(!empty($product['product_barcode'])) {

                   $availableSeason = EcommerceStock::find()->select('product_season_year')->andWhere(['product_barcode'=>$product['product_barcode']])->scalar();
                    $isProblemBox = $availableProductInBox['isProblemBox'];
                    $isExistProductBarcode = $product['isExistProductBarcode'];
                    $moveToOtherBox = $isExistProductBarcode == 'NO' ? 'ДА' : 'НЕТ';
                    if(in_array($availableSeason,[19,20])) {
                        $isProblemBox = 'YES';
                        $isExistProductBarcode = 'NO';
                        $moveToOtherBox = 'ДА';
                    }


                    if($isProblemBox == 'NO') {
                        continue;
                    }

                    $forPDF [$product['box_address_barcode']][] =  [
                        'place_address_barcode' => $product['place_address_barcode'],
                        'box_address_barcode' => $product['box_address_barcode'],
                        'product_barcode' => $product['product_barcode'],
                        'productQty' => $product['productQty'],
                        'product_season_full' => $product['product_season_full'],
                        'isExistProductBarcode' => $isExistProductBarcode,
                        'expectedProductQty' => $product['expectedProductQty'],
                        'sorting' => $availableProductInBox['sorting'],
                        'isProblemBox' =>$isProblemBox,
                        'moveToOtherBox' =>$moveToOtherBox,
                    ];
                }
            }

//            $forPDF [] = [];

        }

//        VarDumper::dump(count($result), 10, true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($forPDF, 10, true);
//        die(' END OK ');

        return $this->actionPdf($forPDF,$orderInfo);
    }

    public function actionPdf($forPDF,$orderInfo)
    {
//        $fileName = 'xxxxx';
//        foreach($result as $i=>$availableProductInBox) {
//
//            if(empty($availableProductInBox['availableProductInBox'])) {
//                continue;
//            }
//
//            ArrayHelper::multisort($availableProductInBox['availableProductInBox'],'expectedProductQty');
//
//            foreach ($availableProductInBox['availableProductInBox'] as $i2 => $product) {
//                if(!empty($product['product_barcode'])) {
//                    $row = $product['place_address_barcode'].';'.
//                        $product['box_address_barcode'].';'.
//                        $product['product_barcode'].';'.
//                        $product['productQty'].';'.
//                        $product['product_season_full'].';'.
//                        $product['isExistProductBarcode'].';'.
//                        $product['expectedProductQty'].';'.
//                        $availableProductInBox['sorting'].';'.
//                        $availableProductInBox['isProblemBox'].';';
//
//                    file_put_contents($fileName,$row."\n",FILE_APPEND);
//                }
//            }
//            file_put_contents($fileName,"\n",FILE_APPEND);
//        }

        return $this->render('transfer-check-pick-list-pdf',['transferList'=>$forPDF,'orderInfo'=>$orderInfo]);
    }


    public function actionAddSeason()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
       die('ecommerce/defacto/ecom-other/add-season DIE');

        $rootPath = 'tmp-file/defacto/b2c-b2b/13-05-2020/SnapshotList_12052020.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $seasonList = [];
        $start = 1;
        for ($i = $start; $i <= 46568; $i++) {
            $skuID = (string)$excelActive->getCell('B' . $i)->getValue();
            $season = (string)$excelActive->getCell('F' . $i)->getValue();
            $seasonList[trim($skuID)] = [
                'SkuID'=>trim($skuID),
                'season'=>trim($season),
            ];
        }


        $allInStock = EcommerceStock::find()->select('client_product_sku')
            ->andWhere(['client_id' => 2])
            ->andWhere('product_season IS NULL OR product_season = "" OR product_season = 0')
            ->groupBy('product_barcode')
            ->column();

        $i = 0;
        foreach($allInStock as $k=>$ProductSku) {
            $productSeason = isset($seasonList[$ProductSku]) ? $seasonList[$ProductSku]['season'] : '';

            $seasonInfo = explode(' ',$productSeason);

            $product_season_year = ArrayHelper::getValue($seasonInfo,'0');
            $product_season = ArrayHelper::getValue($seasonInfo,'1');
            $product_season_full = $productSeason;


            EcommerceStock::updateAll([
                'product_season'=>$product_season,
                'product_season_year'=>$product_season_year,
                'product_season_full'=>$product_season_full,
            ],[
                'client_product_sku'=>$ProductSku
            ]);

//            echo ++$i.' / '.ArrayHelper::getValue($masterDataResult,'Data.SkuId').' / '.$ProductBarcode."\n";
//            echo "<br />";
//            VarDumper::dump(ArrayHelper::getValue($masterDataResult,'Data.SkuId'),10,true);
//            die("xxxx");
        }
//        echo "php yii ecommerce/update-product-season OK";
//        return 0;


        VarDumper::dump($seasonList,10,true);
        die;
    }

    public function actionLostBox()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
       die('ecommerce/defacto/ecom-other/lost-box DIE');

        $rootPath = 'tmp-file/defacto/b2c-b2b/13-05-2020/6366234.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $list = [];
        $start = 1;
        for ($i = $start; $i <= 1439; $i++) {
            $lcOutBox = (string)$excelActive->getCell('A' . $i)->getValue();
            $list[trim($lcOutBox)] = trim($lcOutBox);
        }

        $i = 0;
        $result = [];
        foreach($list as $lc) {
            $ourBox = OutboundBox::find()->select('our_box')
                ->andWhere(['client_box' => $lc])
                ->scalar();

            $result[$ourBox] = $lc;
        }
//        echo "php yii ecommerce/update-product-season OK";
//        return 0;


        $list = Stock::find()->select('box_barcode')
            ->andWhere(['outbound_order_id' => 45113])
            ->column();

        foreach($list as $lc) {
            if(!empty($lc) && !isset($result[$lc])) {
                echo $lc."<br />";
            }
        }


        VarDumper::dump(count($result),10,true);
        echo "<br />";
        echo "<br />";
        VarDumper::dump($result,10,true);
        die;
    }

    public function actionMakeSerdarTransfer()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        die('ecommerce/defacto/ecom-other/make-serdar-transfer DIE');

        $parseService = new ParseTransferFile();
        $pathToResultFile = '05-05-2020/Product-b2c-to-b2b-05-05-2020-result.xlsx';
        $firstStep = $parseService->parseFileByOurBoxResult($pathToResultFile);
        $i = 0;
        //$transferId = 2;   // 6694899
        $transferId = 802; // 6871820

        $productListFromReserved = EcommerceStock::find()->andWhere(['transfer_box_check_step' => $transferId])->all();

        foreach($productListFromReserved as $productStockRow) {
            if(isset($firstStep[$productStockRow->box_address_barcode])) {
                $lcBarcode = $firstStep[$productStockRow->box_address_barcode]['0']['lcBarcode'];
                $productStockRow->transfer_outbound_box = $lcBarcode;
                $productStockRow->save(false);
            }
        }

//        foreach ($firstStep as $ourBox => $items) {
//            foreach ($items as $productBarcode => $item) {
//
//            }
//        }

//        file_put_contents("ddddd-2.txt","\n");
//        file_put_contents("ddddd-2.txt",print_r($firstStep,true)."\n",FILE_APPEND);
//        VarDumper::dump($firstStep,10,true);

        return 'RETURN OK';
    }

    public function actionMakeSerdarTransfer2()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        die('ecommerce/defacto/ecom-other/make-serdar-transfer2 DIE');

        //$transferId = 956; // 7038284 готово
        $transferId = 855; // 6950055 готово

//        $productListFromReserved = EcommerceStock::find()->andWhere(['transfer_box_check_step' => $transferId])->all();
        $productListFromReserved = EcommerceStock::find()->andWhere(['transfer_id' => $transferId])->all();

        foreach($productListFromReserved as $productStockRow) {
                $productStockRow->transfer_outbound_box = $productStockRow->outbound_box;
                $productStockRow->save(false);
        }

        return 'RETURN OK 2 = '.$transferId;
    }

    public function actionMakeSerdarTransfer3()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        die('ecommerce/defacto/ecom-other/make-serdar-transfer3 DIE');


         // 6694899 - ИД 2 +
         // 6871820 - ИД 802

        foreach ([2,802] as $transferId) {

            $productListFromReserved = EcommerceTransferItems::find()->andWhere(['transfer_id' => $transferId])->all();

            foreach ($productListFromReserved as $productStockRow) {
                $productStockRow->status = TransferStatus::SCANNED;
                $productStockRow->accepted_qty = $productStockRow->allocated_qty;
                $productStockRow->save(false);
            }

            $productListFromReserved = EcommerceStock::find()->andWhere(['transfer_id' => $transferId])->all();

            foreach ($productListFromReserved as $productStockRow) {
                $productStockRow->status_transfer = StockTransferStatus::SCANNED;
                $productStockRow->save(false);
            }
        }

        return 'RETURN OK 3 = ';
    }

    public function actionTransferDiff()
    {
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        //die('ecommerce/defacto/ecom-other/transfer-diff DIE');
        $inboundId = 1272;
        $inboundItemList = EcommerceTransferItems::find()
            ->andWhere(['transfer_id'=>$inboundId])
            ->all();

        foreach($inboundItemList as $rowItem) {
            $count = EcommerceStock::find()->andWhere([
                'transfer_id'=>$rowItem->transfer_id,
                'transfer_item_id'=>$rowItem->id,
//                'product_barcode'=>$rowItem->product_barcode,
            ])->count();


            $row =  $rowItem->product_barcode.";".$rowItem->allocated_qty.';'.$rowItem->accepted_qty.';'.$count."\n";//"<br />";
            echo $row."<br />";
//            file_put_contents("inboundItemList-".$inboundId.".csv",$row,FILE_APPEND);

            if($count != $rowItem->accepted_qty) {
//                die("---".$rowItem->product_barcode."-----");
//            if($count != $rowItem->accepted_qty || $rowItem->allocated_qty != $rowItem->accepted_qty) {
                $onStockList = EcommerceStock::find()->andWhere([
                    'transfer_id'=>$rowItem->transfer_id,
                    'transfer_item_id'=>$rowItem->id,
                    'product_barcode'=>$rowItem->product_barcode,
                ])->all();

                if(empty($onStockList)) {
                    $row = $rowItem->product_barcode.";".'-----------------'.';'.'------------------'."\n";//"<br />";
                    echo $row."<br />";
                    file_put_contents("transferItemList-full-".$inboundId.".csv",$row,FILE_APPEND);
                } else {
                    foreach ($onStockList as $stockITem) {
                        $row = $rowItem->product_barcode.";".$stockITem->box_address_barcode.';'.$stockITem->place_address_barcode.";+++"."\n";//"<br />";
                        echo $row."<br />";
                        file_put_contents("transferItemList-full-".$inboundId.".csv",$row,FILE_APPEND);
                    }
                }
            }
        }

        return $this->render('test');
    }


    public  function actionTestSaveDataToFile() {

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        //die('ecommerce/defacto/ecom-other/test-save-data-to-file DIE');
        /*
        $str = "<?php return ['z'=>1,'w'=>2] ?>";
        file_put_contents('defactoConfig/xxxx.php',$str);

         $config = include_once("defactoConfig/xxxx.php");
        VarDumper::dump($config,10,true);
        die; */

//        OMC-1486 / 12203210 / Different products were returned at different times, Therefore more than one return invoice was created.
//        OMC-1491 / 12203213 / Only one product in the shipment was returned.
//        OMC-1489 / 12203212 / All products in shipment were returned at the same time.


        $returnAPIService = new ReturnAPIService();
        $returnService = new ReturnService();
        $returnRepository = new ReturnRepository();
        $stockService = new Service();

        $returnId = 2387;

        $orderInfo = $returnRepository->getOrderInfo($returnId);
        foreach($orderInfo->items as $item) {
            $stockService->updateProductSku($item->product_barcode);
        }

        $productsReadyForSendByAPI = $returnRepository->getProductsReadyForSendByAPI($returnId);
        $productsReadyList = $returnService->prepareProductsReadyForSendByAPI($productsReadyForSendByAPI);

        VarDumper::dump($productsReadyList,10,true);
        die;

//       $result =  $returnAPIService->GetReturnReasonList();
//       $result =  $returnAPIService->GetReturnReasonProcessList();
//       $result =  $returnAPIService->GetShipmentForReturn("OMC-12045356",'Shipment');
//       $result =  $returnAPIService->GetShipmentForReturn("133881694",'Shipment');
//       $result =  $returnAPIService->GetShipmentForReturn("12203222",'CargoReturnCode');

         $result = [];
//       Ex.OrderId = 12203222 , Ex.ShipmentId= 1537 // OK
//       $result =  $returnAPIService->GetShipmentForReturn("1489",'Shipment');
//       $result =  $returnAPIService->GetShipmentForReturn("1491",'Shipment');
//       $result =  $returnAPIService->GetShipmentForReturn("1486",'Shipment');



       $dto = new stdClass();
       $dto->ExternalShipmentId = ArrayHelper::getValue($result,'Data.ExternalShipmentId','');
       $dto->ExternalOrderId = ArrayHelper::getValue($result,'Data.ExternalOrderId','');
       $dto->OrderSource = ArrayHelper::getValue($result,'Data.OrderSource','');
       $dto->IsRefundable = ArrayHelper::getValue($result,'Data.IsRefundable','');
       $dto->CargoReturnCode = @ArrayHelper::getValue($result,'Data.CargoReturnCode','');
       $dto->RefundableMessage = @ArrayHelper::getValue($result,'Data.RefundableMessage','');
       $dto->items = ArrayHelper::getValue($result,'Data.Items.B2CShipmentReturnItemResult',[]);


        if(!$returnRepository->isExistOrderFromAPI($dto->ExternalShipmentId,$dto->ExternalOrderId)) {
           $newReturn = $returnService->createByDTO($dto);
            $totalQty = 0;
            foreach($dto->items as $dtoItem) {
                $returnService->createItemByDTO($newReturn->id,$dtoItem);
                $totalQty += $dtoItem->ReturnedQuantity;
            }

            $newReturn->expected_qty = $totalQty;
            $newReturn->save(false);
        }

        VarDumper::dump($dto,10,true);
//        VarDumper::dump($result,10,true);
    }

    public function actionTest2GetBatches()
    {
        //die('/ecommerce/defacto/ecom-other/test2-get-batches DIE');

        $service = new TransferService();
        $service->GetBatches();
    }

    public function actionAddNine()
    {
//        die('/ecommerce/defacto/ecom-other/add-nine DIE');
        $asDatetimeFormat = 'php:d.m.Y H:i:s';

        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/01/problem-return.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 2;
        $row =
            "Девятка ". " ; ".
            "Номер заказа ". " ; ".
            "Ждали". " ; ".
            "Приняли". " ; ".
            "Источник заказа". " ; ".
            "IsRefundable". " ; ".
            "RefundableMessage ". " ; ".
            "Дата создания". " ; ".
            "Дата изменения". " ; "
            . " ; ";

        file_put_contents('problem-return-23-06-2020.csv',$row."\n",FILE_APPEND);
        for ($i = $start; $i <= 79; $i++) {

            $orderNumber = $excelActive->getCell('A' . $i)->getValue();

            file_put_contents('problem-return-23-06-2020-.csv', $orderNumber . "\n", FILE_APPEND);

            $outboundOrder = EcommerceOutbound::find()->andWhere(['order_number' => $orderNumber])->one();

            $outboundBox = 'не из нашей системы';
            if ($outboundOrder) {
                $outboundBox = EcommerceStock::find()->select('outbound_box')
                               ->andWhere(['outbound_id' => $outboundOrder->id])
                               ->andWhere('outbound_box != 0 AND outbound_box IS NOT NULL')
                               ->scalar();
            }

            $return = EcommerceReturn::find()->andWhere(['order_number'=>$orderNumber])->one();

            $row =
                 $outboundBox. " ; ".
                 $orderNumber. " ; ".
                 $return->expected_qty. " ; ".
                 $return->accepted_qty. " ; ".
                 $return->client_OrderSource. " ; ".
                 $return->client_IsRefundable. " ; ".
                 $return->client_RefundableMessage. " ; ".
                 Yii::$app->formatter->asDatetime($return->created_at, $asDatetimeFormat). " ; ".
                 Yii::$app->formatter->asDatetime($return->updated_at, $asDatetimeFormat). " ; "
                 . " ; ";

            file_put_contents('problem-return-23-06-2020.csv',$row."\n",FILE_APPEND);
        }
    }


    public function actionSendOldReturn()
    {
        die('/ecommerce/defacto/ecom-other/send-old-return DIE');
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/03/ECPReturns.xlsx';
//        $excel = \PHPExcel_IOFactory::load($rootPath);
//        $excel->setActiveSheetIndex(0);
//        $excelActive = $excel->getActiveSheet();
//
//        $start = 2;
//        $returnOrderList = [];
//        for ($i = $start; $i <= 240; $i++) {
//            $orderNumber = (string)$excelActive->getCell('A' . $i)->getValue();
//            if(!isset($returnOrderList[$orderNumber])) {
//                $returnOrderList[$orderNumber] = EcommerceReturn::find()->select('id')->andWhere(["order_number" => $orderNumber,"client_id" => 2])->scalar();
//            }
//        }
//
//        VarDumper::dump($returnOrderList,10,true);

//        $returnOrderId = 1128;
        $returnOrderId = 1517;
        $productsReadyForSendByAPI = $this->getProductsReadyForSendByAPI($returnOrderId);
        $productsReadyList = $this->prepareProductsReadyForSendByAPI($productsReadyForSendByAPI);

        $resultAPI = [];
        $returnAPIService = new ReturnAPIService();
        //$resultAPI = $returnAPIService->send($productsReadyList,$returnOrderId);

        VarDumper::dump($productsReadyList,10,true);
        VarDumper::dump($resultAPI,10,true);
//      VarDumper::dump($productsReadyList,10,true);

        return $this->render('test');
    }

    public function getProductsReadyForSendByAPI($returnId)
    {
        $order = EcommerceReturn::find()->andWhere([
            "id" => $returnId,
            "client_id" => 2,
        ])->one();

        $items = EcommerceStock::find()
            ->select('product_barcode, condition_type, count(product_barcode) as qtyProduct, client_product_sku')
            ->andWhere([
                'return_id' => $returnId,
                'client_id' => 2,
//                'api_status' => [StockAPIStatus::NO],
            ])
            ->groupBy('product_barcode, condition_type')
            ->asArray()
            ->all();

//        foreach ($items as &$item) {
//            $item['client_product_sku'] =  EcommerceReturnItem::find()
//                ->select('client_SkuId')
//                ->andWhere(['return_id' => $returnId,'product_barcode' => $item['product_barcode']])
//                ->scalar();
//        }

        $result = new stdClass();
        $result->order = $order;
        $result->items = $items;

        return $result;
    }


    public function prepareProductsReadyForSendByAPI($productsReadyForSendByAPI) {

        $B2CReturnShipmentItems = [];
        foreach($productsReadyForSendByAPI->items as $item) {
            $B2CReturnShipmentItems [] = [
                'SkuId'=> $item['client_product_sku'],
                'Quantity'=> $item['qtyProduct'],
                'ReturnReasonCode'=> ReturnReason::OTHER, //$productsReadyForSendByAPI->order->return_reason,
                'ReturnReasonProcessCode'=> ( new ReturnOutbound())->convertConditionTypeToAPIValue($item['condition_type']),
            ];
        }

        $result = [
            'ExternalShipmentId'=>$productsReadyForSendByAPI->order->order_number, //'OMC-8186455',
            'UniqueNumber'=>BarcodeManager::generateUuid(), // ? ,
            'CargoReturnCode'=> null, // ? ,
            'RefundUser'=> 'Kayrat', // ? ,
            'RefundUserId'=> '1', // ? ,
            'items'=>$B2CReturnShipmentItems
        ];

        return $result;
    }

    public function actionAddNine2()
    {
//        die('/ecommerce/defacto/ecom-other/add-nine2 DIE');

//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/01/return-ecom.xlsx';
        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/01/canceling-courier.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 2;
        $row =
            "Девятка ". " ; ".
            "Номер заказа ". " ; ".
            "Ждали". " ; ".
            "Приняли". " ; ".
            "Источник заказа". " ; ".
            "IsRefundable". " ; ".
            "RefundableMessage ". " ; ".
            "Дата создания". " ; ".
            "Дата изменения". " ; "
            . " ; ";

        file_put_contents('canceling-courier-23-06-2020.csv',$row."\n",FILE_APPEND);
        for ($i = $start; $i <= 122; $i++) {

            $nine = $excelActive->getCell('A' . $i)->getValue();

            $outboundId = EcommerceStock::find()->select('outbound_id')
                ->andWhere(['outbound_box' => $nine])
                ->andWhere('outbound_box != 0 AND outbound_box IS NOT NULL')
                ->scalar();

            $outboundOrder = EcommerceOutbound::find()->andWhere(['id' => $outboundId])->one();

            $orderNumber = '';
            if($outboundOrder) {
                $orderNumber = $outboundOrder->order_number;
            }

            $row =
                $nine. " ; ".
                $orderNumber. " ; "
                . " ; ";

            file_put_contents('canceling-courier-23-06-2020.csv',$row."\n",FILE_APPEND);
        }
    }


    public function actionAddReturnHistory()
    {
        die('/ecommerce/defacto/ecom-other/add-return-history DIE');

        $outboundRepository = new OutboundRepository();
        $returnRepository = new ReturnRepository();

//        $returnOrders = EcommerceReturn::find()->andWhere(['outbound_id'=>0])->all();
        $returnOrders = EcommerceReturn::find()->andWhere(['client_OrderSource'=>''])->all();

        foreach($returnOrders as $return) {

            $returnOrder = $returnRepository->getOrderByAny($return->order_number);
            $outbound = $outboundRepository->getOrderByAny($returnOrder->order_number);
            if ($outbound) {
//                $returnOrder->customer_name = $outbound->customer_name;
//                $returnOrder->city = $outbound->city;
//                $returnOrder->customer_address = $outbound->customer_address;
//                $returnOrder->client_ReferenceNumber = $outbound->client_ReferenceNumber;
//                $returnOrder->outbound_box = $returnRepository->getOutboundBox($outbound->id);
//                $returnOrder->outbound_id = $outbound->id;
                $returnOrder->client_OrderSource = $outbound->client_ShipmentSource;
                $returnOrder->save(false);
            }
        }

        return ' OK ';
    }

    public function actionAddReturnSkuIdHistory()
    {
//        die('/ecommerce/defacto/ecom-other/add-return-sku-id-history DIE');


        $returnItemsList = EcommerceReturnItem::find()->andWhere('client_SkuId = 0 OR client_SkuId = "" OR client_SkuId IS NULL')->all();

        foreach($returnItemsList as $return) {
             $stock = EcommerceStock::find()
                ->andWhere(['product_barcode'=>$return->product_barcode])
                ->one();

            if($stock) {
                $return->client_SkuId =  $stock->client_product_sku;
                $return->save(false);
            } else {
                echo $return->product_barcode."<br />";
            }

        }

        return ' OK ';
    }



    public function actionRemoveReturnWithErrors()
    {
        die('/ecommerce/defacto/ecom-other/remove-return-with-errors DIE');

        $returnOrders = EcommerceReturn::find()->andWhere(['client_IsRefundable'=>0,'accepted_qty'=>0])->all();

        foreach($returnOrders as $return) {

            EcommerceReturnItem::deleteAll(['return_id'=>$return->id]);
            $return->delete();
        }

        return ' OK ';
    }

    public function actionTestReturn()
    {
        //die('/ecommerce/defacto/ecom-other/test-return DIE');
//        $orderNumber = '25-6466-6852/1';
//        $orderNumber = '25-6466-6852';
//        $orderNumber = '14662952-16227597';
//        $orderNumber = '14662952';
        $orderNumber = '16227597'; // ok Shipment
        $returnAPIService = new ReturnAPIService();
        //$result = $returnAPIService->GetShipmentForReturn($orderNumber, 'Shipment');
//            $result = $returnAPIService->GetShipmentForReturn($orderNumber, 'CargoReturnCode');

         $rr = new ReturnRepository();
         $result = $rr->getOrderByAny($orderNumber);

        VarDumper::dump($result,10,true);
        return ' OK ';
    }

    public function actionShowLog($method_name = null,$find_order = null)
    {
//        die('/ecommerce/defacto/ecom-other/show-log DIE');

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $ecomApiLog = EcommerceApiOutboundLog::find()
                            ->andWhere(['method_name'=>$method_name])
                            ->andWhere('request_data LIKE "%' . trim($find_order) . '%" OR response_data LIKE "%' . trim($find_order) . '%"')
                            ->one();

        if(empty($ecomApiLog)) {

            $ecomApiLog = EcommerceApiInboundLog::find()
                ->andWhere(['method_name' => $method_name])
                ->andWhere('request_data LIKE "%' . trim($find_order) . '%" OR response_data LIKE "%' . trim($find_order) . '%"')
                ->one();
        }
        echo "<br />";
        echo "method_name: ";
        echo "<br />";
        VarDumper::dump($method_name,10,true);
        echo "<br />";
        echo "find_order: ";
        echo "<br />";
        VarDumper::dump($find_order,10,true);
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($ecomApiLog,10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";

        $responseData = '';
        $requestData = '';
        if($ecomApiLog) {

            $responseData = unserialize($ecomApiLog->response_data);
            $requestData = unserialize($ecomApiLog->request_data);
        }

        VarDumper::dump($requestData,10,true);
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        VarDumper::dump($responseData,10,true);
        die;
    }

    public function actionDiffInboundB2c() {
        //die('/ecommerce/defacto/ecom-other/diff-inbound-b2c DIE');
		$count = 0;
        $outboundId = 71859;
//        $outboundId = 71969; = // 104
        $b2bStockList = Stock::find()->select('box_barcode')->andWhere(['outbound_order_id'=>$outboundId])->column();
        $inboundId = 150;
        $b2cStockList = EcommerceInboundItem::find()->select('client_box_barcode')->andWhere(['inbound_id'=>$inboundId])->column();

        $obs = new OutboundBoxService();
        foreach($b2bStockList as $b2bStock) {
			$count++;
            $box = $obs->getClientBoxByBarcode($b2bStock);
            if(in_array($box,$b2cStockList)) {
                echo $box." ; ". $b2bStock." =" .$count."; OK; <br />";
            } else {
                echo $box." ; ". $b2bStock." =" .$count."; NO---; <br />";
            }
        }

        die("-DIE END-");
    }

	public function actionDiffInboundB2cNew() {
		//die('/ecommerce/defacto/ecom-other/diff-inbound-b2c-new DIE');
		$cross_dock_id = [17044];
		$crossDockBoxList = CrossDockItems::find()->distinct()->select('box_barcode')->andWhere(['cross_dock_id'=>$cross_dock_id])->column();
		$inboundId = [138,139,140,141,142,143];
		$b2cStockList = EcommerceStock::find()->distinct()->select('client_box_barcode')->andWhere(['inbound_id'=>$inboundId])->column();

		$result = array_diff($crossDockBoxList,$b2cStockList);
		VarDumper::dump($result);

//		foreach($crossDockBoxList as $b2bStock) {
//			if(in_array($box,$b2cStockList)) {
//				echo $box." ; ". $b2bStock."; OK; <br />";
//			} else {
//				echo $box." ; ". $b2bStock."; NO---; <br />";
//			}
//		}


		die("-DIE END-");
	}

    public function actionTestNewApi() {
        //die('/ecommerce/defacto/ecom-other/test-new-api DIE');
        $ecAPI = new ECommerceAPI2();
        $params = [];
//
        $ShortCode = '';// 'J9495AZAA';
        $SkuId = '';//'224051206';
        $LotOrSingleBarcode = '2300005992309';

        $params['request'] = [
            'BusinessUnitId' => 95540, //self::BUSINESS_UNIT_ID,
            'ProcessRequestedB2CDataType' => 'Full', //Changed
            'CountAllItems' => false,
            'PageIndex' => 0,
            'PageSize' => 0,
        ];

        if($ShortCode) {
            $params['request']['ShortCode'] = $ShortCode;
        }
        if($SkuId) {
            $params['request']['SkuId'] = $SkuId;
        }
        if($LotOrSingleBarcode) {
            $params['request']['LotOrSingleBarcode'] = $LotOrSingleBarcode;
        }

        $result = $ecAPI->GetMasterData($params);

        VarDumper::dump($result,10,true);
        die;
    }

    public function actionTestNewApi2() {
        //die('/ecommerce/defacto/ecom-other/test-new-api2 DIE');

//        $ecAPI = new ECommerceAPI2();
//        $aLcBarcode = '2430012626273';

//        $params = [];
//        $params['request'] = [
//            'BusinessUnitId' =>  95540,
//            'LcBarcode' =>$aLcBarcode,
//        ];
//        $result = $ecAPI->GetInBoundData($params);

        $aLcBarcode = '2430012626273';
        $ecIS_API = new InboundAPIService2();
        $result = $ecIS_API->get($aLcBarcode,1);
        VarDumper::dump($result,10,true);
        die;
    }


    public function actionCheckDiffStock()
    {
//        die('/ecommerce/defacto/ecom-other/check-diff-stock DIE');

        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-11-2020/diff-stock-05-11-2020.xlsx';
//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-11-2020/diff-stock-06-11-2020.xlsx';
//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-11-2020/diff-stock-06-11-2020-2.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 1;

        file_put_contents('diff-stock-06-11-2020-7.csv',"");
        for ($i = $start; $i <= 2443; $i++) {

            $client_product_sku = $excelActive->getCell('A' . $i)->getValue();

            $stockList = EcommerceStock::find()->select('place_address_barcode,box_address_barcode,product_barcode, count(product_barcode) as productQty')
                ->andWhere(['client_product_sku' => $client_product_sku])
//                ->andWhere('transfer_id < 1')
//                ->andWhere("(`system_message` NOT LIKE '%transfer%' OR `note_message1` NOT LIKE '%transfer%' OR `note_message2` NOT LIKE '%transfer%')")
                ->groupBy('product_barcode, box_address_barcode, place_address_barcode')
                ->asArray()
                ->all();


            if(empty($stockList)) {
                echo $client_product_sku."<br />";
                continue;
            }

            foreach($stockList as $stock) {

                $isInTransferExist = EcommerceStock::find()
                    ->andWhere(['client_product_sku' => $client_product_sku])
                    ->andWhere("(transfer_id > 1 OR `system_message` LIKE '%transfer%' OR `note_message1` LIKE '%transfer%' OR `note_message2` LIKE '%transfer%')")
                    ->exists();

                if(!empty($isInTransferExist)) {
                    continue;
                }

                $row =
                    $stock['place_address_barcode']. " ; ".
                    $stock['box_address_barcode']. " ; ".
                    $stock['product_barcode']. " ; ".
                    $stock['productQty']. " ; ".
                    $client_product_sku. " ; "
                    . " ; ";

                file_put_contents('diff-stock-06-11-2020-7.csv',$row."\n",FILE_APPEND);

            }
        }

        return $this->render('test'); //"OK end";
    }

    public function actionCheckDiffStockFull()
    {
        //TODO смотри в консоле
        die('/ecommerce/defacto/ecom-other/check-diff-stock-full DIE');

//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-11-2020/diff-stock-05-11-2020.xlsx';
//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-11-2020/diff-stock-06-11-2020.xlsx';
//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-11-2020/diff-stock-06-11-2020-2.xlsx';
        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/05-11-2020/stock-b2c-diff-07-11-2020.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 1;
        $fileName = 'diff-stock-full-07-11-2020-0.csv';
        $headerTitle =
            "clientProductSku". " ; ".
            "defactoStock". " ; ".
            "nomadexStock". " ; ".
            "diffStock". " ; ".
            "-". " ; ".
            "totalInboundExpectedQty". " ; ".
            "totalInboundAcceptedQty". " ; ".
            "qtyReturn". " ; ".
            "totalInStockForAllTime". " ; ".
            "qtyInTransferBeforeIntegration". " ; ".
            "qtyInTransferAfterIntegration". " ; ".
            "totalInTransfer". " ; ".
            "qtyOutboundGiveToCourier". " ; ".
            "qtyOutboundWithProblem". " ; ".
            "qtyAvailableNow". " ; ".
            " ; ";

        file_put_contents($fileName,$headerTitle."\n");
      // for ($i = $start; $i <= 10; $i++) {
        for ($i = $start; $i <= 2443; $i++) {

            $clientProductSku = $excelActive->getCell('A' . $i)->getValue();
            $defactoStock = $excelActive->getCell('B' . $i)->getValue();
            $nomadexStock = $excelActive->getCell('C' . $i)->getValue();
            $diffStock = $excelActive->getCell('D' . $i)->getValue();


            $totalInboundQty = EcommerceInboundItem::find()
                ->select('SUM(`product_expected_qty`) as sumExpectedQty, SUM(`product_accepted_qty`) as sumAcceptedQty')
                ->andWhere(['client_product_sku' => $clientProductSku])
                ->asArray()
                ->one();

            $qtyInTransferBeforeIntegration = EcommerceStock::find()
                ->andWhere(['client_product_sku' => $clientProductSku])
                ->andWhere("transfer_id < 1 AND (`system_message` LIKE '%transfer%' OR `note_message1` LIKE '%transfer%' OR `note_message2` LIKE '%transfer%')")
                ->count();

            $qtyInTransferAfterIntegration= EcommerceStock::find()
                ->andWhere(['client_product_sku' => $clientProductSku])
                ->andWhere("transfer_id > 1")
                ->count();

            $qtyAvailableNow = EcommerceStock::find()
                ->andWhere([
                    'client_product_sku' => $clientProductSku,
                    'status_availability' => StockAvailability::YES
                ])
                ->count();

            $qtyOutboundGiveToCourier = EcommerceStock::find()
                ->andWhere([
                    'client_product_sku' => $clientProductSku,
                    'status_availability' => StockAvailability::RESERVED,
                    'status_outbound' => StockOutboundStatus::DONE
                ])
                ->andWhere("outbound_id > 0")
                ->andWhere("scan_out_employee_id > 0")
                ->count();

            $qtyReturn = EcommerceStock::find()
                ->andWhere([
                    'client_product_sku' => $clientProductSku,
                ])
                ->andWhere("return_id > 1")
                ->count();

           $totalInStockForAllTime = EcommerceStock::find()
                ->andWhere([
                    'client_product_sku' => $clientProductSku,
                ])
                ->count();

            $qtyOutboundWithProblem = EcommerceStock::find()
                ->andWhere([
                    'client_product_sku' => $clientProductSku,
                ])
                ->andWhere("outbound_id > 0")
                ->andWhere("status_outbound != :status_outbound",[":status_outbound"=>StockOutboundStatus::DONE])
                ->count();

                $totalInTransfer = $qtyInTransferBeforeIntegration+$qtyInTransferAfterIntegration;

                $row =
                    $clientProductSku. " ; ".
                    $defactoStock. " ; ".
                    $nomadexStock. " ; ".
                    $diffStock. " ; ".
                    "-". " ; ".
                    $totalInboundQty['sumExpectedQty']. " ; ".
                    $totalInboundQty['sumAcceptedQty']. " ; ".
                    $qtyReturn. " ; ".
                    $totalInStockForAllTime. " ; ".
                    $qtyInTransferBeforeIntegration. " ; ".
                    $qtyInTransferAfterIntegration. " ; ".
                    $totalInTransfer. " ; ".
                    $qtyOutboundGiveToCourier. " ; ".
                    $qtyOutboundWithProblem. " ; ".
                    $qtyAvailableNow. " ; ".
                    " ; ";

                file_put_contents($fileName,$row."\n",FILE_APPEND);
        }

        return $this->render('test'); //"OK end";
    }


    public function actionCheckDiffCancel()
    {
        //TODO смотри в консоле
        die('/ecommerce/defacto/ecom-other/check-diff-cancel DIE');

        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/07-11-2020/outbound-orders-07-11-2020_08-58-49.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 1;
        $fileName = 'check-diff-cancel-07-11-2020-0.csv';
        $headerTitle =
            "Место" . " ; " .
            "Короб" . " ; " .
            "Шк товара" . " ; " .
            "Кол-во" . " ; " .
            "SkuId" . " ; " .
            "Трансфер до интг-ции" . " ; " .
            "Трансфер после интг-ции" . " ; " .
            "Трансфер да/нет " . " ; " .
            " ; ";

        file_put_contents($fileName, $headerTitle . "\n");
        $lisFromFile = [];
        for ($i = $start; $i <= 2191; $i++) {
            $outboundOrderNumber = $excelActive->getCell('A' . $i)->getValue();
            $lisFromFile[$outboundOrderNumber] = $outboundOrderNumber;
        }

        foreach($lisFromFile as $outboundOrderNumber) {

            $outbound = EcommerceOutbound::find()
                ->andWhere(['order_number' => $outboundOrderNumber])
                ->one();

            if(empty($outbound)) {
                continue;
            }

            $allStock = EcommerceStock::find()->andWhere([
                'outbound_id' => $outbound->id,
            ])
                ->all();

            if(empty($allStock)) {
                continue;
            }

            foreach($allStock as $stock) {

                $qtyInTransferBeforeIntegration = EcommerceStock::find()
                    ->andWhere(['id' => $stock->id])
                    ->andWhere("transfer_id < 1 AND (`system_message` LIKE '%transfer%' OR `note_message1` LIKE '%transfer%' OR `note_message2` LIKE '%transfer%')")
                    ->count();

                $qtyInTransferAfterIntegration = EcommerceStock::find()
                    ->andWhere(['id' => $stock->id])
                    ->andWhere("transfer_id >= 1")
                    ->count();

                $isTransfer = "NO";
                if($qtyInTransferBeforeIntegration > 0 || $qtyInTransferAfterIntegration > 0) {
                    $isTransfer = "YES";
                }

                $qtyInTransferBeforeIntegration = empty($qtyInTransferBeforeIntegration)  ? 'NO' : 'YES';
                $qtyInTransferAfterIntegration = empty($qtyInTransferAfterIntegration)  ? 'NO' : 'YES';

                $row =
                    $stock->place_address_barcode . " ; " .
                    $stock->box_address_barcode . " ; " .
                    $stock->product_barcode . " ; " .
                    1 . " ; " .
                    $stock->client_product_sku . " ; " .
                    $outboundOrderNumber . " ; " .
                    $qtyInTransferBeforeIntegration. " ; ".
                    $qtyInTransferAfterIntegration. " ; ".
                    $isTransfer. " ; ".
                    " ; ";

                file_put_contents($fileName,$row."\n",FILE_APPEND);
            }
        }

        return $this->render('test');
    }

    public function actionCheckDiffStockResult()
    {
//        die('/ecommerce/defacto/ecom-other/check-diff-stock-result DIE');

        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/09-11-2020/20WNand20AUCheck.xlsx';
        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/defacto/09-11-2020/KZProductCheck.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 2;
        $fileName = 'KZProductCheck-2-'.date('Y-m-d-His').'.csv';
        file_put_contents($fileName,"");

        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";
        echo "<br />";

        $result = [];
        for ($i = $start; $i <= 3159; $i++) {

            $placeAddress = $excelActive->getCell('A' . $i)->getValue(); // Полка
            $boxBarcode = $excelActive->getCell('B' . $i)->getValue(); // Коробка
            $productBarcode = $excelActive->getCell('C' . $i)->getValue(); // Шк товара
            $productSkuId = $excelActive->getCell('D' . $i)->getValue(); // Кол-во
            $productQty = $excelActive->getCell('E' . $i)->getValue(); // Sku id
            $factProductQty = $excelActive->getCell('F' . $i)->getValue(); // Физическое кол-во

            $placeAddress = trim($placeAddress); // Полка
            $boxBarcode = trim($boxBarcode); // Коробка
            $productBarcode = trim($productBarcode); // Шк товара
            $productQty = trim($productQty); // Кол-во
            $productSkuId = trim($productSkuId); // Sku id
            $factProductQty = trim($factProductQty); // Физическое кол-во

            $stockList = EcommerceStock::find()->select('place_address_barcode,box_address_barcode,product_barcode, count(product_barcode) as productQty') // , status_availability
                ->andWhere(['client_product_sku' => $productSkuId])
                ->andWhere(['place_address_barcode' => $placeAddress])
                ->andWhere(['box_address_barcode' => $boxBarcode])
                ->andWhere(['product_barcode' => $productBarcode])
                ->groupBy('product_barcode, box_address_barcode, place_address_barcode') // , status_availability
                ->asArray()
                ->all();

            if(empty($stockList)) {
                echo $productSkuId."<br />";
                continue;
            }

            foreach($stockList as $stock) {

                $isInTransferExist = EcommerceStock::find()
                    ->andWhere(['client_product_sku' => $productSkuId])
                    ->andWhere("(transfer_id > 1 OR `system_message` LIKE '%transfer%' OR `note_message1` LIKE '%transfer%' OR `note_message2` LIKE '%transfer%')")
                    ->exists();

                if(!empty($isInTransferExist)) {
                    continue;
                }

                $countStockAvailable = EcommerceStock::find()
                    ->andWhere(['client_product_sku' => $productSkuId])
                    ->andWhere(['place_address_barcode' => $placeAddress])
                    ->andWhere(['box_address_barcode' => $boxBarcode])
                    ->andWhere(['product_barcode' => $productBarcode])
                    ->andWhere(['status_availability' => 2])
                    ->count();

                $row =
                    $placeAddress.";".
                    $boxBarcode.";".
                    $productBarcode.";".
                    $productQty.";".
                    $productSkuId.";".
                    $factProductQty.";".
                    $countStockAvailable.";".
                    ($factProductQty-$countStockAvailable).";".
                    " ; ";

                $result [$productSkuId][] = $row;
            }
        }

        foreach($result as $item=>$rows) {
            foreach ($rows as $row) {
                file_put_contents($fileName, $row . "\n", FILE_APPEND);
            }
//            file_put_contents($fileName,"--;"."\n", FILE_APPEND);
        }

        return $this->render('test'); //"OK end";
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
                $dtoForCreateStock->productName = "PlusInventory08122020";
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

    public function actionTestApi() {
//        die("/ecommerce/defacto/ecom-other/test-api die begin");

        $barcodeService = new BarcodeService();

        $aProductBarcode = "8681816887676";
        $aProductBarcode = "8682863757196";
//       $skuId =  $this->getSkuIdByProductBarcode($aProductBarcode);
//        VarDumper::dump($skuId,10,true);
//       $r =  $this->getTotalProductsByProductBarcode($aProductBarcode);
//       $r =  $this->getSkuIdByProductBarcodeInItem($aProductBarcode);
        $r =  $barcodeService->getTotalProductsByProductBarcode($aProductBarcode);
        VarDumper::dump($r,10,true);
        die;
    }

    public function actionAddAllProducts()
    {
//        die("/ecommerce/defacto/ecom-other/add-all-products die begin");

        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/30-12-2020/transfer-lost-30-12-2020.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 2;
        $productInBoxList = [];
        for ($i = $start; $i <= 34; $i++) {
            $client_product_sku = $excelActive->getCell('A' . $i)->getValue();
            $product_barcode = $excelActive->getCell('B' . $i)->getValue();
            $transfer_id = $excelActive->getCell('C' . $i)->getValue();
            $transfer_item_id = $excelActive->getCell('D' . $i)->getValue();
            $status_transfer = $excelActive->getCell('E' . $i)->getValue();
            $box_address_barcode = $excelActive->getCell('F' . $i)->getValue();
            $place_address_barcode = $excelActive->getCell('G' . $i)->getValue();

            $client_product_sku = trim($client_product_sku);
            $product_barcode = trim($product_barcode);
            $transfer_id = trim($transfer_id);
            $transfer_item_id = trim($transfer_item_id);
            $status_transfer = trim($status_transfer);
            $box_address_barcode = trim($box_address_barcode);
            $place_address_barcode = trim($place_address_barcode);

            $productInBoxList [] = [
                'client_product_sku' => $client_product_sku,
                'product_barcode' => $product_barcode,
                'transfer_id' => $transfer_id,
                'transfer_item_id' => $transfer_item_id,
                'status_transfer' => $status_transfer,
                'box_address_barcode' => $box_address_barcode,
                'place_address_barcode' => $place_address_barcode,
            ];
        }

        $barcodeService = new BarcodeService();

        $fileName = 'add-all-product-'.date('Y-m-d-His').'.csv';

        $header = 'client_product_sku'.";".
            'product_barcode'.";".
            'transfer_id'.";".
            'transfer_item_id'.";".
            'status_transfer'.";".
            'box_address_barcode'.";".
            'place_address_barcode'.";"."\n";

        file_put_contents($fileName,$header);
        //return 'ok ProductCount = ';
        foreach ($productInBoxList as $item) {

           $prodList =  array_map(function($item) {
                return "'".$item."'";
            },$barcodeService->getTotalProductsByProductBarcode($item['product_barcode']));

//            VarDumper::dump($prodList,10,true);
//            die;

            $row = $item['client_product_sku'].";".
//                $item['product_barcode'].";".
                implode(",",$prodList).";".
                $item['transfer_id'].";".
                $item['transfer_item_id'].";".
                $item['status_transfer'].";".
                $item['box_address_barcode'].";".
                $item['place_address_barcode'].";";


            file_put_contents($fileName, $row. "\n", FILE_APPEND);
        }

//        VarDumper::dump($products, 10, true);
//        die;
        return 'ok ProductCount = ';
    }

    public function actionResendCancelShipment() {
        //
         die('/ecommerce/defacto/ecom-other/resend-cancel-shipment');

        $outboundIdList = [
//'OMC-34187369',
//'OMC-35048300',
//'OMC-36180034',
//'OMC-40959448',
//'OMC-41012059',
'OMC-35000002',
        ];

        $result = [];
        foreach($outboundIdList as $outboundNumber) {
			
            $service = new OutboundService();
            $orderInfo = $service->getOrderInfoByOrderNumber($outboundNumber);
            $result[$outboundNumber] = $orderInfo->order->id;
			//$result[$outboundNumber] = $service->CancelShipment($orderInfo->order->id,OutboundCancelStatus::CUSTOMER_REQUESTS_CANCELLATION);
		}
		
		VarDumper::dump($result,10,true);
        return 'OK';
		
        return 'ok CancelShipment '.$ourOutboundId;
    }

	public function actionResendPartialCancelShipment() {
		//
//        die('/ecommerce/defacto/ecom-other/resend-partial-cancel-shipment');

		$outboundIdList = [
			'OMC-18860993',
			'OMC-17596312',
			'OMC-18019040',
			'OMC-39783184',
		];

		$result = [];
		foreach($outboundIdList as $outboundNumber) {

			$service = new OutboundService();
			$orderInfo = $service->getOrderInfoByOrderNumber($outboundNumber);
			$result[$outboundNumber] = $orderInfo->order->id;
			//$result[$outboundNumber] = $service->partialCancelShipment($orderInfo->order->id);
		}

		VarDumper::dump($result,10,true);
		return 'OK';

		return 'ok CancelShipment '.$ourOutboundId;
	}

    public function actionAddProductBarcode()
    {
        //die("/ecommerce/defacto/ecom-other/add-product-barcode die begin");

        $rootPath = Yii::getAlias('@stockDepartment') . '/web/add-product/b2c/15-02-2021/add-to-stock-15-02-2021.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 1;
        $productInBoxList = [];
        for ($i = $start; $i <= 44; $i++) {
            $product_barcode = $excelActive->getCell('A' . $i)->getValue();
            $product_barcode = trim($product_barcode);

            $box_barcode = $excelActive->getCell('B' . $i)->getValue();
            $box_barcode = trim($box_barcode);

            $productInBoxList [] = [
                'product_barcode' => $product_barcode,
                'box_barcode' => $box_barcode,
            ];
        }

        foreach ($productInBoxList as $item) {
           $stock =  EcommerceStock::find()
                ->andWhere(['product_barcode'=>$item['product_barcode']])
                ->andWhere('client_product_sku != ""')
                ->one();

            $client_product_sku = null;
            $client_id = 2;
            if($stock) {
                $client_product_sku = $stock->client_product_sku;
                $client_id = $stock->client_id;
            } else {
                echo $item['product_barcode']."<br />";
            }

            $newStockItem = new EcommerceStock();
            $newStockItem->client_id = $client_id;
            $newStockItem->product_barcode = $item['product_barcode'];
            $newStockItem->client_product_sku = $client_product_sku;
//            $newStockItem->box_address_barcode = '10100000001';
            $newStockItem->box_address_barcode = $item['box_barcode'];
            $newStockItem->place_address_barcode = '4-9-09-1';
            $newStockItem->system_message = 'addManual15022021';
            $newStockItem->status_availability = 2;
            $newStockItem->api_status = 1;
            $newStockItem->condition_type = 1;
            //$newStockItem->save(false);
        }

//        VarDumper::dump($products, 10, true);
        die;
        return 'ok ProductCount = ';
    } 

	public function actionAddProductBarcodeManual()
    {
        //die("/ecommerce/defacto/ecom-other/add-product-barcode-manual die begin");

 
		 
		$productInBoxList [] =	['product_barcode' =>' 8682863953789', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>' 8682446589428', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>' 8682446343167', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>' 8698436529428', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' => '8682446281506', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>  '8682283953031', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>  '8682446106519', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8682283188655', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8682283988392', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>  '8682283295988', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8682446949178', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8682863954977', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8682446675442', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8682283536579', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8698335126773', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	[	'product_barcode' =>   '8682863682078', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
		$productInBoxList [] =	['product_barcode' =>   '8682283653771', 'box_barcode' => '100000020037', 'place_address_barcode' => '4-12-10-1'];
        

        //VarDumper::dump($productInBoxList, 10, true);


        foreach ($productInBoxList as $item) {
			
           $stock =  EcommerceStock::find()
                ->andWhere(['product_barcode'=>trim($item['product_barcode'])])
                ->andWhere('client_product_sku != ""')
                ->one();

            $client_product_sku = null;
            $client_id = 2;
            if($stock) {
                $client_product_sku = $stock->client_product_sku;
                $client_id = $stock->client_id;
            } else {
                echo trim($item['product_barcode'])."<br />";
            }

            $newStockItem = new EcommerceStock();
            $newStockItem->client_id = $client_id;
            $newStockItem->product_barcode = trim($item['product_barcode']);
            $newStockItem->client_product_sku = $client_product_sku;
//            $newStockItem->box_address_barcode = '10100000001';
            $newStockItem->box_address_barcode = trim($item['box_barcode']);
            $newStockItem->place_address_barcode = trim($item['place_address_barcode']);
            $newStockItem->system_message = 'addManualReturn24052021';
            $newStockItem->status_availability = 2;
            $newStockItem->api_status = 1;
            $newStockItem->condition_type = 1;
            //$newStockItem->save(false);
			
			//echo trim($item['product_barcode']).' '.trim($item['box_barcode']).' '.trim($item['place_address_barcode'])."<br />";
			
			//VarDumper::dump($newStockItem, 10, true);
			//die;
			
        }
		
		

//        VarDumper::dump($products, 10, true);
        die;
        return 'ok ProductCount = ';
    }

    public function actionAddFullAddress() {
        //die("/ecommerce/defacto/ecom-other/add-full-address die begin");

        $skuIds = [
            224078571
        ];

       $stockList = EcommerceStock::find()->select('box_address_barcode, place_address_barcode,product_barcode')
                    ->andWhere(['client_product_sku'=>$skuIds])
                    ->groupBy('box_address_barcode, place_address_barcode,product_barcode')
                    ->asArray()
                    ->all();

        $products = ArrayHelper::getColumn($stockList,'product_barcode');
        $products = array_unique($products);
        $productStr = implode(";",$products);

        $uniqueAddressPlace = [];
        foreach($stockList as $stock) {
//            VarDumper::dump($stock,10,true);

           $changeAddressPlaceList = EcommerceChangeAddressPlace::find()
                ->select('from_barcode, to_barcode')
                ->andWhere('from_barcode = :from_barcode OR to_barcode = :to_barcode ',[':from_barcode'=>$stock['box_address_barcode'],':to_barcode'=>$stock['place_address_barcode']])
                ->asArray()
                ->all();

            foreach($changeAddressPlaceList as $addressPlace) {
                $key = $addressPlace['from_barcode'].'_'.$addressPlace['to_barcode'];

//                $uniqueAddressPlace[$key] = $addressPlace['from_barcode']."; ".$addressPlace['to_barcode']."; ".$stock['product_barcode'];
                $uniqueAddressPlace[$key] = $addressPlace['to_barcode']."; ".$addressPlace['from_barcode']."; ".$productStr;
            }
        }

        foreach($uniqueAddressPlace as $addressPlace) {
                echo $addressPlace."<br />";
        }

//        VarDumper::dump($uniqueAddressPlace,10,true);

        die;
        return 'ok';
    }


    public function actionCheckInboundOurWithDefacto()
    {
        //die("/ecommerce/defacto/ecom-other/check-inbound-our-with-defacto die begin");

        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/06-04-2021/KZ-21-067-RW-2-DEFACTO-REPORT.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 2;
        $productInBoxList = [];
        for ($i = $start; $i <= 597; $i++) {

            $LCBarcode = $excelActive->getCell('B' . $i)->getValue();
            $LCBarcode = trim($LCBarcode);

            $productSkuID = $excelActive->getCell('C' . $i)->getValue();
            $productSkuID = trim($productSkuID);

            $productBarcode = $excelActive->getCell('D' . $i)->getValue();
            $productBarcode = trim($productBarcode);

            $productQty = $excelActive->getCell('E' . $i)->getValue();
            $productQty = trim($productQty);

            $productFbQty = $excelActive->getCell('G' . $i)->getValue();
            $productFbQty = trim($productFbQty);

            $productInBoxList [] = [
                'productBarcode' => $productBarcode,
                'LCBarcode' => $LCBarcode,
                'productSkuID' => $productSkuID,
                'productQty' => $productQty,
                'productFbQty' => $productFbQty,
            ];
        }

//        VarDumper::dump($productInBoxList, 10, true);
        $totalProducts = [];
        foreach ($productInBoxList as $item) {
            $totalProducts[$item['productBarcode']][] = $item;
//            $stock =  EcommerceStock::find()
//                ->andWhere(['product_barcode'=>$item['productBarcode']])
//                ->andWhere('client_product_sku != ""')
//                ->one();
        }

        foreach ($totalProducts as $k1=>$items) {
            foreach ($items as $k2=>$item) {
                $realItem = EcommerceInboundItem::find()
                    ->andWhere(['inbound_id' => 86])
                    ->andWhere(['product_barcode' => $item['productBarcode']])
                    ->andWhere(['product_expected_qty' => $item['productQty']])
                    ->andWhere(['product_accepted_qty' => $item['productFbQty']])
                    ->one();

                if(!$realItem) {
                    echo $item['productBarcode']."<br />";
                }
            }
        }




        VarDumper::dump($totalProducts, 10, true);

//        VarDumper::dump($products, 10, true);
        die;
        return 'ok ProductCount = ';
    }

    public function actionFromGuys()
    {
        //die("/ecommerce/defacto/ecom-other/from-guys die begin");

//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/24-06-2021/74.xlsx';
//        $rootPath = \Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/25-06-2021/76.xlsx';
//        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/25-06-2021/25.xlsx';
//        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/28-06-2021/ecommerce_stock-28-06-2021-x.xlsx';
//        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/29-06-2021/ecommerce_stock_party_5.xlsx';
//        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/30-06-2021/ecommerce_stock_find_30-06-2021.xlsx';
        $rootPath = Yii::getAlias('@stockDepartment') . '/web/tmp-file/e-commerce/30-07-2021/ecommerce_stock_find_30-07-2021_002.xlsx';
        $excel = PHPExcel_IOFactory::load($rootPath);
        $excel->setActiveSheetIndex(0);
        $excelActive = $excel->getActiveSheet();

        $start = 2;
        $productInBoxList = [];
        for ($i = $start; $i <= 273; $i++) {

            $place_address_barcode = $excelActive->getCell('A' . $i)->getValue();
            $place_address_barcode = trim($place_address_barcode);

            $box_address_barcode = $excelActive->getCell('B' . $i)->getValue();
            $box_address_barcode = trim($box_address_barcode);

            $client_product_sku = $excelActive->getCell('C' . $i)->getValue();
            $client_product_sku = trim($client_product_sku);

            $product_barcode = $excelActive->getCell('D' . $i)->getValue();
            $product_barcode = trim($product_barcode);

            $physical_qty = $excelActive->getCell('E' . $i)->getValue();
            $physical_qty = trim($physical_qty);

            $productInBoxList [] = [
                'place_address_barcode' => $place_address_barcode,
                'box_address_barcode' => $box_address_barcode,
                'client_product_sku' => $client_product_sku,
                'product_barcode' => $product_barcode,
                'physical_qty' => $physical_qty,
            ];
        }

		$headers = [
			'place_address_barcode',
			'box_address_barcode',
			'client_product_sku',
			'product_barcode',
			'physical_qty',
			'available_qty',
			'outbound_qty',
			'block_qty',
			'total_qty',
		];
		$fileName = 'from-guys-'.date('Y-m-d').'.csv';
	    file_put_contents($fileName,implode($headers,";")."\n");

//        VarDumper::dump($productInBoxList, 10, true);
        $result = [];
        foreach ($productInBoxList as $i=>$item) {

			$result [$i] = [
				'place_address_barcode' => $item['place_address_barcode'],
				'box_address_barcode' => $item['box_address_barcode'],
				'client_product_sku' => $item['client_product_sku'],
				'product_barcode' => $item['product_barcode'],
				'physical_qty' => $item['physical_qty'],
				'available_qty' => $this->_availableQty($item),
				'outbound_qty' => $this->_outboundAvailableQty($item),
				'block_qty' => $this->_blockAvailableQty($item),
//				'total_qty' => $result [$i]['outbound_qty']+$result [$i]['block_qty'],
			];

			$result [$i]['total_qty'] = $result [$i]['outbound_qty']+$result [$i]['block_qty'];

			file_put_contents($fileName,implode($result [$i],";")."\n",FILE_APPEND);
        }

        VarDumper::dump($result, 10, true);

//        VarDumper::dump($products, 10, true);
        die;
        return 'ok ProductCount = ';
    }

    private function _availableQty($item) {
		return  EcommerceStock::find()
											 ->andWhere(['place_address_barcode' => $item['place_address_barcode']])
											 ->andWhere(['box_address_barcode' =>  $item['box_address_barcode']])
											 ->andWhere(['client_product_sku' => $item['client_product_sku']])
											 ->andWhere(['product_barcode' =>  $item['product_barcode']])
											 ->andWhere(['status_availability' => 2])
											 ->count();
	}

	private function _outboundAvailableQty($item) {
		return EcommerceStock::find()
											 ->andWhere(['place_address_barcode' => $item['place_address_barcode']])
											 ->andWhere(['box_address_barcode' =>  $item['box_address_barcode']])
											 ->andWhere(['client_product_sku' => $item['client_product_sku']])
											 ->andWhere(['product_barcode' =>  $item['product_barcode']])
											 ->andWhere(['status_availability' => 3])
											 ->count();
	}

	private function _blockAvailableQty($item) {
		return EcommerceStock::find()
											 ->andWhere(['place_address_barcode' => $item['place_address_barcode']])
											 ->andWhere(['box_address_barcode' =>  $item['box_address_barcode']])
											 ->andWhere(['client_product_sku' => $item['client_product_sku']])
											 ->andWhere(['product_barcode' =>  $item['product_barcode']])
											 ->andWhere(['status_availability' => 4])
											 ->count();
	}
	/*
	 *  Изменение крол-ва товаров на складе с  стокаджасментом . турки прислали шк и количество я свел это с отчетом что искали ребята
	 * */
	public function actionStockAdjustmentFromFile()
	{
//		 die('/ecommerce/defacto/ecom-other/stock-adjustment DIE');
		$path = '/web/tmp-file/e-commerce/stock-adjustment/26-11-2021/';

		$rootPath = Yii::getAlias('@stockDepartment') . $path.'DcStockAdjustmentTemplate.xlsx';
		$excel = PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 2;
		$productList = [];
		for ($i = $start; $i <= 85; $i++) {

			$productBarcode = $excelActive->getCell('A' . $i)->getValue();
			$productBarcode = trim($productBarcode);

			$productQty = $excelActive->getCell('B' . $i)->getValue();
			$productQty = trim($productQty);

			$plusMinus = $excelActive->getCell('C' . $i)->getValue();
			$plusMinus = trim($plusMinus);

			if($plusMinus == '-') {
				$productQty *= -1;
			}

			$productList [$productBarcode] = [
				'product_barcode' => $productBarcode,
				'physical_qty' => isset($productList[$productBarcode]) ? $productList[$productBarcode]['physical_qty'] + $productQty : $productQty,
				'operator' => $plusMinus,
			];
		}

		foreach ($productList as $key=>$productListItem) {
			if($productListItem['physical_qty'] < 0) {
				$productList[$key]['physical_qty']  = $productListItem['physical_qty'] * -1;
				$productList[$key]['operator'] = '-';
			}

			if($productListItem['physical_qty'] > 0) {
				$productList[$key]['operator'] = '+';
			}

			if($productListItem['physical_qty'] == 0) {
				unset($productList[$key]);
			}
		}

		foreach ($productList as $productListItem) {
			$stockAdjustmentForm = new StockAdjustmentForm();
			$stockAdjustmentForm->productBarcode = $productListItem['product_barcode'];
			$stockAdjustmentForm->productQuantity = $productListItem['physical_qty'];
			$stockAdjustmentForm->productOperator = $productListItem['operator'];
			$stockAdjustmentForm->reason = 'Blocked30112021Baris';
			// $stockAdjustmentForm->change();
		}


		VarDumper::dump($productList,10,true);
		die("OK");

		$rootPath = Yii::getAlias('@stockDepartment') . $path.'check-on-stock-19-11-2021_10-00-33_result.xlsx';
		$excel = PHPExcel_IOFactory::load($rootPath);
		$excel->setActiveSheetIndex(0);
		$excelActive = $excel->getActiveSheet();

		$start = 2;
		$checkOnStockList = [];
		for ($i = $start; $i <= 2155; $i++) {
			$place_address_barcode= $excelActive->getCell('B' . $i)->getValue();
			$place_address_barcode = trim($place_address_barcode);

			$box_address_barcode = $excelActive->getCell('C' . $i)->getValue();
			$box_address_barcode = trim($box_address_barcode);

			$product_barcode = $excelActive->getCell('E' . $i)->getValue();
			$product_barcode = trim($product_barcode);

			$physical_qty = $excelActive->getCell('F' . $i)->getValue();
			$physical_qty = trim($physical_qty);

			$available_qty = $excelActive->getCell('G' . $i)->getValue();
			$available_qty = trim($available_qty);

			$block_qty = $excelActive->getCell('I' . $i)->getValue();
			$block_qty = trim($block_qty);

			$checkOnStockList [] = [
				'place_address_barcode' => $place_address_barcode,
				'box_address_barcode' => $box_address_barcode,
				'product_barcode' => $product_barcode,
				'physical_qty' => $physical_qty,
				'available_qty' => $available_qty,
				'block_qty' => $block_qty,
				'used' => 'NO',
			];
		}

		$result = [];
		$resultErrors = [];
		foreach ($productList as $key_i=>$productListItem) {

//			if($productListItem['operator'] == '-') {
//			if($productListItem['operator'] == '+') {
//				$stockAdjustmentForm = new StockAdjustmentForm();
//				$stockAdjustmentForm->productBarcode = $productListItem['product_barcode'];
//				$stockAdjustmentForm->productQuantity = $productListItem['physical_qty'];
//				$stockAdjustmentForm->productOperator = $productListItem['operator'];
//				$stockAdjustmentForm->reason = 'Blocked26112021Baris';
//				$stockAdjustmentForm->change();
//			}

//			$key = $key_i;//.'_'.$productListItem['product_barcode'].'_'.$productListItem['physical_qty'].'_'.$productListItem['operator']
			foreach ($checkOnStockList as $key=>$checkOnStockListItem) {
				$condition = $this->_Operator($checkOnStockListItem,$productListItem);

				if(!$condition) {
					if(!isset($resultErrors[$key_i])) {
						$resultErrors[$key_i] = $productListItem['product_barcode'];
					}

					continue;
				}
					$availableStatus = $productListItem['operator'] == '-' ?
						StockAvailability::YES :
						[
							StockAvailability::NO,
							StockAvailability::NOT_SET,
							StockAvailability::RESERVED,
							StockAvailability::BLOCKED,
						];

					$stockList = EcommerceStock::find()->andWhere([
						'client_id' => 2 ,
						'status_availability' =>   $availableStatus ,
						'place_address_barcode' => $checkOnStockListItem['place_address_barcode'] ,
						'box_address_barcode' => $checkOnStockListItem['box_address_barcode'] ,
						'product_barcode' => $checkOnStockListItem['product_barcode'] ,
					])->limit($productListItem['physical_qty'])->all();

					foreach ($stockList as $stockListItem) {
//						$stockListItem->status_availability = StockAvailability::BLOCKED;
//						$stockListItem->system_message = 'Blocked26112021Baris '.$productListItem['operator'];
//						$stockListItem->save(false);
					}

					$checkOnStockList[$key]['used'] = 'YES';
					$result[] = [
						'place_address_barcode' => $checkOnStockListItem['place_address_barcode'] ,
						'box_address_barcode' => $checkOnStockListItem['box_address_barcode'] ,
						'product_barcode' => $checkOnStockListItem['product_barcode'] ,
						'physical_qty' => $checkOnStockListItem['physical_qty'] ,
						'available_qty' => $checkOnStockListItem['available_qty'] ,
						'block_qty' => $checkOnStockListItem['block_qty'] ,

						'defacto_qty' => $productListItem['physical_qty'] ,
						'defacto_product_barcode' => $productListItem['product_barcode'] ,
						'defacto_operator' => $productListItem['operator'] ,
					];

				$resultErrors[$key_i] = ".";//$productListItem;
			}
		}

		$fileName = 'actionStockAdjustment.xlsx';
		$header = [
			'place_address_barcode' => $checkOnStockListItem['place_address_barcode'] ,
			'box_address_barcode' => $checkOnStockListItem['box_address_barcode'] ,
			'product_barcode' => $checkOnStockListItem['product_barcode'] ,
			'physical_qty' => $checkOnStockListItem['physical_qty'] ,
			'available_qty' => $checkOnStockListItem['available_qty'] ,
			'block_qty' => $checkOnStockListItem['block_qty'] ,

			'defacto_qty' => $productListItem['physical_qty'] ,
			'defacto_product_barcode' => $productListItem['product_barcode'] ,
			'defacto_operator' => $productListItem['operator'] ,
		];

		file_put_contents($fileName,implode(";",array_keys($header))."\n");
		foreach ($result as $item) {
			file_put_contents($fileName,implode(";",$item)."\n",FILE_APPEND);
		}


		return Yii::$app->response->sendFile(Yii::getAlias('@stockDepartment').'/web/'.$fileName, $fileName);

//		VarDumper::dump($resultErrors,10, true);
//		VarDumper::dump($result,10, true);
//		VarDumper::dump($productList,10, true);
		die;
	}

	private function _Operator ($checkOnStockListItem,$productListItem) {

		$condition = false;
    	if($productListItem['operator'] == '-') {
//			$condition = $checkOnStockListItem['product_barcode'] == $productListItem['product_barcode']
//				&&  (($checkOnStockListItem['available_qty'] == $productListItem['physical_qty'] && $checkOnStockListItem['physical_qty'] == '0')
//				 || $checkOnStockListItem['available_qty'] - $checkOnStockListItem['physical_qty'] == $productListItem['physical_qty'] )
//				&& $checkOnStockListItem['used'] == 'NO';

		} elseif ($productListItem['operator'] == '+') {
			$condition = $checkOnStockListItem['product_barcode'] == $productListItem['product_barcode']
				&& $checkOnStockListItem['physical_qty'] == $productListItem['physical_qty']
				&& $checkOnStockListItem['available_qty'] == '0'
				&& $checkOnStockListItem['used'] == 'NO';
		}

    	return $condition;
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

	public  function actionTree() {




	}

	private function tree($tree) {
		$count = $tree['value'];
		if ( isset($tree['left'])) {
			$count += tree($tree['left']);
		}
		if ( isset($tree['right'])) {
			$count += tree($tree['left']);
		}
		return $count;
	}

	public function actionReturnBoxStock()
	{
//		die("/ecommerce/defacto/ecom-other/return-box-stock die begin");

		$boxBarcodes = [
			'700001077859' => '800000270278',
			'700001077858' => '800000270279',
			'700001077857' => '800000270277',
			'700001077856' => '800000270287',
			'700001077855' => '800000270291',
			'700001077854' => '800000270296',
			'700001077853' => '800000270302',
			'700001077852' => '800000270304',
			'700001077851' => '800000270305',
			'700001077850' => '800000270281',
			'700001077849' => '800000270284',
			'700001077848' => '800000270285',
			'700001077847' => '800000270293',
			'700001077846' => '800000270297',
			'700001077845' => '800000270295',
			'700001077844' => '800000270282',
			'700001077843' => '800000270280',
			'700001077842' => '800000270294',
			'700001077841' => '800000270299',
			'700001077840' => '800000270298',
			'700001077839' => '800000270276',
			'700001077838' => '800000270283',
			'700001077837' => '800000270289',
			'700001077836' => '800000270292',
			'700001077835' => '800000270286',
			'700001077834' => '800000270303',
			'700001077833' => '800000270301',
			'700001077832' => '800000270300',
			'700001077831' => '800000270290'
		];

		foreach ($boxBarcodes as $boxSeven=>$box) {
//
			$stockAll = EcommerceStock::find()->andWhere([
															'outbound_order_id' => '53310',
															'box_barcode' => $box,
														])
														->all();
			foreach($stockAll as $stock) {
				$stock->system_status_description= 'return26012022';
				$stock->save(false);
			}
		}

		return "-OK-";
	}


	public function actionSendAcceptedShipments($id)
	{
//		die(' ecommerce/defacto/ecom-other/send-accepted-shipments');
		// ecommerce/defacto/ecom-other/reset-by-outbound-order-id
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";

		$service = new OutboundService();
//		$outboundID = '64528'; // OMC-8531765
		//  $service->resetByOutboundOrderId($outboundID);
//		echo "<br />";
		$resp =  $service->SendAcceptedShipments($id);
//       $resp =  $service->SendShipmentFeedback($outboundID);
//		       VarDumper::dump($id,10,true);
//		       VarDumper::dump($resp,10,true);
//		echo "<br />";
//		die();
		// $service->saveWaybillDocument($outboundID);

//        $orderList = [
//          '7'=>'OMC-8196867',
//          '3'=>'OMC-8176705',
//          '4'=>'OMC-8186455',
//          '5'=>'OMC-8186673',
//        ];
//
//         $orderLog = EcommerceApiOutboundLog::find()->andWhere(['id'=>59])->one();
//
//        VarDumper::dump($orderLog,10,true);
//        echo "<br />";

		return $this->render('send-accepted-shipments',[
			"resp"=>$resp,
			"id"=>$id,
		]);// '<br />-END-TEST<br />';
	}



	public function actionSendShipmentFeedback($id)
	{
//		die(' ecommerce/defacto/ecom-other/send-shipment-feedback');

		$service = new OutboundService();
       $resp =  $service->SendShipmentFeedback($id);
//         $orderLog = EcommerceApiOutboundLog::find()->andWhere(['id'=>59])->one();
//        VarDumper::dump($orderLog,10,true);
//        echo "<br />";

		return $this->render('send-accepted-shipments',[
			"resp"=>$resp,
			"id"=>$id,
		]);
	}

	public function actionCancelShipment($id)
	{
//		die(' ecommerce/defacto/ecom-other/cancel-shipment');

		$service = new OutboundService();
         $service->CancelShipment($id,"202");
//         $orderLog = EcommerceApiOutboundLog::find()->andWhere(['id'=>59])->one();
//        VarDumper::dump($orderLog,10,true);
//        echo "<br />";

		return $this->render('send-accepted-shipments',[
			"resp"=>[],
			"id"=>$id,
		]);
	}


	public function actionSendShipmentFeedbackByList()
	{
//		die(' ecommerce/defacto/ecom-other/send-shipment-feedback-by-list');

		$service = new OutboundService();
		$orders = [
//			76088880	,
//			76110761	,
//			76110944	,
//			76111444	,
//			76111681	,
//			76112221	,
//			76112948	,
//			76113005	,
//			76113677	,
//			76113746	,
//			76114384	,
//			76114540	,
//			76114542	,
//			76115042	,
//			76115699	,
//			76116372	,
//			76116383	,
//			76116400	,
//			76116640	,
//			76116666	,
//			76117015	,
//-------------------
			"OMC-76140052",
			"OMC-76146363",
			"OMC-76146737",
			"OMC-76147023",
			"OMC-76149387",
			"OMC-76149497",
			"OMC-76149963",
			"OMC-76151442",
			"OMC-76152664",
			"OMC-76152630",
			"OMC-76152682",
			"OMC-76152631",
			"OMC-76153363",
			"OMC-76153523",
			"OMC-76153617",
			"OMC-76153860",
			"OMC-76164517",
			"OMC-76164990",
			"OMC-76164920",
//-------------------
//			76110944,
//			76111444,
//			76113005,
//			76114540,
//			76114542,
//			76116666,
//			76152630,
		];
		foreach($orders as $orderNumber) {
			//$orderNumberFull = trim("OMC-".trim($orderNumber));
			$orderNumberFull = trim($orderNumber);
			$orderInfo = $service->getOrderInfoByOrderNumber($orderNumberFull);
			VarDumper::dump($orderNumberFull,10,true);
			if ($orderInfo) {
				VarDumper::dump($orderInfo->order->id, 10, true);
				//$resp =  $service->SendShipmentFeedback($id);
			}
			echo "<br />";
			echo "<br />";

		}
		return "";
//		return $this->render('send-accepted-shipments',[
//			"resp"=>$resp,
//			"id"=>$id,
//		]);
	}


	public function actionFindBySkuId()
	{
		//die("/ecommerce/defacto/ecom-other/find-by-sku-id die begin");

		$skuIds = [
			231851007	,
			231851008	,
			231851022	,
			231851023	,
			231851024	,
			231855483	,
			231863080	,
			231863081	,
			231863082	,
			231863083	,
			231863084	,
			231863085	,
			231863128	,
			231863129	,
			231863130	,
			231863131	,
			231863132	,
			231863133	,
			231867928	,
			231867929	,
			231867930	,
			231867931	,
			231867932	,
			231867933	,
			231867954	,
			231867955	,
			231867956	,
			231867958	,
			231867959	,
			231874602	,
			231874604	,
			231874607	,
			231889020	,
			231889023	,
			231889025	,
			231889191	,
			231889676	,
			231889681	,
			231889682	,
			231889683	,
			231889686	,
			231890406	,
			231913461	,
			231913462	,
			231913631	,
			231913715	,
			231924751	,
			231924753	,
			231924754	,
			231925176	,
			231925256	,
			231925271	,
			231925977	,
			231926050	,
			231926052	,
			231926055	,
			231972337	,
			231972340	,
			231972367	,
			231972369	,
			231989150	,
			231989190	,
			231989191	,
			231989192	,
			231989193	,
			231989210	,
			231989211	,
			231989212	,
			232017019	,
			232017020	,
			232017022	,
			232017038	,
			232017040	,
			232017211	,
			232017213	,
			232017230	,
			232017326	,
			232017327	,
			232017328	,
			232794354	,
			233890913	,
			233890914	,
			233890915	,
			233890916	,
			233890917	,
			233890918	,
			233890919	,
			233890920	,
			233890921	,
			233890922	,
			233890923	,
			233890924	,
			233890925	,
			233890926	,
			233890927	,
			233891205	,
			233891206	,
			233891207	,
			233891208	,
			233897461	,
			233897462	,
			233897463	,
			233897464	,
			233897465	,
			233897466	,
			233897471	,
			233897472	,
			233897473	,
			233897474	,
			233897475	,
			233897476	,
			233897482	,
			233897483	,
			233897484	,
			233897485	,
			233897486	,
			233900038	,
			233900039	,
			233900040	,
			233900041	,
			233900042	,
			233900043	,
			234160571	,
			234160572	,
			234160573	,
			234160574	,
			234160575	,
			234160576	,
			234160582	,
			234160583	,
			234160584	,
			234160585	,
			234160586	,
			234160587	,
			234167466	,
			234167467	,
			234167468	,
			234167469	,
			234167470	,
			234167471	,
			234167476	,
			234167477	,
			234167478	,
			234167479	,
			234167480	,
			234167481	,
			234167545	,
			234167546	,
			234167547	,
			234167548	,
			234167549	,
			234167550	,
			234167554	,
			234167555	,
			234167556	,
			234167557	,
			234167558	,
			234167559	,
			234367655	,
			234367656	,
			234367657	,
			234367658	,
			234367659	,
			234367660	,
			226648378	,
			226648381	,
			226648382	,
			227491361	,
			227491362	,
			227491386	,
			227512834	,
			227512835	,
			227512836	,
			227512838	,
			227512899	,
			227513822	,
			227513833	,
			227529907	,
			227665569	,
			227704771	,
			227962121	,
			227962134	,
			232017366	,
			232017378	,
			232017379	,
			232017380	,
			232017381	,
			232042596	,
			232042599	,
			232042600	,
			232042601	,
			232054033	,
			232054034	,
			232169107	,
			232169108	,
			232169109	,
			232169111	,
			232169112	,
			232174158	,
			232174159	,
			232174160	,
			233937394	,
			233937395	,
			233937396	,
			233937397	,
			233937853	,
			233937854	,
			233937856	,
			233937857	,
			233937874	,
			233937875	,
			233937876	,
			233937877	,
			233938391	,
			233938392	,
			233938393	,
			233938394	,
			233938408	,
			233938410	,
			233938411	,
			233938412	,
			233938422	,
			233938423	,
			233938424	,
			233938425	,
			233938437	,
			233938439	,
			233938440	,
			233938441	,
			233938455	,
			233938456	,
			233938457	,
			233938458	,
			233938472	,
			233938473	,
			233938474	,
			233938475	,
			233947369	,
			234183182	,
			234183183	,
			234183184	,
			234183185	,
			234183200	,
			234183201	,
			234183202	,
			234183203	,
			229733006	,
			229733007	,
			229733008	,
			229733009	,
			229733010	,
			229734676	,
			229737382	,
			229737384	,
			229737570	,
			232271805	,
			232271806	,
			232271807	,
			232271808	,
			232271821	,
			232278480	,
			232278481	,
			232278483	,
			232278485	,
			232278822	,
			232278824	,
			232278827	,
			232298993	,
			232298994	,
			232298995	,
			232299370	,
			232305516	,
			232305518	,
			232305520	,
			232318138	,
			233701147	,
			233701515	,
			233701524	,
			233701633	,
			233960653	,
			233960654	,
			233960655	,
			233960656	,
			233960783	,
			233960784	,
			233960785	,
			233960786	,
			233960788	,
			233960789	,
			233960790	,
			233960791	,
			233965209	,
			233965210	,
			233965211	,
			233965212	,
			233965213	,
			233965214	,
			234242927	,
			234242928	,
			234242929	,
			234242930	,
			234242931	,
			234242932	,
			234400696	,
			234400698	,
			234407925	,
			234407926	,
			234407927	,
			234407928	,
			230745920	,
			230745921	,
			231280889	,
			231280933	,
			231402580	,
			232324464	,
			232333117	,
			233984686	,
			233984687	,
			233984688	,
			233984689	,
			233984690	,
			233996801	,
			233996802	,
			233996803	,
			233996804	,
			234024180	,
			234024181	,
			234024182	,
			234024183	,
			234024184	,
			234024385	,
			234024386	,
			234024387	,
			234024388	,
			234024389	,
			234245530	,
			234245531	,
			234245532	,
			234245533	,
			234245534	,
			234245535	,
			234245859	,
			234245860	,
			234245861	,
			234245862	,
			234245863	,
			234245864	,
			234246017	,
			234246018	,
			234246019	,
			234246020	,
			234246021	,
			234246022	,
			234246032	,
			234246034	,
			234246035	,
			234246036	,
			234246037	,
			234246038	,
			234246056	,
			234246057	,
			234246058	,
			234246059	,
			234246061	,
			234246062	,
			234246063	,
			234246064	,
			234246065	,
			234246066	,
			234251556	,
			234251557	,
			234251558	,
			234251564	,
			234251565	,
			234251566	,
			234251567	,
			234251570	,
			234251571	,
			234251573	,
			234251576	,
			234251662	,
			234263388	,
			231501808	,
			231501818	,
			231502166	,
			231502175	,
			231502176	,
			231502180	,
			231523219	,
			231524700	,
			231524826	,
			231525734	,
			231529778	,
			231535923	,
			231535929	,
			231535931	,
			231536020	,
			231560499	,
			231560501	,
			231566927	,
			231566928	,
			231567046	,
			231581598	,
			231581600	,
			231581857	,
			231581858	,
			231581859	,
			231581860	,
			231581880	,
			231581881	,
			231581882	,
			231581883	,
			232956675	,
			232956677	,
			232956678	,
			232956685	,
			232956688	,
			232956690	,
			234037701	,
			234037702	,
			234037703	,
			234037704	,
			234037711	,
			234037712	,
			234037713	,
			234037714	,
			234037716	,
			234037717	,
			234037718	,
			234037719	,
			234037741	,
			234037742	,
			234037743	,
			234037744	,
			234037756	,
			234037757	,
			234037758	,
			234037759	,
			234038782	,
			234038783	,
			234038784	,
			234038785	,
			234039138	,
			234039139	,
			234039140	,
			234039141	,
			234039143	,
			234039144	,
			234039145	,
			234039146	,
			234049734	,
			234049735	,
			234049736	,
			234049737	,
			234052408	,
			234052409	,
			234052410	,
			234052411	,
			234052420	,
			234052421	,
			234052422	,
			234052423	,
			234266916	,
			234266917	,
			234266918	,
			234267103	,
			234267104	,
			234267105	,
			234277852	,
			234277853	,
			234277854	,
			234277877	,
			234277878	,
			234277879	,
			234277908	,
			231581903	,
			231581904	,
			231581905	,
			231581906	,
			231582055	,
			231582056	,
			231582057	,
			231582058	,
			231582079	,
			231582082	,
			231582085	,
			231582087	,
			231583187	,
			231583188	,
			231583189	,
			231583190	,
			231583244	,
			231583245	,
			231583246	,
			231583247	,
			231584483	,
			231584484	,
			231584485	,
			231584540	,
			231584541	,
			231584542	,
			231584543	,
			231584558	,
			231584559	,
			231584560	,
			231584561	,
			231584576	,
			231584577	,
			231584578	,
			231584579	,
			231598051	,
			231598628	,
			231598697	,
			231628412	,
			231628413	,
			231628414	,
			231628416	,
			231628417	,
			231628418	,
			231628419	,
			231628420	,
			231632746	,
			231632747	,
			231632748	,
			231632749	,
			231632904	,
			231632905	,
			231632906	,
			231632908	,
			231632926	,
			231632927	,
			231632929	,
			231632930	,
			231656285	,
			231656286	,
			231656287	,
			231656288	,
			231656329	,
			231662422	,
			231662423	,
			231662424	,
			231662425	,
			231663859	,
			231663860	,
			231663861	,
			231663862	,
			231663914	,
			231663916	,
			231663918	,
			231663919	,
			231664074	,
			231664285	,
			231664445	,
			233098869	,
			233098870	,
			233098871	,
			233098872	,
			233098873	,
			233109139	,
			233109140	,
			233109211	,
			233109214	,
			233109359	,
			233109361	,
			233109362	,
			233109363	,
			233109364	,
			233109365	,
			233109366	,
			233109367	,
			233137075	,
			233137102	,
			233137106	,
			233217862	,
			234066334	,
			234066335	,
			234066336	,
			234066337	,
			234066338	,
			234066339	,
			234066627	,
			234066628	,
			234066629	,
			234066630	,
			234066631	,
			234066632	,
			231666371	,
			231667661	,
			231667663	,
			231667664	,
			231667887	,
			231667889	,
			231667890	,
			231667941	,
			231667942	,
			231667943	,
			231667944	,
			231667945	,
			231667946	,
			231669160	,
			231669161	,
			231669167	,
			231669482	,
			231671327	,
			231671328	,
			231671329	,
			231671330	,
			231671333	,
			231671334	,
			231671335	,
			231671336	,
			231671461	,
			231671580	,
			231671581	,
			231686727	,
			231686728	,
			231686733	,
			231686875	,
			231686876	,
			231686878	,
			231686879	,
			231687189	,
			231687191	,
			231687192	,
			231687193	,
			231687232	,
			231691007	,
			231691008	,
			231691009	,
			231691010	,
			231692915	,
			231692916	,
			231692917	,
			231692918	,
			231696199	,
			231696202	,
			231702936	,
			231702937	,
			231702953	,
			231702954	,
			231702956	,
			231707104	,
			231707105	,
			231707107	,
			231707108	,
			231707109	,
			231707188	,
			231707190	,
			231707191	,
			231707192	,
			231707193	,
			231712654	,
			231712657	,
			231712658	,
			231712659	,
			231712660	,
			231734200	,
			231770852	,
			231770854	,
			231770855	,
			231770868	,
			231770869	,
			231770870	,
			231770871	,
			231770872	,
			233224753	,
			233224754	,
			233224755	,
			233244051	,
			233244052	,
			233244054	,
			233244055	,
			233402240	,
			233402243	,
			233402244	,
			233402245	,
			233402246	,
			233402247	,
			233780877	,
			233782584	,
			233782587	,
			233782798	,
			233782799	,
			233782800	,
			233782801	,
			233782803	,
			233782804	,
			233782805	,
			233782806	,
			233783515	,
			233783516	,
			233783517	,
			233783518	,
			233783535	,
			233783536	,
			233783537	,
			233783538	,
			233791891	,
			233791896	,
			233795058	,
			233795059	,
			233795060	,
			233795061	,
			233795062	,
			233795063	,
			233795065	,
			233795066	,
			233795068	,
			233795069	,
			233795075	,
			233795142	,
			233795143	,
			233795144	,
			233795146	,
			233795151	,
			233795154	,
			233795864	,
			233795973	,
			233795992	,
			233796342	,
			233796362	,
			233796369	,
			233796379	,
			233796383	,
			234083521	,
			234083522	,
			234083523	,
			234083524	,
			234083525	,
			234083526	,
			234094999	,
			234095000	,
			234095001	,
			234095002	,
			234095004	,
			234095005	,
			234095006	,
			234095007	,
			234095019	,
			234095020	,
			234095021	,
			234095022	,
			234095029	,
			234095030	,
			234095031	,
			234095032	,
			234095040	,
			234095041	,
			234095042	,
			234095043	,
			234096591	,
			234100209	,
			234100210	,
			234100211	,
			234100212	,
			234100213	,
			234338595	,
			231796121	,
			231796123	,
			231798564	,
			231798566	,
			231798567	,
			231798568	,
			231837855	,
			231837857	,
			231837874	,
			231837875	,
			231839889	,
			231839890	,
			231839891	,
			231839892	,
			231839893	,
			231839894	,
			231843469	,
			231843472	,
			231850365	,
			231850366	,
			231850370	,
			231850896	,
			231850900	,
			231851003	,
			231851004	,
			231851005	,
			231851006	,
			233402248	,
			233402249	,
			233803067	,
			233803068	,
			233803069	,
			233803070	,
			233803071	,
			233803085	,
			233803086	,
			233803087	,
			233803088	,
			233803089	,
			233803197	,
			233803198	,
			233803199	,
			233803200	,
			233803201	,
			233803218	,
			233803219	,
			233803220	,
			233803221	,
			233803222	,
			233803240	,
			233803241	,
			233803242	,
			233803243	,
			233803244	,
			233809202	,
			233809241	,
			233809268	,
			233809299	,
			233809491	,
			233809497	,
			233809506	,
			233812982	,
			233813032	,
			233813039	,
			233816472	,
			233816549	,
			233816552	,
			233816632	,
			233816816	,
			233816818	,
			233854716	,
			233854743	,
			233854744	,
			234156586	,
			234156587	,
			234156588	,
			234156589	,
			234156590	,
			234156591	,
			234338596	,
			234338597
		];

		$stockList = EcommerceStock::find()
										   ->select('place_address_barcode, box_address_barcode, product_barcode, count(product_barcode) as productQty')
										   ->andWhere([
											   'client_id' => 2,
											   'client_product_sku' => $skuIds,
											   'status_availability' => StockAvailability::YES,
											   'condition_type' => StockConditionType::UNDAMAGED,
										   ])
										   ->groupBy('product_barcode, box_address_barcode, place_address_barcode')
										   ->orderBy('place_address_sort1')
										   ->asArray()
										   ->all();
		$file = "find-by-sku-ids.csv";
		foreach ($stockList as $stockRow) {
			$row = $stockRow['place_address_barcode'].';'
				.$stockRow['box_address_barcode'].';'
				.$stockRow['product_barcode'].';'
				.$stockRow['productQty'].';'."\n";
			file_put_contents($file,$row,FILE_APPEND);
		}

		VarDumper::dump($stockList,10,true);
		die;
	}
}