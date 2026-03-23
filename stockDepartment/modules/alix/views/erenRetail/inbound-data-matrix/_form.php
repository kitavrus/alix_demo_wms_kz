<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\dataMatrix\models\InboundDataMatrix */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inbound-data-matrix-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inbound_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'inbound_item_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_barcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_model')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'data_matrix_code')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'print_status')->textInput(['maxlength' => true]) ?>

<!--    --><?//= $form->field($model, 'created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'created_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'updated_at')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
