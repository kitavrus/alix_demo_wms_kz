<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
//use yii\widgets\ActiveForm;
//use dosamigos\datepicker\DatePicker;
use yii\bootstrap\Modal;
use yii\helpers\Url;
//use frontend\modules\client\models\Client;
use common\modules\transportLogistics\components\TLHelper;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use common\modules\client\models\Client;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use common\modules\client\models\ClientEmployees;

//use frontend\modules\transportLogistics\models\TlAgents;
//use frontend\modules\transportLogistics\models\TlCars;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="tl-delivery-proposal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'route_from')->dropDownList(TLHelper::getStoreArrayByClientID(ClientEmployees::findOne(['user_id'=>Yii::$app->user->id])->client_id,true),['disabled'=>true]);  ?>
    <?= $form->field($model, 'route_to')->dropDownList(TLHelper::getStoreArrayByClientID(ClientEmployees::findOne(['user_id'=>Yii::$app->user->id])->client_id,true),['disabled'=>true]);  ?>
    <?= $form->field($model, 'number_places')->textInput() ?>
    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>