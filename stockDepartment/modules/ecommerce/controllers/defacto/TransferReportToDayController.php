<?php
namespace app\modules\ecommerce\controllers\defacto;
use common\ecommerce\constants\StockTransferStatus;
use common\ecommerce\entities\EcommerceStockSearch;
use PHPExcel;
use PHPExcel_IOFactory;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Controller;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * TransferReportController implements the CRUD actions for EcommerceTransfer model.
 */
class TransferReportToDayController extends Controller
{
    /**
     * Lists all EcommerceTransfer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EcommerceStockSearch();
        $dataProvider = $searchModel->searchForTransfer(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /*
    * Export to excel
    **/
    public function actionExportToExcel()
    {
        $objPHPExcel = new PHPExcel();

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
        $activeSheet->setCellValue('A' . $i, 'transfer_id')->getColumnDimension('A')->setAutoSize(true); // +
        $activeSheet->setCellValue('B' . $i, 'product_barcode')->getColumnDimension('B')->setAutoSize(true); // +
        $activeSheet->setCellValue('C' . $i, 'transfer_outbound_box')->getColumnDimension('C')->setAutoSize(true); // +
        $activeSheet->setCellValue('D' . $i, 'scan_out_datetime')->getColumnDimension('D')->setAutoSize(true); // +
        $activeSheet->setCellValue('E' . $i, 'status_transfer')->getColumnDimension('E')->setAutoSize(true); // +

		$searchModel = new EcommerceStockSearch();
		$dataProvider = $searchModel->searchForTransfer(Yii::$app->request->queryParams);

        $dataProvider->pagination = false;
        $dps = $dataProvider->getModels();
        $asDatetimeFormat = 'php:d.m.Y H:i:s';
        foreach ($dps as $model) {
            $i++;

            $activeSheet->setCellValue('A' . $i, $searchModel->getTransferById($model->transfer_id));
            $activeSheet->setCellValue('B' . $i, $model->product_barcode);
            $activeSheet->setCellValue('C' . $i, $model->transfer_outbound_box);
            $activeSheet->setCellValue('D' . $i, Yii::$app->formatter->asDatetime($model->scan_out_datetime, $asDatetimeFormat));
            $activeSheet->setCellValue('E' . $i, StockTransferStatus::getValue($model->status_transfer));
//            echo $i."<br />";
        }
//		VarDumper::dump($dps);
//		die;

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="transfer-today' . date('d-m-Y_H-i-s') . '.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		Yii::$app->end();

    }
}
