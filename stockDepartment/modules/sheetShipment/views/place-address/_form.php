<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\sheetShipment\models\SheepShipmentPlaceAddressAR */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sheep-shipment-place-address-ar-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('Buttons', 'Create') : Yii::t('Buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>