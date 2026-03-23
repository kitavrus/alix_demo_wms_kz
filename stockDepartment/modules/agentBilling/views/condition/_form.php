<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\transportLogistics\components\TLHelper;
use kartik\widgets\Select2;
use common\modules\transportLogistics\models\TlAgents;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBillingConditions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-billing-conditions-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?php //= $form->field($model, 'title')->textarea(['rows' => 3]) ?>
    <?=
    $form->field($model, 'route_from', [])->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray(),
        'options' => ['placeholder' => Yii::t('titles', 'Select route from')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>

    <?=
    $form->field($model, 'route_to', [])->widget(Select2::classname(), [
        'language' => 'ru',
        'data' => TLHelper::getStockPointArray(),
        'options' => ['placeholder' => Yii::t('titles', 'Select route to')],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]);
    ?>
    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>
    <?= $form->field($model, 'transport_type')->dropDownList($model::getTransportTypeArray()) ?>
    <?= $form->field($model, 'rule_type')->dropDownList($model::getRuleTypeArray()) ?>

<!--    --><?//= $form->field($model, 'price_invoice')->textInput(['maxlength' => 26]) ?>
<!--    --><?//= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]) ?>
    <?php if($billing->agent->flag_nds == TlAgents::FLAG_NDS_TRUE) {
        echo $form->field($model, 'price_invoice_with_vat')->textInput();
    }  elseif($billing->agent->flag_nds == TlAgents::FLAG_NDS_FALSE || $billing->agent->flag_nds == TlAgents::FLAG_NDS_UNDEFINED) {
        echo $form->field($model, 'price_invoice')->textInput(['maxlength' => 26]);
    } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>