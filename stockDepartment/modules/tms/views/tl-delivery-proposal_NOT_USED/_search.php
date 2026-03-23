<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model stockDepartment\modules\transportLogistics\models\TlDeliveryProposalSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'route_from') ?>

    <?= $form->field($model, 'route_to') ?>

    <?= $form->field($model, 'delivery_date') ?>

    <?php // echo $form->field($model, 'mc') ?>

    <?php // echo $form->field($model, 'mc_actual') ?>

    <?php // echo $form->field($model, 'kg') ?>

    <?php // echo $form->field($model, 'kg_actual') ?>

    <?php // echo $form->field($model, 'number_places') ?>

    <?php // echo $form->field($model, 'number_places_actual') ?>

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
        <?= Html::submitButton(Yii::t('transportLogistics/forms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('transportLogistics/forms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
