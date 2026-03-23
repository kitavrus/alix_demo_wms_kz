<?php

namespace app\modules\outbound\controllers;

use common\modules\outbound\models\OutboundOrderItem;
use common\modules\stock\models\Stock;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use stockDepartment\modules\outbound\models\OutboundOrderItemSearch;
use stockDepartment\modules\outbound\models\OutboundOrderSearch;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\client\models\Client;
use Yii;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;


use common\modules\outbound\service\OutboundBoxService;
use common\components\BarcodeManager;
use yii\helpers\ArrayHelper;
use stockDepartment\modules\outbound\models\UpdateToPointForm;

class ReportController extends Controller
{
    /*
     * Index
     * */
    public function actionIndex()
    {
        $searchModel = new OutboundOrderGridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 25;

        $clientsArray = Client::getActiveWMSItems();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
        ]);
    }

    /*
     * Import to excel
     *
     **/
    public function actionExportToExcel()
    {
        $objPHPExcel = new \PHPExcel();

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


        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('B' . $i, 'Родительский номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('D' . $i, 'Куда'); // +
        $activeSheet->setCellValue('E' . $i, 'Объем (м3)'); // +
        $activeSheet->setCellValue('F' . $i, 'Вес (кг)'); // +
        $activeSheet->setCellValue('G' . $i, 'Отсканированное кол-во мест'); // +
        $activeSheet->setCellValue('H' . $i, 'Предполагаемое кол-во'); // +
        $activeSheet->setCellValue('I' . $i, 'Зарезервированое кол-во'); // +
        $activeSheet->setCellValue('J' . $i, 'Отсканированное кол-во'); // +
        $activeSheet->setCellValue('K' . $i, 'Дата создания заявки у клиента'); // +
        $activeSheet->setCellValue('L' . $i, 'Дата регистрации заказа'); // +
        $activeSheet->setCellValue('M' . $i, 'Дата упаковки'); // +
        $activeSheet->setCellValue('N' . $i, 'Дата отгрузки');
        $activeSheet->setCellValue('O' . $i, 'Дата доставки'); // +
        $activeSheet->setCellValue('P' . $i, 'Статус'); // +
        $activeSheet->setCellValue('Q' . $i, 'Статус груза'); // +
        $activeSheet->setCellValue('R' . $i, 'WMS'); // +
        $activeSheet->setCellValue('S' . $i, 'TR'); // +
        $activeSheet->setCellValue('T' . $i, 'FULL'); // +

        $searchModel = new OutboundOrderGridSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $i++;
            $title = '-';
            if ($to = $model->toPoint) {
                $title = $to->getPointTitleByPattern('{city_name_lat} {shopping_center_name_lat} / {city_name} {shopping_center_name}');
                if (empty($to->shopping_center_name_lat)) {
                    $title = str_replace('/', '', $title);
                }
            }
            $clientTitle = '';
            if ($client = $model->client) {
                $clientTitle = $client->title;
            }

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $model->parent_order_number);
            $activeSheet->setCellValue('C' . $i, $model->order_number);
            $activeSheet->setCellValue('D' . $i, $title);
            $activeSheet->setCellValue('E' . $i, $model->mc);
            $activeSheet->setCellValue('F' . $i, $model->kg);
            $activeSheet->setCellValue('G' . $i, $model->accepted_number_places_qty);
            $activeSheet->setCellValue('H' . $i, $model->expected_qty);
            $activeSheet->setCellValue('I' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('J' . $i, $model->accepted_qty);
            $activeSheet->setCellValue('K' . $i,
                !empty ($model->data_created_on_client) ? Yii::$app->formatter->asDatetime($model->data_created_on_client) : '-');
            $activeSheet->setCellValue('L' . $i,
                !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
            $activeSheet->setCellValue('M' . $i,
                !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date) : '-');
            $activeSheet->setCellValue('N' . $i,
                !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-');
            $activeSheet->setCellValue('O' . $i,
                !empty ($model->date_delivered) ? Yii::$app->formatter->asDatetime($model->date_delivered) : '-');
            $activeSheet->setCellValue('P' . $i, $model->getStatusValue());
            $activeSheet->setCellValue('Q' . $i, $model->getCargoStatusValue());
            $activeSheet->setCellValue('R' . $i, $model->calculateWMS());
            $activeSheet->setCellValue('S' . $i, $model->calculateTR());
            $activeSheet->setCellValue('T' . $i, $model->calculateFULL());

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionView($id)
    {

        $model = $this->findModel($id);
        $itemSearch = new OutboundOrderItemSearch();
        $ItemsProvider = $itemSearch->search(Yii::$app->request->queryParams);
        $ItemsProvider->query->andWhere(['outbound_order_id' => $model->id]);
        $ItemsProvider->query->select('*,((allocated_qty - expected_qty - accepted_qty)+allocated_qty) as order_by');
        $ItemsProvider->query->addOrderBy(new Expression('order_by!=0 DESC'));

        return $this->render('view', [
            'model' => $model,
            'ItemsProvider' => $ItemsProvider,
            'searchModel' => $itemSearch,
        ]);
    }

    /*
 * Import to excel
 *
 **/
    public function actionExportToExcelPlusProduct()
//    public function actionExportToExcel()
    {
        $objPHPExcel = new \PHPExcel();

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


        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('B' . $i, 'Родительский номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('D' . $i, 'Куда'); // +
        $activeSheet->setCellValue('E' . $i, 'Объем (м3)'); // +
        $activeSheet->setCellValue('F' . $i, 'Вес (кг)'); // +
//        $activeSheet->setCellValue('G' . $i, 'Отсканированное кол-во мест'); // +
        $activeSheet->setCellValue('G' . $i, 'Штрих код товара'); // +
        $activeSheet->setCellValue('H' . $i, 'Предполагаемое кол-во'); // +
        $activeSheet->setCellValue('I' . $i, 'Зарезервированое кол-во'); // +
        $activeSheet->setCellValue('J' . $i, 'Отсканированное кол-во'); // +
        $activeSheet->setCellValue('K' . $i, 'Дата создания заявки у клиента'); // +
        $activeSheet->setCellValue('L' . $i, 'Дата регистрации заказа'); // +
        $activeSheet->setCellValue('M' . $i, 'Дата упаковки'); // +
        $activeSheet->setCellValue('N' . $i, 'Дата отгрузки');
        $activeSheet->setCellValue('O' . $i, 'Дата доставки'); // +
        $activeSheet->setCellValue('P' . $i, 'Статус'); // +
        $activeSheet->setCellValue('Q' . $i, 'Статус груза'); // +
//        $activeSheet->setCellValue('R' . $i, 'WMS'); // +
//        $activeSheet->setCellValue('S' . $i, 'TR'); // +
//        $activeSheet->setCellValue('T' . $i, 'FULL'); // +

        $searchModel = new OutboundOrderGridSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $i++;
            $title = '-';
            if ($to = $model->toPoint) {
                $title = $to->getPointTitleByPattern('{city_name_lat} {shopping_center_name_lat} / {city_name} {shopping_center_name}');
                if (empty($to->shopping_center_name_lat)) {
                    $title = str_replace('/', '', $title);
                }
            }
            $clientTitle = '';
            if ($client = $model->client) {
                $clientTitle = $client->title;
            }

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $model->parent_order_number);
            $activeSheet->setCellValue('C' . $i, $model->order_number);
            $activeSheet->setCellValue('D' . $i, $title);
            $activeSheet->setCellValue('E' . $i, $model->mc);
            $activeSheet->setCellValue('F' . $i, $model->kg);
//            $activeSheet->setCellValue('G' . $i, $model->accepted_number_places_qty);
            $activeSheet->setCellValue('H' . $i, $model->expected_qty);
            $activeSheet->setCellValue('I' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('J' . $i, $model->accepted_qty);
            $activeSheet->setCellValue('K' . $i,
                !empty ($model->data_created_on_client) ? Yii::$app->formatter->asDatetime($model->data_created_on_client) : '-');
            $activeSheet->setCellValue('L' . $i,
                !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
            $activeSheet->setCellValue('M' . $i,
                !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date) : '-');
            $activeSheet->setCellValue('N' . $i,
                !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-');
            $activeSheet->setCellValue('O' . $i,
                !empty ($model->date_delivered) ? Yii::$app->formatter->asDatetime($model->date_delivered) : '-');
            $activeSheet->setCellValue('P' . $i, $model->getStatusValue());
            $activeSheet->setCellValue('Q' . $i, $model->getCargoStatusValue());
//            $activeSheet->setCellValue('R' . $i, $model->calculateWMS());
//            $activeSheet->setCellValue('S' . $i, $model->calculateTR());
//            $activeSheet->setCellValue('T' . $i, $model->calculateFULL());
//            $items = OutboundOrderItem::find()->where(['outbound_order_id'=>$model->id])->all();
            $items = OutboundOrderItem::find()->select('*,((allocated_qty - expected_qty - accepted_qty)+allocated_qty) as order_by')->orderBy(new Expression('order_by!=0 DESC'))->andWhere(['outbound_order_id'=>$model->id])->all();
            foreach($items as $item) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $clientTitle);
                $activeSheet->setCellValue('B' . $i, $model->parent_order_number);
                $activeSheet->setCellValue('C' . $i, $model->order_number);
                $activeSheet->setCellValue('D' . $i, $title);
//                $activeSheet->setCellValue('E' . $i, $model->mc);
//                $activeSheet->setCellValue('F' . $i, $model->kg);
                $activeSheet->setCellValue('G' . $i, $item->product_barcode);
                $activeSheet->setCellValue('H' . $i, $item->expected_qty);
                $activeSheet->setCellValue('I' . $i, $item->allocated_qty);
                $activeSheet->setCellValue('J' . $i, $item->accepted_qty);
                $activeSheet->setCellValue('R' . $i, Stock::find()->select('field_extra1')->andWhere(['client_id'=>$model->client_id,'product_barcode'=>$item->product_barcode])->scalar());
//                $activeSheet->setCellValue('K' . $i,
//                    !empty ($model->data_created_on_client) ? Yii::$app->formatter->asDatetime($model->data_created_on_client) : '-');
//                $activeSheet->setCellValue('L' . $i,
//                    !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
//                $activeSheet->setCellValue('M' . $i,
//                    !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date) : '-');
//                $activeSheet->setCellValue('N' . $i,
//                    !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-');
//                $activeSheet->setCellValue('O' . $i,
//                    !empty ($model->date_delivered) ? Yii::$app->formatter->asDatetime($model->date_delivered) : '-');
//                $activeSheet->setCellValue('P' . $i, $model->getStatusValue());
//                $activeSheet->setCellValue('Q' . $i, $model->getCargoStatusValue());
            }

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /**
     * Finds the InboundOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InboundOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = OutboundOrder::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

 /*
    *
    * */
    public function actionPrintBoxKgList()
    {
        $outboundID = Yii::$app->request->get('id');
        if ($outboundID) {
//            $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
//            $client = Client::findOne($clientEmploy->client_id);

            $stockItems = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode')
                ->andWhere([
//                    'client_id'=>$client->id,
                    'outbound_order_id' => $outboundID,
                    'status' => [
                        Stock::STATUS_OUTBOUND_SCANNED,
                        Stock::STATUS_OUTBOUND_PRINT_BOX_LABEL,
                        Stock::STATUS_OUTBOUND_PRINTING_BOX_LABEL,
                    ]
                ])
                ->andWhere('box_size_barcode != ""')
                ->groupBy('box_barcode')
                ->orderBy('box_barcode')
                ->asArray()
                ->all();

            $stockIdMapBoxBarcode = ArrayHelper::map($stockItems,'id','box_barcode');
            $boxAndLcBarcode = ArrayHelper::map(OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode),'our_box', 'client_box');

            $toPoint = '';
            $orderNumberTitle = time().'-rep';
            if($outboundOrder = OutboundOrder::findOne($outboundID)) {
                $orderNumberTitle = $outboundOrder->parent_order_number.'-'.$outboundOrder->order_number;
                if($point = $outboundOrder->toPoint){
                    $toPoint = $point->getPointTitleByPattern('stock');
                }
            }

            return $this->excelPrintPrintBoxKgList([
                'stockItems' => $stockItems,
                'boxAndLcBarcode' => $boxAndLcBarcode,
                'toPoint'=>$toPoint,
                'orderNumberTitle'=>$orderNumberTitle
            ]);
        }

        Yii::$app->session->setFlash('danger', 'Вы не указали номер накладной');
        return $this->redirect('index');
    }

    /*
  * @param array $data
  * */
    private function excelPrintPrintBoxKgList($data)
    {
//        if(Yii::$app->language == 'tr') {
//            Yii::$app->language = 'en';
//        }

        $objPHPExcel = new \PHPExcel();

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


		$activeSheet->setCellValue('C' . 1, 'АКТ ПРИЁМА-ПЕРЕДАЧИ ТОВАРА'); // +
		$activeSheet->setCellValue('A' . 3, $data['toPoint']); // +

        $iCell = 5;
		
        $activeSheet->setCellValue('A' . $iCell, Yii::t('outbound/forms','№')); // +
        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/titles','BOX_BARCODE')); // +
        $activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','BOX_SIZE')); // +
        //$activeSheet->setCellValue('D' . $iCell, Yii::t('outbound/titles','BOX_KG')); // +
        $activeSheet->setCellValue('D' . $iCell, Yii::t('outbound/titles','LC')); // +
        $i = 0;
        foreach ($data['stockItems'] as $row) {
            $iCell++;

            $activeSheet->setCellValue('A' . $iCell, ++$i);
            $activeSheet->setCellValue('B' . $iCell, $row['box_barcode']);
            $activeSheet->setCellValue('C' . $iCell, BarcodeManager::mapM3ToBoxSize($row['box_size_barcode']));
           // $activeSheet->setCellValue('D' . $iCell, $row['box_kg']);
            $activeSheet->setCellValue('D' . $iCell, (isset($data['boxAndLcBarcode'][$row['box_barcode']])? $data['boxAndLcBarcode'][$row['box_barcode']] :''));
        }
		
		$activeSheet->setCellValue('B' . ($iCell+4), 'Груз отгрузил'); // +
		$activeSheet->setCellValue('A' . ($iCell+5), 'ФИО'); // +
		$activeSheet->setCellValue('F' . ($iCell+4), 'Груз принял'); // +
		$activeSheet->setCellValue('E' . ($iCell+5), 'ФИО'); // +
		
		$activeSheet->setCellValue('B' . ($iCell+9), 'М.П'); // +

		$activeSheet->setCellValue('F' . ($iCell+9), 'М.П'); // +


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-report-' . $data['orderNumberTitle'] . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
        return true;
    }
	/*
	 * Import to excel
	 *
	 **/
	public function actionExportToExcelDataMatrix($id)
	{
		// /outbound/report/export-to-excel-data-matrix?id=72687
		$outboundOrder = OutboundOrder::findOne($id);
		$stockList  = Stock::find()
						   ->select("outbound_picking_list_barcode, box_barcode, product_barcode, product_qrcode, id")
						   ->andWhere(["outbound_order_id"=>$id])
						   //->andWhere(["system_status"=>2])
							->orderBy("product_qrcode,box_barcode")
						    ->asArray()
						   ->all();
		$delimiter = " | ";
		$content = "";
		$gsCode = "";
		foreach ($stockList as $line) {
			$dm = trim($line["product_qrcode"]);
			$gs = explode($gsCode,$dm);
			
			if(count($gs) == 1 && !empty($dm)) {
				$qr = $dm;
				$part1 = substr($qr,0,31);
				$part2 = substr($qr,31,6);
				$part3 = substr($qr,37);
				$dm = $part1.$gsCode.$part2.$gsCode.$part3;
			}
			if (empty($dm)) {
				$dm = "no-data-matrix";
			}
			$row = [
				trim($line["outbound_picking_list_barcode"]),
				trim($line["box_barcode"]),
				trim($line["product_barcode"]),
				$dm,
				trim($line["id"]),
			];
			$content .= implode($row,$delimiter).$delimiter."\n";
		}
		$filename = "data-matrix-".$outboundOrder->order_number.".csv";
		return  Yii::$app->response->sendContentAsFile( $content,$filename);
	}

    public function actionUpdateToPoint($id)
    {
        $order = $this->findModel($id);
        $model = new UpdateToPointForm();
        $model->order_id = $id;
        $model->to_point_id = $order->to_point_id;
        $model->order_number = $order->order_number;

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($model->updateOrder()) {
                return $this->redirect(['view', 'id' => $id]);
            }
        }

        return $this->render('update-to-point', [
            'model' => $model,
            'filterWidgetOptionDataRoute' => TLHelper::getStoreArrayByClientID()
        ]);
    }
	
	public function actionEqualizeExpectedAllocated($id)
    {
        $order = $this->findModel($id);

        $updated = OutboundOrder::updateAll(
            ['expected_qty' => $order->allocated_qty],
            ['id' => $id]
        );

        if ($updated) {
            Yii::$app->session->setFlash('success', 'Количество успешно выровнено');
        } else {
            Yii::$app->session->setFlash('error', 'Ошибка при выравнивании количества');
        }

        return $this->redirect(['view', 'id' => $id]);
    }
	
    public function actionDeleteOrder($id)
    {
        $order = $this->findModel($id);

        if(empty($order)) {
			Yii::$app->session->setFlash('success', 'Нет такой накладной');
			return $this->redirect(['view', 'id' => $id]);
		}

		$availableStatus = [
			Stock::STATUS_OUTBOUND_NEW,
			Stock::STATUS_OUTBOUND_FULL_RESERVED,
			Stock::STATUS_OUTBOUND_RESERVING,
			Stock::STATUS_OUTBOUND_PART_RESERVED,
			//Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
		];

        if(!in_array($order->status,$availableStatus)) {
			Yii::$app->session->setFlash('error', 'Накладная уже в обработке');
			//return $this->redirect(['view', 'id' => $id]);
		}
		Stock::resetByOutboundOrderId($order->id);
		$order->deleted = 1;
		$order->save(false);

        Yii::$app->session->setFlash('success', 'Накладная '.$order->order_number.' успешно удалена');

        return $this->redirect(['/outbound/report/index']);
    }
	
	public function actionResetOrder($id)
    {
        $order = $this->findModel($id);

        if(empty($order)) {
			Yii::$app->session->setFlash('success', 'Нет такой накладной');
			return $this->redirect(['view', 'id' => $id]);
		}

		$availableStatus = [
			Stock::STATUS_OUTBOUND_NEW,
			Stock::STATUS_OUTBOUND_FULL_RESERVED,
			Stock::STATUS_OUTBOUND_RESERVING,
			Stock::STATUS_OUTBOUND_PART_RESERVED,
			//Stock::STATUS_OUTBOUND_PRINTED_PICKING_LIST,
		];

        if(!in_array($order->status,$availableStatus)) {
			Yii::$app->session->setFlash('error', 'Накладная уже в обработке');
			//return $this->redirect(['view', 'id' => $id]);
		}
		Stock::resetByOutboundOrderId($order->id);
		//$order->deleted = 1;
		//$order->save(false);

        Yii::$app->session->setFlash('success', 'Накладная '.$order->order_number.' успешно сброшена');

        return $this->redirect(['/outbound/report/index']);
    }
}