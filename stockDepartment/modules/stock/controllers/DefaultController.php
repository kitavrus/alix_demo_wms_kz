<?php

namespace app\modules\stock\controllers;

use app\modules\stock\models\StockSearch;
use stockDepartment\components\Controller;
use Yii;

class DefectController extends Controller
{
    public function actionIndex()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchDefect(Yii::$app->request->queryParams);

        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $availabilityStatusArray = $searchModel->getAvailabilityStatusArray();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conditionTypeArray' => $conditionTypeArray,
            'availabilityStatusArray' => $availabilityStatusArray
        ]);
    }

    /*
     *
     * Export data to EXEL
     *
     * */
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
        $activeSheet->setCellValue('A' . $i, 'Полка'); // +
        $activeSheet->setCellValue('B' . $i, 'Короб'); // +
        $activeSheet->setCellValue('C' . $i, 'ШК товара'); // +
        $activeSheet->setCellValue('D' . $i, 'Количество'); // +
        $activeSheet->setCellValue('E' . $i, 'Состояние'); // +
        $activeSheet->setCellValue('F' . $i, 'Описание'); // +
        $activeSheet->setCellValue('G' . $i, 'Резервирование'); // +

        $searchModel = new StockSearch();
        $dataProvider = $searchModel->searchDefect(Yii::$app->request->queryParams);
        $conditionTypeArray = $searchModel->getConditionTypeArray();
        $availabilityStatusArray = $searchModel->getAvailabilityStatusArray();
        $models = $dataProvider->getModels();

        foreach ($models as $model) {
            $i++;
            $activeSheet->setCellValue('A' . $i, $model['secondary_address'] ?: '');
            $activeSheet->setCellValue('B' . $i, $model['primary_address'] ?: '');
            $activeSheet->setCellValue('C' . $i, $model['product_barcode'] ?: '');
            $activeSheet->setCellValue('D' . $i, $model['qty'] ?: '');
            $activeSheet->setCellValue('E' . $i, $conditionTypeArray[$model['condition_type']] ?: '-');
            $activeSheet->setCellValue('F' . $i, $model['system_status_description'] ?: '');
            $activeSheet->setCellValue('G' . $i, $availabilityStatusArray[$model['status_availability']] ?: '-');
        }

        foreach (range('A', 'G') as $columnID) {
            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="defect-report-' . time() . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }
}