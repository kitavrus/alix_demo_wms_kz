<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceCheckBoxInventory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-check-box-inventory-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inventory_key')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea() ?>

<!--    --><?//= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

<!--    --><?//= $form->field($model, 'expected_product_qty')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'scanned_product_qty')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'expected_box_qty')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'scanned_box_qty')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'begin_datetime')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'end_datetime')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'complete_date')->textInput() ?>
<!---->
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
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
