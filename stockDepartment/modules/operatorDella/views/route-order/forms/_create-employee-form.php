<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientEmployees */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="client-managers-form">
    <?php $form = ActiveForm::begin([
        'id' => 'add-new-client-employee-form',
        'enableClientValidation' => true,
        'validateOnType' => true,
    ]); ?>
    <?= $form->field($model, 'first_name')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'phone', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >+7</div>',
        ]
    ])->widget(\yii\widgets\MaskedInput::className(), [
        'model' => $model,
        'mask' => '999-999-99-99',
    ]); ?>
    <?= $form->field($model, 'phone_mobile', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >+7</div>',
        ]
    ])->widget(\yii\widgets\MaskedInput::className(), [
        'model' => $model,
        'mask' => '999-999-99-99',
    ]); ?>
    <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>
    <?= $form->field($model, 'manager_type')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'status')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'password')->hiddenInput(['value' => 'pce' . time()])->label(false) ?>
    <?= $form->field($model, 'username')->hiddenInput(['value' => 'ce' . time()])->label(false); ?>
    <?= $form->field($model, 'client_id', ['template' => '{input}'])->hiddenInput() ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>