<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\stock\models\Stock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inbound_order_id')->textInput() ?>

    <?= $form->field($model, 'outbound_order_id')->textInput() ?>

    <?= $form->field($model, 'warehouse_id')->textInput() ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

    <?= $form->field($model, 'product_name')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'product_barcode')->textInput(['maxlength' => 54]) ?>

    <?= $form->field($model, 'product_model')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'product_sku')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'box_barcode')->textInput(['maxlength' => 54]) ?>

    <?= $form->field($model, 'condition_type')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'stock_availability')->textInput() ?>

    <?= $form->field($model, 'primary_address')->textInput(['maxlength' => 25]) ?>

    <?= $form->field($model, 'secondary_address')->textInput(['maxlength' => 25]) ?>

    <?= $form->field($model, 'created_user_id')->textInput() ?>

    <?= $form->field($model, 'updated_user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'deleted')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('froms', 'Create') : Yii::t('froms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
