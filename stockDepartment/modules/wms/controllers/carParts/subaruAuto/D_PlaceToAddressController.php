<?php

namespace app\modules\wms\controllers\carParts\subaruAuto;

use common\clientObject\subaruAuto\inbound\forms\PlaceToAddressForm;
use common\clientObject\subaruAuto\inbound\service\PlaceToAddressService;
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
}