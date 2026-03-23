<?php

namespace app\modules\stock\controllers;

use common\ecommerce\entities\EcommerceChangeAddressPlaceSearch;
use Yii;
use common\modules\stock\models\ChangeAddressPlace;
use common\modules\stock\models\ChangeAddressPlaceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChangeAddressPlaceController implements the CRUD actions for ChangeAddressPlace model.
 */
class ChangeAddressPlaceController extends Controller
{


    /**
     * Lists all ChangeAddressPlace models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChangeAddressPlaceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
		$activeSheet->setCellValue('A' . $i, 'Из короба/ места')->getColumnDimension('A')->setAutoSize(true); // +
		$activeSheet->setCellValue('B' . $i, 'В короба/ места')->getColumnDimension('B')->setAutoSize(true); // +
		$activeSheet->setCellValue('C' . $i, 'Сообщение')->getColumnDimension('C')->setAutoSize(true); // +
		$activeSheet->setCellValue('D' . $i, 'Дата перемещения')->getColumnDimension('D')->setAutoSize(true); // +

		$searchModel = new ChangeAddressPlaceSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;
		$dps = $dataProvider->getModels();

		$i = 1;
		$asDatetimeFormat = 'php:d.m.Y H:i:s';
		foreach ($dps as $model) {
			$i++;
			$activeSheet->setCellValue('A' . $i, $model->from_barcode);
			$activeSheet->setCellValue('B' . $i, $model->to_barcode);
			$activeSheet->setCellValue('C' . $i, $model->message);
			$created_at = !empty ($model->created_at) ? Yii::$app->formatter->asDatetime($model->created_at, $asDatetimeFormat) : '-';
			$activeSheet->setCellValue('D' . $i, $created_at);
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="change-box-address-report' . date('d.m.Y') . '.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		Yii::$app->end();
	}
}
