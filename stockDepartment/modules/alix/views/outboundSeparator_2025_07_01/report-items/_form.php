<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\intermode\controllers\outboundSeparator\domain\entities\OutboundSeparatorItems */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outbound-separator-items-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'outbound_separator_id')->textInput() ?>

    <?= $form->field($model, 'outbound_id')->textInput() ?>

    <?= $form->field($model, 'order_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'outbound_box_barcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_barcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_user_id')->textInput() ?>

    <?= $form->field($model, 'updated_user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
