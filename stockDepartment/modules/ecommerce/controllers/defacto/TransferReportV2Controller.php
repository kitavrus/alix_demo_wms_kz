<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\defacto\transfer\repository\TransferRepositoryV2;
use common\ecommerce\entities\EcommerceTransferItems;
use common\ecommerce\entities\EcommerceTransferItemSearchV2;
use common\modules\transportLogistics\components\TLHelper;
use Yii;
use common\ecommerce\entities\EcommerceTransfer;
use common\ecommerce\entities\EcommerceTransferSearchV2;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransferV2ReportController implements the CRUD actions for EcommerceTransfer model.
 */
class TransferReportV2Controller extends Controller
{
    /**
     * Lists all EcommerceTransfer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcommerceTransferSearchV2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		//$clientStoreArray = TLHelper::getStoreArrayByClientID();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'clientStoreArray' => $clientStoreArray,
        ]);
    }

    /**
     * Displays a single EcommerceTransfer model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new EcommerceTransferItemSearchV2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$id);

        return $this->render('view', [
            'model' => $this->findModel($id),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the EcommerceTransfer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommerceTransfer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EcommerceTransfer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    /*
    * Export to excel
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
        $activeSheet->setCellValue('A' . $i, 'client_BatchId')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Status')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Expected quantity')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'Reserved quantity')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'Scanned quantity')->getColumnDimension('E')->setAutoSize(true); // +

        $activeSheet->setCellValue('F' . $i, 'Print picking list date')->getColumnDimension('F')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Packing date')->getColumnDimension('G')->setAutoSize(true); // +

        $activeSheet->setCellValue('H' . $i, 'Date Created')->getColumnDimension('H')->setAutoSize(true); // +
        $activeSheet->setCellValue('I' . $i, 'Expected box qty')->getColumnDimension('I')->setAutoSize(true); // +
        $activeSheet->setCellValue('J' . $i, 'Scanned box qty')->getColumnDimension('J')->setAutoSize(true); // +


        $searchModel = new EcommerceTransferSearchV2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $transferRepository = new TransferRepositoryV2();

        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, $model->client_BatchId);
            $activeSheet->setCellValue('B' . $i, \common\ecommerce\constants\TransferStatus::getValue($model->status, 'EN'));
            $activeSheet->setCellValue('C' . $i, $model->expected_qty);
            $activeSheet->setCellValue('D' . $i, $model->allocated_qty);
            $activeSheet->setCellValue('E' . $i, $model->accepted_qty);

            $print_picking_list_date = !empty ($model->print_picking_list_date) ? Yii::$app->formatter->asDatetime($model->print_picking_list_date, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('F' . $i, $print_picking_list_date);

            $packing_date = !empty ($model->packing_date) ? Yii::$app->formatter->asDatetime($model->packing_date, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('G' . $i, $packing_date);

            $created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
            $activeSheet->setCellValue('H' . $i, $created_at);

            $activeSheet->setCellValue('I' . $i, $model->expected_box_qty);


            $activeSheet->setCellValue('J' . $i, $transferRepository->getCountScannedBox($model->id));
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="transfer-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
    * Export to excel to Box LC
    **/
    public function actionExportToExcelBoxLc()
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
        $activeSheet->setCellValue('A' . $i, 'BatchId')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Box LC')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Counter')->getColumnDimension('C')->setAutoSize(true); // +

        $searchModel = new EcommerceTransferSearchV2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $transferRepository = new TransferRepositoryV2();

        foreach ($dps as $model) {
            $boxLCList = $transferRepository->getScannedBox($model->id);
            $counter = 1;
            foreach ($boxLCList as $k=>$boxLC) {
                $i++;
                $activeSheet->setCellValue('A' . $i,$model->client_BatchId);
                $activeSheet->setCellValue('B' . $i,$boxLC);
                $activeSheet->setCellValue('C' . $i,$counter++);
            }
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="transfer-box-lc-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
    * Export to excel
    *
    **/
    public function actionExportToExcelWithProducts()
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
        $activeSheet->setCellValue('A' . $i, 'client_BatchId')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Status')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Product sku')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'Product barcode')->getColumnDimension('D')->setAutoSize(true); // +

        $activeSheet->setCellValue('E' . $i, 'Expected quantity')->getColumnDimension('E')->setAutoSize(true); // +
        $activeSheet->setCellValue('F' . $i, 'Reserved quantity')->getColumnDimension('F')->setAutoSize(true); // +
        $activeSheet->setCellValue('G' . $i, 'Scanned quantity')->getColumnDimension('G')->setAutoSize(true); // +

        $searchModel = new EcommerceTransferSearchV2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();

        foreach ($dps as $model) {

            // SHOW PRODUCTS
            $productsInOrder = EcommerceTransferItems::find()->andWhere(['transfer_id' => $model->id])->all();
            foreach ($productsInOrder as $product) {
                $i++;
                $activeSheet->setCellValueExplicit('A' . $i, $model->client_BatchId, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('B' . $i, \common\ecommerce\constants\TransferStatus::getValue($model->status, 'EN'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('C' . $i, $product->product_sku, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('D' . $i, $product->product_barcode, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $activeSheet->setCellValueExplicit('E' . $i, $product->expected_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('F' . $i, $product->allocated_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $activeSheet->setCellValueExplicit('G' . $i, $product->accepted_qty, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="transferWithProducts' . date('d.m.Y') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }


    /*
* Export to excel to Box LC
**/
    public function actionExportToExcelBoxLcWithProducts()
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
        $activeSheet->setCellValue('A' . $i, 'BatchId')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Box LC')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Product Barcode')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'Product Sku')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'Product Qty')->getColumnDimension('E')->setAutoSize(true); // +

        $searchModel = new EcommerceTransferSearchV2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $transferRepository = new TransferRepositoryV2();
        foreach ($dps as $model) {
            $boxLCList = $transferRepository->getScannedBoxWithProducts($model->id);
            foreach ($boxLCList as $boxInfo) {
                $i++;
                $activeSheet->setCellValue('A' . $i,$model->client_BatchId);
                $activeSheet->setCellValue('B' . $i,$boxInfo['transferOutboundBox']);
                $activeSheet->setCellValue('C' . $i,$boxInfo['productBarcode']);
                $activeSheet->setCellValue('D' . $i,$boxInfo['productSku']);
                $activeSheet->setCellValue('E' . $i,$boxInfo['productQty']);
            }
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="TransferBoxLcWithProducts' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }

    /*
	 * Export to excel to Box TEN
	 **/
    public function actionExportToExcelBoxTen()
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
        $activeSheet->setCellValue('A' . $i, 'BatchId')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'Box TEN')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'Counter')->getColumnDimension('C')->setAutoSize(true); // +

        $searchModel = new EcommerceTransferSearchV2();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $transferRepository = new TransferRepositoryV2();

        foreach ($dps as $model) {
            $boxTenList = $transferRepository->getExpectedBox($model->client_BatchId);
            $counter = 1;
            foreach ($boxTenList as $k=>$boxTen) {
                $i++;
                $activeSheet->setCellValue('A' . $i,$model->client_BatchId);
                $activeSheet->setCellValue('B' . $i,$boxTen);
                $activeSheet->setCellValue('C' . $i,$counter++);
            }
            $i++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="transfer-box-ten-' . date('d-m-Y_H-i-s') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();
    }


}
