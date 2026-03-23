<?php

namespace app\modules\report\controllers;

use common\modules\stock\models\Stock;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use stockDepartment\modules\outbound\models\OutboundOrderItemSearch;
use app\modules\report\models\OutboundBoxSearch;
use stockDepartment\modules\outbound\models\OutboundOrderSearch;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\client\models\Client;
use Yii;
use clientDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\components\TLHelper;
use clientDepartment\modules\client\components\ClientManager;
use common\modules\client\models\ClientEmployees;

class BoxReportController extends Controller
{
    /*
     * Index
     * */
    public function actionIndex()
    {
        $stock = new Stock();
        $searchModel = new OutboundBoxSearch();
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        if(!ClientManager::canIndexReport() ) {
            throw new NotFoundHttpException('У вас нет доступа к этой странице');
        }

        $storeArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);
        $statusArray = $stock->getStatusArray();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'storeArray' => $storeArray,
            'statusArray' => $statusArray,
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
        $activeSheet->setCellValue('A' . $i, Yii::t('stock/forms', 'Box Barcode')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('outbound/forms', 'Parent order number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('outbound/forms', 'Order number')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('outbound/forms', 'To point id')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('outbound/forms', 'Expected Qty')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('stock/forms', 'Box Volume')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('stock/forms', 'Status')); // +
        $activeSheet->setCellValue('H' . $i, Yii::t('stock/forms', 'Product Barcode')); // +


        $searchModel = new OutboundBoxSearch();

//        $storeArray = TLHelper::getStockPointArray();
        $clientEmploy = ClientEmployees::findOne(['user_id'=>Yii::$app->user->id]);
        $storeArray = TLHelper::getStoreArrayByClientID($clientEmploy->client_id);
        $statusArray = $stock->getStatusArray();

        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();


        foreach ($dps as $data) {
            $i++;
            $title = '-';
            $status = '-';
            if (isset($data['outboundOrder']['to_point_id']) && !empty($data['outboundOrder']['to_point_id'])) {
                if (isset($storeArray[$data['outboundOrder']['to_point_id']])) {
                    $title = $storeArray[$data['outboundOrder']['to_point_id']];
                }
            }

            if (isset($statusArray[$data['status']])) {
                $status = $statusArray[$data['status']];
            }

            $activeSheet->setCellValue('A' . $i, $data['box_barcode']);
            $activeSheet->setCellValue('B' . $i, $data['parent_order_number']);
            $activeSheet->setCellValue('C' . $i, $data['order_number']);
            $activeSheet->setCellValue('D' . $i, $title);
            $activeSheet->setCellValue('E' . $i, $data['product_qty']);
            $activeSheet->setCellValue('F' . $i, $data['box_size_m3']);
            $activeSheet->setCellValue('G' . $i, $status);

            $stockLots = Stock::find()->andWhere([
                'client_id'=>$data['client_id'],
                'box_barcode'=>$data['box_barcode'],
                'outbound_order_id'=>$data['outbound_order_id'],
            ])->all();

            if($stockLots) {
                foreach($stockLots as $stockLot) {
                    $i++;
                    $activeSheet->setCellValue('H' . $i, $stockLot->product_barcode);
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

    public function actionView($id)
    {

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

}