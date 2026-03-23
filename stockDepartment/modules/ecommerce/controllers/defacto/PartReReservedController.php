<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\defacto\outbound\forms\PartReReservedForm;
use common\ecommerce\defacto\outbound\service\PartReReservedService;
use stockDepartment\components\Controller;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Response;

class PartReReservedController extends Controller
{
    public function actionIndex()
    { //  /ecommerce/defacto/outbound/part-re-reserved
        $form = new PartReReservedForm();
        return $this->render('scanning-form', ['model' => $form]);
    }

    /**
    * Scanning form Employee Barcode
    * */
    public function actionEmployeeBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $scanForm = new PartReReservedForm();
        $scanForm->setScenario(PartReReservedForm::SCENARIO_EMPLOYEE_BARCODE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $service = new PartReReservedService($dto);
//            $orderInfo = $service->getOrderInfo($dto->order->id);
            return [
                'success' => 'Y',
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
        ];
    }

    /**
    * Scanning form handler Is Picking List Barcode
    * */
    public function actionPickListBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $result = '';
        $scanForm = new PartReReservedForm();
        $scanForm->setScenario(PartReReservedForm::SCENARIO_PICK_LIST_BARCODE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();
            $service = new PartReReservedService($dto);
            $reservedProducts = $service->getReservedProducts($dto->order->id);
            return [
                'success' => 'Y',
                'result' =>  $this->renderPartial('_picking-list',['reservedProducts'=>$reservedProducts]),
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'result' => $result,
        ];
    }


    public function actionShowOtherProductAddresses()
    { // show-other-product-addresses
        Yii::$app->response->format = Response::FORMAT_JSON;

        $stockId = Yii::$app->request->get('stockId');
        $changeReason = Yii::$app->request->get('changeReason');

        $errors = [];
        $scanForm = new PartReReservedForm();
        $scanForm->setScenario(PartReReservedForm::SCENARIO_PICK_LIST_BARCODE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new PartReReservedService();
            $result = $service->showOtherProductAddresses($stockId,$changeReason);
            return [
                'success' => 'Y',
                'stockId'=> $stockId,
                'result' => $this->renderPartial('_other-place-address-list',[
                    'freeProducts'=>$result->freeProducts,
                    'stock'=>$result->stock,
                    'changeReason'=>$result->changeReason
                ]),
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
        ];
    }

    public function actionReReserved()
    { // change-addresses
        Yii::$app->response->format = Response::FORMAT_JSON;

        $newStockId = Yii::$app->request->get('newStockId');
        $oldStockId = Yii::$app->request->get('oldStockId');
        $changeReason = Yii::$app->request->get('changeReason');

        $errors = [];
        $scanForm = new PartReReservedForm();
        $scanForm->setScenario(PartReReservedForm::SCENARIO_PICK_LIST_BARCODE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new PartReReservedService();
            $stock = $service->changeReservedAddress($newStockId,$oldStockId,$changeReason);

            return [
                'success' => 'Y',
//                'stockId'=> $stockId,
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
        ];
    }
}