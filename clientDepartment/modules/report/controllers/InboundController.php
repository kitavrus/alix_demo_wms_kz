<?php

namespace app\modules\report\controllers;

use clientDepartment\modules\report\models\OutboundOrderGridSearch;
use common\modules\client\models\ClientEmployees;
use common\modules\inbound\models\ConsignmentInboundOrders;
use common\modules\inbound\models\InboundOrder;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\product\models\Product;
use common\modules\product\models\ProductBarcodes;
use common\modules\stock\models\Stock;
use stockDepartment\modules\wms\managers\defacto\api\DeFactoSoapAPIV2;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use Yii;
use clientDepartment\components\Controller;
use clientDepartment\modules\report\models\InboundOrderSearch;
use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrderItem;
use clientDepartment\modules\report\models\InboundOrderItemSearch;
use yii\helpers\BaseFileHelper;

class InboundController extends Controller
{
    public function actionIndex()
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $showExportPnButton = false;
        $searchModel = new InboundOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['client_id' => $clientEmploy->client_id]);
        $cloneQuery = clone $dataProvider->query;

        //Провреям результат выборки для определение того, показывать нам кнопку 'экспорт ПН' или нет
        if($data = $cloneQuery->groupBy('consignment_inbound_order_id')->asArray()->all()){
            if(count($data)==1){
                if(isset($data[0]['consignment_inbound_order_id'])){
                    if($cio = ConsignmentInboundOrders::find()->andWhere([
                        'client_id'=>$clientEmploy->client_id,
                        'id' => $data[0]['consignment_inbound_order_id'
                        ]])->one()){
                        if($cio->status == Stock::STATUS_INBOUND_COMPLETE){
                            $showExportPnButton = true;
                        }
                    }

                }
            }
        }
        //VarDumper::dump($dataProvider, 10 ,true); die;
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientStoreArray' => $clientStoreArray,
            'showExportPnButton' => $showExportPnButton,
        ]);
    }

    public function actionView($id)
    {
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

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
     * Finds the TlDeliveryProposalBilling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InboundOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);

        if (($model = InboundOrder::findOne(['id'=>$id, 'client_id'=>$clientEmploy->client_id])) !== null) {
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

        $noFindDataProvider = null;
        $noReservedDataProvider = null;

        $searchModel = new OutboundOrderGridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['client_id' => $clientEmploy->client_id]);


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
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $objPHPExcel = new \PHPExcel();
        $clientStoreArray = TLHelper::getStoreArrayByClientID($client->id);

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
        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Order Number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'From point id')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('outbound/forms', 'To point id')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('inbound/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('inbound/forms', 'Accepted Qty')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('inbound/forms', 'Accepted Number Places Qty')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('inbound/forms', 'Expected Number Places Qty')); // +
        $activeSheet->setCellValue('I' . $i, Yii::t('inbound/forms', 'Expected Datetime')); // +
        $activeSheet->setCellValue('J' . $i, Yii::t('inbound/forms', 'Begin Datetime')); // +
        $activeSheet->setCellValue('K' . $i, Yii::t('inbound/forms', 'Confirmed At')); // +
        $activeSheet->setCellValue('L' . $i, Yii::t('inbound/forms', 'Created At')); // +
        $activeSheet->setCellValue('M' . $i, Yii::t('inbound/forms', 'Status')); // +
        $activeSheet->setCellValue('N' . $i, Yii::t('inbound/forms', 'Order Type')); // +
		$activeSheet->setCellValue('O' . $i, Yii::t('inbound/forms', 'Comment')); // +
		$activeSheet->setCellValue('P' . $i, Yii::t('inbound/forms', 'Order Number')); // +
		$activeSheet->setCellValue('Q' . $i, Yii::t('inbound/forms', 'Sku ID')); // +
		
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new InboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $fromPointId = isset ($clientStoreArray[$model->from_point_id]) ? $clientStoreArray[$model->from_point_id] : '-';
            $toPointId = isset ($clientStoreArray[$model->to_point_id]) ? $clientStoreArray[$model->to_point_id] : '-';
            $i++;
            $activeSheet->setCellValue('A' . $i, $model->id);
            $activeSheet->setCellValue('B' . $i, substr($model->order_number,0,11));
            $activeSheet->setCellValue('C' . $i, $fromPointId);
            $activeSheet->setCellValue('D' . $i, $toPointId);
            $activeSheet->setCellValue('E' . $i, $model->expected_qty);
            $activeSheet->setCellValue('F' . $i, $model->accepted_qty);
            $activeSheet->setCellValue('G' . $i, $model->accepted_number_places_qty); // +
            $activeSheet->setCellValue('H' . $i, $model->expected_number_places_qty); // +
            $activeSheet->setCellValue('I' . $i, !empty($model->expected_datetime) ? Yii::$app->formatter->asDatetime($model->expected_datetime): "-"); // +
            $activeSheet->setCellValue('J' . $i, !empty($model->begin_datetime) ? Yii::$app->formatter->asDatetime($model->begin_datetime): "-"); // +
            $activeSheet->setCellValue('K' . $i, !empty($model->date_confirm)? Yii::$app->formatter->asDatetime($model->date_confirm): "-"); // +
            $activeSheet->setCellValue('L' . $i, !empty($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at): "-"); // +
            $activeSheet->setCellValue('M' . $i, $model->getStatusValue()); // +
            $activeSheet->setCellValue('N' . $i, $model->getOrderTypeValue()); // +
			$activeSheet->setCellValue('O' . $i, $model->comments); // +
			$activeSheet->setCellValue('P' . $i, $model->order_number); // +

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/'.$client->id.'/inbound/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'inbound-report-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
     * Экспорт приходной накладной (Colins)
     *
     **/
    public function actionExportPn()
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
        $activeSheet->setCellValue('D' . $i, Yii::t('inbound/forms', 'Accepted Qty'));

        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new InboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $filterOrders = ArrayHelper::map($dataProvider->query->asArray()->all(), 'id', 'id');


        $itemsArray = [];
        if($filterOrders){
           $inboundItems = InboundOrderItem::find()
               ->select('product_barcode, inbound_order_id, sum(expected_qty) AS expected_qty')
               ->andWhere(['inbound_order_id'=>$filterOrders])
               ->groupBy('product_barcode')
               ->orderBy('inbound_order_id')
               ->asArray()
               ->all();

            //Массив для мапинга принятого кол-ва: ШК => принятое кол-во
            $acceptedQty = Stock::find()
                ->select('product_barcode, count(id) AS accepted_qty')
                ->andWhere([
                    'client_id' => $client->id,
                    'inbound_order_id' => $filterOrders,
                ])
                ->andWhere(['status_availability' => [Stock::STATUS_AVAILABILITY_YES,Stock::STATUS_AVAILABILITY_RESERVED]])
                ->groupBy('product_barcode')
                ->asArray()
                ->all();

            //Массив для мапинга цены: ШК => цена
            $priceMap = ProductBarcodes::find()
                ->select('product_barcodes.id, product.id, product_barcodes.product_id, product_barcodes.barcode, product.price')
                ->joinWith('product')
                ->andWhere([
                    'product_barcodes.client_id' => $client->id,
                    'product_barcodes.barcode' => ArrayHelper::map($inboundItems, 'product_barcode', 'product_barcode'),
                ])
                ->asArray()
                ->all();

            $acceptedQty = ArrayHelper::map($acceptedQty, 'product_barcode', 'accepted_qty');
            $priceMap = ArrayHelper::map($priceMap, 'barcode', 'price');

            foreach($inboundItems as $item){
                $price = '-not-set-';
                if(isset($priceMap[$item['product_barcode']])){
                    $price = Yii::$app->formatter->asDecimal($priceMap[$item['product_barcode']], 2);
                }

                $itemsArray[$item['product_barcode']] = [
                    'product_barcode' => $item['product_barcode'],
                    'price' => $price,
                    'expected_qty' => $item['expected_qty'],
                    'accepted_qty' => isset($acceptedQty[$item['product_barcode']]) ? $acceptedQty[$item['product_barcode']]: '0'
                ];

            }
        }

        //VarDumper::dump($itemsArray, 10, true); die;

        if($itemsArray){
            foreach ($itemsArray as $row){
                $i++;
                $activeSheet->setCellValue('A' . $i, $row['product_barcode']);
                $activeSheet->setCellValue('B' . $i, $row['expected_qty']);
                $activeSheet->setCellValue('C' . $i, $row['price']);
                $activeSheet->setCellValue('D' . $i, $row['accepted_qty']);
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/'.$client->id.'/inbound/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'inbound-report-export-PN-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
     * Import to excel
     *
     **/
    public function actionExportDifferencesToExcel()
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
            ->setTitle('inbound-differences' . date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A' . $i, Yii::t('inbound/forms', 'Party number')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Order Number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'From point id')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('stock/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('inbound/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('inbound/forms', 'Accepted Qty')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('inbound/forms', 'Difference')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('inbound/forms', 'Status')); // +
		$activeSheet->setCellValue('I' . $i, Yii::t('inbound/forms', 'Sku ID')); // +

        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);
        $searchModel = new InboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dataProvider->query->andWhere('expected_qty!=accepted_qty');

        $inboundOrders = $dataProvider->getModels();

        foreach ($inboundOrders as $io) {
            if($items = $io->orderItems){
                foreach($items as $item){
                    if($item->expected_qty != $item->accepted_qty){
                        $i++;
                        $activeSheet->setCellValue('A' . $i, $io->parent_order_number);
                        $activeSheet->setCellValue('B' . $i, $io->order_number);
                        $activeSheet->setCellValue('C' . $i, isset($clientStoreArray[$io->from_point_id]) ? $clientStoreArray[$io->from_point_id] : '-');
                        $activeSheet->setCellValue('D' . $i, $item->product_barcode);
                        $activeSheet->setCellValue('E' . $i, $item->expected_qty); // +
                        $activeSheet->setCellValue('F' . $i, $item->accepted_qty);
                        $activeSheet->setCellValue('G' . $i, ($item->expected_qty - $item->accepted_qty)*-1);
                        $activeSheet->setCellValue('H' . $i, $io->getStatusValue()); // +
						$activeSheet->setCellValue('I' . $i, $item->product_sku); // +
                    }

                }

            }


        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/'.$client->id.'/inbound/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'inbound-orders-differences-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

     /*
     * Import to excel
     *
     **/
    public function actionExportToExcelFull()
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
            ->setTitle('inbound-differences' . date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A' . $i, Yii::t('inbound/forms', 'Party number')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Order Number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'From point id')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('stock/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('inbound/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('inbound/forms', 'Accepted Qty')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('inbound/forms', 'Difference')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('inbound/forms', 'Status')); // +
        $activeSheet->setCellValue('I' . $i, Yii::t('inbound/forms', 'Created At')); // +
		$activeSheet->setCellValue('J' . $i, Yii::t('inbound/forms', 'Article')); // +
		$activeSheet->setCellValue('K' . $i, Yii::t('inbound/forms', 'Size')); // +
		$activeSheet->setCellValue('L' . $i, Yii::t('inbound/forms', 'Дата Закрытия')); // +
		$activeSheet->setCellValue('M' . $i, Yii::t('inbound/forms', 'Sku ID')); // +

        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);
        $searchModel = new InboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
//        $dataProvider->query->andWhere('expected_qty!=accepted_qty');

        $inboundOrders = $dataProvider->getModels();

        foreach ($inboundOrders as $io) {
//            if($items = $io->orderItems){
            if($items = $io->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('order_by!=0 DESC'))->all()) {
                foreach($items as $item){
//                    if($item->expected_qty != $item->accepted_qty) {
                        $i++;
                        $activeSheet->setCellValue('A' . $i, $io->parent_order_number);
                        $activeSheet->setCellValue('B' . $i, $io->order_number);
                        $activeSheet->setCellValue('C' . $i, isset($clientStoreArray[$io->from_point_id]) ? $clientStoreArray[$io->from_point_id] : '-');
                        $activeSheet->setCellValue('D' . $i, $item->product_barcode);
                        $activeSheet->setCellValue('E' . $i, $item->expected_qty); // +
                        $activeSheet->setCellValue('F' . $i, $item->accepted_qty);
                        $activeSheet->setCellValue('G' . $i, ($item->expected_qty - $item->accepted_qty)*-1);
                        $activeSheet->setCellValue('H' . $i, $io->getStatusValue()); // +
                        $activeSheet->setCellValue('I' . $i, !empty($io->created_at) ? Yii::$app->formatter->asDatetime($io->created_at): "-");
                        $activeSheet->setCellValue('J' . $i, $item->product_model);		
                        $activeSheet->setCellValue('K' . $i, $item->product_size);		
						$activeSheet->setCellValue('L' . $i,  !empty($io->date_confirm) ? Yii::$app->formatter->asDatetime($io->date_confirm): "-");	
						$activeSheet->setCellValue('M' . $i, $item->product_sku); // +						
//                    }
                }
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/'.$client->id.'/inbound/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'inbound-orders-full-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }

    /*
     * Import to excel
     *
     **/
    public function actionExportToExcelFullOne()
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
            ->setTitle('inbound-differences' . date('d.m.Y'));


        $i = 1;
        $activeSheet->setCellValue('A' . $i, Yii::t('inbound/forms', 'Party number')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Order Number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'From point id')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('stock/forms', 'Product barcode')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('inbound/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('inbound/forms', 'Accepted Qty')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('inbound/forms', 'Difference')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('inbound/forms', 'Status')); // +
        $activeSheet->setCellValue('I' . $i, Yii::t('inbound/forms', 'Created At')); // +
        $activeSheet->setCellValue('J' . $i, Yii::t('inbound/forms', 'Article')); // +
        $activeSheet->setCellValue('K' . $i, Yii::t('inbound/forms', 'Client SkuId')); // +
		

        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $modelId = Yii::$app->request->get('id');

        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $clientStoreArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);


        $searchModel = new InboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id'=>$client->id]);
        $dataProvider->query->andFilterWhere(['id'=>$modelId]);

        $inboundOrders = $dataProvider->getModels();

        foreach ($inboundOrders as $io) {
            if($items = $io->getOrderItems()->select('*,(expected_qty - accepted_qty) as order_by')->orderBy(new Expression('order_by!=0 DESC'))->all()) {
                foreach($items as $item) {
                        $i++;
                        $activeSheet->setCellValue('A' . $i, $io->parent_order_number);
                        $activeSheet->setCellValue('B' . $i, $io->order_number);
                        $activeSheet->setCellValue('C' . $i, isset($clientStoreArray[$io->from_point_id]) ? $clientStoreArray[$io->from_point_id] : '-');
                        $activeSheet->setCellValue('D' . $i, $item->product_barcode);
                        $activeSheet->setCellValue('E' . $i, $item->expected_qty); // +
                        $activeSheet->setCellValue('F' . $i, $item->accepted_qty);
                        $activeSheet->setCellValue('G' . $i, ($item->expected_qty - $item->accepted_qty)*-1);
                        $activeSheet->setCellValue('H' . $i, $io->getStatusValue()); // +
                        $activeSheet->setCellValue('I' . $i, !empty($io->created_at) ? Yii::$app->formatter->asDatetime($io->created_at): "-");
                        $activeSheet->setCellValue('J' . $i, $item->product_model);
                        $activeSheet->setCellValue('K' . $i, $item->product_sku);
                }
            }
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/'.$client->id.'/inbound/export/'.date('Ymd').'/'.date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'inbound-orders-full-export-'.time().'.xlsx';
        $fullPath = $dirPath.'/'.$fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath,$fileName);
    }
	
	
	/*
 * Import to excel
 *
 **/
	public function actionCommentsForm($id)
	{
		$model = $this->findModel($id);
//		\yii\helpers\VarDumper::dump($model,10,true);
//		\yii\helpers\VarDumper::dump($model->order_number,10,true);
//		die;
		$model->setScenario("CommentsAdd");
		if ($model->load(Yii::$app->request->post()) && $model->save(false)) {
		//            return $this->redirect(['/transportLogistics/tl-delivery-proposal/view', 'id' => $model->tl_delivery_proposal_id]);
		return $this->redirect(['view', 'id' => $model->id]);
		} else {
		return $this->render('update', [
		'model' => $model,
		]);
		}
	}
	
}
