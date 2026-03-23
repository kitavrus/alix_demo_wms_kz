<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\billing\models\TlDeliveryProposalBillingConditions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-billing-conditions-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?//= $form->field($model, 'tl_delivery_proposal_billing_id')->hiddenInput(['']) ?>

<!--    --><?//= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'title')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'price_invoice_with_vat')->textInput(['maxlength' => 26]) ?>

    <?= $form->field($model, 'formula_tariff')->textInput(['maxlength' => 256]) ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>
<!--    --><?//= $form->field($model, 'delivery_type')->dropDownList($model::getDeliveryTypeArray()) ?>
    <?= $form->field($model, 'sort_order')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'deleted')->textInput() ?>

    <?= Html::hiddenInput('tl_delivery_proposal_billing_id',Yii::$app->request->get('rule_id')) ?>

<!--    --><?//= Html::hiddenInput('tl_delivery_proposal_billing_id','') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
