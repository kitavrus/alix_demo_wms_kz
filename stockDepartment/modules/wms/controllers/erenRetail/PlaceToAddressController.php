<?php

namespace app\modules\wms\controllers\erenRetail;

use stockDepartment\modules\wms\managers\erenRetail\placement\BoxToBox;
use stockDepartment\modules\wms\managers\erenRetail\placement\BoxToBoxForm;
use stockDepartment\modules\wms\managers\erenRetail\placement\BoxToPlace;
use stockDepartment\modules\wms\managers\erenRetail\placement\PlaceToAddressForm;
use common\clientObject\main\service\BoxToBoxService;
use common\clientObject\main\inbound\service\PlaceToAddressService;
use stockDepartment\modules\wms\managers\erenRetail\placement\ProductInBoxToBox;
use stockDepartment\modules\wms\managers\erenRetail\placement\ProductInBoxToBoxForm;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
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
			BoxToPlace::change($placeToAddressForm->fromPlaceAddress,$placeToAddressForm->toPlaceAddress);
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
            BoxToBox::change($boxToBoxForm->fromBox,$boxToBoxForm->toBox);
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

    //-----------------------------------------------------------------------------
    public function actionProductInBoxToBox() {
        $boxToBoxForm = new ProductInBoxToBoxForm();
        return $this->render('product-in-box-to-box', [
            'boxToBoxForm' => $boxToBoxForm,
        ]);
    }
    public function actionScanProductFromBox()
    { // scan-from-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $boxToBoxForm = new ProductInBoxToBoxForm();
        $boxToBoxForm->setScenario('onFromBox');

        if ($boxToBoxForm->load(Yii::$app->request->post()) && $boxToBoxForm->validate()) {
            return [
                'success' => 'Y',
				"countProductInFromBox"=> ProductInBoxToBox::getCountProductInBox($boxToBoxForm->fromBox),
            ];
        }

        $errors = ActiveForm::validate($boxToBoxForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,

        ];
    }

	public function actionScanProductToBox()
	{ // scan-to-box
		Yii::$app->response->format = Response::FORMAT_JSON;
		$boxToBoxForm = new ProductInBoxToBoxForm();
		$boxToBoxForm->setScenario('onToBox');
		if ($boxToBoxForm->load(Yii::$app->request->post()) && $boxToBoxForm->validate()) {
			return [
				'success' => 'Y',
				"countProductInToBox"=> ProductInBoxToBox::getCountProductInBox($boxToBoxForm->toBox),
				"productsInBox"=> $this->renderPartial('_products_in_box',
					[
						'productsInBox'=>ProductInBoxToBox::getProductInBox($boxToBoxForm->toBox)
					]),
			];
		}

		$errors = ActiveForm::validate($boxToBoxForm);
		return [
			'success' => (empty($errors) ? 'Y' : 'N'),
			'errors' => $errors
		];
	}

    public function actionScanProductProductBarcode()
    { // scan-product-barcode
        Yii::$app->response->format = Response::FORMAT_JSON;

        $boxToBoxForm = new ProductInBoxToBoxForm();
        $boxToBoxForm->setScenario('onProductBarcode');

        if ($boxToBoxForm->load(Yii::$app->request->post()) && $boxToBoxForm->validate()) {
			ProductInBoxToBox::change($boxToBoxForm->fromBox,$boxToBoxForm->productBarcode,$boxToBoxForm->toBox);
            return [
                'success' => 'Y',
				"countProductInFromBox"=> ProductInBoxToBox::getCountProductInBox($boxToBoxForm->fromBox),
				"countProductInToBox"=> ProductInBoxToBox::getCountProductInBox($boxToBoxForm->toBox),
				"productsInBox"=> $this->renderPartial('_products_in_box',
					[
						'productsInBox'=>ProductInBoxToBox::getProductInBox($boxToBoxForm->toBox)
					]),
            ];
        }

        $errors = ActiveForm::validate($boxToBoxForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }


}