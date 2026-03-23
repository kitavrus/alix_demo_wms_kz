<?php
/**
 * Created by PhpStorm.
 * User: KitavrusAdmin
 * Date: 12.12.2019
 * Time: 10:01
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\app\modules\ecommerce\assets\defacto\ReturnOutboundFormAsset::register($this);
?>
<div id="messages-scanning-container">
    <div id="messages-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'messages-scanning-list',
            'class' => 'alert-info hidden',
        ],
        'body' => '<span id="messages-scanning-list-body"></span>',
    ]);
    ?>
</div>
<h1>
    <div style=" float:left; font-size: 25px;">Возвраты Defacto Ecommerce</div>
</h1>
<br/>
<?= Html::tag('span', Yii::t('outbound/buttons', 'Compete'), ['data-url' => Url::toRoute('complete'), 'class' => 'btn btn-danger pull-right', 'id' => 'returnform-complete-bt', 'style' => 'margin-left:10px;']) ?>
<br/>
<br/>
<?php $form = ActiveForm::begin([
        'id' => 'returnform',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>

<?= $form->field($model, 'employeeBarcode')->textInput([
    'class' => 'form-control input-lg',
    'data-url' => Url::toRoute('employee-barcode')
]); ?>

<?= $form->field($model, 'orderNumber',
    ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="order-info-qty">0/0</div>'
        ]
    ]
)->textInput([
    'class' => 'form-control ext-large-input',
    'data-url' => Url::toRoute('order-number')
])
?>

<?= $form->field($model, 'boxBarcode',
    ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="box-info-qty">0</div>'
        ]
    ]
)->textInput([
    'class' => 'form-control ext-large-input',
    'data-url' => Url::toRoute('box-barcode')
])
?>

<?= $form->field($model, 'productBarcode'
)->textInput([
    'class' => 'form-control ext-large-input',
    'data-url' => Url::toRoute('product-barcode')
])->label(Yii::t('outbound/forms', 'Product Barcode')) ?>

<?= $form->field($model, 'returnProcess')->dropDownList(\common\ecommerce\constants\ReturnOutbound::getAll(), [
])->label(Yii::t('outbound/forms', 'Тип упаковки')) ?>

<?php ActiveForm::end(); ?>

<div class="row" style="margin: 20px 1px">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Содержимое короба'), ['data-url' => Url::toRoute('show-box-items'), 'class' => 'btn btn-success', 'id' => 'returnform-show-box-items-bt', 'style' => 'margin-left:-10px;']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Содержимое заказа'), ['data-url' => Url::toRoute('show-order-items'), 'class' => 'btn btn-success', 'id' => 'returnform-show-order-items-bt', 'style' => 'margin-left:10px;']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Clear Box'), ['data-url' => Url::toRoute('empty-box'), 'class' => 'btn btn-warning pull-right', 'id' => 'returnform-empty-box-bt', 'style' => 'margin-left:10px;']) ?>
</div>

<div id="error-container">
    <div id="error-base-line"></div>
    <?= Alert::widget([
        'options' => [
            'id' => 'error-list',
            'class' => 'alert-danger hidden',
        ],
        'body' => '',
    ]);
    ?>
</div>
<div id="show-items" class="table-responsive"></div>
