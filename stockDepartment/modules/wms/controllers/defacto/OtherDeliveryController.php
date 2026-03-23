<?php

namespace app\modules\wms\controllers\defacto;

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

class OtherDeliveryController extends \stockDepartment\components\Controller
{
    public function actionOutbound()
    {
        $routeDirectionService = new \common\modules\city\RouteDirection\service\Service();
        $pageSize = 200;
        $clientId = 2;
//        $toPointId = $routeDirectionService->getWestStore();
//        $toPointId = $routeDirectionService->getNorthStore();

        $storeAdkUstKamen = [10];  // 10  АДК	Усть-Каменогорск
        $storeSemey = [198]; // 198  	Семей
        $toPointId = ArrayHelper::merge($routeDirectionService->getWestStore(),$routeDirectionService->getNorthStore(),$storeAdkUstKamen,$storeSemey);

        $searchModelOutbound = new OutboundOrderGridSearch();
        $dataProviderOutbound = $searchModelOutbound->search(Yii::$app->request->queryParams);
        $dataProviderOutbound->pagination->pageSize = $pageSize;
        $dataProviderOutbound->query->andWhere(['status'=>Stock::STATUS_OUTBOUND_PREPARED_DATA_FOR_API]);
        $dataProviderOutbound->query->andWhere(['client_id'=>$clientId]);
        $dataProviderOutbound->query->andWhere(['to_point_id'=>$toPointId]);
        $dataProviderOutbound->query->orderBy(['updated_at'=>SORT_DESC]);

        $searchModelCrossDock = new CrossDockSearch();
        $dataProviderCrossDock = $searchModelCrossDock->search(Yii::$app->request->queryParams);
        $dataProviderCrossDock->query->andWhere(['to_point_id'=>$toPointId]);
        $dataProviderCrossDock->pagination->pageSize = $pageSize;
        $dataProviderCrossDock->query->andWhere(['client_id'=>$clientId]);
        $dataProviderCrossDock->query->andWhere(['status'=>Stock::STATUS_CROSS_DOCK_COMPLETE]);
        $dataProviderCrossDock->query->andWhere(['NOT IN','id',[1047,999,454,453,444,44,103,99,98,89,85,84,75,68,69,59]]);
        $dataProviderCrossDock->query->orderBy(['updated_at'=>SORT_DESC]);


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
//            'searchModel' => $searchModel,
            'dataProvider' => $arrayDataProvider,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
            'statusArray' => $statusArray,
            'statusCargoArray' => $statusCargoArray,
        ]);
    }

    public function actionPrint()
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
                $boxSize = \common\components\BarcodeManager::mapM3ToBoxSize(\common\components\BarcodeManager::getIntegerM3($box['box_m3']));

                $boxList [] = [
                    'boxIndex' => ++$boxIndex,
                    'orderNumber' => $crossDock->party_number,
                    'parentOrderNumber' => ltrim($crossDock->internal_barcode,'2-'),
                    'shopName' => $shopToTitle,
                    'boxBarcode' => $boxBarcodeLC,
                    'boxSize' => $boxSize,
                    'boxBarcodeLC' => $boxBarcodeLC,
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
        $activeSheet->setCellValue('A' . $iCell,'Мы, нижеподписавшиеся представитель компании ТОО "Nomadex 3PL" и представитель компании ТОО "Eurasia Trans Team" составили настоящий акт о том, что представитель компании ТОО "Nomadex 3PL" передал, а представитель компании ТОО "Eurasia Trans Team" принял груз согласно перечню:');
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
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        $activeSheet->getColumnDimension('F')->setAutoSize(true);
        $activeSheet->getColumnDimension('G')->setAutoSize(true);
        $i = 0;
        $cellFontSize = 11;
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
                $boxSize = \common\components\BarcodeManager::mapM3ToBoxSize(\common\components\BarcodeManager::getIntegerM3($box['box_m3']));

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
