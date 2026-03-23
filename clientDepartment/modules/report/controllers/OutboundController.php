<?php

namespace app\modules\report\controllers;

use app\modules\report\models\ExportLabelForm;
use app\modules\report\models\OutboundBoxSearch;
use app\models\StockExtraField;
//use Com\Tecnick\Pdf\Tcpdf;
use common\modules\outbound\service\OutboundBoxService;
use common\modules\product\models\defacto\Colors;
use common\modules\product\models\defacto\Products;
use common\modules\store\models\Store;
use clientDepartment\modules\report\models\OutboundOrderGridSearch;
use common\components\BarcodeManager;
use common\modules\client\models\ClientEmployees;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2Manager;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use clientDepartment\modules\report\models\TlDeliveryProposalSearch;
use Yii;
use clientDepartment\components\Controller;
use clientDepartment\modules\report\models\OutboundOrderSearch;
use common\modules\client\models\Client;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use \DateTime;
use common\modules\outbound\models\OutboundOrderItem;
use clientDepartment\modules\report\models\OutboundOrderItemSearch;
use yii\helpers\BaseFileHelper;
use common\modules\product\models\ProductBarcodes;
use common\modules\stock\models\Stock;
use yii\web\UploadedFile;
use common\components\FailDeliveryStatus\StatusList;


class OutboundController extends Controller
{
    public function actionIndex()
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);

        $searchModel = new OutboundOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['client_id' => $clientEmploy->client_id]);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientStoreArray' => $clientStoreArray,
        ]);
    }

    public function actionView($id)
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

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

    /**
     * Finds the TlDeliveryProposalBilling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OutboundOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
//        $client = ClientManager::findModelClient(Yii::$app->user->id);

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);

        if (($model = OutboundOrder::findOne(['id'=>$id, 'client_id'=>$clientEmploy->client_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
    * Operation report
    *
    * */
    public function actionOperationReport()
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

//        $client = ClientManager::findModelClient(Yii::$app->user->id);
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);

        $noFindDataProvider = null;
        $noReservedDataProvider = null;

        $searchModel = new OutboundOrderGridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['client_id' => $client->id]);


        if(!empty($searchModel->order_number) || !empty($searchModel->parent_order_number)) {
            $q =  OutboundOrder::find();
            $q->andFilterWhere([
                'id' => $searchModel->id,
                'client_id' => $searchModel->client_id,
                'parent_order_number' => $searchModel->parent_order_number,
                'order_number' => $searchModel->order_number,
                'status' => $searchModel->status,
            ]);
            $q->select('id');

            $ids = $q->column();

            $noReservedQuery = OutboundOrderItem::find()->where('expected_qty != allocated_qty')->andWhere(['outbound_order_id'=>$ids]);
            $noReservedDataProvider = new ActiveDataProvider([
                'query' => $noReservedQuery,
                'pagination' => [
                    'pageSize' => 300,
                ],
                'sort'=> ['defaultOrder' => ['outbound_order_id'=>SORT_DESC]]
            ]);

            $noFindQuery =  OutboundOrderItem::find()->where('accepted_qty != allocated_qty')->andWhere(['outbound_order_id'=>$ids]);
            $noFindDataProvider = new ActiveDataProvider([
                'query' => $noFindQuery,
                'pagination' => [
                    'pageSize' => 300,
                ],
                'sort'=> ['defaultOrder' => ['outbound_order_id'=>SORT_DESC]]
            ]);
        }


        return $this->render('operation-report-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'noFindDataProvider' => $noFindDataProvider,
            'noReservedDataProvider' => $noReservedDataProvider,
        ]);
    }

    /*
     * Import to excel
     *
     **/
    public function actionExportToExcel()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }
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
        $activeSheet->setCellValue('A' . $i, Yii::t('outbound/forms', 'Parent order number')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('outbound/forms', 'Order number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'To point id')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('outbound/forms', 'Volume (м³)')); // +
//        $activeSheet->setCellValue('E' . $i, 'Вес (кг)'); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('outbound/forms', 'Accepted Number Places Qty')); // + E
        $activeSheet->setCellValue('F' . $i, Yii::t('outbound/forms', 'Expected Qty')); // + F
        $activeSheet->setCellValue('G' . $i, Yii::t('outbound/forms', 'Allocate Qty')); // + G
        $activeSheet->setCellValue('H' . $i, Yii::t('outbound/forms', 'Accepted Qty')); // + H
        $activeSheet->setCellValue('I' . $i, Yii::t('outbound/forms', 'Data created on client')); // + I
        $activeSheet->setCellValue('J' . $i, Yii::t('outbound/forms', 'Created At')); // + J
        $activeSheet->setCellValue('K' . $i, Yii::t('outbound/forms', 'Packing date')); // + K
        $activeSheet->setCellValue('L' . $i, Yii::t('outbound/forms', 'Date left our warehouse')); // +L
        $activeSheet->setCellValue('M' . $i, Yii::t('outbound/forms', 'Date delivered')); // + M
        $activeSheet->setCellValue('N' . $i, Yii::t('inbound/forms', 'Cargo status')); // + M
        $activeSheet->setCellValue('O' . $i, 'WMS'); // + N
        $activeSheet->setCellValue('P' . $i, 'TR'); // + O
        $activeSheet->setCellValue('Q' . $i, 'FULL'); // + P
		$activeSheet->setCellValue('R' . $i, 'Delivery fail reason');
        $activeSheet->setCellValue('S' . $i, 'Delivery fail reason comment');


        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $searchModel = new OutboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dps = $dataProvider->getModels();

        $value = TLHelper::getStoreArrayByClientID($client->id);
        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $i++;
            $title = ArrayHelper::getValue($value,$model->to_point_id,'');

            $activeSheet->setCellValue('A' . $i, substr($model->order_number,0,11));
            $activeSheet->setCellValue('B' . $i, $model->order_number);
            $activeSheet->setCellValue('C' . $i, $title);
            $activeSheet->setCellValue('D' . $i, $model->mc);
//            $activeSheet->setCellValue('E' . $i, $model->kg);
            $activeSheet->setCellValue('E' . $i, $model->accepted_number_places_qty);
            $activeSheet->setCellValue('F' . $i, $model->expected_qty);
            $activeSheet->setCellValue('G' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('H' . $i, $model->accepted_qty);
            $activeSheet->setCellValue('I' . $i, !empty ($model->data_created_on_client) ? Yii::$app->formatter->asDatetime($model->data_created_on_client,$asDatetimeFormat) : '-');
            $activeSheet->setCellValue('J' . $i, !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at,$asDatetimeFormat) : '-');
            $activeSheet->setCellValue('K' . $i, !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date,$asDatetimeFormat) : '-');
            $activeSheet->setCellValue('L' . $i, !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse,$asDatetimeFormat) : '-');
            $activeSheet->setCellValue('M' . $i, !empty ($model->date_delivered) ? Yii::$app->formatter->asDatetime($model->date_delivered,$asDatetimeFormat) : '-');
            $activeSheet->setCellValue('N' . $i, $model->getCargoStatusValue());
            $activeSheet->setCellValue('O' . $i, $model->calculateWMS());
            $activeSheet->setCellValue('P' . $i, $model->calculateTR());
            $activeSheet->setCellValue('Q' . $i, $model->calculateFULL());
			
			if($fds = StatusList::getValue($model->fail_delivery_status)) {
                $activeSheet->setCellValue('R' . $i,$fds->statusText);
                $activeSheet->setCellValue('S' . $i,$fds->otherStatus);
            }

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/'.$client->id.'/outbound/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'outbound-order-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
     * Экспорт расходной накладной (Colins)
     *
     **/
    public function actionExportRn()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
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
        $activeSheet->setCellValue('A' . $i, Yii::t('stock/forms', 'Product barcode'));
        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Expected Qty'));
        $activeSheet->setCellValue('C' . $i, Yii::t('forms', 'Price'));
        $activeSheet->setCellValue('D' . $i, Yii::t('stock/forms', 'Box Barcode'));

        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new OutboundOrderSearch();
        $modelId = Yii::$app->request->get('id');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dataProvider->query->andFilterWhere(['id'=>$modelId]);
        $filterOrders = $dataProvider->query->asArray()->all();
        $filterOrders = ArrayHelper::map($filterOrders, 'id', 'id');

        $stockItems = Stock::find()->select('product_barcode, box_barcode, count(product_barcode) AS product_qty')
                                   ->andWhere(['outbound_order_id' =>$filterOrders, 'client_id'=>$client->id])
                                   ->andWhere('box_barcode!= ""')
                                   ->orderBy('box_barcode')
                                   ->groupBy('product_barcode, box_barcode')
                                   ->asArray()
                                   ->all();
        if($stockItems){
            foreach ($stockItems as $row){
                $price = '-not-set-';
                if($product = ProductBarcodes::getProductByBarcode($client->id, $row['product_barcode'])){
                    $price = Yii::$app->formatter->asDecimal($product->price, 2);
                }
                $i++;
                $activeSheet->setCellValue('A' . $i, $row['product_barcode']);
                $activeSheet->setCellValue('B' . $i, $row['product_qty']);
                $activeSheet->setCellValue('C' . $i, $price);
                $activeSheet->setCellValue('D' . $i, $row['box_barcode']);
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/'.$client->id.'/outbound/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'outbound-report-export-RN-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
    * Import to excel with products
    *
    **/
    public function actionExportToExcelPlusProduct()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

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
        $activeSheet->setCellValue('B' . $i, Yii::t('outbound/forms', 'Parent order number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'Order number')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('outbound/forms', 'To point id')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('outbound/forms', 'Volume (м³)')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('outbound/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('outbound/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('I' . $i, Yii::t('outbound/forms', 'Allocate Qty')); // +
        $activeSheet->setCellValue('J' . $i, Yii::t('outbound/forms', 'Accepted Qty')); // +
        $activeSheet->setCellValue('K' . $i, Yii::t('outbound/forms', 'Data created on client')); // +
        $activeSheet->setCellValue('L' . $i, Yii::t('outbound/forms', 'Created At')); // +
        $activeSheet->setCellValue('M' . $i, Yii::t('outbound/forms', 'Packing date')); // +
        $activeSheet->setCellValue('N' . $i, Yii::t('outbound/forms', 'Date left our warehouse'));
        $activeSheet->setCellValue('O' . $i, Yii::t('outbound/forms', 'Date delivered')); // +
        $activeSheet->setCellValue('P' . $i, Yii::t('inbound/forms', 'Cargo status')); // +

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);

        $modelId = Yii::$app->request->get('id');

        $searchModel = new OutboundOrderGridSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dataProvider->query->andFilterWhere(['id'=>$modelId]);
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
//            $activeSheet->setCellValue('F' . $i, $model->kg);
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
            $activeSheet->setCellValue('P' . $i, $model->getCargoStatusValue());

            $items = OutboundOrderItem::find()->select('*,((allocated_qty - expected_qty - accepted_qty)+allocated_qty) as order_by')->orderBy(new Expression('order_by!=0 DESC'))->andWhere(['outbound_order_id'=>$model->id])->all();
            foreach($items as $item) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $clientTitle);
                $activeSheet->setCellValue('B' . $i, $model->parent_order_number);
                $activeSheet->setCellValue('C' . $i, $model->order_number);
                $activeSheet->setCellValue('D' . $i, $title);
                $activeSheet->setCellValue('G' . $i, $item->product_barcode);
                $activeSheet->setCellValue('H' . $i, $item->expected_qty);
                $activeSheet->setCellValue('I' . $i, $item->allocated_qty);
                $activeSheet->setCellValue('J' . $i, $item->accepted_qty);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
       *
       * */
    public function actionPrintBoxKgList()
    {
        $outboundID = Yii::$app->request->get('id');
        if ($outboundID) {
            $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
            $client = Client::findOne($clientEmploy->client_id);

            $stockItems = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode')
                ->andWhere([
                    'client_id'=>$client->id,
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

//           $stockIds = ArrayHelper::getColumn($stockItems,'id');
//           $stockIdMapBoxBarcode = ArrayHelper::map($stockItems,'id','box_barcode');

//           $stockExtraFields = \common\modules\stock\models\StockExtraField::find()
//                                ->andWhere(['parent_id'=>$stockIds])
//                                ->groupBy('parent_id, field_name')
//                                ->asArray()
//                                ->all();
//            $boxAndLcBarcode = [];
//            foreach ($stockExtraFields as $stockExtraField) {
//                $key = $stockExtraField['parent_id'];
//                if(isset($stockIdMapBoxBarcode[$key]) && $stockExtraField['field_name'] == \common\modules\stock\models\StockExtraField::OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO) {
//                    $boxAndLcBarcode[$stockIdMapBoxBarcode[$key]] = $stockExtraField['field_value'];
//                }
//            }
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
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

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


        $iCell = 1;
        $activeSheet->setCellValue('A' . $iCell, Yii::t('outbound/forms','№')); // +
        $activeSheet->setCellValue('B' . $iCell, Yii::t('outbound/titles','BOX_BARCODE')); // +
        $activeSheet->setCellValue('C' . $iCell, Yii::t('outbound/titles','BOX_SIZE')); // +
        $activeSheet->setCellValue('D' . $iCell, Yii::t('outbound/titles','BOX_KG')); // +
        $activeSheet->setCellValue('E' . $iCell, Yii::t('outbound/titles','LC')); // +
        $i = 0;
        foreach ($data['stockItems'] as $row) {
            $iCell++;

            $activeSheet->setCellValue('A' . $iCell, ++$i);
            $activeSheet->setCellValue('B' . $iCell, $row['box_barcode']);
            $activeSheet->setCellValue('C' . $iCell, BarcodeManager::mapM3ToBoxSize($row['box_size_barcode']));
            $activeSheet->setCellValue('D' . $iCell, $row['box_kg']);
            $activeSheet->setCellValue('E' . $iCell, (isset($data['boxAndLcBarcode'][$row['box_barcode']])? $data['boxAndLcBarcode'][$row['box_barcode']] :''));
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-report-' . $data['orderNumberTitle'] . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
        return true;
    }

    /*
     * @param array $data
     * */
    public function actionPrintProductsInBox()
    {
        if(Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }

        $objPHPExcel = new \PHPExcel();
        $stock = new Stock();

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
        $activeSheet->setCellValue('A' . $i, Yii::t('stock/forms', 'Client ID')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('stock/forms', 'Box Barcode')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'Parent order number')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('outbound/forms', 'Order number')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('outbound/forms', 'To point id')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('outbound/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('stock/forms', 'Box Volume')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('stock/forms', 'Status')); // +
        $activeSheet->setCellValue('I' . $i, Yii::t('stock/forms', 'Product Barcode')); // +
        $activeSheet->setCellValue('J' . $i, Yii::t('stock/forms', 'Kg box')); // +
        $activeSheet->setCellValue('K' . $i, Yii::t('stock/forms', 'Article')); // +
        $activeSheet->setCellValue('L' . $i, Yii::t('stock/forms', 'Name')); // +


        $modelId = Yii::$app->request->get('id');

        $searchModel = new OutboundBoxSearch();
        $clientsArray = Client::getActiveItems();
        $statusArray = $stock->getStatusArray();

        $dataProvider = $searchModel->searchProductInBoxArray($modelId);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $data) {
            $i++;
            $title = '';
            $clientTitle = '';
            $status = '';

            if ($to = Store::findOne($data['outboundOrder']['to_point_id'])) {
                $title = $to->getPointTitleByPattern('{city_name_lat} {shopping_center_name_lat} / {city_name} {shopping_center_name}');
                if (empty($to->shopping_center_name_lat)) {
                    $title = str_replace('/', '', $title);
                }
            }

            if (isset($clientsArray[$data['client_id']])) {
                $clientTitle = $clientsArray[$data['client_id']];
            }

            if (isset($statusArray[$data['status']])) {
                $status = $statusArray[$data['status']];
            }

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $data['box_barcode']);
            $activeSheet->setCellValue('C' . $i, $data['parent_order_number']);
            $activeSheet->setCellValue('D' . $i, $data['order_number']);
            $activeSheet->setCellValue('E' . $i, $title);
            $activeSheet->setCellValue('F' . $i, $data['product_qty']);
            $activeSheet->setCellValue('G' . $i, $data['box_size_m3']);
            $activeSheet->setCellValue('H' . $i, $status);
			$activeSheet->setCellValue('J' . $i, $data['box_kg']);
			//$activeSheet->setCellValue('K' . $i, $data['product_model']);
			//$activeSheet->setCellValue('L' . $i, $data['product_name']);

            $stockLots = Stock::find()->andWhere([
                'client_id'=>$data['client_id'],
                'box_barcode'=>$data['box_barcode'],
                'outbound_order_id'=>$data['outbound_order_id'],
            ])->all();

            if($stockLots) {
                foreach($stockLots as $stockLot) {
                    $i++;
                    $activeSheet->setCellValue('I' . $i, $stockLot->product_barcode);
					$activeSheet->setCellValue('K' . $i, $stockLot->product_model);
			        $activeSheet->setCellValue('L' . $i, $stockLot->product_name);
                }
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-boxes-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
     *
     * */
    public function actionPrintExportDoc()
    {   // /report/outbound/print-export-doc
        die('die - print-export-doc');
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize ' => '2560 MB');
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);


        $xls = \PHPExcel_IOFactory::load('ActiveColors.xls');
        $xls->setActiveSheetIndex(0);
        $colorActive = $xls->getActiveSheet()->toArray();
        $colors = [];
        foreach($colorActive as $color) {
            $colors [$color[0]] = $color[1];
        }

        $mappingOurBobBarcodeToDeFacto = [];
        $mappingOurBobBarcodeToDeFactoRevers = [];
        $pathToCSVFile = 'mappingOutboundBarcodeToDefacto.csv';
        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $mappingOurBobBarcodeToDeFacto[$data[1]] = $data[2];
                $mappingOurBobBarcodeToDeFactoRevers[$data[2]] = $data[1];
            }
        }

//        VarDumper::dump($mappingOurBobBarcodeToDeFacto,10,true);
//        die;
        $f = 'PackinglistBelarus42898-2'; // Packing-list-Russia-38322
//        $f = 'PackinglistBelarus45943+'; // Packing-list-Russia-38322
//        $f = 'PackinglistRussia-43655+'; // Packing-list-Russia-38322
        $xls = \PHPExcel_IOFactory::load('new/20170227/'.$f.'.xlsx');

        $xls->setActiveSheetIndex(0);
        $stocks = $xls->getActiveSheet()->toArray();
        $dataInBoxes = [];
        array_shift($stocks);

//        foreach($stocks as $k=>$stock) {
//            if(isset($mappingOurBobBarcodeToDeFacto[$stock[14]])) {
//                $stocks[$k][15] = $mappingOurBobBarcodeToDeFacto[$stock[14]];
//            } else {
//                echo $stock[14]."<br />";
//            }
//        }

//        VarDumper::dump($stocks,10,true);
//        die;
        $i = 0;
        $fileName = $f.'-result';
        foreach($stocks as $stock) {
            $i += 1;
            $color = '';
            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->getMasterData(null,$stock['4']);
            if (!$dataFromAPI['HasError']) {
                if (!empty($dataFromAPI['Data'])) {
                    $resultDataArray = $dataFromAPI['Data'];
                    $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
                    foreach ($resultDataArray as $resultData) {

                        if(isset($colors[$resultData->Color])) {
                            $color = $colors[$resultData->Color];
                            break;
                        }

                        $c2 = $resultData->Color[0].''.$resultData->Color[1];
                        if(isset($colors[$c2])) {
                            $color = $colors[$resultData->Color];
                            break;
                        }

                        $c3 = $resultData->Color[0].''.$resultData->Color[1].''.$resultData->Color[2];
                        if(isset($colors[$c3])) {
                            $color = $colors[$c3];
                            break;
                        }
                        file_put_contents('color'.$fileName.'.log', $resultData->Color . ";" . $stock['4'] . "\n", FILE_APPEND);
                    }
                }
                usleep(60000);
            }
//            $boxNumberKey =
            $shopName = trim($stock['0']);
            $partyNumber = trim($stock['1']);
            $boxNumber = trim($stock['16']); // trim($stock['15']);
            if (empty($shopName)) break;
            if(!empty($stock['18'])) {
//            if(!empty($stock['17'])) {
//                $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['boxNumber'] = $stock['17'];
                $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['boxNumber'] = $stock['18'];
            } else {
//                VarDumper::dump($stock,10,true);
//                die;
//                file_put_contents("dataInBoxes.log",print_r($stock,true)."\n",FILE_APPEND);
            }

            if(!isset($dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'])){ $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'] = ''; }
            $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'][$stock['9']] =  $stock['9'];
            $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['importer'] = $this->getImporterByCode($this->getCodeFromStoreName($stock['0']));
            $dataInBoxes[$partyNumber][$shopName][$boxNumber]['lots'][] = [
                'product'=>$stock['3'],
                'color'=> $color,
                'classification'=>$stock['7'],
                'qty'=>$stock['10'],
                'sizeBreakdown'=>$stock['8'],
                'grossW'=> $stock['20'],
                'netW'=>$stock['19'],
            ];
            file_put_contents('i'.$fileName.'.log',$i."\n",FILE_APPEND);

            //if ($i == 50) break;

        }
        file_put_contents('print-export-doc-data-'.$fileName.'-'.time().'.log',print_r($dataInBoxes,true)."\n"."\n"."\n",FILE_APPEND);
//        VarDumper::dump($dataInBoxes,10,true);
//        die;

        return $this->render('print-export-doc',['dataInBoxes'=>$dataInBoxes,'mappingOurBobBarcodeToDeFacto'=>$mappingOurBobBarcodeToDeFactoRevers]);
    }

    /*
     *
     * */
    public function actionPrintExportDoc1()
    {   // report/outbound/print-export-doc1
        die('die - print-export-doc-1');
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array('memoryCacheSize ' => '2560 MB');
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

//
        $xls = \PHPExcel_IOFactory::load('ActiveColors.xls');
        $xls->setActiveSheetIndex(0);
        $colorActive = $xls->getActiveSheet()->toArray();
        $colors = [];
        foreach($colorActive as $color) {
            $colors [$color[0]] = $color[1];
        }

        $mappingOurBobBarcodeToDeFacto = [];
        $mappingOurBobBarcodeToDeFactoRevers = [];
//        $pathToCSVFile = 'mappingOurBobBarcodeToDefacto.csv';
        $pathToCSVFile = 'mappingOutboundBarcodeToDefacto.csv';
        if (($handle = fopen($pathToCSVFile, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $mappingOurBobBarcodeToDeFacto[$data[1]] = $data[2];
//                $mappingOurBobBarcodeToDeFacto[$data[1]]['LC'] = $data[2];
//                $mappingOurBobBarcodeToDeFacto[$data[1]]['WAYBILL'] = $data[3];

                $mappingOurBobBarcodeToDeFactoRevers[$data[2]] = $data[1];
            }
        }
//
//        foreach($mappingOurBobBarcodeToDeFacto as $kBox=>$vLC) {
//            if($stkIDs = Stock::find()->select('id')->andWhere(['box_barcode'=>$kBox,'client_id'=>Client::CLIENT_DEFACTO])->column()) {
//                StockExtraField::saveBoxDefacto($stkIDs, [
//                    StockExtraField::OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO => $vLC['LC'],
//                    StockExtraField::OUTBOUND_BOX_FIELD_NAME_DEFACTO => $kBox,
//                    StockExtraField::OUTBOUND_WAYBILL_NUMBER_FIELD_NAME_DEFACTO => $vLC['WAYBILL'],
//                ]);
//            }
//        }


//        VarDumper::dump($mappingOurBobBarcodeToDeFacto,10,true);
//        die("OK");

        $fileName = 'RUS-176420-176492-176550-176606-176638-176711-176746';
        $xls = \PHPExcel_IOFactory::load('new/201709/22/'.$fileName.'.xlsx');

        $xls->setActiveSheetIndex(0);
        $stocks = $xls->getActiveSheet()->toArray();
        $dataInBoxes = [];
        array_shift($stocks);

//        VarDumper::dump($stocks,10,true);
//        echo "<br />";
//        echo "<br />";
//        echo "<br />";
//        VarDumper::dump($mappingOurBobBarcodeToDeFacto,10,true);
//        die;
        foreach($stocks as $k=>$stock) {
            $key = trim($stock[1]);
//            VarDumper::dump($stock[1],10,true);
//            VarDumper::dump(isset($mappingOurBobBarcodeToDeFacto[$key]),10,true);
//            VarDumper::dump($mappingOurBobBarcodeToDeFacto[$stock[1]],10,true);
            if(isset($mappingOurBobBarcodeToDeFacto[$key]) && !is_null($key)) {
                $stocks[$k][5] = $mappingOurBobBarcodeToDeFacto[$key];
            } elseif(!empty($key)) {
                echo $key."<br />";
                die("is null---->");
            } else {
//                echo $stock[1]."<br />";
//                die("Y---->");
            }
        }

        foreach($stocks as $k=>$stock) {
            $key = trim($stock[1]);
            if(isset($mappingOurBobBarcodeToDeFacto[$key])) {
                $stocks[$k][5] = $mappingOurBobBarcodeToDeFacto[$key];
                file_put_contents('result-'.$fileName.'-2.csv',
                    $stock[0] . ";"
                    .$key . ";"
                    .$stock[2] . ";"
                    .$stock[3] . ";"
                    .(isset($stock[4]) ? $stock[4] : '') . ";"
                    .$stock[5] . ";"
//                    .$stock[6] . ";"
                    ."\n", FILE_APPEND);
            } elseif(!empty($key)) {
                echo $key."<br />";
                die("is null---->");
            } else {
//                echo $stock[1]."<br />";
//                die("Y---->");
            }
        }

//        file_put_contents('color-'.$fileName.'.log', $resultData->Color . ";" . $stock['4'] . "\n", FILE_APPEND);'




        VarDumper::dump($stocks,10,true);
        echo "<br />";
        echo "YPA";
        echo "<br />";
        die;
        $i = 0;
        foreach($stocks as $stock) {
            $i += 1;
            $color = '';
            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->getMasterData(null,$stock['4']);
            if (!$dataFromAPI['HasError']) {
                if (!empty($dataFromAPI['Data'])) {
                    $resultDataArray = $dataFromAPI['Data'];
                    $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
                    foreach ($resultDataArray as $resultData) {

                        if(isset($colors[$resultData->Color])) {
                            $color = $colors[$resultData->Color];
                            break;
                        }

                        $c2 = $resultData->Color[0].''.$resultData->Color[1];
                        if(isset($colors[$c2])) {
                            $color = $colors[$resultData->Color];
                            break;
                        }

                        $c3 = $resultData->Color[0].''.$resultData->Color[1].''.$resultData->Color[2];
                        if(isset($colors[$c3])) {
                            $color = $colors[$c3];
                            break;
                        }
                        file_put_contents('color-'.$fileName.'.log', $resultData->Color . ";" . $stock['4'] . "\n", FILE_APPEND);
                    }
                }
                usleep(60000);
            }

            $shopName = trim($stock['0']);
            $partyNumber = trim($stock['1']);
            $boxNumber = trim($stock['16']); // trim($stock['15']);
            if (empty($shopName)) break;
            if(!empty($stock['18'])) {
//            if(!empty($stock['17'])) {
//                $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['boxNumber'] = $stock['17'];
                $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['boxNumber'] = $stock['18'];
            }

            if(!isset($dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'])){ $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'] = ''; }
            $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'][$stock['9']] =  $stock['9'];
            $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['importer'] = $this->getImporterByCode($this->getCodeFromStoreName($stock['0']));
            $dataInBoxes[$partyNumber][$shopName][$boxNumber]['lots'][] = [
                'product'=>$stock['3'],
                'color'=> $color,
                'classification'=>$stock['7'],
                'qty'=>$stock['10'],
                'sizeBreakdown'=>$stock['8'],
                'grossW'=> $stock['20'],
                'netW'=>$stock['19'],
            ];
            file_put_contents('i-'.$fileName.'.log',$i."\n",FILE_APPEND);

            //if ($i == 50) break;

        }
        file_put_contents('print-export-doc-data-'.time().'.log',print_r($dataInBoxes,true)."\n"."\n"."\n",FILE_APPEND);
//        VarDumper::dump($dataInBoxes,10,true);
//        die;

        return $this->render('print-export-doc',['dataInBoxes'=>$dataInBoxes,'mappingOurBobBarcodeToDeFacto'=>$mappingOurBobBarcodeToDeFactoRevers]);
    }

    /*
     *
     * */
    private function getCodeFromStoreName($storeName)
    {
        $r = '';
        if(!empty($storeName)) {
            $storeName = explode(' ',$storeName);
            if(isset($storeName[0]) && !empty($storeName[0])) {
                $r = $storeName[0];
            }
        }

        return $r;
    }
    /*
     *
     * */
    private function getImporterByCode($code) {
        $r = '';
        switch($code) {
            case "BEL": $r = 'LLC Ozon Retail - Bobruyskaya str. 6-13, 22006, Minsk, Republic of Belarus (Tax nr: 192294467)';
                break;
            case "RUS": $r = 'Ozon Giyim RSY LLC Russian Federation, 105066, Moskva, Novoryazanskaya Str. No:27 Building 2 Office 12';
                break;
        }
        return $r;
    }


    private function getOutBoxBarcodeByLcBarcode($lcBarcode)
    {
        $r = Stock::find()->select('box_barcode')->andWhere([
            'id' => (new Query())
                ->select('parent_id')
                ->from(StockExtraField::tableName())
                ->andWhere([
                    'field_name' => StockExtraField::OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO,
                    'field_value' => $lcBarcode
                ]),

        ])->scalar();

        return $r ? $r : '';
    }

//    private function getOutBoxBarcodeByLcBarcode($lcBarcode)
//    {
//        $r = StockExtraField::find()->select('field_value')->andWhere([
//            'field_name' => StockExtraField::OUTBOUND_BOX_FIELD_NAME_DEFACTO,
//            'parent_id' => (new Query())
//                ->select('parent_id')
//                ->from(StockExtraField::tableName())
//                ->andWhere([
//                    'field_name' => StockExtraField::OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO,
//                    'field_value' => $lcBarcode
//                ]),
//
//        ])->scalar();
//
//        return $r ? $r : '';
//    }

    /**
     * Displays a single Stock model.
     * @param integer $id
     * @return mixed
     */
    public function actionExportLabel()
    {
        $dateTimeUnique = date('Ymd').date('His');
        $dateUnique = date('Ymd');
        $timeUnique = date('Ymd').date('His');
        $dirPathRoot = 'uploads/import-export/'.$dateTimeUnique.'/';
        $dirPathResultRoot = 'uploads/import-export/'.$dateTimeUnique.'result/';

        ////Yii::$app->get('tcpdf');;
        $model = new ExportLabelForm();
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $filePath = '';

        if  (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->validate()) {
                //Путь сохранения загруженного файла
                $dirPath = 'uploads/' .$client->title. '/export-label/outbound-in/'.$dateUnique.'/'.$timeUnique;
                //Путь сохранения сгенерированного нами файла
//                $outPath = 'uploads/' .$client->title. '/export-label/outbound-out/'.$dateUnique.'/';
                BaseFileHelper::createDirectory($dirPath);
                $f = $model->file->getBaseName() . '.' . $model->file->getExtension();
                $pathToCSVFile = $dirPath.'/'.$f;
                $model->file->saveAs($pathToCSVFile);

                // Start parse XLSX file
                $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                $cacheSettings = array('memoryCacheSize ' => '2560 MB');
                \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                // color Start
//                $xls = \PHPExcel_IOFactory::load('ActiveColors.xls');
//                $xls->setActiveSheetIndex(0);
//                $colorActive = $xls->getActiveSheet()->toArray();
//                $colors = [];
//                $colorCache = [];
//                foreach($colorActive as $color) {
//                    $colorCache [$color[0]] = $color[1];
//                }
//
//                foreach ($colors as $colorKey=>$colorTitle) {
//                    Colors::create($colorKey,$colorTitle);
//                }

                // color End
                $colors = [];
//                $colors = Colors::find()->select('kod','title')->asArray()->all();
                $colors = ArrayHelper::map(Colors::find()->select('cod, title')->asArray()->all(),'cod','title');


                $xls = \PHPExcel_IOFactory::load($pathToCSVFile);

                $xls->setActiveSheetIndex(0);
                $stocks = $xls->getActiveSheet()->toArray();
                $dataInBoxes = [];
                array_shift($stocks);

//                VarDumper::dump($stocks,10,true);
//                die;
                $i = 0;
                $fileName = $f.'-result';
                $colorCache = [];
                foreach($stocks as $stock) {
                    $i += 1;
                    $color = '';
                    $colorKey = trim($stock['5']);
                    if(empty($colorKey)) {
                        continue;
                    }
//                    $colorKey = $stock['4'];
                    if(!isset($colorCache[$colorKey])) { // JOIN?
                        $product = Products::find()->andWhere(['LotOrSingleBarcode' => $colorKey])->one();
                        if ($product) {
                            $colorKod = $product->Color;
                            if(isset($colors[$colorKod])) {
                                $colorCache[$colorKey] = $color = $colors[$colorKod];
                            }

                            $colorKod = (isset($product->Color[0]) ? $product->Color[0] : '').''.(isset($product->Color[1]) ? $product->Color[1] : '');
                            if(isset($colors[$colorKod])) {
                                $colorCache[$colorKey] = $color = $colors[$colorKod];
                            }
//
//                            $colorKod = $product->Color[0].''.$product->Color[1].''.$product->Color[2];
                            $colorKod = (isset($product->Color[0]) ? $product->Color[0] : '').''.(isset($product->Color[1]) ? $product->Color[1] : '').''.(isset($product->Color[2]) ? $product->Color[2] : '');
                            if(isset($colors[$colorKod])) {
                                $colorCache[$colorKey] = $color = $colors[$colorKod];
                            }
                        }
                    } else {
                        $color = $colorCache[$colorKey];
                    }

                    if(empty($color)) {
                            $api = new DeFactoSoapAPIV2();
                            $dataFromAPI = $api->getMasterData(null,$colorKey);
                            if (!$dataFromAPI['HasError']) {
                                if (!empty($dataFromAPI['Data'])) {
                                    $resultDataArray = $dataFromAPI['Data'];
                                    $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
                                    foreach ($resultDataArray as $resultData) {

                                        //Products::create($resultData->SkuId,$resultData->LotOrSingleBarcode,$resultData->ShortCode,$resultData->Color);
                                        file_put_contents('getMasterData-colorKod.log',print_r($resultData,true)."\n",FILE_APPEND);
                                        $colorKod = $resultData->Color;
                                        if(isset($colors[$colorKod])) {
                                            $colorCache[$colorKey] = $color = $colors[$colorKod];
                                            break;
                                        }

//                                        $colorKod = $resultData->Color[0].''.$resultData->Color[1];
                                        $colorKod = (isset($resultData->Color[0]) ? $resultData->Color[0] : $resultData->Color[0]).''.(isset($resultData->Color[1]) ? $resultData->Color[1] : $resultData->Color[1]);
                                        if(isset($colors[$colorKod])) {
                                            $colorCache[$colorKey] = $color = $colors[$colorKod];
                                            break;
                                        }
//
//                                        $colorKod = $resultData->Color[0].''.$resultData->Color[1].''.$resultData->Color[2];
                                        $colorKod = (isset($resultData->Color[0]) ? $resultData->Color[0] : $resultData->Color[0]).''.(isset($resultData->Color[1]) ? $resultData->Color[1] : $resultData->Color[1]).''.(isset($resultData->Color[2]) ? $resultData->Color[2] : $resultData->Color[2]);
                                        if(isset($colors[$colorKod])) {
                                            $colorCache[$colorKey] = $color = $colors[$colorKod];
                                            break;
                                        }
                                        file_put_contents('colorK-'.$fileName.'-'.date('Ymd').'.log', $resultData->Color . ";" . $colorKey . "\n", FILE_APPEND);
                                    }
                                }
                            }
                        }

                    $shopName = trim($stock['0']);
                    $partyNumber = trim($stock['1']);
//                    $boxNumber = trim($stock['16']);
                    $boxNumber = trim($stock['17']);
                    if (empty($shopName)) break;
//                    if(!empty($stock['18'])) {
                    if(!empty($stock['19'])) {
                        $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['boxNumber'] = $stock['19'];
//                        $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['boxNumber'] = $stock['18'];
                    }

                    if(!isset($dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'])){ $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'] = ''; }
                    $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'][$stock['10']] =  $stock['10'];
//                    $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['madeIn'][$stock['9']] =  $stock['9'];
                    $dataInBoxes[$partyNumber][$shopName][$boxNumber]['box']['importer'] = $this->getImporterByCode($this->getCodeFromStoreName($stock['0']));
                    $dataInBoxes[$partyNumber][$shopName][$boxNumber]['lots'][] = [
                        'product'=>$stock['4'],
//                        'product'=>$stock['3'],
                        'color'=> $color,
                        'classification'=>$stock['8'],
//                        'classification'=>$stock['7'],
                        'qty'=>$stock['11'],
//                        'qty'=>$stock['10'],
                        'sizeBreakdown'=>$stock['9'],
//                        'sizeBreakdown'=>$stock['8'],
                        'grossW'=> $stock['21'],
//                        'grossW'=> $stock['20'],
                        'netW'=>$stock['20'],
//                        'netW'=>$stock['19'],
                    ];
                    file_put_contents('i'.$fileName.'.log',$i."\n",FILE_APPEND);

                    //if ($i == 50) break;

                }

                file_put_contents('colorCache.log',print_r($colorCache,true),FILE_APPEND);


                foreach ($dataInBoxes as $partyNumber => $shopNames) {
                    foreach ($shopNames as $shopName => $boxes) {
                        $pdf = new \TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                        $pdf->SetFont('arial', '', 8); //ok
                        $pdf->SetMargins(10, 5, 10);
                        $pdf->setPrintHeader(false);
                        $pdf->setPrintFooter(false);
// set default monospaced font
                        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                        $orderOnOnePage = 2;

                        foreach ($boxes as $LcBarcode => $dataInBox) {

                            if ($orderOnOnePage % 2 == 0) {
                                $pdf->AddPage();
                            } else {
                                $pdf->SetAbsY(150);
                            } // 2 3 4 5

                            $html = '';
                            $grossWeight = 0;
                            $netWeight = 0;
                            $cartonNumber = $dataInBox['box']['boxNumber'];
                            $madeIn = implode(',',$dataInBox['box']['madeIn']);
                            $importer = $dataInBox['box']['importer'];

                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Importer:</td>
                <td width="85%">' . $importer . '</td>
            </tr>
        </table>';

//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Exporter:</td>
                <td width="85%">Defacto Perakende Ticaret A.Ş./Turkey</td>
            </tr>
        </table>';

//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Made In:</td>
                <td width="85%">' . trim($madeIn, ',') . '</td>
            </tr>
        </table>';
//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="15%" style="font-weight: bold">Trade Mark:</td>
                <td width="15%">DeFacto</td>
                <td width="70%" align="right">' . $LcBarcode . '</td>
            </tr>
        </table>';
                            $pdf->writeHTML($html, true, false, true, false, '');
                            $pdf->ln(1);

                            $html = '';
                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="12%" style="font-weight: bold">Product</td>
                <td width="14%" style="font-weight: bold">Color</td>
                <td width="12%" style="font-weight: bold">Classification</td>
                <td width="8%"  style="font-weight: bold">Qty</td>
                <td width="40%" style="font-weight: bold">Size Breakdown</td>
                <td width="7%"  style="font-weight: bold">GrossW</td>
                <td width="7%"  style="font-weight: bold">NetW</td>
            </tr>';

                            foreach ($dataInBox['lots'] as $item) {

                                $grossWeight += $item['grossW'];
                                $netWeight += $item['netW'];

                                $html .= '
            <tr>
                <td width="12%" style="border: 0.4px solid black">' . $item['product'] . '</td>
                <td width="14%" style="border: 0.4px solid black">' . $item['color'] . '</td>
                <td width="12%" style="border: 0.4px solid black">' . $item['classification'] . '</td>
                <td width="8%" style="border: 0.4px solid black">' . $item['qty'] . '</td>
                <td width="40%" style="border: 0.4px solid black">' . $item['sizeBreakdown'] . '</td>
                <td width="7%" style="border: 0.4px solid black">' . '0' . '</td>
                <td width="7%" style="border: 0.4px solid black">' . '0' . '</td>
            </tr>';
                            }

                            $html .= '</table>';

                            $pdf->writeHTML($html, true, false, true, false, '');
                            $pdf->ln(1);

                            $html = '';
                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="20%" style="font-weight: bold">Gross Weight:</td>
                <td width="20%">' . $grossWeight . '</td>
                <td width="60%" align="right" style="font-weight: bold">Carton Number: ' . $cartonNumber . '</td>
            </tr>
        </table>';
//            $pdf->writeHTML($html, true, false, true, false, '');
//
//            $html = '';
                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="20%" style="font-weight: bold">Net Weight:</td>
                <td width="80%">' . $netWeight . '</td>
            </tr>
        </table>';

                            $pdf->writeHTML($html, true, false, true, false, '');

                            $html = '';
                            $html .= '<table width="100%" style="padding:2px" >
            <tr>
                <td width="100%">' . $this->getOutBoxBarcodeByLcBarcode($LcBarcode).' / '.$partyNumber.' / '.$shopName. '</td>
            </tr>
        </table>';

                            $pdf->writeHTML($html, true, false, true, false, '');

                            $orderOnOnePage += 1;
                        }

                        $pdf->lastPage();
                        $dirPath = $dirPathRoot.'/'.$partyNumber;
                        $fileName = $partyNumber.'-'.$shopName.'-import-export.pdf';
                        \yii\helpers\BaseFileHelper::createDirectory($dirPath);
                        $fullPath = $dirPath.'/'.$fileName;
                        $pdf->Output($fullPath, 'F');
                        unset($pdf);
                    }
                }

                \yii\helpers\BaseFileHelper::createDirectory($dirPathResultRoot);
                $zip = new \ZipArchive();
                $zipFileName = 'export-label-'.$dateUnique.'-'.$timeUnique.'2.zip';
                $ret = $zip->open($dirPathResultRoot.$zipFileName, \ZipArchive::CREATE);
                if ($ret !== TRUE) {
                    printf('Failed with code %d', $ret);
                } else {
                    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirPathRoot), \RecursiveIteratorIterator::SELF_FIRST);
                    foreach ($iterator as $path) {
                        if (!$path->isDir()) {
                            $pathToDir = str_replace(['\.', '\..', '.', '..'], '', $path->__toString());
                            $zip->addFile($path->__toString(), basename(dirname($pathToDir)) . '/' . basename($path->__toString()));
                        }
                    }
                    $zip->close();
                }
                $filePath = Url::to('@web/'.$dirPathResultRoot.$zipFileName);

                Yii::$app->getSession()->setFlash('success', Yii::t('inbound/messages', 'Файл успешно загружен. Вы можете скачать файл с нашими данными нажав на кнопку "Скачать файл"'));

            }
        }

        return $this->render('export-label-form', [
            'model' => $model,
            'href' => $filePath
        ]);
    }
    /*
     *
     * */
    public function actionPrintExportDocTEST_TEST()
    {
        // print-export-doc

        $objPHPExcel = new \PHPExcel();


// Открываем файл
        $xls = \PHPExcel_IOFactory::load('pkl.xlsx');
// Устанавливаем индекс активного листа
        $xls->setActiveSheetIndex(0);
// Получаем активный лист
        $sheet = $xls->getActiveSheet()->toArray();
                                    VarDumper::dump($sheet,10,true);
                            die;
        echo "<table>";

// Получили строки и обойдем их в цикле
        echo "<table>";

        for ($i = 1; $i <= $sheet->getHighestRow(); $i++) {
            echo "<tr>";

            $nColumn = \PHPExcel_Cell::columnIndexFromString(
                $sheet->getHighestColumn());

            for ($j = 0; $j < $nColumn; $j++) {
                $value = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                echo "<td>$value</td>";
            }

            echo "</tr>";
        }
        echo "</table>";

        die;
        ///-----------------------------------------------
        $outboundId = Yii::$app->request->get('id');

        $outboundOrder =  OutboundOrder::findOne($outboundId);

        $stocks = Stock::find()->select('box_barcode, product_barcode, count(product_barcode) as qtyLotInBox, box_kg, product_model')
                        ->andWhere([
                            'client_id'=>Client::CLIENT_DEFACTO,
                            'outbound_order_id'=>$outboundId,
                        ])
                        ->orderBy('box_barcode, product_barcode')
                        ->groupBy('box_barcode, product_barcode')
                        ->asArray()
                        ->all();

        $dataInBoxes = [];
        foreach($stocks as $stock) {

            $color = $size = $classification = $SizeBreakdown  =  '';
            $grossW = 0.00;
            $netW = 0.00;

            $api = new DeFactoSoapAPIV2();
            $dataFromAPI = $api->getMasterData(null,$stock['product_barcode']);
            if (!$dataFromAPI['HasError']) {
                if (!empty($dataFromAPI['Data'])) {
                    $resultDataArray = $dataFromAPI['Data'];

                    $resultDataArray = count($resultDataArray) == 1 ? [$resultDataArray] : $resultDataArray;
                    foreach ($resultDataArray as $resultData) {
                        $stock['product_model'] = $resultData->ShortCode;

                        if(!empty($resultData->Description)) {
                            $description = explode("/",$resultData->Description);
                            if(isset($description[1]) && !empty($description[1])) {
                                $classification = $description[1];
                            }
                        }

                        if(!empty($resultData->Note)) {
                            $colorName = $resultData->Note;
                            if(isset($resultData->Size)) {
                                $colorName = trim(trim($resultData->Note,$resultData->Size));
                            }
                            $color = $colorName;
                        }

                    }
                }
            }

//                            VarDumper::dump($description,10,true);
//                            die;

            $dataInBoxes[$stock['box_barcode']][] = [
                'product'=>$stock['product_model'],
                'color'=>$color,
                'classification'=>$classification,
                'qty'=>$stock['qtyLotInBox'],
                'sizeBreakdown'=>$SizeBreakdown,
                'grossW'=>$grossW,
                'netW'=>$netW,
                'grossWeight'=>$stock['box_kg'],
            ];
        }

//        VarDumper::dump($dataInBoxes,10,true);
//        VarDumper::dump($dataFromAPI,10,true);
//        die;

        return $this->render('print-export-doc',['dataInBoxes'=>$dataInBoxes]);
    }
    /*
   *
   * */
    public function actionPrintBoxKgListByParent()
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $searchModel = new OutboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dps = $dataProvider->getModels();
        $outboundIDs = ArrayHelper::getColumn($dps,'id');

        if ($outboundIDs) {
            $stockItems = Stock::find()
                ->select('stock.id, box_barcode, box_kg, box_size_barcode , outbound_order_id')
                ->andWhere([
                    'client_id'=>$client->id,
                    'outbound_order_id' => $outboundIDs,
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

//            $stockIds = ArrayHelper::getColumn($stockItems,'id');

//            $stockExtraFields = \common\modules\stock\models\StockExtraField::find()
//                ->andWhere(['parent_id'=>$stockIds])
//                ->groupBy('parent_id, field_name')
//                ->asArray()
//                ->all();
//            $boxAndLcBarcode = [];
//            $parentIds = [];
//            foreach ($stockExtraFields as $stockExtraField) {
//                $key = $stockExtraField['parent_id'];
//                if(!isset($parentIds[$key]) && $stockExtraField['field_name'] == 'OUTBOUND_BOX_FIELD_NAME_DEFACTO') {
//                    $boxAndLcBarcode[$stockExtraField['field_value']] = '';
//                    $parentIds[$key] = $stockExtraField['field_value'];
//                } elseif(isset($parentIds[$key]) && $stockExtraField['field_name'] == 'OUTBOUND_LC_BARCODE_FIELD_NAME_DEFACTO') {
//                    $boxAndLcBarcode[$parentIds[$key]] = $stockExtraField['field_value'];
//                }
//            }

            $stockIdMapBoxBarcode = ArrayHelper::map($stockItems,'id','box_barcode');
            $boxAndLcBarcode = ArrayHelper::map(OutboundBoxService::getAllByBarcode($stockIdMapBoxBarcode),'our_box', 'client_box');


            $toPoint = '';
            $orderNumberTitle = time().'-rep';
//            if($outboundOrder = OutboundOrder::findOne($outboundID)) {
//                $orderNumberTitle = $outboundOrder->parent_order_number.'-'.$outboundOrder->order_number;
//                if($point = $outboundOrder->toPoint){
//                    $toPoint = $point->getPointTitleByPattern('stock');
//                }
//            }

            return $this->excelPrintPrintBoxKgList([
                'stockItems' => $stockItems,
                'boxAndLcBarcode' => $boxAndLcBarcode,
                'toPoint'=>$toPoint,
                'orderNumberTitle'=>$orderNumberTitle
            ]);
        }

        Yii::$app->session->setFlash('danger', 'Вы не указали номер накладной');
        return $this->redirect('/report/outbound/index');
    }
	
/*
		 public function actionCompleted() {

		 $BatchId = Yii::$app->request->get('id');
		 $api =  new DeFactoSoapAPIV2();

		 $params['request'] = [
			 'BusinessUnitId'=>'1029',
			 'BatchId'=>$BatchId,
			 'PageSize'=>'0',
			 'PageIndex'=>'0',
			 'CountAllItems'=>false,
		 ];

		 $result = $api->sendRequest('MarkBatchforCompleted',$params);
//		 $result =$params;

		 file_put_contents("MarkBatchforCompleted.txt",print_r($result,true)."\n",FILE_APPEND);

		 Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Completed'));

		 return $this->redirect("/report/outbound/index");
	 }
*/

	 public function actionCompleteApi() {

		 if(!ClientManager::canIndexReport() ) {
			 throw new NotFoundHttpException('У вас нет доступа к этой странице');
		 }

		 $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
		 $client = Client::findOne($clientEmploy->client_id);
		 $searchModel = new OutboundOrderSearch();

		 $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		 $dataProvider->pagination = false;
		 $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
		 $dps = $dataProvider->getModels();


		 $batchIds = [];

		 foreach ($dps as $model) {
		 	if(($model->api_complete_status == "no" || empty($model->api_complete_status)) && $model->status == 27) {
				$batchIds[$model->parent_order_number] = $model->parent_order_number;
			}
		 }

		 if(empty($batchIds)) {
			 Yii::$app->getSession()->setFlash('warning', Yii::t('outbound/messages', 'Эта партия уже принята'));
			 return $this->redirect("/report/outbound/index");
		 }

		 $api =  new DeFactoSoapAPIV2();

		 foreach ($batchIds as $batchId) {
//			 echo $batchId."<br />";

			 $params['request'] = [
				 'BusinessUnitId'=>'1029',
				 'BatchId'=>$batchId,
				 'PageSize'=>'0',
				 'PageIndex'=>'0',
				 'CountAllItems'=>false,
			 ];
			 
// VarDumper::dump($params,10,true);
			 
			 $result = $api->sendRequest('MarkBatchforCompleted',$params);
			 file_put_contents("MarkBatchforCompleted.txt",print_r($result,true)."\n",FILE_APPEND);

			 OutboundOrder::updateAll(["api_complete_status"=>"yes"],["parent_order_number"=>$batchId]);
		 }

		 Yii::$app->getSession()->setFlash('success', Yii::t('outbound/messages', 'Completed BatchId : '.$batchId));

		 return $this->redirect("/report/outbound/index");
	 }
	 
}
