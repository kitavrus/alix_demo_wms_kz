<?php

namespace app\modules\warehouseDistribution\controllers\koton;

use app\modules\warehouseDistribution\models\OutboundOrderGridSearch;
use common\modules\client\models\ClientEmployees;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use Yii;
use clientDepartment\components\Controller;
use app\modules\warehouseDistribution\models\OutboundOrderSearch;
use common\modules\client\models\Client;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use \DateTime;
use common\modules\outbound\models\OutboundOrderItem;
use app\modules\warehouseDistribution\models\OutboundOrderItemSearch;
use yii\helpers\BaseFileHelper;
use common\modules\product\models\ProductBarcodes;
use common\modules\stock\models\Stock;

class ReportController extends Controller
{
    public function actionIndex()
    {
        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);

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
        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $model = $this->findModel($id);
        $itemSearch = new OutboundOrderItemSearch();
        $ItemsProvider = $itemSearch->search(Yii::$app->request->queryParams);
        $ItemsProvider->query->andWhere(['outbound_order_id' => $model->id]);

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

        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);

        if (($model = OutboundOrder::findOne(['id' => $id, 'client_id' => $clientEmploy->client_id])) !== null) {
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
        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

//        $client = ClientManager::findModelClient(Yii::$app->user->id);
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);

        $noFindDataProvider = null;
        $noReservedDataProvider = null;

        $searchModel = new OutboundOrderGridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['client_id' => $client->id]);


        if (!empty($searchModel->order_number) || !empty($searchModel->parent_order_number)) {
            $q = OutboundOrder::find();
            $q->andFilterWhere([
                'id' => $searchModel->id,
                'client_id' => $searchModel->client_id,
                'parent_order_number' => $searchModel->parent_order_number,
                'order_number' => $searchModel->order_number,
                'status' => $searchModel->status,
            ]);
            $q->select('id');

            $ids = $q->column();

            $noReservedQuery = OutboundOrderItem::find()->where('expected_qty != allocated_qty')->andWhere(['outbound_order_id' => $ids]);
            $noReservedDataProvider = new ActiveDataProvider([
                'query' => $noReservedQuery,
                'pagination' => [
                    'pageSize' => 300,
                ],
                'sort' => ['defaultOrder' => ['outbound_order_id' => SORT_DESC]]
            ]);

            $noFindQuery = OutboundOrderItem::find()->where('accepted_qty != allocated_qty')->andWhere(['outbound_order_id' => $ids]);
            $noFindDataProvider = new ActiveDataProvider([
                'query' => $noFindQuery,
                'pagination' => [
                    'pageSize' => 300,
                ],
                'sort' => ['defaultOrder' => ['outbound_order_id' => SORT_DESC]]
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
        if (Yii::$app->language == 'tr') {
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


        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        $searchModel = new OutboundOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id' => $client->id]);
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
            $activeSheet->setCellValue('A' . $i, $model->parent_order_number);
            $activeSheet->setCellValue('B' . $i, $model->order_number);
            $activeSheet->setCellValue('C' . $i, $title);
            $activeSheet->setCellValue('D' . $i, $model->mc);
//            $activeSheet->setCellValue('E' . $i, $model->kg);
            $activeSheet->setCellValue('E' . $i, $model->accepted_number_places_qty);
            $activeSheet->setCellValue('F' . $i, $model->expected_qty);
            $activeSheet->setCellValue('G' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('H' . $i, $model->accepted_qty);
            $activeSheet->setCellValue('I' . $i, !empty ($model->data_created_on_client) ? Yii::$app->formatter->asDatetime($model->data_created_on_client) : '-');
            $activeSheet->setCellValue('J' . $i, !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at) : '-');
            $activeSheet->setCellValue('K' . $i, !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date) : '-');
            $activeSheet->setCellValue('L' . $i, !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse) : '-');
            $activeSheet->setCellValue('M' . $i, !empty ($model->date_delivered) ? Yii::$app->formatter->asDatetime($model->date_delivered) : '-');
            $activeSheet->setCellValue('N' . $i, $model->getCargoStatusValue());
            $activeSheet->setCellValue('O' . $i, $model->calculateWMS());
            $activeSheet->setCellValue('P' . $i, $model->calculateTR());
            $activeSheet->setCellValue('Q' . $i, $model->calculateFULL());

        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $dirPath = 'uploads/' . $client->id . '/outbound/export/' . date('Ymd') . '/' . date('His');
        BaseFileHelper::createDirectory($dirPath);
        $fileName = 'outbound-order-export-' . time() . '.xlsx';
        $fullPath = $dirPath . '/' . $fileName;
        $objWriter->save($fullPath);
        return Yii::$app->response->sendFile($fullPath, $fileName);
    }

    /*
     * Экспорт расходной накладной (Colins)
     *
     **/
    public function actionExportRn()
    {
        if (Yii::$app->language == 'tr') {
            Yii::$app->language = 'en';
        }
        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);
        //$objPHPExcel = new \PHPExcel();

//        $objPHPExcel->getProperties()
//            ->setCreator("Report Reportov")
//            ->setLastModifiedBy("Report Reportov")
//            ->setTitle("Office 2007 XLSX Test Document")
//            ->setSubject("Office 2007 XLSX Test Document")
//            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//            ->setKeywords("office 2007 openxml php")
//            ->setCategory("Report");
//
//        $activeSheet = $objPHPExcel
//            ->setActiveSheetIndex(0)
//            ->setTitle('report-' . date('d.m.Y'));


//        $i = 1;
//        $activeSheet->setCellValue('A' . $i, Yii::t('stock/forms', 'Product barcode'));
//        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Expected Qty'));
//        $activeSheet->setCellValue('C' . $i, Yii::t('forms', 'Price'));
//        $activeSheet->setCellValue('D' . $i, Yii::t('stock/forms', 'Box Barcode'));

        if (!ClientManager::canIndexReport()) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $searchModel = new OutboundOrderSearch();
        $modelId = Yii::$app->request->get('id');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id' => $client->id]);
        $dataProvider->query->andFilterWhere(['id' => $modelId]);
        $filterOrders = $dataProvider->query->asArray()->all();
        $filterOrders = ArrayHelper::map($filterOrders, 'id', 'id');

        $stockItems = Stock::find()->select('product_barcode, updated_at')
            ->andWhere(['outbound_order_id' => $filterOrders, 'client_id' => $client->id])
            ->andWhere('box_barcode!= ""')
            ->orderBy('product_barcode')
            ->asArray()
            ->all();
        //VarDumper::dump($stockItems, 10, true);
        //die;
        if ($stockItems) {
            $i = 0;
            $dirPath = 'uploads/cotton/export/' . date('Ymd') . '/' . date('His');
            BaseFileHelper::createDirectory($dirPath);
            $fileName = 'outbound-report-export-RN-' . time() . '.txt';
            $fullPath = $dirPath . '/' . $fileName;
            $f = fopen($fullPath, "w");
            foreach ($stockItems as $row) {
                $i++;
                $textRow = $i. ' '. date('Y/m/d H:i:s', $row['updated_at']). ' ' . $row['product_barcode'] . "\r\n";
                fwrite($f, $textRow);
            }

            fclose($f);
            return Yii::$app->response->sendFile($fullPath, $fileName);
        }

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

        $clientEmploy = ClientEmployees::findOne(['user_id' => Yii::$app->user->id]);
        $client = Client::findOne($clientEmploy->client_id);

        $searchModel = new OutboundOrderGridSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andFilterWhere(['client_id' => $client->id]);

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
            $items = OutboundOrderItem::find()->where(['outbound_order_id'=>$model->id])->all();
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

}
