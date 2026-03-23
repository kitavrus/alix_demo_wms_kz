<?php
namespace app\modules\ecommerce\controllers\defacto;

use common\ecommerce\constants\OutboundCancelStatus;
use common\ecommerce\defacto\outbound\forms\CancelForm;
use common\ecommerce\defacto\outbound\service\OutboundService;
use stockDepartment\components\Controller;
use Yii;
use yii\helpers\VarDumper;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

class CancelController extends Controller
{
    public function actionIndex()
    {
        $form = new CancelForm();
        return $this->render('index',['model'=>$form]);
    }


    public function actionOrderNumber()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';

        $model = new CancelForm();
        $model->setScenario(CancelForm::SCENARIO_OUTBOUND_ORDER_NUMBER);

        if (!($model->load(Yii::$app->request->post()) && $model->validate())) {
            $errors = ActiveForm::validate($model);
        }

        return [
            'success' => (empty($errors) ? 'Y' : 'N'),
            'errors' => $errors,
            'messages' => $messages,
        ];
    }


    public function actionCancel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $errors = [];
        $messages = '';
        $scanForm = new CancelForm();
        $scanForm->setScenario(CancelForm::SCENARIO_CANCEL_REASON);

        if ($scanForm->load(Yii::$app->request->post()) && $scanForm->validate()) {
            $dto = $scanForm->getDTO();

            $service = new OutboundService();
            $orderInfo = $service->getOrderInfoByOrderNumber($dto->outboundOrderNumber);
            $service->CancelShipment($orderInfo->order->id,$dto->cancelReason);

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