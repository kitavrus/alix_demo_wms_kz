<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\inbound\models\InboundOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inbound-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'supplier_id')->textInput() ?>

    <?= $form->field($model, 'warehouse_id')->textInput() ?>

    <?= $form->field($model, 'order_number')->textInput(['maxlength' => 64]) ?>

    <?= $form->field($model, 'order_type')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'expected_qty')->textInput() ?>

    <?= $form->field($model, 'accepted_qty')->textInput() ?>

    <?= $form->field($model, 'accepted_number_places_qty')->textInput() ?>

    <?= $form->field($model, 'expected_number_places_qty')->textInput() ?>

    <?= $form->field($model, 'expected_datetime')->textInput() ?>

    <?= $form->field($model, 'begin_datetime')->textInput() ?>

    <?= $form->field($model, 'end_datetime')->textInput() ?>

    <?= $form->field($model, 'created_user_id')->textInput() ?>

    <?= $form->field($model, 'updated_user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('forms', 'Create') : Yii::t('forms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
