<?php

namespace app\modules\outbound\controllers;

use common\modules\stock\models\Stock;
use stockDepartment\modules\outbound\models\OutboundOrderGridSearch;
use stockDepartment\modules\outbound\models\OutboundOrderItemSearch;
use app\modules\outbound\models\OutboundBoxSearch;
use stockDepartment\modules\outbound\models\OutboundOrderSearch;
use common\modules\inbound\models\InboundOrderItem;
use common\modules\client\models\Client;
use Yii;
use stockDepartment\components\Controller;
use common\modules\inbound\models\InboundOrder;
use common\modules\outbound\models\OutboundOrder;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use common\modules\transportLogistics\components\TLHelper;

class BoxReportController extends Controller
{
    /*
     * Index
     * */
    public function actionIndex()
    {
        $stock = new Stock();
        $searchModel = new OutboundBoxSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);

        $clientsArray = Client::getActiveWMSItems();
        $storeArray = TLHelper::getStockPointArray();
        $statusArray = $stock->getStatusArray();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientsArray' => $clientsArray,
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
        $activeSheet->setCellValue('J' . $i, Yii::t('stock/forms', 'Product Name')); // +


        $searchModel = new OutboundBoxSearch();

        $clientsArray = Client::getActiveItems();
        $storeArray = TLHelper::getStockPointArray();
        $statusArray = $stock->getStatusArray();

        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();


        foreach ($dps as $data) {
            $i++;
            $title = '-';
            $clientTitle = '-';
            $status = '-';
            if (isset($data['outboundOrder']['to_point_id']) && !empty($data['outboundOrder']['to_point_id'])) {
                if (isset($storeArray[$data['outboundOrder']['to_point_id']])) {
                    $title = $storeArray[$data['outboundOrder']['to_point_id']];
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

            $stockLots = Stock::find()->andWhere([
                                            'client_id'=>$data['client_id'],
                                            'box_barcode'=>$data['box_barcode'],
                                            'outbound_order_id'=>$data['outbound_order_id'],
            ])->all();

            if($stockLots) {
                foreach($stockLots as $stockLot) {
                    $i++;
                    $activeSheet->setCellValue('I' . $i, $stockLot->product_barcode);
                    $inboundLine = InboundOrderItem::find()->andWhere([
                        'inbound_order_id'=>$stockLot->inbound_order_id,
                        'product_barcode'=>$stockLot->product_barcode,
                    ])->one();
                    if($inboundLine) {
                        $activeSheet->setCellValue('J' . $i, $inboundLine->product_name);
                    }
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