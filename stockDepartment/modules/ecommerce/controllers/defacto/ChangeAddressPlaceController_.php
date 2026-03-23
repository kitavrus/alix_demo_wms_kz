<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\defacto\changeAddressPlace\forms\BoxToBoxForm;
use common\ecommerce\defacto\changeAddressPlace\forms\BoxToPlaceForm;
use common\ecommerce\defacto\changeAddressPlace\forms\ProductToBoxForm;
use common\ecommerce\defacto\changeAddressPlace\service\ChangeAddressPlaceService;
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
}