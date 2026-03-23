<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\product\models\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'client_id')->hiddenInput()->label(false);  ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 256]) ?>
    <?= $form->field($model, 'model')->textInput(['maxlength' => 32]) ?>
    <?= $form->field($model, 'color')->textInput() ?>
    <?= $form->field($model, 'size')->textInput() ?>
    <?= $form->field($model, 'category')->textInput() ?>
    <?= $form->field($model, 'gender')->textInput() ?>
    <?= $form->field($model, 'field_extra1')->textInput() ?>
    <?= $form->field($model, 'field_extra2')->textInput() ?>
    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
