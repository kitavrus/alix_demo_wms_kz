<?php

namespace stockDepartment\modules\other\controllers;


use common\modules\outbound\models\OutboundBox;
use stockDepartment\modules\other\domain\outboundBoxMap\OutboundBoxMapForm;
use yii\bootstrap\ActiveForm;
use yii\web\Response;
use Yii;

// /other/outbound-box-map/box-to-box
class OutboundBoxMapController extends \yii\web\Controller
{
	//-----------------------------------------------------------------------------
	public function actionBoxToBox() {
		$boxToBoxForm = new OutboundBoxMapForm();
		return $this->render('box-to-box', [
			'boxToBoxForm' => $boxToBoxForm,
		]);
	}
	public function actionScanFromBox()
	{ // scan-from-box
		Yii::$app->response->format = Response::FORMAT_JSON;

		$boxToBoxForm = new OutboundBoxMapForm();
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
		$boxToBoxForm = new OutboundBoxMapForm();
		$boxToBoxForm->setScenario('onToBox');
		if ($boxToBoxForm->load(Yii::$app->request->post()) && $boxToBoxForm->validate()) {
			$box = OutboundBox::find()
			  ->andWhere(["our_box"=>$boxToBoxForm->fromBox])
			  ->andWhere("created_user_id is null")
			  ->one();
			if (empty($box)) {
				$box = new OutboundBox();
				$box->our_box = $boxToBoxForm->fromBox;
			}
			$box->client_box = $boxToBoxForm->toBox;
			$box->save(false);
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
}

