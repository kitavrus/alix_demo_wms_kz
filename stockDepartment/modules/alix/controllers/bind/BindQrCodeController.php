<?php

namespace stockDepartment\modules\alix\controllers\bind;

use Yii;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use common\modules\stock\models\Stock;
use stockDepartment\components\Controller;
use stockDepartment\modules\alix\controllers\bind\domain\BindQrCodeForm;

class BindQrCodeController extends Controller
{
    public function actionIndex()
    {
        $bindForm = new BindQrCodeForm();

        return $this->render('index', [
            'bindForm' => $bindForm,
        ]);
    }

    public function actionValidateScannedBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new BindQrCodeForm();
        $model->scenario = 'ScannedBox';
        $model->load(Yii::$app->request->post());

        if ($model->validate()) {
            return [
                'success' => 1,
                'countProductInBox' => $model::getScannedProductInBox($model->box_barcode),
            ];
        }

        return [
            'success' => 0,
            'errors' => ActiveForm::validate($model),
        ];
    }

    public function actionScanProductInBox()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new BindQrCodeForm();
        $model->scenario = 'ScannedProduct';
        $model->load(Yii::$app->request->post());

        if ($model->validate()) {
             return [
                'success' => 1,
             ];
        }

        $errors = ActiveForm::validate($model);
        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }

    public function actionScanOurProduct()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new BindQrCodeForm();
        $model->setScenario('ScanOurProduct');

        $model->load(Yii::$app->request->post());

        if ($model->validate()) {
            return [
                'success' => 1,
            ];
        }

        $errors = ActiveForm::validate($model);
        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }

    public function actionBindQrCode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new BindQrCodeForm();
        $model->scenario = 'BindQrCode';

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $product = Stock::find()
                ->andWhere([
                    "primary_address" => $model->box_barcode,
                    "product_barcode" => $model->product_barcode,
                    'status_availability' => Stock::STATUS_AVAILABILITY_YES,
                ])
                ->andWhere('(our_product_barcode IS NULL OR our_product_barcode = "") AND (bind_qr_code IS NULL OR bind_qr_code = "")')
                ->one();

            if (!$product) {
                return [
                    'success' => 0,
                    'message' => 'Товар с таким ШК в указанном коробе уже привязан наш ШК товара и QR-код',
                ];
            }

            $product->our_product_barcode = $model->our_product_barcode;
            $product->bind_qr_code = $model->bind_qr_code;
            if ($product->save(false)) {
                return [
                    'success' => 1,
                    'message' => 'QR-код был успешно добавлен',
                ];
            } else {
                return [
                    'success' => 0,
                    'message' => 'Ошибка при сохранении QR-кода',
                ];
            }
        }

        $errors = ActiveForm::validate($model);
        return [
            'success' => (empty($errors) ? '1' : '0'),
            'errors' => $errors
        ];
    }
}
