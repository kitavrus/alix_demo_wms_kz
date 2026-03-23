<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\placementUnit\models\PlacementUnit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="placement-unit-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'barcode')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>