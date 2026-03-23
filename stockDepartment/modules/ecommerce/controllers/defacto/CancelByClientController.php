<?php
namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\OutboundCancelStatus;
use common\ecommerce\defacto\outbound\forms\CancelByClientForm;
use common\ecommerce\defacto\outbound\forms\CancelForm;
use common\ecommerce\defacto\outbound\service\CancelOutboundByClientService;
use common\ecommerce\defacto\outbound\service\OutboundService;
use stockDepartment\components\Controller;
use Yii;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

class CancelByClientController extends Controller
{
    public function actionIndex()
    {
        $form = new CancelByClientForm();
        return $this->render('index',['model'=>$form]);
    }

    public function actionOrderNumber()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $scanForm = new CancelByClientForm();
        $scanForm->setScenario(CancelForm::SCENARIO_OUTBOUND_ORDER_NUMBER);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new CancelOutboundByClientService();
            $service->orderNumber($scanForm->getDTO());

            return [
                'success'=>'Y',
                'items' => $this->renderPartial('_show-all-order-items', ['items' => $service->showAllOrderItems($scanForm->getDTO()),'dto'=>$scanForm->getDTO()]),
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }

    public function actionSetBoxAddress()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $scanForm = new CancelByClientForm();
        $scanForm->setScenario(CancelByClientForm::SCENARIO_BOX_ADDRESS);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new CancelOutboundByClientService();
            $service->boxAddress($scanForm->getDTO());

            return [
                'success'=>'Y',
                'items' => $this->renderPartial('_show-all-order-items', ['items' => $service->showAllOrderItems($scanForm->getDTO()),'dto'=>$scanForm->getDTO()]),
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }

    //
    public function actionShowOrderItems()
    { // show-order-items
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new CancelByClientForm();
        $scanForm->setScenario(CancelByClientForm::SCENARIO_SHOW_ORDER_ITEMS);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new CancelOutboundByClientService();
            return [
                'success' => 'Y',
                'items' => $this->renderPartial('_show-order-items', ['items' => $service->showOrderItems($scanForm->getDTO()),'dto'=>$scanForm->getDTO()]),
            ];
        }

        $errors = ActiveForm::validate($scanForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    //
    public function actionShowAllOrderItems()
    { // show-order-items
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new CancelByClientForm();
        $scanForm->setScenario(CancelByClientForm::SCENARIO_SHOW_ALL_ORDER_ITEMS);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new CancelOutboundByClientService();
            return [
                'success' => 'Y',
                'items' => $this->renderPartial('_show-all-order-items', ['items' => $service->showAllOrderItems($scanForm->getDTO()),'dto'=>$scanForm->getDTO()]),
            ];
        }

        $errors = ActiveForm::validate($scanForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }

    //
    public function actionEmptyBox()
    { // empty-box
        Yii::$app->response->format = Response::FORMAT_JSON;

        $scanForm = new CancelByClientForm();
        $scanForm->setScenario(CancelByClientForm::SCENARIO_EMPTY_BOX);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new CancelOutboundByClientService();
            $service->emptyBox($scanForm->getDTO());
            return [
                'success' => 'Y',
            ];
        }

        $errors = ActiveForm::validate($scanForm);
        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors
        ];
    }



    public function actionCancel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $scanForm = new CancelByClientForm();
        $scanForm->setScenario(CancelByClientForm::SCENARIO_CANCEL_DONE);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $service = new CancelOutboundByClientService();
             $service->cancel($scanForm->getDTO());

            return [
                'success'=>'Y',
            ];
        } else {
            $errors = ActiveForm::validate($scanForm);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }
}