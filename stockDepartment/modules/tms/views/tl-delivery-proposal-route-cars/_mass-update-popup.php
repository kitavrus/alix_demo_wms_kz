<?php

use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlCars;
use common\modules\transportLogistics\models\TlAgents;
use common\modules\client\models\Client;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

/* @var $this yii\web\View
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-form">
    <?php $form = ActiveForm::begin([
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
        'id'=>'mass-update-model-popup-form',
        'enableClientValidation' => true,
        'validateOnType' => true,
    ]); ?>
        <?= $form->field($model, 'status_invoice')->dropDownList($model->getInvoiceStatusArray()) ?>
    <div class="form-group">
        <?= Html::submitButton( Yii::t('transportLogistics/buttons', 'Update'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>