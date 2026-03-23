<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\main\inbound\forms\BoxToBoxForm;
use common\clientObject\main\inbound\forms\PlaceToAddressForm;
use common\clientObject\main\service\BoxToBoxService;
use common\clientObject\main\inbound\service\PlaceToAddressService;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class PlaceToAddressController extends Controller
{
    public function actionIndex()
    {
        $placeToAddressForm = new PlaceToAddressForm();
        return $this->render('index', [
            'placeToAddressForm' => $placeToAddressForm,
        ]);
    }
    public function actionScanFromPlacementUnitAddress()
    { // scan-from-placement-unit-address
        Yii::$app->response->format = Response::FORMAT_JSON;

        $placeToAddressForm = new PlaceToAddressForm();
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
    public function actionScanToPlaceAddress()
    { // scan-to-place-address
        Yii::$app->response->format = Response::FORMAT_JSON;

        $placeToAddressForm = new PlaceToAddressForm();
        $placeToAddressForm->setScenario('onToPlaceAddress');

        if ($placeToAddressForm->load(Yii::$app->request->post()) && $placeToAddressForm->validate()) {
            $placeToAddressService = new PlaceToAddressService($placeToAddressForm->getDTO());
            $placeToAddressService->placementToAddress();
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

    public function actionScanProductBarcode()
    { // scan-product-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $boxToBoxForm = new BoxToBoxForm();
        $boxToBoxForm->setScenario('onProductBarcode');

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
            BoxToBoxService::boxToBox($boxToBoxForm->fromBox,$boxToBoxForm->productBarcode,$boxToBoxForm->toBox);
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