<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpensesType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-route-unforeseen-expenses-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList($model::getStatusArray()) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
