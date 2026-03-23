<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalOrderBoxes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-order-boxes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tl_delivery_proposal_id')->textInput() ?>

    <?= $form->field($model, 'box_barcode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_user_id')->textInput() ?>

    <?= $form->field($model, 'updated_user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
