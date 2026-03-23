<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 17.09.14
 * Time: 17:21
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use app\modules\transportLogistics\transportLogistics;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalRouteUnforeseenExpenses */
/* @var $modelDRoute common\modules\transportLogistics\models\TlDeliveryRoutes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-route-unforeseen-expenses-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'type_id')->dropDownList($model->getTypeArray(),['prompt'=>'']) ?>
    <?= $form->field($model, 'price_cache')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'cash_no')->dropDownList($modelDRoute->getPaymentMethodArray()) ?>
    <?= $form->field($model, 'who_pays')->dropDownList($model->getWhoPaysArray()) ?>
    <?= $form->field($model, 'price_with_vat')->textInput(['maxlength' => 26]) ?>
    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'tl_delivery_proposal_default_sub_route_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRoute->id]) ?>
    <?= $form->field($model, 'tl_delivery_proposal_default_route_id',['template'=>'{input}'])->hiddenInput(['value'=>$modelDRoute->tl_delivery_proposal_default_route_id]) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>