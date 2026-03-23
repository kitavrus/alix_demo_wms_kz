<?php

namespace app\modules\sheetShipment\controllers;

use stockDepartment\modules\sheetShipment\forms\SheetShipmentForm;
use stockDepartment\modules\sheetShipment\service\SheetShipmentService;
use Yii;
use stockDepartment\components\Controller;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

/**
 * Default controller for the `sheetShipment` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index',['sheetShipmentForm'=> new SheetShipmentForm()]);
    }

    public function actionMove()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $sheetShipmentForm = new SheetShipmentForm();

        if ($sheetShipmentForm->load(Yii::$app->request->post()) && $sheetShipmentForm->validate()) {
             SheetShipmentService::create($sheetShipmentForm->getDto());
        }

        return [
            'success'=> '0',
            'successMessages'=> '',
//            'errors' => ActiveForm::validate($sheetShipmentForm),
            'errors' => $sheetShipmentForm->getErrors(),
        ];
    }
}