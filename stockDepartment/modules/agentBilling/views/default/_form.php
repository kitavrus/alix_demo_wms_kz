<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\modules\transportLogistics\transportLogistics;
use kartik\datecontrol\DateControl;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

use common\modules\transportLogistics\components\TLHelper;
use common\modules\transportLogistics\models\TlAgents;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBilling */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="Tl-agent-billing-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'agent_id')->dropDownList(TlAgents::getActiveAgentsArray(),
        ['prompt' => Yii::t('titles', 'Select agent')]); ?>

    <?= $form->field($model, 'cash_no')->dropDownList($model::getPaymentMethodArray()) ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
