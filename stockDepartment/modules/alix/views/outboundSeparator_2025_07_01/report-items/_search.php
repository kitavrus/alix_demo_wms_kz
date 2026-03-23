<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\intermode\controllers\outboundSeparator\domain\entities\OutboundSeparatorItemsSerach */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="outbound-separator-items-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'outbound_separator_id') ?>

    <?= $form->field($model, 'outbound_id') ?>

    <?= $form->field($model, 'order_number') ?>

    <?= $form->field($model, 'outbound_box_barcode') ?>

    <?php // echo $form->field($model, 'product_barcode') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_user_id') ?>

    <?php // echo $form->field($model, 'updated_user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
