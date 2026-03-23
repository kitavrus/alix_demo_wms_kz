<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 23.09.14
 * Time: 16:34
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalOrderExtras */
/* @var $modelDRouteOrder common\modules\transportLogistics\models\TlDeliveryProposalOrders */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-order-extras-form">

    <?php $form = ActiveForm::begin(); ?>

<!--    --><?//= $form->field($model, 'client_id')->textInput() ?>
<!--    --><?//= $form->field($model, 'tl_delivery_proposal_id')->textInput() ?>
<!--    --><?//= $form->field($model, 'tl_delivery_route_id')->textInput() ?>
<!--    --><?//= $form->field($model, 'tl_delivery_proposal_order_id')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'number_places')->textInput() ?>
    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'client_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRouteOrder->client_id]) ?>
    <?= $form->field($model, 'tl_delivery_proposal_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRouteOrder->tl_delivery_proposal_id]) ?>
    <?= $form->field($model, 'tl_delivery_proposal_order_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRouteOrder->id]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('buttons', 'Create') : Yii::t('buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>