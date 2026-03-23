<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\OutboundCancelStatus;
use common\ecommerce\constants\OutboundStatus;
use common\ecommerce\defacto\changeAddressPlace\forms\BoxToBoxForm;
use common\ecommerce\defacto\changeAddressPlace\forms\BoxToPlaceForm;
use common\ecommerce\defacto\changeAddressPlace\forms\ProductToBoxForm;
use common\ecommerce\defacto\changeAddressPlace\service\ChangeAddressPlaceService;
use common\ecommerce\entities\EcommerceChangeAddressPlace;
use common\ecommerce\entities\EcommerceChangeAddressPlaceSearch;
use common\ecommerce\entities\EcommerceInboundSearch;
use common\ecommerce\entities\EcommerceOutboundItem;
use common\ecommerce\entities\EcommerceOutboundSearch;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class ChangeAddressPlaceController extends Controller
{
    public function actionIndex()
    {
        $placeToAddressForm = new BoxToPlaceForm();
        return $this->render('index', [
            'placeToAddressForm' => $placeToAddressForm,
        ]);
    }
    public function actionScanBoxBarcode()
    { // scan-box-address
        Yii::$app->response->format = Response::FORMAT_JSON;

        $placeToAddressForm = new BoxToPlaceForm();
        $placeToAddressForm->setScenario('onFromAddress');

        if ($placeToAddressForm->load(Yii::$app->request->post()) && $placeToAddressForm->validate()) {
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($placeToAddressForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanPlaceAddress()
    { // scan-to-place-address
        Yii::$app->response->format = Response::FORMAT_JSON;

        $placeToAddressForm = new BoxToPlaceForm();
        $placeToAddressForm->setScenario('onToPlaceAddress');

        if ($placeToAddressForm->load(Yii::$app->request->post()) && $placeToAddressForm->validate()) {
//
            $placeToAddressService = new ChangeAddressPlaceService();
            $dto = $placeToAddressForm->getDTO();
            $placeToAddressService->changeBoxPlaceAddress($dto->fromPlaceAddress,$dto->toPlaceAddress);
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($placeToAddressForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    //-----------------------------------------------------------------------------
    public function actionProductToBox() {
        $productToBoxForm = new ProductToBoxForm();
        return $this->render('product-to-box', [
            'boxToBoxForm' => $productToBoxForm,
        ]);
    }
    public function actionScanProductFromBox()
    { // scan-from-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $productToBoxForm = new ProductToBoxForm();
        $productToBoxForm->setScenario('onFromBox');

        if ($productToBoxForm->load(Yii::$app->request->post()) && $productToBoxForm->validate()) {
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($productToBoxForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanProductBarcode()
    { // scan-product-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $productToBoxForm = new ProductToBoxForm();
        $productToBoxForm->setScenario('onProductBarcode');

        if ($productToBoxForm->load(Yii::$app->request->post()) && $productToBoxForm->validate()) {
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($productToBoxForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanProductToBox()
    { // scan-to-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $productToBoxForm = new ProductToBoxForm();
        $productToBoxForm->setScenario('onToBox');

        if ($productToBoxForm->load(Yii::$app->request->post()) && $productToBoxForm->validate()) {
            $placeToAddressService = new ChangeAddressPlaceService();
            $dto = $productToBoxForm->getDTO();
            $placeToAddressService->moveProductFromBoxToBox($dto->fromBox,$dto->productBarcode,$dto->toBox);
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($productToBoxForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }


    //-----------------------------------------------------------------------------
    public function actionBoxToBox() {
        $boxToBoxForm = new BoxToBoxForm();

        return $this->render('box-to-box', [
            'boxToBoxForm' => $boxToBoxForm,
        ]);
    }
    public function actionScanFromBox()
    { // scan-from-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $boxToBoxForm = new BoxToBoxForm();
        $boxToBoxForm->setScenario('onFromBox');

        if ($boxToBoxForm->load(Yii::$app->request->post()) && $boxToBoxForm->validate()) {
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($boxToBoxForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    public function actionScanToBox()
    { // scan-to-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $boxToBoxForm = new BoxToBoxForm();
        $boxToBoxForm->setScenario('onToBox');

        if ($boxToBoxForm->load(Yii::$app->request->post()) && $boxToBoxForm->validate()) {
            $placeToAddressService = new ChangeAddressPlaceService();
            $dto = $boxToBoxForm->getDTO();
            $placeToAddressService->moveAllProductsFromBoxToBox($dto->fromBox,$dto->toBox);
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($boxToBoxForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

	public function actionReport()
	{ // report
		$searchModel = new EcommerceChangeAddressPlaceSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		return $this->render('report', [
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
		$activeSheet->setCellValue('C' . $i, 'Товар')->getColumnDimension('C')->setAutoSize(true); // +
		$activeSheet->setCellValue('D' . $i, 'Дата перемещения')->getColumnDimension('D')->setAutoSize(true); // +

		$searchModel = new EcommerceChangeAddressPlaceSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->pagination = false;
		$dps = $dataProvider->getModels();

		$i = 1;
		$asDatetimeFormat = 'php:d.m.Y H:i:s';
		foreach ($dps as $model) {
			$i++;
			$activeSheet->setCellValue('A' . $i, $model->from_barcode);
			$activeSheet->setCellValue('B' . $i, $model->to_barcode);
			$activeSheet->setCellValue('C' . $i, $model->product_barcode);
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