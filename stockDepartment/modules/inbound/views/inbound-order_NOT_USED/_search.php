<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\inbound\models\InboundOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inbound-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'supplier_id') ?>

    <?= $form->field($model, 'warehouse_id') ?>

    <?= $form->field($model, 'order_number') ?>

    <?php // echo $form->field($model, 'order_type') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'expected_qty') ?>

    <?php // echo $form->field($model, 'accepted_qty') ?>

    <?php // echo $form->field($model, 'accepted_number_places_qty') ?>

    <?php // echo $form->field($model, 'expected_number_places_qty') ?>

    <?php // echo $form->field($model, 'expected_datetime') ?>

    <?php // echo $form->field($model, 'begin_datetime') ?>

    <?php // echo $form->field($model, 'end_datetime') ?>

    <?php // echo $form->field($model, 'created_user_id') ?>

    <?php // echo $form->field($model, 'updated_user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('forms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('forms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
