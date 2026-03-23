<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\transportLogistics\models\TlDeliveryProposalRouteCarsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-route-cars-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'route_city_from') ?>

    <?= $form->field($model, 'route_city_to') ?>

    <?= $form->field($model, 'delivery_date') ?>

    <?= $form->field($model, 'driver_name') ?>

    <?php // echo $form->field($model, 'driver_phone') ?>

    <?php // echo $form->field($model, 'driver_auto_number') ?>

    <?php // echo $form->field($model, 'mc_filled') ?>

    <?php // echo $form->field($model, 'kg_filled') ?>

    <?php // echo $form->field($model, 'agent_id') ?>

    <?php // echo $form->field($model, 'car_id') ?>

    <?php // echo $form->field($model, 'grzch') ?>

    <?php // echo $form->field($model, 'cash_no') ?>

    <?php // echo $form->field($model, 'price_invoice') ?>

    <?php // echo $form->field($model, 'price_invoice_with_vat') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'status_invoice') ?>

    <?php // echo $form->field($model, 'comment') ?>

    <?php // echo $form->field($model, 'created_user_id') ?>

    <?php // echo $form->field($model, 'updated_user_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('forms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('forms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
