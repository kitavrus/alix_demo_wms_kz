<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\ReturnOutboundStatus;
use common\ecommerce\entities\EcommerceInbound;
use common\ecommerce\entities\EcommerceOutboundSearch;
use common\ecommerce\entities\EcommerceReturn;
use common\ecommerce\entities\EcommerceReturnItem;
use common\ecommerce\entities\EcommerceReturnItemSearch;
use common\ecommerce\entities\EcommerceReturnSearch;
use PHPExcel;
use PHPExcel_IOFactory;
use stockDepartment\components\Controller;
use Yii;
use yii\web\NotFoundHttpException;


class ReturnReportController extends Controller
{
	/**
	 * Lists all EcommerceOutbound models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel = new EcommerceReturnSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination->pageSize = 100;

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Displays a single EcommerceOutbound model.
	 * @param integer $id
	 * @return mixed
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public function actionView($id)
	{
		$searchModel = new EcommerceReturnItemSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

		return $this->render('view', [
			'model' => $this->one($id),
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
	}

	/**
	 * Finds the EcommerceInbound model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return EcommerceReturn the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function one($id)
	{
		if (($model = EcommerceReturn::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}


    public function actionExportToExcel()
    {
        //die('ecommerce/defacto/return-report/export-to-excel DIE');

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
        $activeSheet->setCellValue('A' . $i, 'Номер Заказа')->getColumnDimension('A')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('B' . $i, 'Кол-во ожидали')->getColumnDimension('B')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('C' . $i, 'Кол-во приняли')->getColumnDimension('C')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('D' . $i, 'TTN')->getColumnDimension('D')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('E' . $i, 'короб отгрузки')->getColumnDimension('E')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('F' . $i, 'Дата создания')->getColumnDimension('F')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('G' . $i, 'IsRefundable')->getColumnDimension('G')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('H' . $i, 'RefundableMessage')->getColumnDimension('H')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('I' . $i, 'OrderSource')->getColumnDimension('I')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('J' . $i, 'Status')->getColumnDimension('J')->setAutoSize(true); // +; // +



		$searchModel = new EcommerceReturnSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;
		$dps = $dataProvider->getModels();

        $file = 'return-report-'.date('Y-m-d').'-result.xlsx';
        $asDatetimeFormat = 'php:d.m.Y';// H:i:s';
        foreach ($dps as $return) {
//            $allProducts = EcommerceReturnItem::find()->andWhere(['return_id'=>$return['id']])->asArray()->all();
//            foreach ($allProducts as $product) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $return['order_number']);
                $activeSheet->setCellValue('B' . $i, $return['expected_qty']);
                $activeSheet->setCellValue('C' . $i, $return['accepted_qty']);
                $activeSheet->setCellValue('D' . $i, $return['client_ReferenceNumber']);
                $activeSheet->setCellValue('E' . $i, $return['outbound_box']);
                $activeSheet->setCellValue('F' . $i, Yii::$app->formatter->asDate($return['created_at'],$asDatetimeFormat));
                $activeSheet->setCellValue('G' . $i, $return['client_IsRefundable']);
                $activeSheet->setCellValue('H' . $i, $return['client_RefundableMessage']);
                $activeSheet->setCellValue('I' . $i, $return['client_OrderSource']);
                $activeSheet->setCellValue('J' . $i, ReturnOutboundStatus::getValue($return['status']));
//            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();

//        return \Yii::$app->response->sendFile($file);

        die('ok - ReturnReport - END');
    }

    public function actionExportToExcelWithProducts()
    {
        //die('ecommerce/defacto/return-report/export-to-excel-with-products DIE');

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
        $activeSheet->setCellValue('A' . $i, 'Номер Заказа')->getColumnDimension('A')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('B' . $i, 'Шк товара')->getColumnDimension('B')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('C' . $i, 'Кол-во ожидали')->getColumnDimension('C')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('D' . $i, 'Кол-во приняли')->getColumnDimension('D')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('E' . $i, 'TTN')->getColumnDimension('E')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('F' . $i, 'короб отгрузки')->getColumnDimension('F')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('G' . $i, 'Дата создания')->getColumnDimension('G')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('H' . $i, 'IsRefundable')->getColumnDimension('H')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('I' . $i, 'RefundableMessage')->getColumnDimension('I')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('J' . $i, 'OrderSource')->getColumnDimension('J')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('K' . $i, 'Status')->getColumnDimension('K')->setAutoSize(true); // +; // +
        $activeSheet->setCellValue('L' . $i, 'SkuId')->getColumnDimension('L')->setAutoSize(true); // +; // +


		$searchModel = new EcommerceReturnSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;
		$dps = $dataProvider->getModels();

        $file = 'return-full-report-'.date('Y-m-d').'-result.xlsx';
        $asDatetimeFormat = 'php:d.m.Y';// H:i:s';
        foreach ($dps as $return) {
            $allProducts = EcommerceReturnItem::find()->andWhere(['return_id'=>$return['id']])->asArray()->all();
            foreach ($allProducts as $product) {
                $i++;
                $activeSheet->setCellValue('A' . $i, $return['order_number']);
                $activeSheet->setCellValue('B' . $i, $product['product_barcode']);
                $activeSheet->setCellValue('C' . $i, $product['expected_qty']);
                $activeSheet->setCellValue('D' . $i, $product['accepted_qty']);
                $activeSheet->setCellValue('E' . $i, $return['client_ReferenceNumber']);
                $activeSheet->setCellValue('F' . $i, $return['outbound_box']);
                $activeSheet->setCellValue('G' . $i, Yii::$app->formatter->asDate($return['created_at'],$asDatetimeFormat));
                $activeSheet->setCellValue('H' . $i, $return['client_IsRefundable']);
                $activeSheet->setCellValue('I' . $i, $return['client_RefundableMessage']);
                $activeSheet->setCellValue('J' . $i, $return['client_OrderSource']);
                $activeSheet->setCellValue('K' . $i, ReturnOutboundStatus::getValue($return['status']));
                $activeSheet->setCellValue('L' . $i, $product['client_SkuId']);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        Yii::$app->end();

//        return \Yii::$app->response->sendFile($file);

        die('ok - ReturnReport - END');
    }
}