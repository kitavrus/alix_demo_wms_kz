<?php

namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\defacto\returnOutbound\forms\ReturnForm;
use common\ecommerce\defacto\returnOutbound\service\ReturnService;
use stockDepartment\components\Controller;

use Yii;
use yii\bootstrap\ActiveForm;
use yii\helpers\VarDumper;
use yii\web\Response;

class ReturnOutboundController extends Controller
{
    public function actionIndex()
    {
        $form = new ReturnForm();
        return $this->render('index', ['model' => $form]);
    }

    /*
    * Scanning form handler Is Employee Barcode
    * DONE
    * */
    public function actionEmployeeBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new ReturnForm();
        $model->setScenario(ReturnForm::SCENARIO_EMPLOYEE_BARCODE);

        if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }

    /*
    * Scanning form handler Is Employee Barcode
    * DONE
    * */
    public function actionOrderNumber()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $expectedQty = 0;
        $acceptedQty = 0;
        $packageInfo = [];

        $returnForm = new ReturnForm();
        $returnForm->setScenario(ReturnForm::SCENARIO_ORDER_NUMBER);
        $returnForm->load(Yii::$app->request->post());

        $service = new ReturnService();
        $service->preLoadOrderNumber($returnForm->getDTO());


        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $returnOrderInfo = $service->scanOrderNumber($returnForm->getDTO());

            $expectedQty = intval($returnOrderInfo->order->expected_qty);
            $acceptedQty = intval($returnOrderInfo->order->accepted_qty);
        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'expectedQty'=> $expectedQty,
            'acceptedQty'=> $acceptedQty,
        ];
    }

    /*
 * Scanning form handler Is Employee Barcode
 * DONE
 * */
    public function actionBoxBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $qtyInbox = 0;

        $returnForm = new ReturnForm();
        $returnForm->setScenario(ReturnForm::SCENARIO_BOX_BARCODE);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $service = new ReturnService();
            $qtyInbox = $service->scanBoxBarcode($returnForm->getDTO());
        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'qtyInbox'=> $qtyInbox,
        ];
    }

    /*
     * Scanning form handler Is Product Barcode
     * */
    public function actionProductBarcode()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $expectedQty = 0;
        $acceptedQty = 0;
        $qtyInbox = 0;

        $returnForm = new ReturnForm();
        $returnForm->setScenario(ReturnForm::SCENARIO_PRODUCT_BARCODE);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $service = new ReturnService();
            $returnOrderInfo = $service->scanProductBarcode($returnForm->getDTO());
            $qtyInbox = $service->scanBoxBarcode($returnForm->getDTO());

            $expectedQty = intval($returnOrderInfo->order->expected_qty);
            $acceptedQty = intval($returnOrderInfo->order->accepted_qty);
        } else {
            $errors = ActiveForm::validate($returnForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'expectedQty'=> $expectedQty,
            'acceptedQty'=> $acceptedQty,
            'qtyInbox'=> $qtyInbox,
        ];
    }

    //
    public function actionShowOrderItems()
    { // show-order-items
        Yii::$app->response->format = Response::FORMAT_JSON;

        $returnForm = new ReturnForm();
        $returnForm->setScenario(ReturnForm::SCENARIO_SHOW_ORDER_ITEMS);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $service = new ReturnService();
            return [
                'success' => 'Y',
                'items' => $this->renderPartial('_show-order-items', ['items' => $service->showOrderItems($returnForm->getDTO()),'dto'=>$returnForm->getDTO()]),
            ];
        }

        $errors = ActiveForm::validate($returnForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    //
    public function actionShowBoxItems()
    { // show-box-items
        Yii::$app->response->format = Response::FORMAT_JSON;

        $returnForm = new ReturnForm();
        $returnForm->setScenario(ReturnForm::SCENARIO_SHOW_BOX_ITEMS);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $service = new ReturnService();
            return [
                'success' => 'Y',
                'items' => $this->renderPartial('_show-box-items', ['items' => $service->showBoxItems($returnForm->getDTO()),'dto'=>$returnForm->getDTO()]),
            ];
        }

        $errors = ActiveForm::validate($returnForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    //
    public function actionEmptyBox()
    { // empty-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $expectedQty = 0;
        $acceptedQty = 0;
        $qtyInbox = 0;

        $returnForm = new ReturnForm();
        $returnForm->setScenario(ReturnForm::SCENARIO_EMPTY_BOX);

        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $service = new ReturnService();
            $returnOrderInfo = $service->emptyBox($returnForm->getDTO());
            $expectedQty = intval($returnOrderInfo->order->expected_qty);
            $acceptedQty = intval($returnOrderInfo->order->accepted_qty);
        }

        $errors = ActiveForm::validate($returnForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'expectedQty'=> $expectedQty,
            'acceptedQty'=> $acceptedQty,
            'qtyInbox'=> $qtyInbox,
        ];
    }

    //
    public function actionComplete()
    { // complete
        Yii::$app->response->format = Response::FORMAT_JSON;

        $returnForm = new ReturnForm();
        $returnForm->setScenario(ReturnForm::SCENARIO_COMPLETE);
        $returnOrderInfo = [];
        if ($returnForm->load(Yii::$app->request->post()) && $returnForm->validate()) {
            $service = new ReturnService();
            $returnOrderInfo = $service->completeOrder($returnForm->getDTO());
        }

        $errors = ActiveForm::validate($returnForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'returnOrderInfo' => $returnOrderInfo,
        ];
    }
}