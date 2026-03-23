<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Inventory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'client_id')->dropDownList($clientsArray) ?>
    <?= $form->field($model, 'order_number')->textInput(['maxlength' => true]) ?>
<!--    --><?php //echo $form->field($model, 'status')->textInput() ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('inventory/forms', 'Create') : Yii::t('inventory/forms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>