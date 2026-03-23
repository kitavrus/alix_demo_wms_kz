<?php

namespace stockDepartment\modules\alix\controllers\bind;

use Yii;
use common\modules\client\models\Client;
use stockDepartment\components\Controller;
use stockDepartment\modules\alix\controllers\bind\domain\StockBindReport;

class ReportController extends Controller
{
    public function actionBindQrCodeReport()
    {
        $searchModel = new StockBindReport();
        list($dataProvider,$query) = $searchModel->searchArray(Yii::$app->request->queryParams);

        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $statusArray = $searchModel->getStatusArray();
        $availabilityStatusArray = $searchModel->getAvailabilityStatusArray();
        $lostStatusArray = $searchModel->getLostStatusArray();
        $clientsArray = Client::getActiveWMSItems();

        return $this->render('bind-qr-code-report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conditionTypeArray' => $conditionTypeArray,
            'statusArray' => $statusArray,
            'availabilityStatusArray' => $availabilityStatusArray,
            'lostStatusArray' => $lostStatusArray,
            'clientsArray' => $clientsArray,
        ]);
    }

    public function actionBindQrCodeExportToExcel()
    {
        $detail = Yii::$app->request->get('detail');

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

        $activeSheet->setCellValue('A' . $i, 'Количество'); // +
        $activeSheet->setCellValue('B' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('C' . $i, 'Наш ШК товара'); // +
        $activeSheet->setCellValue('D' . $i, 'QR code'); // +
        $activeSheet->setCellValue('E' . $i, 'Короб'); // +
        $activeSheet->setCellValue('F' . $i, 'Полка'); // +
        $activeSheet->setCellValue('G' . $i, 'Модель'); // +

        $searchModel = new StockBindReport();
        $dataProviderResult = $searchModel->searchArray(Yii::$app->request->queryParams);
        list($dataProvider,$query) = $dataProviderResult;
        $dataProvider->pagination = false;
        $products = $dataProvider->getModels();
        
        foreach ($products as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['qty']); // +
            $activeSheet->setCellValue('B' . $i, $model['product_barcode']); // +
            $activeSheet->setCellValue('C' . $i, $model['our_product_barcode']); // +
            $activeSheet->setCellValue('D' . $i, $model['bind_qr_code']); // +
            $activeSheet->setCellValue('E' . $i, $model['primary_address']); // +
            $activeSheet->setCellValue('F' . $i, $model['secondary_address']); // +
            $activeSheet->setCellValue('G' . $i, $model['product_model']); // +
        }

        foreach (range('A', 'G') as $columnID) {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $fileName = 'bind-qr-code-report-' . date('Ymd_H-i-s');
        if($detail) {
            $fileName .= '-detail';
        }

        $fileName .= '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}