<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\city\models\RouteDirections */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="route-directions-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'our_box_to_stock_barcode')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'client_box_barcode')->textInput(['maxlength' => true]) ?>
<!--    --><?php //echo $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton( Yii::t('app', 'Обновить'), ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>