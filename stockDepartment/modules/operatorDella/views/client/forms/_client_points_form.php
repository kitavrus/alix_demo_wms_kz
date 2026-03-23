<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use personalDepartment\modules\tariff\models\DeliveryCalculatorForm;
use common\modules\billing\models\TlDeliveryProposalBilling;
use app\modules\operatorDella\models\DeliveryOrderSearch;
?>

<?= Html::beginTag('div', ['class'=>'client-points-form col-sm-5']) ?>

<?= Html::label(Yii::t('client/titles', 'Sender'), 'sender');?>
<?= Html::dropDownList('sender', '', DeliveryOrderSearch::getPointsByClient($model->id), ['prompt'=>Yii::t('client/titles', 'Select sender'), 'class'=>'form-control', 'id'=>'sender']);?>
<?= Html::endTag('br')?>
<?= Html::label(Yii::t('client/titles', 'Recipient'), 'recipient');?>
<?= Html::dropDownList('recipient', '', DeliveryOrderSearch::getPointsByClient($model->id),['prompt'=>Yii::t('client/titles', 'Select recipient'),'class'=>'form-control', 'id'=>'recipient']); ?>
<?= Html::endTag('br')?>
<?= Html::label(Yii::t('client/forms', 'Weight(kg)'), 'weight');?>
<?= Html::textInput('weight', '', ['class'=>'form-control', 'id'=>'weight']); ?>
<?= Html::endTag('br')?>
<?= Html::label(Yii::t('client/forms', 'Volume(м³)'), 'volume');?>
<?= Html::textInput('volume', '', ['class'=>'form-control', 'id'=>'volume']); ?>
<?= Html::endTag('br')?>
<?= Html::label(Yii::t('client/forms', 'Delivery type'), 'delivery_type');?>
<?= Html::dropDownList('delivery_type', '', TlDeliveryProposalBilling::getDeliveryTypeArray(), ['prompt'=>Yii::t('client/titles', 'Select delivery type'),'class'=>'form-control', 'id'=>'delivery-type']); ?>
<?= Html::endTag('br')?>

<div class="form-group">
    <?= Html::button(Yii::t('client/buttons', 'Add order'),['class' => 'btn btn-primary', 'id'=>'quick-order', 'data-client' =>$model->id]); ?>
    <?= Html::button(Yii::t('client/buttons', 'Pre-calculate price'),['class' => 'btn btn-warning', 'id'=>'pre-calculate', 'data-client' =>$model->id]); ?>
</div>

<?= Html::endTag('div')?>
<div class="col-sm-5">
    <div id="delivery-result" class="panel panel-danger hidden">
        <div class="panel-heading">
            <strong> <?= Yii::t('frontend/titles', 'Delivery cost: ') ?></strong>
        </div>
        <div class="panel-body">
            <h2></h2>
        </div>
    </div>
</div>
