<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 12.09.14
 * Time: 10:38
 */


use yii\helpers\Html;
//use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
//use common\modules\client\models\Client;
use app\modules\transportLogistics\transportLogistics;
use common\modules\transportLogistics\components\TLHelper;

/* @var $this yii\web\View */
/* @var $model common\modules\transportLogistics\models\TlDeliveryProposalOrders */
/* @var $deliveryProposalModel common\modules\transportLogistics\models\TlDeliveryProposal */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tl-delivery-proposal-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_type')->dropDownList($model::getOrderTypeArray()) ?>
    <?= $form->field($model, 'delivery_type')->dropDownList($model::getDeliveryTypeArray()) ?>

    <?= $form->field($model, 'order_number')->textInput() ?>
    <?php echo $form->field($model, 'number_places')->textInput() ?>
<!--    --><?php //= $form->field($model, 'number_places_actual')->textInput() ?>
    <?= $form->field($model, 'mc')->textInput() ?>
<!--    --><?php //= $form->field($model, 'mc_actual')->textInput() ?>
    <?= $form->field($model, 'kg')->textInput() ?>
    <?= $form->field($model, 'title')->textInput() ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 8]) ?>
<!--    --><?//= $form->field($model, 'kg_actual')->textInput() ?>

    <?= $form->field($model, 'tl_delivery_proposal_id',['template'=>'{input}'])->hiddenInput(['value'=>$deliveryProposalModel->id]) ?>
    <?= $form->field($model, 'client_id',['template'=>'{input}'])->hiddenInput(['value'=>$deliveryProposalModel->client_id]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('transportLogistics/buttons', 'Create') : Yii::t('transportLogistics/buttons', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>