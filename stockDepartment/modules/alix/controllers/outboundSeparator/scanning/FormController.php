<?php

namespace stockDepartment\modules\alix\controllers\outboundSeparator\scanning;

use stockDepartment\components\Controller;
use stockDepartment\modules\alix\controllers\outboundSeparator\scanning\forms\ScanningForm;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class FormController extends Controller
{
    // /alix/outboundSeparator/scan/form/index
    public function actionIndex()
    {
        $form = new ScanningForm();
        return $this->render('scanning-form',['model'=>$form,"items"=>$form->getActiveOutboundSeparator()]);
    }

	/**
	 * Scanning form handler order barcode
	 * */
	public function actionGetInfoByOrder()
	{
		Yii::$app->response->format = Response::FORMAT_JSON;

		$errors = [];
		$orderInfo = [
			"countNotScanned"=>0,
			"countScanned"=>0,
		];
		$form = new ScanningForm();
		$form->setScenario(ScanningForm::SCENARIO_OUTBOUND_SEPARATOR);
		if (!($form->load(Yii::$app->request->post()) && $form->validate())) {
			$errors = ActiveForm::validate($form);
		} else {
			$orderInfo = $form->getInfoByOrder();
		}
		return [
			'success' => (empty($errors) ? 'Y' : 'N'),
			'errors' => $errors,
			'orderInfo' => $orderInfo,
		];
	}

    /**
    * Scanning form handler in box barcode
    * */
    public function actionInBoxBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
		$inBoxInfo = [
			"countScanned"=>0,
		];
		$form = new ScanningForm();
		$form->setScenario(ScanningForm::SCENARIO_IN_BOX_BARCODE);
        if (!($form->load(Yii::$app->request->post()) && $form->validate())) {
            $errors = ActiveForm::validate($form);
        } else {
			$inBoxInfo = $form->getInBoxInfo();
		}
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'inBoxInfo' => $inBoxInfo,
        ];
    }

    /**
    * Scanning form handler out box barcode
    * */
    public function actionOutBoxBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
		$outBoxInfo = [
			"total_in_box"=>0, // Должно быть в коробе
			"out_box_scanned"=>0, // Отсканированно
			"out_box_not_scanned"=>0, // Не отсканированны
			"items"=>[
				"in_box"=>[],
				"out_box"=>[],
			],
		];
		$form = new ScanningForm();
		$form->setScenario(ScanningForm::SCENARIO_OUT_BOX_BARCODE);
        if (!($form->load(Yii::$app->request->post()) && $form->validate())) {
            $errors = ActiveForm::validate($form);
        } else {
			$outBoxInfo = $form->getOutBoxInfo();
		}
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'outBoxInfo' => $outBoxInfo,
			'items' => $this->renderPartial('_scanning-items', ['outBoxInfo' => $outBoxInfo]),
        ];
    }

    /**
     * Scanning form handler Is Product Barcode
     * */
    public function actionProductBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = [];
		$outBoxInfo = [
			"total_in_box"=>0, // Должно быть в коробе
			"out_box_scanned"=>0, // Отсканированно
			"out_box_not_scanned"=>0, // Не отсканированны
			"items"=>[
				"in_box"=>[],
				"out_box"=>[],
			],
		];
		$inBoxInfo = [
			"countScanned"=>0,
		];

		$orderInfo = [
			"countNotScanned"=>0,
			"countScanned"=>0,
		];
		$form = new ScanningForm();
		$form->setScenario(ScanningForm::SCENARIO_PRODUCT_BARCODE);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
			$form->scannedProductOnStock();
			$outBoxInfo = $form->getOutBoxInfo();
			$inBoxInfo = $form->getInBoxInfo();
			$orderInfo = $form->getInfoByOrder();
        } else {
            $errors = ActiveForm::validate($form);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
			'outBoxInfo' => $outBoxInfo,
			'inBoxInfo' => $inBoxInfo,
			'orderInfo' => $orderInfo,
			'items' => $this->renderPartial('_scanning-items', ['outBoxInfo' => $outBoxInfo]),
        ];
    }
}