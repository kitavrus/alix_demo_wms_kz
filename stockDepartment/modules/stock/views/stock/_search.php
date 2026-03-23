<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\stock\models\StockSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'inbound_order_id') ?>

    <?= $form->field($model, 'outbound_order_id') ?>

    <?= $form->field($model, 'warehouse_id') ?>

    <?= $form->field($model, 'product_id') ?>

    <?php // echo $form->field($model, 'product_name') ?>

    <?php // echo $form->field($model, 'product_barcode') ?>

    <?php // echo $form->field($model, 'product_model') ?>

    <?php // echo $form->field($model, 'product_sku') ?>

    <?php // echo $form->field($model, 'box_barcode') ?>

    <?php // echo $form->field($model, 'condition_type') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'stock_availability') ?>

    <?php // echo $form->field($model, 'primary_address') ?>

    <?php // echo $form->field($model, 'secondary_address') ?>

    <?php // echo $form->field($model, 'created_user_id') ?>

    <?php // echo $form->field($model, 'updated_user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('froms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('froms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
