<?php

namespace app\modules\report\controllers\erenRetail;
//namespace app\modules\wms\controllers\carParts\main;

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
    public function actionIndex()
    {
        $pageSize = 9999;
        $clientId = 103;

        $searchModelOutbound = new OutboundOrderGridSearch();
        $dataProviderOutbound = $searchModelOutbound->search(Yii::$app->request->queryParams);
        $dataProviderOutbound->pagination->pageSize = $pageSize;
        //$dataProviderOutbound->query->andWhere(['status'=>Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL]);
        $dataProviderOutbound->query->andWhere(['client_id'=>$clientId]);
        $dataProviderOutbound->query->orderBy(['packing_date'=>SORT_DESC]);
        $dataProviderOutbound->query->limit(600);

        $clientsArray = Client::getActiveWMSItems();
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientId);
        $stock = new Stock();
        $statusArray = $stock->getStatusArray();
        $statusCargoArray = $searchModelOutbound->getCargoStatusArray();

        $arrayDataProvider = new ArrayDataProvider([
            'allModels' => $dataProviderOutbound->query->asArray()->all(),
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
        ]);



        return $this->render('index', [
			'searchModel' => $searchModelOutbound,
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
        $typeOutbound = 'ob';

        foreach($ids as $id) {
            $type = substr($id,0,2);
            if($type == $typeOutbound) {
                $outboundIDs [] = str_replace($typeOutbound,'',$id);
            }
        }

        $outboundOrders = OutboundOrder::find()->andWhere(['id'=>$outboundIDs])->all();
        $shopList = [];
        $boxList = [];
        foreach ($outboundOrders as $order) {
            $store = \common\modules\store\models\Store::findOne($order->to_point_id);
            $shopToTitle = $store->getPointTitleByPattern('full');

			if(isset($shopList[$shopToTitle])) {
				$shopList [$shopToTitle] ['acceptedNumberPlaceQty'] = $shopList [$shopToTitle] ['acceptedNumberPlaceQty'] + $order->accepted_number_places_qty;
				$shopList [$shopToTitle] ['forShowInReport'] [$order->parent_order_number]  =  $order->parent_order_number;
			} else {
				$shopList [$shopToTitle] = [
					'id' => $order->id,
					'orderNumber' => $order->order_number,
					'parentOrderNumber' => $order->parent_order_number,
					'shopName' => $shopToTitle,
					'acceptedNumberPlaceQty' => $order->accepted_number_places_qty,
				];
				$shopList [$shopToTitle] ['forShowInReport'] [$order->parent_order_number]  =  $order->parent_order_number;
			}


            $boxOnStock = Stock::find()
//                ->select('stock.id, box_barcode, box_kg, box_size_barcode')
				->select('box_barcode,product_barcode, count(product_barcode) as productQty')
                ->andWhere([
                    'outbound_order_id' => $order->id,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                ])
                ->groupBy('box_barcode,product_barcode')
                ->orderBy('box_barcode')
				->asArray()
                ->all();

			$boxIndex = [];
            foreach($boxOnStock as $productOnBox) {
				if(!isset($boxIndex[$productOnBox['box_barcode']])) {
					$boxIndex[$productOnBox['box_barcode']] = 1;
				}
                $boxList [] = [
                    'orderNumber'=>$order->order_number,
                    'parentOrderNumber'=>$order->parent_order_number,
                    'shopName'=>$shopToTitle,
                    'boxBarcode'=>$productOnBox['box_barcode'],
                    'productBarcode'=>$productOnBox['product_barcode'],
                    'productQty'=>$productOnBox['productQty'],
                    'createdAt'=>$order->created_at,
                    'boxIndex'=>count($boxIndex),
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

        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight(0.01);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft(0.3);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom(0.3);

		$fontSizeForHeader = 10;

        $iCell = 1;
        $borderCellStart = 'A' . $iCell;
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

        $activeSheet->setCellValue('E' . $iCell, Yii::t('outbound/titles','Кол-во'));
        $activeSheet->getStyle('E' . $iCell)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		  $activeSheet->setCellValue('F' . $iCell, Yii::t('outbound/titles','Номер заказа'));
		  $activeSheet->getStyle('F' . $iCell)
					  ->getAlignment()
					  ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		  $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($fontSizeForHeader);
		  $activeSheet->mergeCells('F'.$iCell.':J'.$iCell);


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

        $borderCellEnd = 'J' . $iCell;
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

         $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);

        /////// Begin
        $iCell = 1;
        $objPHPExcel->createSheet()->setTitle('СПИСОК ЗАКАЗОВ');
        $objPHPExcel->setActiveSheetIndex(1);
        $activeSheet =  $objPHPExcel->getActiveSheet();
        // PRINTING SETTING BEGIN
        $objPHPExcel->getActiveSheet()->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
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

        $activeSheet->setCellValue('F' . $iCell, 'ШК товара');
        $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);

        $activeSheet->setCellValue('G' . $iCell, 'Кол-во товаров');
        $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);

//        $activeSheet->setCellValue('H' . $iCell, 'Received Date by DC');
//        $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);
//
//        $activeSheet->setCellValue('I' . $iCell, 'Truck Exit Date From DC');
//        $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);


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

//        $asDatetimeFormat = 'php:d.m.Y H:i:s';
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

            $activeSheet->setCellValue('C' . $iCell,$boxRow['shopName']);
            $activeSheet->getStyle('C' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('C' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValue('D' . $iCell,$boxRow['boxIndex']);
            $activeSheet->getStyle('D' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('D' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('E' . $iCell,$boxRow['boxBarcode'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('E' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('E' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValue('F' . $iCell,$boxRow['productBarcode']);
            $activeSheet->getStyle('F' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('F' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $activeSheet->setCellValueExplicit('G' . $iCell,$boxRow['productQty'],\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $activeSheet->getStyle('G' . $iCell)->getFont()->setSize($cellFontSize);
            $activeSheet->getStyle('G' . $iCell)
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//            $activeSheet->setCellValueExplicit('H' . $iCell, Yii::$app->formatter->asDatetime($boxRow['createdAt'],$asDatetimeFormat) ,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
//            $activeSheet->getStyle('H' . $iCell)->getFont()->setSize($cellFontSize);
//            $activeSheet->getStyle('H' . $iCell)
//                ->getAlignment()
//                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

//            $activeSheet->setCellValueExplicit('I' . $iCell, Yii::$app->formatter->asDatetime(time(),$asDatetimeFormat),\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
//            $activeSheet->getStyle('I' . $iCell)->getFont()->setSize($cellFontSize);
//            $activeSheet->getStyle('I' . $iCell)
//                ->getAlignment()
//                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        }

        $borderCellEnd = 'I' . $iCell;

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $border = $borderCellStart.':'.$borderCellEnd;
        $activeSheet->getStyle($border)->applyFromArray($styleArray);



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-report-' . $iCell . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $writer->save('php://output');

        Yii::$app->end();
        return true;
    }
}
