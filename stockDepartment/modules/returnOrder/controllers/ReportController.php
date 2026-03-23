<?php

namespace app\modules\returnOrder\controllers;

use common\modules\client\models\Client;
use common\modules\returnOrder\models\ReturnOrderItems;
use Yii;
use stockDepartment\components\Controller;
use common\modules\returnOrder\models\ReturnOrder;
use app\modules\returnOrder\models\ReturnOrderSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class ReportController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new ReturnOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveWMSItems();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientsArray' => $clientsArray,
        ]);
    }

    public function actionView($id)
    {

        $model = $this->findModel($id);
        $ItemsProvider = new ActiveDataProvider([
            'query' => $model->getOrderItems(),
        ]);

        return $this->render('view', [
            'model' => $model,
            'ItemsProvider' => $ItemsProvider,
        ]);
    }

    /**
     * Finds the ReturnOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReturnOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = ReturnOrder::findOne(['id'=>$id])) !== null) {
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
        $activeSheet->setCellValue('F' . $i, 'Начали сканировать'); // +
        $activeSheet->setCellValue('G' . $i, 'Закончили сканировать'); // +
        $activeSheet->setCellValue('H' . $i, 'Дата создания'); // +
        $activeSheet->setCellValue('I' . $i, 'Статус'); // +
        $activeSheet->setCellValue('J' . $i, 'UrunKodu'); // +
        $activeSheet->setCellValue('K' . $i, 'KoliBarkod'); // +


        $searchModel = new ReturnOrderSearch();

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
            $activeSheet->setCellValue('F' . $i, !empty($model->begin_datetime) ? Yii::$app->formatter->asDatetime($model->begin_datetime): "-"); // +
            $activeSheet->setCellValue('G' . $i, !empty($model->end_datetime)? Yii::$app->formatter->asDatetime($model->end_datetime): "-"); // +
            $activeSheet->setCellValue('H' . $i, !empty($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at): "-"); // +
            $activeSheet->setCellValue('I' . $i, $model->getStatusValue()); // +

            $KoliBarkod = '';
            $UrunKodu = '';
            if($model->extra_fields) {

                try {
                    $extraFields = \yii\helpers\Json::decode($model->extra_fields);
                } catch (\yii\base\InvalidParamException $e) {
                    file_put_contents('return-order-errors.log',$model->id,FILE_APPEND);
                }

                $koliResponseData = [];
                if(isset($extraFields['IadeKabulResult->Koli'])) {
                    $koliResponseData = $extraFields['IadeKabulResult->Koli'];
                } elseif(isset($extraFields['KoliIadeKabulResult->Koli'])) {
                    $koliResponseData = $extraFields['KoliIadeKabulResult->Koli'];
                } elseif(isset($extraFields['koliResponse'] )) {
                    $koliResponseData = $extraFields['koliResponse'];
                }

                if(!empty($koliResponseData) && isset($koliResponseData['KoliBarkod'])) {
                    $KoliBarkod = $koliResponseData['KoliBarkod'];
                }
                if(!empty($koliResponseData) && isset($koliResponseData['UrunKodu'])) {
                    $UrunKodu = $koliResponseData['UrunKodu'];
                }
            }

            $activeSheet->setCellValue('J' . $i, $UrunKodu); // +
            $activeSheet->setCellValue('K' . $i, $KoliBarkod); // +

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="return-orders-report-' . time() . '.xlsx"');
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
        $activeSheet->setCellValue('A' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('B' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('C' . $i, 'Заявленное кол-во'); // +
        $activeSheet->setCellValue('D' . $i, 'Принятое кол-во'); // +
        $activeSheet->setCellValue('E' . $i, 'Клиент'); // +
        $activeSheet->setCellValue('F' . $i, 'LC box'); // +
		$activeSheet->setCellValue('G' . $i, 'Магазин'); // +
		
        $searchModel = new ReturnOrderSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $clientTitleMapArray = ArrayHelper::map(Client::find()->asArray()->all(), 'id', 'title');

        if($returnOrders = $dataProvider->getModels()){

            //VarDumper::dump($orderMapArray, 10, true); die;

            foreach ($returnOrders as $model) {
                //$orderNumber = $model->order_number;

                if($returnItems = $model->orderItems){
                    foreach ($returnItems as $item) {
                        $i++;
                        $activeSheet->setCellValue('A' . $i, $model->order_number);
                        $activeSheet->setCellValue('B' . $i, $item->product_barcode);
                        $activeSheet->setCellValue('C' . $i, $item->expected_qty);
                        $activeSheet->setCellValue('D' . $i, $item->accepted_qty);
                        $activeSheet->setCellValue('E' . $i, $clientTitleMapArray[$model->client_id]);
                        $activeSheet->setCellValue('F' . $i, $item->client_box_barcode);

                        if($store = \common\modules\store\models\Store::findOne(['id'=>$item->from_point_id])) {
                            $storeTitle = \common\modules\store\models\Store::getPointTitle($store->id);
                        }

                        $activeSheet->setCellValue('G' . $i, $storeTitle);
						
                    }

                } else {
                    $i++;
                    $activeSheet->setCellValue('A' . $i, $model->order_number);
                    $activeSheet->setCellValue('B' . $i, '-');
                    $activeSheet->setCellValue('C' . $i, '0');
                    $activeSheet->setCellValue('D' . $i, '0');
                    $activeSheet->setCellValue('E' . $i, $clientTitleMapArray[$model->client_id]);
                }


            }
        }



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="return-orders-report-full' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
    * Import to excel
    *
    **/
    public function actionExportToExcelFull_OLD()
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
        $activeSheet->setCellValue('A' . $i, 'Номер заказа'); // +
        $activeSheet->setCellValue('B' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('C' . $i, 'Заявленное кол-во'); // +
        $activeSheet->setCellValue('D' . $i, 'Принятое кол-во'); // +
        $activeSheet->setCellValue('E' . $i, 'Клиент'); // +

        $searchModel = new ReturnOrderSearch();

        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $returnOrderIDs = ArrayHelper::map($dataProvider->getModels(), 'id', 'id');
        $orderMapArray = ArrayHelper::map($dataProvider->getModels(), 'id', 'order_number');
        $clientsArray =  ArrayHelper::map($dataProvider->getModels(), 'id', 'client_id');
        $clientMapArray = ArrayHelper::map(Client::find()->andWhere(['id' => $clientsArray])->asArray()->all(), 'id', 'title');


        if($returnOrderIDs){
            $returnItems = ReturnOrderItems::find()
                ->andWhere([
                    'return_order_id' => $returnOrderIDs
                ])
                ->andWhere('accepted_qty > 0')
                ->orderBy('return_order_id DESC')
                ->asArray()
                ->all();
            //VarDumper::dump($orderMapArray, 10, true); die;

            foreach ($returnItems as $model) {
                $orderNumber = isset($orderMapArray[$model['return_order_id']]) ? $orderMapArray[$model['return_order_id']] : '-';
                $clientId = isset($clientsArray[$model['return_order_id']]) ? $clientsArray[$model['return_order_id']] : '-';
                $clientTitle = isset($clientMapArray[$clientId]) ? $clientMapArray[$clientId] : '-';
                $i++;
                $activeSheet->setCellValue('A' . $i, $orderNumber);
                $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
                $activeSheet->setCellValue('C' . $i, $model['expected_qty']);
                $activeSheet->setCellValue('D' . $i, $model['accepted_qty']);
                $activeSheet->setCellValue('E' . $i, $clientTitle);

            }
        }



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="return-orders-report-full' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }


}