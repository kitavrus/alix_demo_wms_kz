<?php

namespace app\modules\stock\controllers;

use common\modules\client\models\Client;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use Yii;
use common\modules\stock\models\Stock;
use app\modules\stock\models\StockSearch;
use app\modules\stock\models\StockRemains;
use stockDepartment\components\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\modules\stock\models\RackAddress;
use common\modules\transportLogistics\components\TLHelper;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends Controller
{
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['post'],
//                ],
//            ],
//        ];
//    }

    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionSearchItem()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);

        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $statusArray = $searchModel->getStatusArray();
        $availabilityStatusArray = $searchModel->getAvailabilityStatusArray();
        $lostStatusArray = $searchModel->getLostStatusArray();
        return $this->render('search-item', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conditionTypeArray' => $conditionTypeArray,
            'statusArray' => $statusArray,
            'availabilityStatusArray' => $availabilityStatusArray,
            'lostStatusArray' => $lostStatusArray,
        ]);
    }

    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Stock model.
     * @param integer $id
     * @return mixed
     */
/*    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }*/

    /**
     * Creates a new Stock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
/*    public function actionCreate()
    {
        $model = new Stock();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing Stock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
/*    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Deletes an existing Stock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
/*    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the Stock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /*
     *
     * Export data to EXEL
     *
     * */
    public function actionExportAvailableToExcel()
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
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, 'ШК'); // +
        $activeSheet->setCellValue('B'.$i, 'Кол-во'); // +

        $itemsProcessQuery = Stock::find()
            ->select('product_barcode, count(product_barcode) as items ')
            ->where([
                'status_availability' => Stock::STATUS_AVAILABILITY_YES,
            ])
            ->groupBy('product_barcode')
            ->asArray()
            ->all();
        foreach($itemsProcessQuery as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['items']);

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="full-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /**
     * Lists all Stock models.
     * @return mixed
     */
    public function actionSearchRemains()
    {
        $searchModel = new StockRemains();
        list($dataProvider,$query) = $searchModel->searchArray(Yii::$app->request->queryParams);

        $queryAddress = clone $query;
        $queryBox = clone $query;
        $qtyAddress = $queryAddress->select('address_pallet_qty')->groupBy('secondary_address')->sum('address_pallet_qty');
        $qtyBox = $queryBox->groupBy('primary_address')->count();
		$queryProduct = clone $query;
		$qtyProduct = $queryProduct->groupBy("")->count();
		
		
        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $statusArray = $searchModel->getStatusArray();
        $availabilityStatusArray = $searchModel->getAvailabilityStatusArray();
        $lostStatusArray = $searchModel->getLostStatusArray();
        $clientsArray = Client::getActiveWMSItems();

        return $this->render('search-remains', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conditionTypeArray' => $conditionTypeArray,
            'statusArray' => $statusArray,
            'availabilityStatusArray' => $availabilityStatusArray,
            'lostStatusArray' => $lostStatusArray,
            'clientsArray' => $clientsArray,
            'qtyAddress' => $qtyAddress,
            'qtyBox' => $qtyBox,
			'qtyProduct' => $qtyProduct,
        ]);
    }

    /*
    * Import to excel
    *
    **/
    public function actionRemainsExportToExcel()
    {
        $detail = Yii::$app->request->get('detail');

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
        $activeSheet->setCellValue('B' . $i, 'TIR'); // +
        $activeSheet->setCellValue('C' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('D' . $i, 'Количество'); // +
        $activeSheet->setCellValue('E' . $i, 'Состояние'); // +
		$activeSheet->setCellValue('F' . $i, 'SKU ид'); // +

        if($detail) {
            $activeSheet->setCellValue('E' . $i, 'Короб'); // +
            $activeSheet->setCellValue('F' . $i, 'Полка'); // +
            $activeSheet->setCellValue('G' . $i, 'Модель'); // +
            $activeSheet->setCellValue('H' . $i, 'Инв ид'); // +
            $activeSheet->setCellValue('I' . $i, 'Инв короб'); // +
            $activeSheet->setCellValue('J' . $i, 'Инв полка'); // +
            $activeSheet->setCellValue('K' . $i, 'Состояние'); // +
			$activeSheet->setCellValue('L' . $i, 'SKU ид'); // +
        }

        $searchModel = new StockRemains();
//        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $dataProviderResult = $searchModel->searchArray(Yii::$app->request->queryParams);
        $conditionTypeArray = $searchModel->getConditionTypeArray();
        list($dataProvider,$query) = $dataProviderResult;
        $dataProvider->pagination = false;
        $products = $dataProvider->getModels();
        $clientTitles = [];
        $inboundOrderTitles = [];
//        VarDumper::dump($products,10,true);
//        die('-STOP-');
//        $boxes = [];
        foreach ($products as $model) {
            $i++;
            $clientTitle = '';
            if (!isset($clientTitles[$model['client_id']])) {
                if($client = Client::findOne($model['client_id'])) {
                    $clientTitles[$model['client_id']] = $client->title;
                    $clientTitle = $client->title;
                }
            } else {
                $clientTitle = $clientTitles[$model['client_id']];
            }

            $activeSheet->setCellValue('A' . $i, $clientTitle);

            $inboundTitle = '';

            $activeSheet->setCellValue('B' . $i, $inboundTitle);
            $activeSheet->setCellValueExplicit('C' . $i, $model['product_barcode'], \PHPExcel_Cell_DataType::TYPE_STRING);
            $activeSheet->setCellValue('D' . $i, $model['qty']);
            $activeSheet->setCellValue('E' . $i, ArrayHelper::getValue($conditionTypeArray,$model['condition_type']));
			$activeSheet->setCellValue('F' . $i, $model['product_sku']);
//            $boxes[$model['primary_address']] = $model['primary_address'];
            if($detail) {
                $activeSheet->setCellValue('E' . $i, $model['primary_address']);
                $activeSheet->setCellValue('F' . $i, $model['secondary_address']);
                $activeSheet->setCellValue('G' . $i, $model['product_model']);
                $activeSheet->setCellValue('H' . $i, $model['inventory_id']);
                $activeSheet->setCellValue('I' . $i, $model['inventory_primary_address']);
                $activeSheet->setCellValue('J' . $i, $model['inventory_secondary_address']);
                $activeSheet->setCellValue('K' . $i, ArrayHelper::getValue($conditionTypeArray,$model['condition_type']));
				$activeSheet->setCellValue('L' . $i, $model['product_sku']);
            }
        }

        $fileName = 'remains-product-in-stock-report-' . date('Ymd_H-i-s');
        if($detail) {
            $fileName .= '-detail';
        }

        $fileName .= '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
    * Export search products to pdf
    *
    **/
    public function actionItemExportToExcel()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchForItems(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $statusArray = $searchModel->getStatusArray();
        $availabilityStatusArray = $searchModel->getAvailabilityStatusArray();

        return $this->render('print/search-item-pdf',[
            'query'=>$dataProvider->query,
            'statusArray'=>$statusArray,
            'availabilityStatusArray'=>$availabilityStatusArray,
        ]);
    }

    /*
 *
 * */
    public function actionSearchHistoryByBarcode()
    { // search-history-by-barcode
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchHistoryArray(Yii::$app->request->queryParams);
//        $conditionTypeArray = $searchModel->getConditionTypeArray();
        /*        return $this->render('index', [
                    'conditionTypeArray' => $conditionTypeArray,
                ]);*/
        return $this->render('search-history-by-barcode',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
     *
     * Export data to EXEL
     *
     * */
    public function actionHistoryExportToExcel()
    {

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchHistoryArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $data = $dataProvider->getModels();
        $objPHPExcel = new \PHPExcel();

        $objPHPExcel->getProperties()
            ->setCreator("Report Reportov")
            ->setLastModifiedBy("Report Reportov")
            ->setTitle("Office 2007 CSV Test Document")
            ->setSubject("Office 2007 CSV Test Document")
            ->setDescription("Test document for Office 2007 CSV, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Report");

        $activeSheet = $objPHPExcel
            ->setActiveSheetIndex(0)
            ->setTitle('report-'.date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A'.$i, Yii::t('stock/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('B'.$i, Yii::t('stock/forms', 'Product model')); // +
        $activeSheet->setCellValue('C'.$i, Yii::t('stock/forms', 'Inbound order')); // +
        $activeSheet->setCellValue('D'.$i, Yii::t('stock/forms', 'Outbound order')); // +
        $activeSheet->setCellValue('E'.$i, Yii::t('stock/forms', 'Qty')); // +


        foreach($data as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i,  $model['product_barcode']);
            $activeSheet->setCellValue('B' . $i, $model['product_model']);

            $inboundOrder = '-';
            if($in = \common\modules\inbound\models\InboundOrder::findOne($model['inbound_order_id'])) {
                $inboundOrder = $in->order_number;
            }

            $outboundOrder = '-';
            if($o = \common\modules\outbound\models\OutboundOrder::findOne($model['outbound_order_id'])) {
                $outboundOrder = $o->order_number;
            }

            $activeSheet->setCellValue('C' . $i,$inboundOrder );
            $activeSheet->setCellValue('D' . $i, $outboundOrder);
            $activeSheet->setCellValue('E' . $i, $model['qty']);

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/DeFacto/stock/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'stock-report-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }
	
	

    public function actionWhereFromBox() {

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchWhereFromBox(Yii::$app->request->queryParams);

        return $this->render('where-from-box/index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientStoreArray' => TLHelper::getStoreArrayByClientID(),
        ]);
    }
	
}