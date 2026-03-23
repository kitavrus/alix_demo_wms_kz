<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use personalDepartment\modules\tariff\models\DeliveryCalculatorForm;
use common\modules\billing\models\TlDeliveryProposalBilling;
use app\modules\order\models\DeliveryOrderSearch;
?>

<?= Html::beginTag('div', ['class'=>'transportation-order-form']) ?>

<?php $form = ActiveForm::begin([
    'id' => 'transportation-order-filter-form',
    'method' => 'GET',
    'options' => [
        'class'=>'form-inline'
    ],
]); ?>

<?= Html::tag('h3',Yii::t('client/titles', 'Filter'))?>
<?= $form->field($searchModel, 'id')->textInput()->label(Yii::t('client/forms', 'TTN number')); ?>
<?= $form->field($searchModel, 'client_id')->dropDownList($clientArray,['prompt'=>Yii::t('client/titles', 'Select')]);/*->label(Yii::t('client/forms', 'TTN number')); */ ?>
<?= $form->field($searchModel, 'cityFrom')->dropDownList(DeliveryCalculatorForm::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT), ['prompt'=>Yii::t('client/titles', 'Select')])->label(Yii::t('client/forms', 'Point from')) ?>
<?= $form->field($searchModel, 'cityTo')->dropDownList(DeliveryCalculatorForm::getDefaultRoutesTo(TlDeliveryProposalBilling::TARIFF_TYPE_PERSON_DEFAULT), ['prompt'=>Yii::t('client/titles', 'Select')])->label(Yii::t('client/forms', 'Point to')) ?>
<?= $form->field($searchModel, 'status')->dropDownList($searchModel->getStatusArray(),['prompt'=>Yii::t('client/titles', 'Select')]) ?>

<?= Html::endTag('br')?>
<div class="form-group">
    <?= Html::submitButton(Yii::t('client/buttons', 'Apply'), ['class' =>'btn btn-success']) ?>
    <?= Html::a(Yii::t('client/buttons', 'Clear'), ['my-orders'], ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
<?= Html::endTag('div') ?>
<?= Html::endTag('br')?>
<script type="text/javascript">
    $(document).ready(function(){

    });
</script>