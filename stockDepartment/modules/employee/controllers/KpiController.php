<?php

namespace app\modules\employee\controllers;

use common\modules\transportLogistics\components\TLHelper;
use Yii;
use common\modules\client\models\Client;
use common\modules\outbound\models\OutboundOrder;
use common\modules\stock\models\Stock;
use stockDepartment\modules\employee\models\OutboundPickingListSearch;
use stockDepartment\modules\employee\models\OutboundOrderGridSearch;
use common\modules\outbound\models\OutboundPickingLists;
use stockDepartment\components\Controller;

class KpiController extends Controller
{
    /**
     * Lists all Employees models.
     * @return mixed
     */
    public function actionPickingOutbound()
    {
        $searchModel = new OutboundPickingListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientsArray =  Client::getActiveItems();

        return $this->render('picking-outbound', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'clientsArray' => $clientsArray,
        ]);
    }

    /*
    * Import to excel
    *
    **/
    public function actionPickingOutboundExportToExcel()
    { //picking-export-to-excel
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
            ->setTitle('kpi employee picking' . date('d.m.Y'));

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Штрихкод');
        $activeSheet->setCellValue('B' . $i, 'Сотрудник');
        $activeSheet->setCellValue('C' . $i, 'Кол-во лотов');
        $activeSheet->setCellValue('D' . $i, 'Разница');
        $activeSheet->setCellValue('E' . $i, 'Разница %');
        $activeSheet->setCellValue('F' . $i, 'Время по KPI сборки');
        $activeSheet->setCellValue('G' . $i, 'Фак-е время сборки');
        $activeSheet->setCellValue('H' . $i, 'Статус');
        $activeSheet->setCellValue('I' . $i, 'Начало сборки');
        $activeSheet->setCellValue('J' . $i, 'Конец сборки');
        $activeSheet->setCellValue('K' . $i, 'Клиент');
        $activeSheet->setCellValue('L' . $i, 'Время за 1 лот');

        $searchModel = new OutboundPickingListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $clientsArray =  Client::getActiveItems();

        foreach ($dps as $model) {
            $i++;
            $KPIValue = '0';
            $timeByLot = '0';
            if(!empty($model->kpi_value)) {
                $timeByLot =  $model->kpi_value / $model->showCountLot();
                $KPIValue =  Yii::$app->formatter->asDuration($model->kpi_value);
            }

            $diffRealBeginEndDateTime = '0';
            if($tmpValue = $model->showDiffRealBeginEndDateTime()) {
                $diffRealBeginEndDateTime = Yii::$app->formatter->asDuration($tmpValue);
            }

            $diffKPIBeginEndDateTime = '0';
            if($tmpValue = $model->showDiffKPIBeginEndDateTime()) {
                $diffKPIBeginEndDateTime = Yii::$app->formatter->asDuration($tmpValue);
            }

            $beginDatetime = '0';
            if($model->begin_datetime) {
                $beginDatetime = Yii::$app->formatter->asDatetime($model->begin_datetime);
            }

            $endDatetime = '0';
            if($model->end_datetime) {
                $endDatetime = Yii::$app->formatter->asDatetime($model->end_datetime);
            }

            $activeSheet->setCellValue('A' . $i, $model->barcode);
            $activeSheet->setCellValue('B' . $i, $model->showEmployeeName());
            $activeSheet->setCellValue('C' . $i, $model->showCountLot()); //
            $activeSheet->setCellValue('D' . $i, $diffKPIBeginEndDateTime);
            $activeSheet->setCellValue('E' . $i, Yii::$app->formatter->asDecimal($model->showPercentDiffKPIBeginEndDateTime(),2));
            $activeSheet->setCellValue('F' . $i, $KPIValue);

            $activeSheet->setCellValue('G' . $i, $diffRealBeginEndDateTime);
            $activeSheet->setCellValue('H' . $i, $model->getStatusValue());
            $activeSheet->setCellValue('I' . $i, $beginDatetime);
            $activeSheet->setCellValue('J' . $i, $endDatetime);
            $activeSheet->setCellValue('K' . $i, \yii\helpers\ArrayHelper::getValue($clientsArray,$model->client_id));
            $activeSheet->setCellValue('L' . $i, $timeByLot);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="kpi-employee-picking-export-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
     *
     *
     * */
    public function actionScanningOutbound()
    {
        $searchModel = new OutboundOrderGridSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientsArray = Client::getActiveWMSItems();
        $clientStoreArray = TLHelper::getStoreArrayByClientID();

        return $this->render('scanning-outbound', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'clientsArray' => $clientsArray,
            'clientStoreArray' => $clientStoreArray,
        ]);
    }
    /*
     *
     *
     * */
    public function actionCrossDock()
    {
        $searchModel = new OutboundPickingListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientsArray =  Client::getActiveItems();

        return $this->render('cross-dock', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'clientsArray' => $clientsArray,
        ]);
    }
    /*
     *
     *
     * */
    public function actionInbound()
    {
        $searchModel = new OutboundPickingListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $clientsArray =  Client::getActiveItems();

        return $this->render('inbound', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'clientsArray' => $clientsArray,
        ]);
    }





    /*
     * TODO ALI FOR TEST
     * set-data
     * */
    public function actionSetData()
    {
        $query = OutboundPickingLists::find();
        $query->with('employee');
        $all = $query->all();

        foreach($all as $value) {
            $o = OutboundOrder::findOne($value->outbound_order_id);
            $countProduct =  Stock::find()->andWhere(['outbound_picking_list_id'=>$value->id])->count();
            if($o) {
                $pikingTime = \common\modules\kpiSettings\models\KpiSetting::getPickingTime($o->client_id, $countProduct);
                if($pikingTime) {
                    $value->client_id = $o->client_id;
                    $value->kpi_value = $pikingTime;
                    $value->save(false);
                }
            }
        }
        return 'return set data';
    }
}