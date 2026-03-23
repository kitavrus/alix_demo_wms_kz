<?php

namespace stockDepartment\modules\crossDock\controllers;


use common\modules\client\models\Client;
use common\modules\crossDock\models\CrossDock;
use common\modules\transportLogistics\components\TLHelper;
use stockDepartment\modules\crossDock\models\CrossDockItemSearch;
use stockDepartment\modules\crossDock\models\CrossDockSearch;
use yii\web\NotFoundHttpException;
use yii;
use common\modules\stock\models\Stock;

class ReportController extends \stockDepartment\components\Controller
{
    public function actionIndex()
    {
        $searchModel = new CrossDockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientStoreArray = TLHelper::getStoreArrayByClientID();
        $clientsArray = Client::getActiveWMSItems();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
        ]);
    }

    public function actionView($id)
    {
        $stock = new Stock();
        $model = $this->findModel($id);
        $searchItem = new CrossDockItemSearch();
        $ItemsProvider = $searchItem->search(Yii::$app->request->queryParams);
        $ItemsProvider->query->andWhere(['cross_dock_id' => $model->id]);
        $clientsArray = Client::getActiveItems();
        $statusArray = $stock->getStatusArray();
        $clientStoreArray = TLHelper::getStoreArrayByClientID($model->client_id);

        return $this->render('view', [
            'model' => $model,
            'ItemsProvider' => $ItemsProvider,
            'searchModel' => $searchItem,
            'clientsArray' => $clientsArray,
            'statusArray' => $statusArray,
            'clientStoreArray' => $clientStoreArray,

        ]);
    }

    /**
     * Finds the InboundOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CrossDock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = CrossDock::findOne(['id'=>$id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /*
   * Import to excel
   *
   **/
    public function actionExportToExcelFull()
    {

        $searchModel = new CrossDockSearch();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();
        $clientsArray = Client::getActiveItems();

        $dataProvider = $searchModel->searchReport(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->allModels;
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
        $activeSheet->setCellValue('A' . $i, Yii::t('inbound/forms', 'Client ID')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('inbound/forms', 'Party number')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('stock/forms', 'To point ID')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('stock/forms', 'Volume')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('stock/forms', 'Qty')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('stock/forms', 'Total volume')); // +

//        foreach ($dps as $k => $model){
//            $clientTitle = '';
//            $items = $model->orderItems;
//
//            if($client = $model->client){
//                $clientTitle = $client->title;
//            }
//
//            $party_number = $model->party_number;
//            $toPoint = isset($clientStoreArray[$model->to_point_id]) ? $clientStoreArray[$model->to_point_id] : '-';
//
//
//
//        }

        $dataExcel = [];
        foreach ($dps as $model) {
            $items = $model['orderItems'];
            $toPoint = isset($clientStoreArray[$model['to_point_id']]) ? $clientStoreArray[$model['to_point_id']] : '-';
            $clientTitle = isset($clientsArray[$model['client_id']]) ? $clientsArray[$model['client_id']] : '-';

            if($items){
                $itemsData =[];
                foreach ($items as $item){

                    $itemsData[$item['box_m3']][] = $item['expected_number_places_qty'];

                }
                foreach ($itemsData as $vol => $count){
                    $dataExcel[]=[
                        'client' => $clientTitle,
                        'id' => $model['id'],
                        'party_number' => $model['party_number'],
                        'point' => $toPoint,
                        'volume' => $vol,
                        'qty' => count($count),
                        'all_volume' => $vol * count ($count),
                    ];
                }



            }

        }
        //yii\helpers\VarDumper::dump($dataExcel, 10, true); die;
        if($dataExcel){

            foreach ($dataExcel as $row){
                $i++;
                $activeSheet->setCellValue('A' . $i, $row['client']);
                $activeSheet->setCellValue('B' . $i, $row['party_number']);
                $activeSheet->setCellValue('C' . $i, $row['point']);
                $activeSheet->setCellValue('D' . $i, $row['volume']);
                $activeSheet->setCellValue('E' . $i, $row['qty']);
                $activeSheet->setCellValue('F' . $i, $row['all_volume']); // +
            }
        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="cross-dock-orders-report-full' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
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
        $activeSheet->setCellValue('A' . $i, Yii::t('inbound/forms', 'Client ID')); // +
        $activeSheet->setCellValue('B' . $i, Yii::t('stock/forms', 'To point ID')); // +
        $activeSheet->setCellValue('C' . $i, Yii::t('inbound/forms', 'Party number')); // +
        $activeSheet->setCellValue('D' . $i, Yii::t('stock/forms', 'Box volume')); // +
        $activeSheet->setCellValue('E' . $i, Yii::t('inbound/forms', 'Expected Number Places Qty')); // +
        $activeSheet->setCellValue('F' . $i, Yii::t('inbound/forms', 'Accepted Number Places Qty')); // +
        $activeSheet->setCellValue('G' . $i, Yii::t('forms', 'Created At')); // +
//        $activeSheet->setCellValue('G' . $i, Yii::t('stock/forms', 'Brut weight')); // +
//        $activeSheet->setCellValue('H' . $i, Yii::t('stock/forms', 'Net weight')); // +
//        $activeSheet->setCellValue('I' . $i, Yii::t('forms', 'Created At')); // +

        $searchModel = new CrossDockSearch();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();
        $clientsArray = Client::getActiveItems();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {
            $i++;

            $toPoint = isset($clientStoreArray[$model['to_point_id']]) ? $clientStoreArray[$model['to_point_id']] : '-';
            $clientTitle = isset($clientsArray[$model['client_id']]) ? $clientsArray[$model['client_id']] : '-';

            $activeSheet->setCellValue('A' . $i, $clientTitle);
            $activeSheet->setCellValue('B' . $i, $toPoint);
            $activeSheet->setCellValue('C' . $i, $model->party_number);
            $activeSheet->setCellValue('D' . $i, $model->box_m3);
            $activeSheet->setCellValue('E' . $i, $model->expected_number_places_qty);
            $activeSheet->setCellValue('F' . $i, $model->accepted_number_places_qty);
            $activeSheet->setCellValue('G' . $i, Yii::$app->formatter->asDatetime($model->created_at));
//            $activeSheet->setCellValue('G' . $i, $model->weight_brut);
//            $activeSheet->setCellValue('H' . $i, $model->weight_net);
//            $activeSheet->setCellValue('I' . $i, Yii::$app->formatter->asDatetime($model->created_at));

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="cross-dock-orders-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}
