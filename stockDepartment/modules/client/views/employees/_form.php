<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\client\models\ClientEmployees */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-managers-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?//= $form->field($model, 'store_id')->textInput() ?>
    <?= $form->field($model, 'store_id')->dropDownList($storeList,['prompt'=>Yii::t('titles', 'Please select')]) ?>

<!--    --><?//= $form->field($model, 'client_id')->textInput() ?>

<!--    --><?//= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'manager_type')->dropDownList($model::getTypeArray(),['prompt'=>Yii::t('titles', 'Please select')]) ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()); ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= $form->field($model, 'client_id',['template'=>'{input}'])->hiddenInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
