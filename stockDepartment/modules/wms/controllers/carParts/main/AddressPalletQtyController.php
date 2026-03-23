<?php

namespace app\modules\wms\controllers\carParts\main;

use common\clientObject\main\forms\AddressPalletQtyForm;
use common\clientObject\main\inbound\service\PlaceToAddressService;
use common\modules\stock\models\Stock;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class AddressPalletQtyController extends Controller
{
    public function actionIndex()
    {
        $placeToAddressForm = new AddressPalletQtyForm();
        return $this->render('index', [
            'placeToAddressForm' => $placeToAddressForm,
        ]);
    }

    public function actionScanPlaceAddress()
    { // scan-place-address
        Yii::$app->response->format = Response::FORMAT_JSON;

        $placeToAddressForm = new AddressPalletQtyForm();
        $placeToAddressForm->setScenario('onPlaceAddress');

        if ($placeToAddressForm->load(Yii::$app->request->post()) && $placeToAddressForm->validate()) {
            $qtyBoxInAddress = Stock::find()->andWhere([
                'secondary_address'=>$placeToAddressForm->placeAddress,
                'status_availability'=>Stock::STATUS_AVAILABILITY_YES,
            ])->groupBy('primary_address')->count();

            return [
                'success' => 'Y',
                'qtyBoxInAddress' => $qtyBoxInAddress,
            ];
        }

        $errors = ActiveForm::validate($placeToAddressForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }
    public function actionAddPalletPlaceQty()
    { // add-pallet-place-qty
        Yii::$app->response->format = Response::FORMAT_JSON;

        $placeToAddressForm = new AddressPalletQtyForm();
        $placeToAddressForm->setScenario('onPalletPlaceQty');

        if ($placeToAddressForm->load(Yii::$app->request->post()) && $placeToAddressForm->validate()) {
             Stock::updateAll(['address_pallet_qty'=>$placeToAddressForm->palletPlaceQty],['secondary_address'=>$placeToAddressForm->placeAddress]);
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