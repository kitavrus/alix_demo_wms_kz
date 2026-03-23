<?php

namespace app\modules\freeenter\controllers;

use common\components\DeliveryProposalManager;
use common\modules\transportLogistics\models\TlDeliveryProposal;
use kartik\datecontrol\DateControl;
use kartik\datecontrol\Module;
use stockDepartment\modules\freeenter\models\EnterDeliveryDate;
use app\modules\freeenter\forms\SaveTTNForm;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use Yii;

class DefaultController extends Controller
{

    public function actionSaveTtn($key) {

        $this->layout = 'main-free-enter';
        $ttnForm = new SaveTTNForm();

        $ttnForm->checkKey($key);


        $ttnForm->setScenario('onTTN');
        if ($ttnForm->load(Yii::$app->request->post()) && $ttnForm->validate()) {
            $ttnForm->saveClientTTN();
            Yii::$app->getSession()->setFlash('success', "ТТНка клиента успешно сохранена");
            return $this->refresh();
        }

        return $this->render('enter-client-ttn-form',['ttnForm'=>$ttnForm]);
    }

    public function actionEnterDeliveryDate()
    {
        // enter-delivery-date
        $this->layout = 'main-free-enter';
        $model = new EnterDeliveryDate();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dp = TlDeliveryProposal::findOne($model->ttn_number);

            $dispFormat =  Module::parseFormat(Yii::$app->params['dateControlDisplay']['datetime'], Module::FORMAT_DATETIME);
            $dispTimezone =  Yii::$app->params['dateControlDisplayTimezone'];
            $saveFormat = Module::parseFormat( Yii::$app->params['dateControlSave']['datetime'], Module::FORMAT_DATETIME);
            $saveTimezone =  Yii::$app->params['dateControlSaveTimezone'];

            $tm = new \DateTimeZone('UTC');
            $tmp = new \DateTime('now',$tm);
            $displayDate = $tmp->setTimezone(new \DateTimeZone($dispTimezone))->format($dispFormat);
            //+ dispFormat d-m-Y H:i:s
            //+ dispTimezone Asia/Almaty
            //+ displayDate 13-11-2015 02:00:10
            //+ saveFormat U
            // saveTimezone UTC
            // 1447358410
            $settings = [];
            $date = DateControl::getTimestamp($displayDate, $dispFormat, $dispTimezone, $settings);
            if (empty($date) || !$date) {
                $value = '';
            } elseif ($saveTimezone != null) {
                $value = $date->setTimezone(new \DateTimeZone($saveTimezone))->format($saveFormat);
            } else {
                $value = $date->format($saveFormat);
            }

            $dp->delivery_date = $value;
            $dp->status = TlDeliveryProposal::STATUS_DELIVERED;
            if($dp->save(false)) {
                $dpManager = new DeliveryProposalManager(['id'=>$dp->id]);
                $dpManager->onUpdateProposal();
                Yii::$app->session->setFlash('success', 'Спасибо, дата доставки задана успешно!!!');
            }
            return $this->refresh();
        }

        return $this->render('enter-delivery-date-form',['model'=>$model]);
    }


}