<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceBarcodeManager */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-barcode-manager-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'barcode_prefix')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>