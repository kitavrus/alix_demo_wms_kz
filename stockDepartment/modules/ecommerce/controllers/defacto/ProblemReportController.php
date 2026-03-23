<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\OutboundCancelStatus;
use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceInboundItemSearch;
use common\ecommerce\entities\EcommerceInboundSearch;
use common\ecommerce\entities\EcommerceOutbound;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceOutboundItemSearch;
use common\ecommerce\entities\EcommerceOutboundSearch;
use common\ecommerce\entities\EcommerceStock;
use common\ecommerce\entities\EcommerceStockSearch;
use common\modules\outbound\models\OutboundOrderItem;
use stockDepartment\components\Controller;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;


class ProblemReportController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchFindDamageProductOnStock(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /*
  * Export to excel
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
        $activeSheet->setCellValue('A' . $i, 'Номер заказа')->getColumnDimension('A')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('B' . $i, 'Шк товара')->getColumnDimension('B')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('C' . $i, 'Причина Отмены')->getColumnDimension('C')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('D' . $i, 'Причина Отмены')->getColumnDimension('D')->setAutoSize(true); // +; // +

        $activeSheet->setCellValue('A' . $i, 'Order number'); // +
        $activeSheet->setCellValue('B' . $i, 'Product Barcode'); // +
        $activeSheet->setCellValue('C' . $i, 'Reason re reserved'); // +
        $activeSheet->setCellValue('D' . $i, 'Date Created'); // +


        $searchModel = new EcommerceStockSearch();

        $dataProvider = $searchModel->searchFindDamageProductOnStock(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, $model['order_re_reserved']);
            $activeSheet->setCellValue('B' . $i, $model['product_barcode']);
            $activeSheet->setCellValue('C' . $i, (new \common\ecommerce\constants\OutboundCancelStatus())->getValue($model['reason_re_reserved']));
            $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('D' . $i, $created_at);

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="problem-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}