<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\ecommerce\entities\EcommerceCheckBoxInventorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ecommerce-check-box-inventory-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <table class="table" width="100%" cellspacing="10">
        <tr>
            <td width="10%">
                <?= $form->field($model, 'inventory_key') ?>
            </td>
            <td width="10%">
                <?= $form->field($model, 'status')->dropDownList((new \common\b2b\domains\checkBox\constants\CheckBoxStatus())->getAll(), ['prompt' => '']); ?>
            </td>
        </tr>
    </table>

<!--    --><?//= $form->field($model, 'expected_product_qty') ?>

<!--    --><?//= $form->field($model, 'scanned_product_qty') ?>

    <?php // echo $form->field($model, 'expected_box_qty') ?>

    <?php // echo $form->field($model, 'scanned_box_qty') ?>

    <?php // echo $form->field($model, 'begin_datetime') ?>

    <?php // echo $form->field($model, 'end_datetime') ?>

    <?php // echo $form->field($model, 'complete_date') ?>

    <?php // echo $form->field($model, 'created_user_id') ?>

    <?php // echo $form->field($model, 'updated_user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('buttons', 'Очистить поиск'), 'index', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
