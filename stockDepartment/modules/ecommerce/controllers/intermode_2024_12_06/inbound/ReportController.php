<?php

namespace app\modules\ecommerce\controllers\intermode\inbound;

use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInbound;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundItemSearch;
use app\modules\ecommerce\controllers\intermode\inbound\domain\entities\EcommerceInboundSearch;
use app\modules\ecommerce\controllers\intermode\stock\domain\entities\EcommerceStockSearch;
use stockDepartment\components\Controller;
use Yii;
use yii\web\NotFoundHttpException;


class ReportController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new EcommerceInboundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination->pageSize = 100;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $searchModel = new EcommerceInboundItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$id);

        return $this->render('view', [
            'model' => $this->InboundOne($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Finds the EcommerceInbound model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceInbound the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function InboundOne($id)
    {
        if (($model = EcommerceInbound::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all EcommerceOutbound models.
     * @return mixed
     */
    public function actionOnStock()
    {
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchArray(Yii::$app->request->queryParams);

        return $this->render('stock/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	/**
	* Export to excel
	*
	**/
    public function actionInboundExportToExcel()
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

        $forDastan  = Yii::$app->request->get('forDastan');

        $i = 1;
        $activeSheet->setCellValue('A' . $i, 'Номер заказа')->getColumnDimension('A')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('B' . $i, 'Статус')->getColumnDimension('B')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('C' . $i, 'Предполагаемое кол-во')->getColumnDimension('C')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('D' . $i, 'Зарезервированое кол-во')->getColumnDimension('D')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('E' . $i, 'Отсканированное кол-во')->getColumnDimension('E')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('F' . $i, 'Дата создания')->getColumnDimension('F')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('G' . $i, 'Дата упаковки')->getColumnDimension('G')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('H' . $i, 'Дата отгрузки')->getColumnDimension('H')->setAutoSize(true); // +;

        $activeSheet->setCellValue('A' . $i, 'Order number')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Status')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Expected')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'Reserved')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'Scanned')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'Date Created')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Date Packed')->getColumnDimension('G')->setAutoSize(true); // +
        $activeSheet->setCellValue('H' . $i, 'Date Courier')->getColumnDimension('H')->setAutoSize(true);
        $activeSheet->setCellValue('I' . $i, 'Shipment Source')->getColumnDimension('I')->setAutoSize(true);
        $activeSheet->setCellValue('J' . $i, 'Cancel reason')->getColumnDimension('J')->setAutoSize(true);

        if ($forDastan) {
            $activeSheet->setCellValue('K' . $i, 'TTN Delivery')->getColumnDimension('K')->setAutoSize(true);
        }

        $activeSheet->setCellValue('L' . $i, 'External order number')->getColumnDimension('L')->setAutoSize(true);

        $searchModel = new EcommerceInboundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, $model->order_number);
            $activeSheet->setCellValue('B' . $i, \common\ecommerce\constants\OutboundStatus::getValue($model->status, 'EN'));
            $activeSheet->setCellValue('C' . $i, $model->expected_qty);
            $activeSheet->setCellValue('D' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('E' . $i, $model->accepted_qty);

            $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('F' . $i, $created_at);

            $packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('G' . $i, $packing_date);

            $date_left_warehouse = !empty ($model->date_left_warehouse) ? Yii::$app->formatter->asDatetime($model->date_left_warehouse, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('H' . $i, $date_left_warehouse);

            $client_ShipmentSource = !empty($model->client_ShipmentSource) ? $model->client_ShipmentSource : '-';
            $activeSheet->setCellValue('I' . $i, $client_ShipmentSource);

            $client_CancelReason = !empty($model->client_CancelReason) ? OutboundCancelStatus::getValue($model->client_CancelReason) : '-';
            $activeSheet->setCellValue('J' . $i, $client_CancelReason);

            if ($forDastan) {
                $activeSheet->setCellValue('K' . $i, $model->client_ReferenceNumber);
            }
            $activeSheet->setCellValue('L' . $i, $model->external_order_number);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="outbound-orders-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}