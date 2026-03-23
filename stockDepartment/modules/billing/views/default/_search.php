<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\billing\models\TlDeliveryProposalBillingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-billing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'country_id') ?>

    <?= $form->field($model, 'region_id') ?>

    <?= $form->field($model, 'city_id') ?>

    <?php // echo $form->field($model, 'route_from') ?>

    <?php // echo $form->field($model, 'route_to') ?>

    <?php // echo $form->field($model, 'mc') ?>

    <?php // echo $form->field($model, 'kg') ?>

    <?php // echo $form->field($model, 'number_places') ?>

    <?php // echo $form->field($model, 'price_invoice') ?>

    <?php // echo $form->field($model, 'price_invoice_with_vat') ?>

    <?php // echo $form->field($model, 'formula_tariff') ?>

    <?php // echo $form->field($model, 'status') ?>

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
