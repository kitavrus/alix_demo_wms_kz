<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use personalDepartment\modules\tariff\models\DeliveryCalculatorForm;
use common\modules\billing\models\TlDeliveryProposalBilling;
use app\modules\order\models\DeliveryOrderSearch;
?>

<?= Html::beginTag('div', ['class'=>'client-search-form']) ?>

<?php $form = ActiveForm::begin([
    'id' => 'client-search-form',
    'method' => 'GET',
    'options' => [
        'class'=>'form-inline'
    ],
]); ?>
<?= Html::tag('h3',Yii::t('client/titles', 'Client search'))?>
<?= $form->field($searchModel, 'phone_mobile')->textInput()->label(Yii::t('client/forms', 'Phone')); ?>
<?= $form->field($searchModel, 'full_name')->textInput()->label(Yii::t('client/forms', 'Name')); ?>
<?= $form->field($searchModel, 'email')->textInput()->label(Yii::t('client/forms', 'Email')); ?>
<?//= $form->field($searchModel, 'client_type')->dropDownList($searchModel->getClientTypeArray(),['prompt'=>Yii::t('client/titles', 'Select')])->label(Yii::t('client/forms', 'Client Type')); ?>
<?= Html::endTag('br')?>
<div class="form-group">
    <?= Html::submitButton(Yii::t('client/buttons', 'Search'), ['class' =>'btn btn-success']) ?>
    <?= Html::a(Yii::t('client/buttons', 'Clear'), ['index'], ['class' => 'btn btn-primary']) ?>
</div>
