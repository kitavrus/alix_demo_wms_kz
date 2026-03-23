<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\modules\city\models\City;

/* @var $this yii\web\View */
/* @var $model common\modules\leads\models\TransportationOrderLead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transportation-order-lead-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'status')->dropDownList($model->getStatusArray()) ?>

    <?= $form->field($model, 'customer_name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'customer_phone')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'customer_street')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'customer_house')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'customer_floor')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'customer_apartment')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'recipient_name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'recipient_phone')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'recipient_street')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_house')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_floor')->textInput(['maxlength' => 128]) ?>
    <?= $form->field($model, 'recipient_apartment')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'from_city_id')->dropDownList(City::getArrayData()) ?>

    <?= $form->field($model, 'to_city_id')->dropDownList(City::getArrayData()) ?>

    <?= $form->field($model, 'places')->textInput() ?>

    <?= $form->field($model, 'customer_comment')->textarea() ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'volume')->textInput() ?>

    <?= $form->field($model, 'declared_value')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'package_description')->textInput(['maxlength' => 128]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
