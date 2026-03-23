<?php

namespace app\modules\report\controllers;

use common\modules\crossDock\models\CrossDock;
use common\modules\crossDock\models\CrossDockItems;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use common\modules\client\models\Client;
use Yii;
use stockDepartment\modules\crossDock\models\CrossDockSearch;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\components\BarcodeManager;

use clientDepartment\components\Controller;

class OtherDeliveryController extends Controller
{
    public function actionOutbound()
    {
        $routeDirectionService = new \common\modules\city\RouteDirection\service\Service();
        $pageSize = 9999;
        $clientId = 2;
		
//        $storeAdkUstKamen = [10];  // 10  АДК	Усть-Каменогорск
//        $storeSemey = [198]; // 198  	Семей

        $toPointId = ArrayHelper::merge(
            $routeDirectionService->getWestStore(),
            $routeDirectionService->getNorthStore(),
            $routeDirectionService->getSouthStore(),
            $routeDirectionService->getEasternStore(),
            $routeDirectionService->getAlmatyStore()
        );

        $searchModelOutbound = new OutboundOrderGridSearch();
        $dataProviderOutbound = $searchModelOutbound->search(Yii::$app->request->queryParams);
        $dataProviderOutbound->pagination->pageSize = $pageSize;
        $dataProviderOutbound->query->andWhere(['status'=>Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API]);
        $dataProviderOutbound->query->andWhere(['client_id'=>$clientId]);
        $dataProviderOutbound->query->andWhere(['to_point_id'=>$toPointId]);
//        $dataProviderOutbound->query->orderBy(['updated_at'=>SORT_DESC]);
        $dataProviderOutbound->query->orderBy(['packing_date'=>SORT_DESC]);
        $dataProviderOutbound->query->limit(600);

        $searchModelCrossDock = new CrossDockSearch();
        $dataProviderCrossDock = $searchModelCrossDock->search(Yii::$app->request->queryParams);
        $dataProviderCrossDock->query->andWhere(['to_point_id'=>$toPointId]);
        $dataProviderCrossDock->pagination->pageSize = $pageSize;
        $dataProviderCrossDock->query->andWhere(['client_id'=>$clientId]);
        $dataProviderCrossDock->query->andWhere(['status'=>Stock::STATUS_CROSS_DOCK_COMPLETE]);
        $dataProviderCrossDock->query->andWhere(['NOT IN','id',[1047,999,454,453,444,44,103,99,98,89,85,84,75,68,69,59]]);
		
        $dataProviderCrossDock->query->orderBy(['accepted_datetime'=>SORT_DESC]);
        $dataProviderCrossDock->query->limit(200);
//        $dataProviderCrossDock->query->orderBy(['updated_at'=>SORT_DESC]);

		if(!empty($searchModelOutbound->packing_date)) {
			$date = explode('/',$searchModelOutbound->packing_date);
			$date[0] = trim($date[0]).' 00:00:00';
			$date[1] = trim($date[1]).' 23:59:59';
			$dataProviderCrossDock->query->andWhere(['between', 'accepted_datetime', strtotime($date[0]),strtotime($date[1])]);
		}
		

        $clientsArray = Client::getActiveWMSItems();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();
        $stock = new Stock();
        $statusArray = $stock->getStatusArray();
        $statusCargoArray = $searchModelOutbound->getCargoStatusArray();

        $allModels = ArrayHelper::merge(
            $dataProviderOutbound->query->asArray()->all(),
            $dataProviderCrossDock->query->asArray()->all()
        );

        $arrayDataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
            'key'=> function($data) {
                if(isset($data['internal_barcode'])) {
                    return 'cd'.$data['id'];
                }
//
                return 'ob'.$data['id'];
            },
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            //'sort' => [//
            //    'attributes' => [
           //         'created_at',
           ///         'cargo_status',
//],
          //  ],
        ]);



        return $this->render('outbound/index', [
//            'searchModel' => $allModels,
			'searchModel' => $searchModelOutbound,
            'dataProvider' => $arrayDataProvider,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
            'statusArray' => $statusArray,
            'statusCargoArray' => $statusCargoArray,
        ]);
    }
	
	  public function actionPrint() {
        $ids = explode(',',Yii::$app->request->get('ids'));
        $outboundIDs = [];
        $crossDockIDs = [];
        $typeCrossDock = 'cd';
        $typeOutbound = 'ob';

        foreach($ids as $id) {
            $type = substr($id,0,2);
            if($type == $typeOutbound) {
                $outboundIDs [] = str_replace($typeOutbound,'',$id);
            }
            if($type == $typeCrossDock) {
                $crossDockIDs [] = str_replace($typeCrossDock,'',$id);
            }
        }

        $outboundOrders = OutboundOrder::find()->andWhere(['id'=>$outboundIDs])->all();
		OutboundOrder::updateAll([
			  "print_outbound_status"=>"yes",
		  ],['id'=>$outboundIDs]);
		  
        $shopList = [];
        $boxList = [];
        foreach ($outboundOrders as $order) {
            $store = \common\modules\store\models\Store::findOne($order->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');

			if(isset($shopList[$shopToTitle])) {
				$shopList [$shopToTitle] ['acceptedNumberPlaceQty'] = $shopList [$shopToTitle] ['acceptedNumberPlaceQty'] + $order->accepted_number_places_qty;
				$shopList [$shopToTitle] ['forShowInReport'] [$order->parent_order_number]  =  $order->parent_order_number;
			} else {
				//$forShowInReport[$order->parent_order_number] = $order->parent_order_number;
				$shopList [$shopToTitle] = [
					'id' => $order->id,
					//'forShowInReport' =>$forShowInReport,
					'orderNumber' => $order->order_number,
					'parentOrderNumber' => $order->parent_order_number,
					'shopName' => $shopToTitle,
					'acceptedNumberPlaceQty' => $order->accepted_number_places_qty,
				];
				$shopList [$shopToTitle] ['forShowInReport'] [$order->parent_order_number]  =  $order->parent_order_number;
			}


            $boxOnStock = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode')
                ->andWhere([
                    'outbound_order_id' => $order->id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                ])
                //->andWhere('box_size_barcode != ""')
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnStock as $box) {
				
				//VarDumper::dump($box,10,true);
				$inboundLCStock = Stock::find()
				   ->select('inbound_client_box,primary_address')
				   ->andWhere(['id' => \yii\helpers\ArrayHelper::getValue([$box],'0.id')])
				   ->asArray()
				   ->one();
				   
				$inboundLC = "";
				//if (BarcodeManager::isReturnBoxBarcode($inboundLCStock['primary_address'])) {
					$inboundLC =  $inboundLCStock['inbound_client_box'];
				//}
				

                $stockIdMapBoxBarcode = \yii\helpers\ArrayHelper::map([$box],'id','box_barcode');
                $boxAndLcBarcode =  \common\modules\outbound\service\OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode);
                $boxBarcode = \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.our_box');
                $boxBarcodeLC =  \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.client_box');
                
				$boxSize = empty($box['box_size_barcode']) ? 32 : $box['box_size_barcode'];
                $boxSize =  \common\components\BarcodeManager::mapM3ToBoxSize($boxSize);

                $boxList [] = [
                    'boxIndex'=>++$boxIndex,
                    'orderNumber'=>$order->order_number,
                    'parentOrderNumber'=>$order->parent_order_number,
                    'shopName'=>$shopToTitle,
                    'boxBarcode'=>$boxBarcode,
                    'boxSize'=>$boxSize,
                    'boxBarcodeLC'=>$boxBarcodeLC,
                    'createdAt'=>$order->created_at,
					'inboundLC'=>$inboundLC,
                ];
            }
        }

//		$forShowInReport = [];
        $crossDockOrders = CrossDock::find()->andWhere(['id'=>$crossDockIDs])->all();
		
		CrossDock::updateAll([
		  	"print_outbound_status"=>"yes",
		  ],['id'=>$crossDockIDs]);
		  
        foreach ($crossDockOrders as $crossDock) {

            $store = \common\modules\store\models\Store::findOne($crossDock->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');

            if(isset($shopList[$shopToTitle])) {
				$shopList [$shopToTitle] ['acceptedNumberPlaceQty'] = $shopList [$shopToTitle] ['acceptedNumberPlaceQty'] + $crossDock->accepted_number_places_qty;
				$shopList [$shopToTitle] ['forShowInReport'] [ltrim($crossDock->internal_barcode,'2-')]  =  ltrim($crossDock->internal_barcode,'2-');
			} else {
				//$forShowInReport[ltrim($crossDock->internal_barcode,'2-')] = ltrim($crossDock->internal_barcode,'2-');
				$shopList [$shopToTitle] = [
					'id'=>$crossDock->id,
					//'forShowInReport' =>$forShowInReport,
					'partyNumber'=>$crossDock->party_number,
					'internalBarcode'=> ltrim($crossDock->internal_barcode,'2-'),
					'shopName'=>$shopToTitle,
					'acceptedNumberPlaceQty'=>$crossDock->accepted_number_places_qty,
				];
				$shopList [$shopToTitle] ['forShowInReport'] [ltrim($crossDock->internal_barcode,'2-')]  =  ltrim($crossDock->internal_barcode,'2-');
			}


            $boxOnCrossDock = CrossDockItems::find()
                ->select('box_barcode, box_m3')
                ->andWhere([
                    'cross_dock_id' => $crossDock->id,
                    'status' => [
                        Stock::STATUS_CROSS_DOCK_SCANNED,
//                        0,
                    ]
                ])
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnCrossDock as $box) {

                $boxBarcodeLC = $box['box_barcode'];
                //$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize(\common\components\BarcodeManager::getIntegerM3($box['box_m3']));
				
				
				$integerM3BoxM3 = \common\components\BarcodeManager::getIntegerM3($box['box_m3']);
				if (empty($integerM3BoxM3)) {
					file_put_contents("empty-getIntegerM3.log","empty-boxOnCrossDock-box-m3: ".$boxBarcodeLC."\n",FILE_APPEND);
					$integerM3BoxM3 = null;
				}
				
				file_put_contents("empty-getIntegerM3-2.log","empty-boxOnCrossDock-box-m3: ".$boxBarcodeLC." -------- ".$integerM3BoxM3." -------- ".$box['box_m3']."\n",FILE_APPEND);

				$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize($integerM3BoxM3);
				

                $boxList [] = [
                    'boxIndex' => ++$boxIndex,
                    'orderNumber' => $crossDock->party_number,
                    'parentOrderNumber' => ltrim($crossDock->internal_barcode,'2-'),
                    'shopName' => $shopToTitle,
                    'boxBarcode' => $boxBarcodeLC,
                    'boxSize' => $boxSize,
                    'boxBarcodeLC' => $boxBarcodeLC,
                    'createdAt'=>$crossDock->created_at,
					'inboundLC'=>"",
                ];
            }
        }
        $objPHPExcel = new Spreadsheet();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('АКТ');
//            ->setTitle('report-' . date('d.m.Y'));

        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

		$fontSizeForHeader = 10;

        $iCell = 2;
        $activeSheet->setCellValue('D' . $iCell, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +

        $iCell += 2;
        $activeSheet->setCellValue('A' . $iCell,'г.Алматы');
        $activeSheet->setCellValue('H' . $iCell,date('d.m.Y'));
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize(10);

        $iCell += 1;
        $activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "Warehouse Management IAA" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "Warehouse Management IAA" принял груз согласно перечню:');
        $activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
//        $activeSheet->mergeCells('A'.$iCell.':H'.($iCell+=4));
        $activeSheet->mergeCells('A'.$iCell.':J'.($iCell+=4));
        $iCell += 3;
        $borderCellStart = 'A' . $iCell;
//        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/forms','№'));
        $activeSheet->setCellValue('A' . $iCell, Yii::t('outbound/forms','№ ТТН'));
        $activeSheet->getStyle('A' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		  $activeSheet->getStyle('A' . $iCell)->getFont()->setSize($fontSizeForHeader);

        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/titles','Магазин получатель'));
        $activeSheet->getStyle('B' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		  $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($fontSizeForHeader);

        $activeSheet->mergeCells('B'.$iCell.':D'.$iCell);

//        $activeSheet->setCellValue('F' . $iCell, Yii::t('outbound/titles','Количество коробок'));
        $activeSheet->setCellValue('E' . $iCell, Yii::t('outbound/titles','Кол-во'));
        $activeSheet->getStyle('E' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


//        $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

		  $activeSheet->setCellValue('F' . $iCell, Yii::t('outbound/titles','Номер заказа'));
		  $activeSheet->getStyle('F' . $iCell)
					  ->getAlignment()
					  ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		  $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($fontSizeForHeader);
//		  $activeSheet->mergeCells('F'.$iCell.':H'.$iCell);
		  $activeSheet->mergeCells('F'.$iCell.':J'.$iCell);

//        $i = 0;

        $fontSizeForContent = 8;
        foreach ($shopList as $shopRow) {
            $iCell += 1;
            $activeSheet->setCellValue('A' . $iCell, "");

            $activeSheet->setCellValue('B' . $iCell, $shopRow['shopName']);
			$activeSheet->getStyle('B' . $iCell)->getFont()->setSize($fontSizeForContent);
            $activeSheet->mergeCells('B'.$iCell.':D'.$iCell);

            $activeSheet->setCellValue('E' . $iCell, $shopRow['acceptedNumberPlaceQty']);
            $activeSheet->getStyle('E' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$activeSheet->getStyle('E' . $iCell)->getFont()->setSize($fontSizeForContent);

            $activeSheet->setCellValue('F' . $iCell, implode("/",$shopRow['forShowInReport']));
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$activeSheet->getStyle('F' . $iCell)->getFont()->setSize($fontSizeForContent);

			$activeSheet->mergeCells('F'.$iCell.':J'.$iCell);
			
        }

//        $borderCellEnd = 'G' . $iCell;
        $borderCellEnd = 'J' . $iCell;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

         $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        $activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
        $activeSheet->setCellValue('A' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('A' . ($iCell+8), 'Подпись _________'); // +

        $activeSheet->setCellValue('H' . ($iCell+4), 'Груз принял'); // +
        $activeSheet->setCellValue('G' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('G' . ($iCell+8), 'Гос номер тс __________________'); // +
        $activeSheet->setCellValue('G' . ($iCell+10), 'Подпись _________'); // +
        $activeSheet->setCellValue('B' . ($iCell+10), 'М.П'); // +
        $activeSheet->setCellValue('H' . ($iCell+12), 'М.П'); // +

        $iCell += 15;
        /////// Begin
        $iCell = 1;
        $objPHPExcel->createSheet()->setTitle('СПИСОК ЗАКАЗОВ');
        $objPHPExcel->setActiveSheetIndex(1);
        $activeSheet =  $objPHPExcel->getActiveSheet();
        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);


        ////// End
        $cellFontSize = 7;
        $borderCellStart = 'A' . $iCell;
        $activeSheet->setCellValue('A' . $iCell,'№');
        $activeSheet->getStyle('A' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $activeSheet->getStyle('A' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('B' . $iCell, '№ Заказа');
        $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('C' . $iCell, 'Магазин');
        $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('D' . $iCell, '№ Короба');
        $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('E' . $iCell, 'ШК короба');
        $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('F' . $iCell, 'Размер короба');
        $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('G' . $iCell, 'LC');
        $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('H' . $iCell, 'Received Date by DC');
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('I' . $iCell, 'Truck Exit Date From DC');
        $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);


        $activeSheet->getColumnDimension('A')->setAutoSize(false);
        $activeSheet->getColumnDimension('A')->setWidth(4);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        $activeSheet->getColumnDimension('F')->setAutoSize(true);
        $activeSheet->getColumnDimension('G')->setAutoSize(true);

        $activeSheet->getColumnDimension('H')->setAutoSize(false);
        $activeSheet->getColumnDimension('H')->setWidth(19);

        $activeSheet->getColumnDimension('I')->setAutoSize(false);
        $activeSheet->getColumnDimension('I')->setWidth(21);

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        $i = 0;
        $cellFontSize = 7;
        foreach ($boxList as $boxRow) {
            $iCell += 1;
            $activeSheet->setCellValue('A' . $iCell,++$i);
            $activeSheet->getStyle('A' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValue('B' . $iCell,$boxRow['parentOrderNumber']);
            $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//            $activeSheet->mergeCells('B'.$iCell.':C'.$iCell);

            $activeSheet->setCellValue('C' . $iCell,$boxRow['shopName']);
            $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('C' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('D'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('D' . $iCell,$boxRow['boxIndex']);
            $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('D' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

            $activeSheet->setCellValueExplicit('E' . $iCell,$boxRow['boxBarcode'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('E' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('H'.$iCell.':I'.$iCell);

            $activeSheet->setCellValue('F' . $iCell,$boxRow['boxSize']);
            $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('J'.$iCell.':K'.$iCell);

            $activeSheet->setCellValueExplicit('G' . $iCell,$boxRow['boxBarcodeLC'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('G' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('L'.$iCell.':M'.$iCell);

            $activeSheet->setCellValueExplicit('H' . $iCell, Yii::$app->formatter->asDatetime($boxRow['createdAt'],$asDatetimeFormat) ,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('H' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('I' . $iCell, Yii::$app->formatter->asDatetime(time(),$asDatetimeFormat),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
//            $activeSheet->setCellValueExplicit('I' . $iCell,date('d.m.Y H:i'),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('I' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				
				$activeSheet->setCellValue('J' . $iCell, $boxRow['inboundLC']);
				
        }

        $borderCellEnd = 'I' . $iCell;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-report-' . $iCell . '.xlsx"');
        header('Cache-Control: max-age=0');

//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        $objWriter->save('php://output');


//        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($objPHPExcel);
//        $writer->writeAllSheets();
//        $writer->save("05featuredemo.pdf");


        $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $writer->save('php://output');
//        exit;

        Yii::$app->end();
        return true;








//        VarDumper::dump($outboundIDs,10,true);
//        VarDumper::dump($crossDockIDs,10,true);
//        die('stop');


//        return $this->render('outbound/pick-list-pdf', [
//            'outboundOrders' => $outboundOrders,
//            'crossDockOrders' => $crossDockOrders,
//        ]);
    }
	
	  public function actionPrint_OLd_07_11_2022()
    {
        $ids = explode(',',Yii::$app->request->get('ids'));
        $outboundIDs = [];
        $crossDockIDs = [];
        $typeCrossDock = 'cd';
        $typeOutbound = 'ob';

        foreach($ids as $id) {
            $type = substr($id,0,2);
            if($type == $typeOutbound) {
                $outboundIDs [] = str_replace($typeOutbound,'',$id);
            }
            if($type == $typeCrossDock) {
                $crossDockIDs [] = str_replace($typeCrossDock,'',$id);
            }
        }

        $outboundOrders = OutboundOrder::find()->andWhere(['id'=>$outboundIDs])->all();
        $shopList = [];
        $boxList = [];
        foreach ($outboundOrders as $order) {
            $store = \common\modules\store\models\Store::findOne($order->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');
            
			if(isset($shopList[$shopToTitle])) {
				$shopList [$shopToTitle] ['acceptedNumberPlaceQty'] = $shopList [$shopToTitle] ['acceptedNumberPlaceQty'] + $order->accepted_number_places_qty;
			} else {
				$shopList [$shopToTitle] = [
					'id' => $order->id,
					'orderNumber' => $order->order_number,
					'parentOrderNumber' => $order->parent_order_number,
					'shopName' => $shopToTitle,
					'acceptedNumberPlaceQty' => $order->accepted_number_places_qty,
				];
			}


            $boxOnStock = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode')
                ->andWhere([
                    'outbound_order_id' => $order->id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                ])
                //->andWhere('box_size_barcode != ""')
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnStock as $box) {

                $stockIdMapBoxBarcode = \yii\helpers\ArrayHelper::map([$box],'id','box_barcode');
                $boxAndLcBarcode =  \common\modules\outbound\service\OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode);
                $boxBarcode = \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.our_box');
                $boxBarcodeLC =  \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.client_box');
                
				$boxSize = empty($box['box_size_barcode']) ? 32 : $box['box_size_barcode'];
                $boxSize =  \common\components\BarcodeManager::mapM3ToBoxSize($boxSize);

                $boxList [] = [
                    'boxIndex'=>++$boxIndex,
                    'orderNumber'=>$order->order_number,
                    'parentOrderNumber'=>$order->parent_order_number,
                    'shopName'=>$shopToTitle,
                    'boxBarcode'=>$boxBarcode,
                    'boxSize'=>$boxSize,
                    'boxBarcodeLC'=>$boxBarcodeLC,
                    'createdAt'=>$order->created_at,
                ];
            }
        }


        $crossDockOrders = CrossDock::find()->andWhere(['id'=>$crossDockIDs])->all();
        foreach ($crossDockOrders as $crossDock) {

            $store = \common\modules\store\models\Store::findOne($crossDock->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');
			
            if(isset($shopList[$shopToTitle])) {
				$shopList [$shopToTitle] ['acceptedNumberPlaceQty'] = $shopList [$shopToTitle] ['acceptedNumberPlaceQty'] + $crossDock->accepted_number_places_qty;
			} else {
				$shopList [$shopToTitle] = [
					'id'=>$crossDock->id,
					'partyNumber'=>$crossDock->party_number,
					'internalBarcode'=> ltrim($crossDock->internal_barcode,'2-'),
					'shopName'=>$shopToTitle,
					'acceptedNumberPlaceQty'=>$crossDock->accepted_number_places_qty,
				];
			}


            $boxOnCrossDock = CrossDockItems::find()
                ->select('box_barcode, box_m3')
                ->andWhere([
                    'cross_dock_id' => $crossDock->id,
                    'status' => [
                        Stock::STATUS_CROSS_DOCK_SCANNED,
                        //0,
                    ]
                ])
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnCrossDock as $box) {

                $boxBarcodeLC = $box['box_barcode'];
                //$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize(\common\components\BarcodeManager::getIntegerM3($box['box_m3']));
				
				
				$integerM3BoxM3 = \common\components\BarcodeManager::getIntegerM3($box['box_m3']);
				if (empty($integerM3BoxM3)) {
					file_put_contents("empty-getIntegerM3.log","empty-boxOnCrossDock-box-m3: ".$boxBarcodeLC."\n",FILE_APPEND);
					$integerM3BoxM3 = null;
				}

				$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize($integerM3BoxM3);
				

                $boxList [] = [
                    'boxIndex' => ++$boxIndex,
                    'orderNumber' => $crossDock->party_number,
                    'parentOrderNumber' => ltrim($crossDock->internal_barcode,'2-'),
                    'shopName' => $shopToTitle,
                    'boxBarcode' => $boxBarcodeLC,
                    'boxSize' => $boxSize,
                    'boxBarcodeLC' => $boxBarcodeLC,
                    'createdAt'=>$crossDock->created_at,
                ];
            }
        }
        $objPHPExcel = new Spreadsheet();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('АКТ');
//            ->setTitle('report-' . date('d.m.Y'));

        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $iCell = 2;
        $activeSheet->setCellValue('D' . $iCell, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +

        $iCell += 2;
        $activeSheet->setCellValue('A' . $iCell,'г.Алматы');
        $activeSheet->setCellValue('H' . $iCell,date('d.m.Y'));
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize(10);

        $iCell += 1;
        $activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "Warehouse Management IAA" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "Warehouse Management IAA" принял груз согласно перечню:');
        $activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
        $activeSheet->mergeCells('A'.$iCell.':H'.($iCell+=4));
        $iCell += 3;
        $borderCellStart = 'B' . $iCell;
        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/forms','№'));
        $activeSheet->getStyle('B' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','Магазин получатель'));
        $activeSheet->getStyle('C' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

        $activeSheet->setCellValue('F' . $iCell, Yii::t('outbound/titles','Количество коробок'));
        $activeSheet->getStyle('F' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

        $i = 0;
        foreach ($shopList as $shopRow) {
            $iCell += 1;
            $activeSheet->setCellValue('B' . $iCell, ++$i);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


            $activeSheet->setCellValue('C' . $iCell, $shopRow['shopName']);
            $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('F' . $iCell, $shopRow['acceptedNumberPlaceQty']);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);
        }

        $borderCellEnd = 'G' . $iCell;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

         $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);


/*
        $activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
        $activeSheet->setCellValue('A' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('A' . ($iCell+8), 'Подпись _________'); // +

        $activeSheet->setCellValue('F' . ($iCell+4), 'Груз принял'); // +
        $activeSheet->setCellValue('E' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('E' . ($iCell+8), 'Подпись _________'); // +
        $activeSheet->setCellValue('B' . ($iCell+10), 'М.П'); // +
        $activeSheet->setCellValue('F' . ($iCell+10), 'М.П'); // +
		*/
		
		$activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
        $activeSheet->setCellValue('A' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('A' . ($iCell+8), 'Подпись _________'); // +

        $activeSheet->setCellValue('F' . ($iCell+4), 'Груз принял'); // +
        $activeSheet->setCellValue('E' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('E' . ($iCell+8), 'Гос номер тс __________________'); // +
        $activeSheet->setCellValue('E' . ($iCell+10), 'Подпись _________'); // +
        $activeSheet->setCellValue('B' . ($iCell+10), 'М.П'); // +
        $activeSheet->setCellValue('F' . ($iCell+12), 'М.П'); // +

        $iCell += 15;
        /////// Begin
        $iCell = 1;
        $objPHPExcel->createSheet()->setTitle('СПИСОК ЗАКАЗОВ');
        $objPHPExcel->setActiveSheetIndex(1);
        $activeSheet =  $objPHPExcel->getActiveSheet();
        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);


        ////// End
        $cellFontSize = 7;
        $borderCellStart = 'A' . $iCell;
        $activeSheet->setCellValue('A' . $iCell,'№');
        $activeSheet->getStyle('A' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $activeSheet->getStyle('A' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('B' . $iCell, '№ Заказа');
        $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('C' . $iCell, 'Магазин');
        $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('D' . $iCell, '№ Короба');
        $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('E' . $iCell, 'ШК короба');
        $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('F' . $iCell, 'Размер короба');
        $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('G' . $iCell, 'LC');
        $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('H' . $iCell, 'Received Date by DC');
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('I' . $iCell, 'Truck Exit Date From DC');
        $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);


        $activeSheet->getColumnDimension('A')->setAutoSize(false);
        $activeSheet->getColumnDimension('A')->setWidth(4);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        $activeSheet->getColumnDimension('F')->setAutoSize(true);
        $activeSheet->getColumnDimension('G')->setAutoSize(true);

        $activeSheet->getColumnDimension('H')->setAutoSize(false);
        $activeSheet->getColumnDimension('H')->setWidth(19);

        $activeSheet->getColumnDimension('I')->setAutoSize(false);
        $activeSheet->getColumnDimension('I')->setWidth(21);

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        $i = 0;
        $cellFontSize = 7;
        foreach ($boxList as $boxRow) {
            $iCell += 1;
            $activeSheet->setCellValue('A' . $iCell,++$i);
            $activeSheet->getStyle('A' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValue('B' . $iCell,$boxRow['parentOrderNumber']);
            $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//            $activeSheet->mergeCells('B'.$iCell.':C'.$iCell);

            $activeSheet->setCellValue('C' . $iCell,$boxRow['shopName']);
            $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('C' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('D'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('D' . $iCell,$boxRow['boxIndex']);
            $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('D' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

            $activeSheet->setCellValueExplicit('E' . $iCell,$boxRow['boxBarcode'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('E' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('H'.$iCell.':I'.$iCell);

            $activeSheet->setCellValue('F' . $iCell,$boxRow['boxSize']);
            $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('J'.$iCell.':K'.$iCell);

            $activeSheet->setCellValueExplicit('G' . $iCell,$boxRow['boxBarcodeLC'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('G' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('L'.$iCell.':M'.$iCell);

            $activeSheet->setCellValueExplicit('H' . $iCell, Yii::$app->formatter->asDatetime($boxRow['createdAt'],$asDatetimeFormat) ,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('H' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('I' . $iCell, Yii::$app->formatter->asDatetime(time(),$asDatetimeFormat),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
//            $activeSheet->setCellValueExplicit('I' . $iCell,date('d.m.Y H:i'),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('I' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $borderCellEnd = 'I' . $iCell;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-report-' . $iCell . '.xlsx"');
        header('Cache-Control: max-age=0');

//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        $objWriter->save('php://output');


//        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($objPHPExcel);
//        $writer->writeAllSheets();
//        $writer->save("05featuredemo.pdf");


        $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $writer->save('php://output');
//        exit;

        Yii::$app->end();
        return true;








//        VarDumper::dump($outboundIDs,10,true);
//        VarDumper::dump($crossDockIDs,10,true);
//        die('stop');


//        return $this->render('outbound/pick-list-pdf', [
//            'outboundOrders' => $outboundOrders,
//            'crossDockOrders' => $crossDockOrders,
//        ]);
    }

    public function actionOutboundMinsk()
    {
        $routeDirectionService = new \common\modules\city\RouteDirection\service\Service();
        $pageSize = 9999;
        $clientId = 2;

//        $storeAdkUstKamen = [10];  // 10  АДК	Усть-Каменогорск
//        $storeSemey = [198]; // 198  	Семей

//        $toPointId = ArrayHelper::merge(
//            $routeDirectionService->getWestStore(),
//            $routeDirectionService->getNorthStore(),
//            $routeDirectionService->getSouthStore(),
//            $routeDirectionService->getEasternStore()
//        );
        $toPointId = $routeDirectionService->getBelarusStore();


        $searchModelOutbound = new OutboundOrderGridSearch();
        $dataProviderOutbound = $searchModelOutbound->search(Yii::$app->request->queryParams);
        $dataProviderOutbound->pagination->pageSize = $pageSize;
        $dataProviderOutbound->query->andWhere(['status'=>Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API]);
        $dataProviderOutbound->query->andWhere(['client_id'=>$clientId]);
        $dataProviderOutbound->query->andWhere(['to_point_id'=>$toPointId]);
//        $dataProviderOutbound->query->orderBy(['updated_at'=>SORT_DESC]);
        $dataProviderOutbound->query->orderBy(['packing_date'=>SORT_DESC]);
        $dataProviderOutbound->query->limit(600);

        $searchModelCrossDock = new CrossDockSearch();
        $dataProviderCrossDock = $searchModelCrossDock->search(Yii::$app->request->queryParams);
        $dataProviderCrossDock->query->andWhere(['to_point_id'=>$toPointId]);
        $dataProviderCrossDock->pagination->pageSize = $pageSize;
        $dataProviderCrossDock->query->andWhere(['client_id'=>$clientId]);
        $dataProviderCrossDock->query->andWhere(['status'=>Stock::STATUS_CROSS_DOCK_COMPLETE]);
//        $dataProviderCrossDock->query->andWhere(['NOT IN','id',[1047,999,454,453,444,44,103,99,98,89,85,84,75,68,69,59]]);

        $dataProviderCrossDock->query->orderBy(['accepted_datetime'=>SORT_DESC]);
        $dataProviderCrossDock->query->limit(200);
//        $dataProviderCrossDock->query->orderBy(['updated_at'=>SORT_DESC]);


        $clientsArray = Client::getActiveWMSItems();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();
        $stock = new Stock();
        $statusArray = $stock->getStatusArray();
        $statusCargoArray = $searchModelOutbound->getCargoStatusArray();

        $allModels = ArrayHelper::merge(
            $dataProviderOutbound->query->asArray()->all(),
            $dataProviderCrossDock->query->asArray()->all()
        );

        $arrayDataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
            'key'=> function($data) {
                if(isset($data['internal_barcode'])) {
                    return 'cd'.$data['id'];
                }
//
                return 'ob'.$data['id'];
            },
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            //'sort' => [//
            //    'attributes' => [
            //         'created_at',
            ///         'cargo_status',
//],
            //  ],
        ]);



        return $this->render('outbound-minsk/index', [
//            'searchModel' => $allModels,
            'dataProvider' => $arrayDataProvider,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
            'statusArray' => $statusArray,
            'statusCargoArray' => $statusCargoArray,
        ]);
    }



    public function actionPrintBelarus()
    {
        $ids = explode(',',Yii::$app->request->get('ids'));
        $outboundIDs = [];
        $crossDockIDs = [];
        $typeCrossDock = 'cd';
        $typeOutbound = 'ob';

        foreach($ids as $id) {
            $type = substr($id,0,2);
            if($type == $typeOutbound) {
                $outboundIDs [] = str_replace($typeOutbound,'',$id);
            }
            if($type == $typeCrossDock) {
                $crossDockIDs [] = str_replace($typeCrossDock,'',$id);
            }
        }

        $outboundOrders = OutboundOrder::find()->andWhere(['id'=>$outboundIDs])->all();
        $shopList = [];
        $boxList = [];
        foreach ($outboundOrders as $order) {
            $store = \common\modules\store\models\Store::findOne($order->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');
            $shopList [] = [
                'id'=>$order->id,
                'orderNumber'=>$order->order_number,
                'parentOrderNumber'=>$order->parent_order_number,
                'shopName'=>$shopToTitle,
                'acceptedNumberPlaceQty'=>$order->accepted_number_places_qty,
            ];


            $boxOnStock = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode, box_kg')
                ->andWhere([
                    'outbound_order_id' => $order->id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                ])
                //->andWhere('box_size_barcode != ""')
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnStock as $box) {

                $stockIdMapBoxBarcode = \yii\helpers\ArrayHelper::map([$box],'id','box_barcode');
                $boxAndLcBarcode =  \common\modules\outbound\service\OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode);
                $boxBarcode = \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.our_box');
                $boxBarcodeLC =  \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.client_box');

                $boxSize = empty($box['box_size_barcode']) ? 32 : $box['box_size_barcode'];
                $boxSize =  \common\components\BarcodeManager::mapM3ToBoxSize($boxSize);

                $boxList [] = [
                    'boxIndex'=>++$boxIndex,
                    'orderNumber'=>$order->order_number,
                    'parentOrderNumber'=>$order->parent_order_number,
                    'shopName'=>$shopToTitle,
                    'boxBarcode'=>$boxBarcode,
                    'boxSize'=>$boxSize,
                    'boxBarcodeLC'=>$boxBarcodeLC,
                    'createdAt'=>$order->created_at,
                    'boxKg'=>$box['box_kg'],
                ];
            }
        }


        $crossDockOrders = CrossDock::find()->andWhere(['id'=>$crossDockIDs])->all();
        foreach ($crossDockOrders as $crossDock) {

            $store = \common\modules\store\models\Store::findOne($crossDock->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');
            $shopList [] = [
                'id'=>$crossDock->id,
                'partyNumber'=>$crossDock->party_number,
                'internalBarcode'=> ltrim($crossDock->internal_barcode,'2-'),
                'shopName'=>$shopToTitle,
                'acceptedNumberPlaceQty'=>$crossDock->accepted_number_places_qty,
            ];


            $boxOnCrossDock = CrossDockItems::find()
                ->select('box_barcode, box_m3,weight_brut')
                ->andWhere([
                    'cross_dock_id' => $crossDock->id,
                    'status' => [
                        Stock::STATUS_CROSS_DOCK_SCANNED,
                        0,
                    ]
                ])
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnCrossDock as $box) {

                $boxBarcodeLC = $box['box_barcode'];
                //$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize(\common\components\BarcodeManager::getIntegerM3($box['box_m3']));
				
				
				$integerM3BoxM3 = \common\components\BarcodeManager::getIntegerM3($box['box_m3']);
				if (empty($integerM3BoxM3)) {
					file_put_contents("empty-getIntegerM3.log","empty-boxOnCrossDock-box-m3: ".$boxBarcodeLC."\n",FILE_APPEND);
					$integerM3BoxM3 = null;
				}

				$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize($integerM3BoxM3);
				

                $boxList [] = [
                    'boxIndex' => ++$boxIndex,
                    'orderNumber' => $crossDock->party_number,
                    'parentOrderNumber' => ltrim($crossDock->internal_barcode,'2-'),
                    'shopName' => $shopToTitle,
                    'boxBarcode' => $boxBarcodeLC,
                    'boxSize' => $boxSize,
                    'boxBarcodeLC' => $boxBarcodeLC,
                    'createdAt'=>$crossDock->created_at,
                    'boxKg'=>$box['weight_brut'],
                ];
            }
        }
        $objPHPExcel = new Spreadsheet();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('АКТ');
//            ->setTitle('report-' . date('d.m.Y'));

        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $iCell = 2;
        $activeSheet->setCellValue('D' . $iCell, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +

        $iCell += 2;
        $activeSheet->setCellValue('A' . $iCell,'г.Алматы');
        $activeSheet->setCellValue('H' . $iCell,date('d.m.Y'));
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize(10);

        $iCell += 1;
        $activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "Warehouse Management IAA" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "Warehouse Management IAA" принял груз согласно перечню:');
        $activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
        $activeSheet->mergeCells('A'.$iCell.':H'.($iCell+=4));
        $iCell += 3;
        $borderCellStart = 'B' . $iCell;
        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/forms','№'));
        $activeSheet->getStyle('B' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','Магазин получатель'));
        $activeSheet->getStyle('C' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

        $activeSheet->setCellValue('F' . $iCell, Yii::t('outbound/titles','Количество коробок'));
        $activeSheet->getStyle('F' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

        $i = 0;
        foreach ($shopList as $shopRow) {
            $iCell += 1;
            $activeSheet->setCellValue('B' . $iCell, ++$i);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


            $activeSheet->setCellValue('C' . $iCell, $shopRow['shopName']);
            $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('F' . $iCell, $shopRow['acceptedNumberPlaceQty']);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);
        }

        $borderCellEnd = 'G' . $iCell;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        $activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
        $activeSheet->setCellValue('A' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('A' . ($iCell+8), 'Подпись _________'); // +

        $activeSheet->setCellValue('F' . ($iCell+4), 'Груз принял'); // +
        $activeSheet->setCellValue('E' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('E' . ($iCell+8), 'Подпись _________'); // +
        $activeSheet->setCellValue('B' . ($iCell+10), 'М.П'); // +
        $activeSheet->setCellValue('F' . ($iCell+10), 'М.П'); // +

        $iCell += 15;
        /////// Begin
        $iCell = 1;
        $objPHPExcel->createSheet()->setTitle('СПИСОК ЗАКАЗОВ');
        $objPHPExcel->setActiveSheetIndex(1);
        $activeSheet =  $objPHPExcel->getActiveSheet();
        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);


        ////// End
        $cellFontSize = 7;
        $borderCellStart = 'A' . $iCell;
        $activeSheet->setCellValue('A' . $iCell,'№');
        $activeSheet->getStyle('A' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $activeSheet->getStyle('A' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('B' . $iCell, '№ Заказа');
        $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('C' . $iCell, 'Магазин');
        $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('D' . $iCell, '№ Короба');
        $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('E' . $iCell, 'ШК короба');
        $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('F' . $iCell, 'Размер короба');
        $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('G' . $iCell, 'LC');
        $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('H' . $iCell, 'Received Date by DC');
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('I' . $iCell, 'Truck Exit Date From DC');
        $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('J' . $iCell, 'Box weight');
        $activeSheet->getStyle('J' . $iCell)->getFont()->setSize($cellFontSize);


        $activeSheet->getColumnDimension('A')->setAutoSize(false);
        $activeSheet->getColumnDimension('A')->setWidth(4);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        $activeSheet->getColumnDimension('F')->setAutoSize(true);
        $activeSheet->getColumnDimension('G')->setAutoSize(true);

        $activeSheet->getColumnDimension('H')->setAutoSize(false);
        $activeSheet->getColumnDimension('H')->setWidth(19);

        $activeSheet->getColumnDimension('I')->setAutoSize(false);
        $activeSheet->getColumnDimension('I')->setWidth(21);

        $activeSheet->getColumnDimension('J')->setAutoSize(false);
        $activeSheet->getColumnDimension('J')->setWidth(21);

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        $i = 0;
        $cellFontSize = 7;
        foreach ($boxList as $boxRow) {
            $iCell += 1;
            $activeSheet->setCellValue('A' . $iCell,++$i);
            $activeSheet->getStyle('A' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValue('B' . $iCell,$boxRow['parentOrderNumber']);
            $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//            $activeSheet->mergeCells('B'.$iCell.':C'.$iCell);

            $activeSheet->setCellValue('C' . $iCell,$boxRow['shopName']);
            $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('C' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('D'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('D' . $iCell,$boxRow['boxIndex']);
            $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('D' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

            $activeSheet->setCellValueExplicit('E' . $iCell,$boxRow['boxBarcode'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('E' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('H'.$iCell.':I'.$iCell);

            $activeSheet->setCellValue('F' . $iCell,$boxRow['boxSize']);
            $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('J'.$iCell.':K'.$iCell);

            $activeSheet->setCellValueExplicit('G' . $iCell,$boxRow['boxBarcodeLC'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('G' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('L'.$iCell.':M'.$iCell);

            $activeSheet->setCellValueExplicit('H' . $iCell, Yii::$app->formatter->asDatetime($boxRow['createdAt'],$asDatetimeFormat) ,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('H' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('I' . $iCell, Yii::$app->formatter->asDatetime(time(),$asDatetimeFormat),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
//            $activeSheet->setCellValueExplicit('I' . $iCell,date('d.m.Y H:i'),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('I' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('J' . $iCell, $boxRow['boxKg'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('J' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('J' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $borderCellEnd = 'I' . $iCell;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-belarus-report-' . $iCell . '.xlsx"');
        header('Cache-Control: max-age=0');

//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        $objWriter->save('php://output');


//        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($objPHPExcel);
//        $writer->writeAllSheets();
//        $writer->save("05featuredemo.pdf");


        $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $writer->save('php://output');
//        exit;

        Yii::$app->end();
        return true;
    }

    public function actionOutboundRussia()
    {
        $routeDirectionService = new \common\modules\city\RouteDirection\service\Service();
        $pageSize = 9999;
        $clientId = 2;

//        $storeAdkUstKamen = [10];  // 10  АДК	Усть-Каменогорск
//        $storeSemey = [198]; // 198  	Семей

//        $toPointId = ArrayHelper::merge(
//            $routeDirectionService->getWestStore(),
//            $routeDirectionService->getNorthStore(),
//            $routeDirectionService->getSouthStore(),
//            $routeDirectionService->getEasternStore()
//        );
        $toPointId = $routeDirectionService->getRussiaStore();


        $searchModelOutbound = new OutboundOrderGridSearch();
        $dataProviderOutbound = $searchModelOutbound->search(Yii::$app->request->queryParams);
        $dataProviderOutbound->pagination->pageSize = $pageSize;
        $dataProviderOutbound->query->andWhere(['status'=>Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API]);
        $dataProviderOutbound->query->andWhere(['client_id'=>$clientId]);
        $dataProviderOutbound->query->andWhere(['to_point_id'=>$toPointId]);
//        $dataProviderOutbound->query->orderBy(['updated_at'=>SORT_DESC]);
        $dataProviderOutbound->query->orderBy(['packing_date'=>SORT_DESC]);
        $dataProviderOutbound->query->limit(600);

        $searchModelCrossDock = new CrossDockSearch();
        $dataProviderCrossDock = $searchModelCrossDock->search(Yii::$app->request->queryParams);
        $dataProviderCrossDock->query->andWhere(['to_point_id'=>$toPointId]);
        $dataProviderCrossDock->pagination->pageSize = $pageSize;
        $dataProviderCrossDock->query->andWhere(['client_id'=>$clientId]);
        $dataProviderCrossDock->query->andWhere(['status'=>Stock::STATUS_CROSS_DOCK_COMPLETE]);
        $dataProviderCrossDock->query->andWhere(['NOT IN','id',[1050,1002]]);

        $dataProviderCrossDock->query->orderBy(['accepted_datetime'=>SORT_DESC]);
        $dataProviderCrossDock->query->limit(200);
//        $dataProviderCrossDock->query->orderBy(['updated_at'=>SORT_DESC]);


        $clientsArray = Client::getActiveWMSItems();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();
        $stock = new Stock();
        $statusArray = $stock->getStatusArray();
        $statusCargoArray = $searchModelOutbound->getCargoStatusArray();

        $allModels = ArrayHelper::merge(
            $dataProviderOutbound->query->asArray()->all(),
            $dataProviderCrossDock->query->asArray()->all()
        );

        $arrayDataProvider = new ArrayDataProvider([
            'allModels' => $allModels,
            'key'=> function($data) {
                if(isset($data['internal_barcode'])) {
                    return 'cd'.$data['id'];
                }
//
                return 'ob'.$data['id'];
            },
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            //'sort' => [//
            //    'attributes' => [
            //         'created_at',
            ///         'cargo_status',
//],
            //  ],
        ]);



        return $this->render('outbound-russia/index', [
//            'searchModel' => $allModels,
            'dataProvider' => $arrayDataProvider,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
            'statusArray' => $statusArray,
            'statusCargoArray' => $statusCargoArray,
        ]);
    }



    public function actionPrintRussia()
    {
        $ids = explode(',',Yii::$app->request->get('ids'));
        $outboundIDs = [];
        $crossDockIDs = [];
        $typeCrossDock = 'cd';
        $typeOutbound = 'ob';

        foreach($ids as $id) {
            $type = substr($id,0,2);
            if($type == $typeOutbound) {
                $outboundIDs [] = str_replace($typeOutbound,'',$id);
            }
            if($type == $typeCrossDock) {
                $crossDockIDs [] = str_replace($typeCrossDock,'',$id);
            }
        }

        $outboundOrders = OutboundOrder::find()->andWhere(['id'=>$outboundIDs])->all();
        $shopList = [];
        $boxList = [];
        foreach ($outboundOrders as $order) {
            $store = \common\modules\store\models\Store::findOne($order->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');
            $shopList [] = [
                'id'=>$order->id,
                'orderNumber'=>$order->order_number,
                'parentOrderNumber'=>$order->parent_order_number,
                'shopName'=>$shopToTitle,
                'acceptedNumberPlaceQty'=>$order->accepted_number_places_qty,
            ];


            $boxOnStock = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode, box_kg')
                ->andWhere([
                    'outbound_order_id' => $order->id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                ])
                //->andWhere('box_size_barcode != ""')
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnStock as $box) {

                $stockIdMapBoxBarcode = \yii\helpers\ArrayHelper::map([$box],'id','box_barcode');
                $boxAndLcBarcode =  \common\modules\outbound\service\OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode);
                $boxBarcode = \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.our_box');
                $boxBarcodeLC =  \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.client_box');

                $boxSize = empty($box['box_size_barcode']) ? 32 : $box['box_size_barcode'];
                $boxSize =  \common\components\BarcodeManager::mapM3ToBoxSize($boxSize);

                $boxList [] = [
                    'boxIndex'=>++$boxIndex,
                    'orderNumber'=>$order->order_number,
                    'parentOrderNumber'=>$order->parent_order_number,
                    'shopName'=>$shopToTitle,
                    'boxBarcode'=>$boxBarcode,
                    'boxSize'=>$boxSize,
                    'boxBarcodeLC'=>$boxBarcodeLC,
                    'createdAt'=>$order->created_at,
                    'boxKg'=>$box['box_kg'],
                ];
            }
        }


        $crossDockOrders = CrossDock::find()->andWhere(['id'=>$crossDockIDs])->all();
        foreach ($crossDockOrders as $crossDock) {

            $store = \common\modules\store\models\Store::findOne($crossDock->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default-2');
            $shopList [] = [
                'id'=>$crossDock->id,
                'partyNumber'=>$crossDock->party_number,
                'internalBarcode'=> ltrim($crossDock->internal_barcode,'2-'),
                'shopName'=>$shopToTitle,
                'acceptedNumberPlaceQty'=>$crossDock->accepted_number_places_qty,
            ];


            $boxOnCrossDock = CrossDockItems::find()
                ->select('box_barcode, box_m3,weight_brut')
                ->andWhere([
                    'cross_dock_id' => $crossDock->id,
                    'status' => [
                        Stock::STATUS_CROSS_DOCK_SCANNED,
                        0,
                    ]
                ])
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnCrossDock as $box) {

                $boxBarcodeLC = $box['box_barcode'];
                //$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize(\common\components\BarcodeManager::getIntegerM3($box['box_m3']));
				
				
				$integerM3BoxM3 = \common\components\BarcodeManager::getIntegerM3($box['box_m3']);
				if (empty($integerM3BoxM3)) {
					file_put_contents("empty-getIntegerM3.log","empty-boxOnCrossDock-box-m3: ".$boxBarcodeLC."\n",FILE_APPEND);
					$integerM3BoxM3 = null;
				}

				$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize($integerM3BoxM3);
				

                $boxList [] = [
                    'boxIndex' => ++$boxIndex,
                    'orderNumber' => $crossDock->party_number,
                    'parentOrderNumber' => ltrim($crossDock->internal_barcode,'2-'),
                    'shopName' => $shopToTitle,
                    'boxBarcode' => $boxBarcodeLC,
                    'boxSize' => $boxSize,
                    'boxBarcodeLC' => $boxBarcodeLC,
                    'createdAt'=>$crossDock->created_at,
                    'boxKg'=>$box['weight_brut'],
                ];
            }
        }
        $objPHPExcel = new Spreadsheet();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('АКТ');
//            ->setTitle('report-' . date('d.m.Y'));

        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

        $iCell = 2;
        $activeSheet->setCellValue('D' . $iCell, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +

        $iCell += 2;
        $activeSheet->setCellValue('A' . $iCell,'г.Алматы');
        $activeSheet->setCellValue('H' . $iCell,date('d.m.Y'));
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize(10);

        $iCell += 1;
        $activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "Warehouse Management IAA" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "Warehouse Management IAA" принял груз согласно перечню:');
        $activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
        $activeSheet->mergeCells('A'.$iCell.':H'.($iCell+=4));
        $iCell += 3;
        $borderCellStart = 'B' . $iCell;
        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/forms','№'));
        $activeSheet->getStyle('B' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','Магазин получатель'));
        $activeSheet->getStyle('C' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

        $activeSheet->setCellValue('F' . $iCell, Yii::t('outbound/titles','Количество коробок'));
        $activeSheet->getStyle('F' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

        $i = 0;
        foreach ($shopList as $shopRow) {
            $iCell += 1;
            $activeSheet->setCellValue('B' . $iCell, ++$i);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


            $activeSheet->setCellValue('C' . $iCell, $shopRow['shopName']);
            $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('F' . $iCell, $shopRow['acceptedNumberPlaceQty']);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);
        }

        $borderCellEnd = 'G' . $iCell;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        $activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
        $activeSheet->setCellValue('A' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('A' . ($iCell+8), 'Подпись _________'); // +

        $activeSheet->setCellValue('F' . ($iCell+4), 'Груз принял'); // +
        $activeSheet->setCellValue('E' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('E' . ($iCell+8), 'Подпись _________'); // +
        $activeSheet->setCellValue('B' . ($iCell+10), 'М.П'); // +
        $activeSheet->setCellValue('F' . ($iCell+10), 'М.П'); // +

        $iCell += 15;
        /////// Begin
        $iCell = 1;
        $objPHPExcel->createSheet()->setTitle('СПИСОК ЗАКАЗОВ');
        $objPHPExcel->setActiveSheetIndex(1);
        $activeSheet =  $objPHPExcel->getActiveSheet();
        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);


        ////// End
        $cellFontSize = 7;
        $borderCellStart = 'A' . $iCell;
        $activeSheet->setCellValue('A' . $iCell,'№');
        $activeSheet->getStyle('A' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $activeSheet->getStyle('A' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('B' . $iCell, '№ Заказа');
        $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('C' . $iCell, 'Магазин');
        $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('D' . $iCell, '№ Короба');
        $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('E' . $iCell, 'ШК короба');
        $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('F' . $iCell, 'Размер короба');
        $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('G' . $iCell, 'LC');
        $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('H' . $iCell, 'Received Date by DC');
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('I' . $iCell, 'Truck Exit Date From DC');
        $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('J' . $iCell, 'Box weight');
        $activeSheet->getStyle('J' . $iCell)->getFont()->setSize($cellFontSize);


        $activeSheet->getColumnDimension('A')->setAutoSize(false);
        $activeSheet->getColumnDimension('A')->setWidth(4);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        $activeSheet->getColumnDimension('F')->setAutoSize(true);
        $activeSheet->getColumnDimension('G')->setAutoSize(true);

        $activeSheet->getColumnDimension('H')->setAutoSize(false);
        $activeSheet->getColumnDimension('H')->setWidth(19);

        $activeSheet->getColumnDimension('I')->setAutoSize(false);
        $activeSheet->getColumnDimension('I')->setWidth(21);

        $activeSheet->getColumnDimension('J')->setAutoSize(false);
        $activeSheet->getColumnDimension('J')->setWidth(21);

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        $i = 0;
        $cellFontSize = 7;
        foreach ($boxList as $boxRow) {
            $iCell += 1;
            $activeSheet->setCellValue('A' . $iCell,++$i);
            $activeSheet->getStyle('A' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValue('B' . $iCell,$boxRow['parentOrderNumber']);
            $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//            $activeSheet->mergeCells('B'.$iCell.':C'.$iCell);

            $activeSheet->setCellValue('C' . $iCell,$boxRow['shopName']);
            $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('C' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('D'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('D' . $iCell,$boxRow['boxIndex']);
            $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('D' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

            $activeSheet->setCellValueExplicit('E' . $iCell,$boxRow['boxBarcode'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('E' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('H'.$iCell.':I'.$iCell);

            $activeSheet->setCellValue('F' . $iCell,$boxRow['boxSize']);
            $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('J'.$iCell.':K'.$iCell);

            $activeSheet->setCellValueExplicit('G' . $iCell,$boxRow['boxBarcodeLC'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('G' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('L'.$iCell.':M'.$iCell);

            $activeSheet->setCellValueExplicit('H' . $iCell, Yii::$app->formatter->asDatetime($boxRow['createdAt'],$asDatetimeFormat) ,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('H' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('I' . $iCell, Yii::$app->formatter->asDatetime(time(),$asDatetimeFormat),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
//            $activeSheet->setCellValueExplicit('I' . $iCell,date('d.m.Y H:i'),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('I' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('J' . $iCell, $boxRow['boxKg'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('J' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('J' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $borderCellEnd = 'I' . $iCell;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-russia-report-' . $iCell . '.xlsx"');
        header('Cache-Control: max-age=0');

//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        $objWriter->save('php://output');


//        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($objPHPExcel);
//        $writer->writeAllSheets();
//        $writer->save("05featuredemo.pdf");


        $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $writer->save('php://output');
//        exit;

        Yii::$app->end();
        return true;
    }


    public function actionPrint_old() {
        $ids = explode(',',Yii::$app->request->get('ids'));
        $outboundIDs = [];
        $crossDockIDs = [];
        $typeCrossDock = 'cd';
        $typeOutbound = 'ob';

        foreach($ids as $id) {
            $type = substr($id,0,2);
            if($type == $typeOutbound) {
                $outboundIDs [] = str_replace($typeOutbound,'',$id);
            }
            if($type == $typeCrossDock) {
                $crossDockIDs [] = str_replace($typeCrossDock,'',$id);
            }
        }

        $outboundOrders = OutboundOrder::find()->andWhere(['id'=>$outboundIDs])->all();
        $shopList = [];
        $shopTotalQty = [];
        $boxList = [];
        $boxTotalQty = 0;
        foreach ($outboundOrders as $order) {
            $store = \common\modules\store\models\Store::findOne($order->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default');
            $shopList [] = [
             'id'=>$order->id,
             'orderNumber'=>$order->order_number,
             'parentOrderNumber'=>$order->parent_order_number,
             'shopName'=>$shopToTitle,
             'acceptedNumberPlaceQty'=>$order->accepted_number_places_qty,
            ];


            $boxOnStock = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode')
                ->andWhere([
                    'outbound_order_id' => $order->id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                ])
                ->andWhere('box_size_barcode != ""')
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnStock as $box) {

                $stockIdMapBoxBarcode = \yii\helpers\ArrayHelper::map([$box],'id','box_barcode');
                $boxAndLcBarcode =  \common\modules\outbound\service\OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode);
                $boxBarcode = \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.our_box');
                $boxBarcodeLC =  \yii\helpers\ArrayHelper::getValue($boxAndLcBarcode,'0.client_box');
                $boxSize =  \common\components\BarcodeManager::mapM3ToBoxSize($box['box_size_barcode']);

                $boxList [] = [
                    'boxIndex'=>++$boxIndex,
                    'orderNumber'=>$order->order_number,
                    'parentOrderNumber'=>$order->parent_order_number,
                    'shopName'=>$shopToTitle,
                    'boxBarcode'=>$boxBarcode,
                    'boxSize'=>$boxSize,
                    'boxBarcodeLC'=>$boxBarcodeLC,
                ];
            }
        }


        $crossDockOrders = CrossDock::find()->andWhere(['id'=>$crossDockIDs])->all();
        foreach ($crossDockOrders as $crossDock) {

            $store = \common\modules\store\models\Store::findOne($crossDock->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('default');
            $shopList [] = [
                'id'=>$crossDock->id,
                'partyNumber'=>$crossDock->party_number,
                'internalBarcode'=>$crossDock->internal_barcode,
                'shopName'=>$shopToTitle,
                'acceptedNumberPlaceQty'=>$crossDock->accepted_number_places_qty,
            ];


            $boxOnCrossDock = CrossDockItems::find()
                ->select('box_barcode, box_m3')
                ->andWhere([
                    'cross_dock_id' => $crossDock->id,
                    'status' => [
                        Stock::STATUS_CROSS_DOCK_SCANNED,
                        0,
                    ]
                ])
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->all();

            $boxIndex = 0;
            foreach($boxOnCrossDock as $box) {

                $boxBarcodeLC = $box['box_barcode'];
               // $boxSize = \common\components\BarcodeManager::mapM3ToBoxSize(\common\components\BarcodeManager::getIntegerM3($box['box_m3']));
			   
			   
				$integerM3BoxM3 = \common\components\BarcodeManager::getIntegerM3($box['box_m3']);
				if (empty($integerM3BoxM3)) {
					file_put_contents("empty-getIntegerM3.log","empty-boxOnCrossDock-box-m3: ".$boxBarcodeLC."\n",FILE_APPEND);
					$integerM3BoxM3 = null;
				}

				$boxSize = \common\components\BarcodeManager::mapM3ToBoxSize($integerM3BoxM3);

                $boxList [] = [
                    'boxIndex' => ++$boxIndex,
                    'orderNumber' => $crossDock->party_number,
                    'parentOrderNumber' => $crossDock->internal_barcode,
                    'shopName' => $shopToTitle,
                    'boxBarcode' => $boxBarcodeLC,
                    'boxSize' => $boxSize,
                    'boxBarcodeLC' => $boxBarcodeLC,
                ];
            }
        }

        $objPHPExcel = new Spreadsheet();
//        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-' . date('d.m.Y'));

        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
//            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);


        $iCell = 2;
        $activeSheet->setCellValue('D' . $iCell, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +

        $iCell += 2;
        $activeSheet->setCellValue('A' . $iCell,'г.Алматы');
        $activeSheet->setCellValue('H' . $iCell,date('d.m.Y'));
        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize(10);

        $iCell += 1;
        $activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "Eurasia Trans Team" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "Eurasia Trans Team" принял груз согласно перечню:');
        $activeSheet->getStyle('A' . $iCell)->getAlignment()->setWrapText(true);
        $activeSheet->mergeCells('A'.$iCell.':H'.($iCell+=3));
        $iCell += 3;
        $borderCellStart = 'B' . $iCell;
        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/forms','№'));
        $activeSheet->getStyle('B' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','Магазин получатель'));
        $activeSheet->getStyle('C' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

        $activeSheet->setCellValue('F' . $iCell, Yii::t('outbound/titles','Количество коробок'));
        $activeSheet->getStyle('F' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


        $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

        $i = 0;
        foreach ($shopList as $shopRow) {
            $iCell += 1;
            $activeSheet->setCellValue('B' . $iCell, ++$i);
            $activeSheet->getStyle('B' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


            $activeSheet->setCellValue('C' . $iCell, $shopRow['shopName']);
            $activeSheet->mergeCells('C'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('F' . $iCell, $shopRow['acceptedNumberPlaceQty']);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);
        }

        $borderCellEnd = 'G' . $iCell;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

         $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        $activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
        $activeSheet->setCellValue('A' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('A' . ($iCell+8), 'Подпись _________'); // +

        $activeSheet->setCellValue('F' . ($iCell+4), 'Груз принял'); // +
        $activeSheet->setCellValue('E' . ($iCell+6), 'ФИО _________________________'); // +
        $activeSheet->setCellValue('E' . ($iCell+8), 'Подпись _________'); // +
        $activeSheet->setCellValue('B' . ($iCell+10), 'М.П'); // +
        $activeSheet->setCellValue('F' . ($iCell+10), 'М.П'); // +

        $iCell += 15;
        $borderCellStart = 'A' . $iCell;

        $activeSheet->setCellValue('A' . $iCell,'№');
        $activeSheet->getStyle('A' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);


        $activeSheet->setCellValue('B' . $iCell, '№ Заказа');


        $activeSheet->setCellValue('C' . $iCell, 'Магазин');
//        $activeSheet->setCellValue('D' . $iCell, 'Магазин');
//        $activeSheet->mergeCells('D'.$iCell.':E'.$iCell);

        $activeSheet->setCellValue('D' . $iCell, '№ Короба');
//        $activeSheet->setCellValue('F' . $iCell, '№ Короба');
//        $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

        $activeSheet->setCellValue('E' . $iCell, 'ШК короба');
//        $activeSheet->setCellValue('G' . $iCell, 'ШК короба');
//        $activeSheet->mergeCells('H'.$iCell.':I'.$iCell);

        $activeSheet->setCellValue('F' . $iCell, 'Размер короба');
//        $activeSheet->setCellValue('J' . $iCell, 'Размер короба');
//        $activeSheet->mergeCells('J'.$iCell.':K'.$iCell);

        $activeSheet->setCellValue('G' . $iCell, 'LC');
//        $activeSheet->setCellValue('L' . $iCell, 'LC');
//        $activeSheet->mergeCells('L'.$iCell.':M'.$iCell);


//        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
//        $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
        $activeSheet->getColumnDimension('B')->setWidth(12);
        $activeSheet->getColumnDimension('C')->setWidth(18);
        $activeSheet->getColumnDimension('D')->setWidth(12);
        $activeSheet->getColumnDimension('E')->setWidth(12);
        $activeSheet->getColumnDimension('F')->setWidth(15);
        $activeSheet->getColumnDimension('G')->setWidth(12);
        $i = 0;
        $cellFontSize = 8;
        foreach ($boxList as $boxRow) {
            $iCell += 1;
            $activeSheet->setCellValue('A' . $iCell,++$i);
            $activeSheet->getStyle('A' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValue('B' . $iCell,$boxRow['orderNumber']);
            $activeSheet->getStyle('B' . $iCell)->getFont()->setSize($cellFontSize);
//            $activeSheet->mergeCells('B'.$iCell.':C'.$iCell);

            $activeSheet->setCellValue('C' . $iCell,$boxRow['shopName']);
            $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);
//            $activeSheet->mergeCells('D'.$iCell.':E'.$iCell);

            $activeSheet->setCellValue('D' . $iCell,$boxRow['boxIndex']);
            $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('D' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
//            $activeSheet->mergeCells('F'.$iCell.':G'.$iCell);

            $activeSheet->setCellValue('E' . $iCell,$boxRow['boxBarcode']);
            $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);
//            $activeSheet->mergeCells('H'.$iCell.':I'.$iCell);

            $activeSheet->setCellValue('F' . $iCell,$boxRow['boxSize']);
            $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);
//            $activeSheet->mergeCells('J'.$iCell.':K'.$iCell);

            $activeSheet->setCellValue('G' . $iCell,$boxRow['boxBarcodeLC']);
            $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);
//            $activeSheet->mergeCells('L'.$iCell.':M'.$iCell);
        }

        $borderCellEnd = 'G' . $iCell;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
//                    'color' => ['argb' => FFFF0000],
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-report-' . $iCell . '.xlsx"');
        header('Cache-Control: max-age=0');

//        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//        $objWriter->save('php://output');


        $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $writer->save('php://output');
//        exit;

        Yii::$app->end();
        return true;








//        VarDumper::dump($outboundIDs,10,true);
//        VarDumper::dump($crossDockIDs,10,true);
//        die('stop');


//        return $this->render('outbound/pick-list-pdf', [
//            'outboundOrders' => $outboundOrders,
//            'crossDockOrders' => $crossDockOrders,
//        ]);
    }
}
