<?php

namespace app\modules\inbound\controllers;

use app\modules\inbound\inbound;
use stockDepartment\modules\inbound\models\InboundOrderSearch;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\client\models\Client;
use Yii;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use stockDepartment\modules\inbound\models\InboundOrderItemSearch;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;
use app\modules\stock\models\StockSearch;
use stockDepartment\modules\intermode\controllers\product\domains\ProductService;
use common\modules\transportLogistics\components\TLHelper;

class ReportController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new InboundOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere([
			"order_type"=>[
				InboundOrder::ORDER_TYPE_RETURN,
				InboundOrder::ORDER_TYPE_INBOUND,
				InboundOrder::ORDER_TYPE_ECOMM_RETURN,
			]
		]);

        $clientsArray = Client::getActiveWMSItems();
		$clientStoreArray = TLHelper::getStoreArrayByClientID();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientsArray' => $clientsArray,
			'clientStoreArray' => $clientStoreArray,
        ]);
    }

    public function actionView($id)
    {

        $model = $this->findModel($id);
        $searchItem = new InboundOrderItemSearch();
        $ItemsProvider = $searchItem->search(Yii::$app->request->queryParams);
        $ItemsProvider->query->andWhere(['inbound_order_id' => $model->id]);
        $ItemsProvider->query->select('*,(expected_qty - accepted_qty) as order_by');
        $ItemsProvider->query->addOrderBy(new Expression('order_by!=0 DESC'));

        return $this->render('view', [
            'model' => $model,
            'ItemsProvider' => $ItemsProvider,
            'searchModel' => $searchItem,
        ]);
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

        if (($model = InboundOrder::findOne(['id'=>$id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
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
        $activeSheet->setCellValue('A' . $i, 'ID'); // +
        $activeSheet->setCellValue('B' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('D' . $i, 'Заявленное кол-во'); // +
        $activeSheet->setCellValue('E' . $i, 'Принятое кол-во'); // +
        $activeSheet->setCellValue('F' . $i, 'Фактическое кол-во мест'); // +
        $activeSheet->setCellValue('G' . $i, 'Заявленное кол-во мест'); // +
        $activeSheet->setCellValue('H' . $i, 'Ожидаемая дата поставки'); // +
        $activeSheet->setCellValue('I' . $i, 'Начали сканировать'); // +
        $activeSheet->setCellValue('J' . $i, 'Дата подтверждения'); // +
        $activeSheet->setCellValue('K' . $i, 'Дата создания'); // +
        $activeSheet->setCellValue('L' . $i, 'Статус'); // +
        $activeSheet->setCellValue('M' . $i, 'Тип'); // +
        $activeSheet->setCellValue('N' . $i, 'Инвойс'); // +

        $searchModel = new InboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $i++;

            $clientTitle = '';
            if($client = $model->client){
                $clientTitle = $client->title;
            }

            $activeSheet->setCellValue('A' . $i, $model->id);
            $activeSheet->setCellValue('B' . $i, $model->order_number);
            $activeSheet->setCellValue('C' . $i, $clientTitle);
            $activeSheet->setCellValue('D' . $i, $model->expected_qty);
            $activeSheet->setCellValue('E' . $i, $model->accepted_qty);
            $activeSheet->setCellValue('F' . $i, $model->accepted_number_places_qty); // +
            $activeSheet->setCellValue('G' . $i, $model->expected_number_places_qty); // +
            $activeSheet->setCellValue('H' . $i, !empty($model->expected_datetime) ? Yii::$app->formatter->asDatetime($model->expected_datetime): "-"); // +
            $activeSheet->setCellValue('I' . $i, !empty($model->begin_datetime) ? Yii::$app->formatter->asDatetime($model->begin_datetime): "-"); // +
            $activeSheet->setCellValue('J' . $i, !empty($model->date_confirm)? Yii::$app->formatter->asDatetime($model->date_confirm): "-"); // +
            $activeSheet->setCellValue('K' . $i, !empty($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at): "-"); // +
            $activeSheet->setCellValue('L' . $i, $model->getStatusValue()); // +
            $activeSheet->setCellValue('M' . $i, $model->getOrderTypeValue()); // +
            $activeSheet->setCellValue('N' . $i, $model->parent_order_number); // +
			
            $modelItems = $model->getOrderItems()->select('*')->all();
            foreach($modelItems as $item) {
				try {
					$json = Json::decode($item->product_serialize_data);
					//$activeSheet->setCellValue('N' . $i, $json['apiLogValue']['AppointmentBarcode']);
					$activeSheet->setCellValue('N' . $i, ArrayHelper::getValue($json,'apiLogValue.AppointmentBarcode'));
				} catch (\yii\base\InvalidParamException $e) {
					$activeSheet->setCellValue('N' . $i, '');
				}
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-orders-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

   /*
   * Import to excel
   *
   **/
    public function actionExportToExcelFull()
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
        $activeSheet->setCellValue('A' . $i, 'ID'); // +
        $activeSheet->setCellValue('B' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('D' . $i, 'Шк товара'); // +
        $activeSheet->setCellValue('E' . $i, 'Модель товара'); // +
        $activeSheet->setCellValue('F' . $i, 'Заявленное кол-во мест'); // +
        $activeSheet->setCellValue('G' . $i, 'Фактическое кол-во мест'); // +
        $activeSheet->setCellValue('H' . $i, 'Наименование'); // +

        $searchModel = new InboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {

            $clientTitle = '';
            if($client = $model->client){
                $clientTitle = $client->title;
            }

//            $modelItems =  $model->getOrderItems()->all();
            $modelItems = $model->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('order_by!=0 DESC'))->all();
            foreach($modelItems as $item) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $model->id);
                $activeSheet->setCellValue('B' . $i, $model->order_number);
                $activeSheet->setCellValue('C' . $i, $clientTitle);
                $activeSheet->setCellValue('D' . $i, $item->product_barcode);
                $activeSheet->setCellValue('E' . $i, $item->product_model);
                $activeSheet->setCellValue('F' . $i, $item->expected_qty);
                $activeSheet->setCellValue('G' . $i, $item->accepted_qty);
                $activeSheet->setCellValue('H' . $i, $item->product_name);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-orders-report-with-products-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }


    /*
* Import to excel
*
**/
    public function actionExportToExcelFullOne()
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
        $activeSheet->setCellValue('A' . $i, 'ID'); // +
        $activeSheet->setCellValue('B' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('C' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('D' . $i, 'Шк товара'); // +
        $activeSheet->setCellValue('E' . $i, 'Модель товара'); // +
        $activeSheet->setCellValue('F' . $i, 'Заявленное кол-во мест'); // +
        $activeSheet->setCellValue('G' . $i, 'Фактическое кол-во мест'); // +
        $activeSheet->setCellValue('H' . $i, 'Наименование'); // +

        $modelId = Yii::$app->request->get('id');

        $searchModel = new InboundOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['id'=>$modelId]);
        $dataProvider->pagination = false;

        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {

            $clientTitle = '';
            if($client = $model->client){
                $clientTitle = $client->title;
            }

//            $modelItems =  $model->getOrderItems()->all();
            $modelItems = $model->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('order_by!=0 DESC'))->all();
            foreach($modelItems as $item) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $model->id);
                $activeSheet->setCellValue('B' . $i, $model->order_number);
                $activeSheet->setCellValue('C' . $i, $clientTitle);
                $activeSheet->setCellValue('D' . $i, $item->product_barcode);
                $activeSheet->setCellValue('E' . $i, $item->product_model);
                $activeSheet->setCellValue('F' . $i, $item->expected_qty);
                $activeSheet->setCellValue('G' . $i, $item->accepted_qty);
                $activeSheet->setCellValue('H' . $i, $item->product_name);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-orders-report-with-products-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

	/*
	* Import to excel
	*
	**/
    public function actionPrintDataMatrix()
    {
		$inboundId = Yii::$app->request->get('id');
		$newBoxBarcodeList  = \common\modules\dataMatrix\models\InboundDataMatrix::find()
																				 ->select("product_barcode,product_model,data_matrix_code")
																				 ->andWhere(["inbound_id"=>$inboundId])
																				 ->andWhere("data_matrix_code != ''")
																				 ->andWhere("data_matrix_code like '%0105012123981840213zREHvoe%'")
																				 ->asArray()
																				 ->all();

    	return $this->render("_print-barcode",["newBoxBarcodeList"=>$newBoxBarcodeList]);
    }
	
	
	public function actionDeleteOrder($id)
	{
		$order = $this->findModel($id);

		if(empty($order)) {
			Yii::$app->session->setFlash('success', 'Нет такой накладной');
			return $this->redirect(['view', 'id' => $id]);
		}


		if($order->accepted_qty > 0) {
			Yii::$app->session->setFlash('error', 'Накладная в обработке. Очистите все короба и повторите попытку');
			return $this->redirect(['view', 'id' => $id]);
		}
		$order->deleted = 1;
		$order->save(false);

		Yii::$app->session->setFlash('success', 'Накладная '.$order->order_number.' успешно удалена');

		return $this->redirect(['/inbound/report/index']);
	}
	
   /**
	*
	**/
    public function actionPrintOneDataMatrix()
    {
		$barcode = "8054041233431";
		$service  = new ProductService();
		$productData = $service->getProductInfoByBarcode($barcode);
		$dto = [
			"product_model"=>$productData->product->model,
			"product_barcode"=>$barcode,
			"data_matrix_code"=>"0108054041233431213klSPh7HjAr)i91KZF092aF00uTLRtJ6ZplcXwwD2lLCTXjM=aF00uTLRtJ6ZplcXwwD2lLCTXjM=aF00uTLRtJ6ZplcXwwD2lLCTXjM=aF00",
		];
    	return $this->render("_print-one-dm",["productData"=>$dto]);
    }
	
	
	
	
	 public function actionPlacementData($id)
    {
        $model = $this->findModel($id);
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchPlacementData($id, Yii::$app->request->queryParams);

        return $this->render('placement-data', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
	
	public function actionExportPlacementData($id)
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchPlacementData($id, Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report System")
            ->setLastModifiedBy("Report System")
            ->setTitle("Placement Data Report")
            ->setSubject("Placement Data Report")
            ->setDescription("Placement data report generated using PHPExcel")
            ->setKeywords("placement data report")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('placement-data-' . date('d.m.Y'));

        $i = 1;
        $activeSheet->setCellValue('A' . $i, Yii::t('inbound/forms', 'Secondary address'));
        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Primary address'));
        $activeSheet->setCellValue('C' . $i, Yii::t('inbound/forms', 'Product Barcode'));
        $activeSheet->setCellValue('D' . $i, Yii::t('inbound/forms', 'Qty'));

        $models = $dataProvider->getModels();
        foreach ($models as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['secondary_address'] ?: '-');
            $activeSheet->setCellValue('B' . $i, $model['primary_address'] ?: '-');
            $activeSheet->setCellValue('C' . $i, $model['product_barcode'] ?: '-');
            $activeSheet->setCellValue('D' . $i, $model['qty'] ?: '-');
        }

        foreach (range('A', 'D') as $columnID) {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="inbound-orders-report-with-products-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    public function actionExportToExcelDiscrepancies()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator("Report")
            ->setLastModifiedBy("Report")
            ->setTitle("Discrepancy Report")
            ->setDescription("Inbound order discrepancies");

        $activeSheet = $objPHPExcel->setActiveSheetIndex(0)->setTitle('Discrepancies');

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Заявленное кол-во');
        $activeSheet->setCellValue('B' . $i, 'Принятое количество');
        $activeSheet->setCellValue('C' . $i, 'Разница');
        $activeSheet->setCellValue('D' . $i, 'ID');
        $activeSheet->setCellValue('E' . $i, 'ШК товара');

        $searchModel = new InboundOrderItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(new Expression('CAST(accepted_qty AS UNSIGNED) != CAST(expected_qty AS UNSIGNED)'))
            ->andWhere(['!=', 'id', -1])
            ->andWhere(new Expression('CAST(accepted_qty AS UNSIGNED) > 0'))
            ->groupBy('product_barcode');
        $dataProvider->pagination = false;
        $models = $dataProvider->getModels();

        foreach ($models as $model) {
            $i++;
            $diff = abs($model['expected_qty'] - $model['accepted_qty']);
            $activeSheet->setCellValue('A' . $i, $model['expected_qty']);
            $activeSheet->setCellValue('B' . $i, $model['accepted_qty']);
            $activeSheet->setCellValue('C' . $i, $diff);
            $activeSheet->setCellValue('D' . $i, $model['id']);
            $activeSheet->setCellValue('E' . $i, $model['product_barcode']);
        }

        foreach (range('A', 'E') as $columnID) {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="discrepancies-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output');
        Yii::$app->end();
    }

    public function actionExportToExcelPlus()
    {
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator("Report")
            ->setLastModifiedBy("Report")
            ->setTitle("Plus Report")
            ->setDescription("Inbound order over-receipt");

        $activeSheet = $objPHPExcel->setActiveSheetIndex(0)->setTitle('Pluses');

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Заявленное кол-во');
        $activeSheet->setCellValue('B' . $i, 'Принятое количество');
        $activeSheet->setCellValue('C' . $i, 'Разница');
        $activeSheet->setCellValue('D' . $i, 'ID');
        $activeSheet->setCellValue('E' . $i, 'ШК товара');

        $searchModel = new InboundOrderItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query
            ->andWhere(new Expression('CAST(accepted_qty AS UNSIGNED) > CAST(expected_qty AS UNSIGNED)'))
            ->andWhere(['!=', 'id', -1])
            ->groupBy('product_barcode');
        $dataProvider->pagination = false;
        $models = $dataProvider->getModels();

        foreach ($models as $model) {
            $i++;
            $diff = abs($model['expected_qty'] - $model['accepted_qty']);
            $activeSheet->setCellValue('A' . $i, $model['expected_qty']);
            $activeSheet->setCellValue('B' . $i, $model['accepted_qty']);
            $activeSheet->setCellValue('C' . $i, $diff);
            $activeSheet->setCellValue('D' . $i, $model['id']);
            $activeSheet->setCellValue('E' . $i, $model['product_barcode']);
        }

        foreach (range('A', 'D') as $columnID) {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="pluses-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output');
        Yii::$app->end();
    }
}