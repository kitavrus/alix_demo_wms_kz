<?php
/**
 * Created by PhpStorm.
 * User: kitavrus
 * Date: 02.02.15
 * Time: 14:58
 */
//use yii\helpers\Html;
//use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\app\modules\ecommerce\assets\defacto\OutboundListFormAsset::register($this);
?>
<h1>Лист отгрузки Defacto Ecommerce</h1>
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

<?= Html::tag('span',
    Yii::t('outbound/buttons', 'Печатаем лист'),
    [
        'data-url' => Url::toRoute('print-document'),
        'data-validate-url' => Url::toRoute('print'),
        'class' => 'btn btn-danger',
        'id' => 'outboundlistform-print-bt',
        'style' => 'margin-top:-42px; float:right; font-size: 25px;'
    ]) ?>

<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'outboundlistform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'title')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('title')
    ]); ?>

    <?= $form->field($model, 'courierCompany')->dropDownList(\common\ecommerce\constants\CourierCompany::getAll(),[
        'prompt' => 'Укажите компанию курьер',
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('courier-company')
    ]); ?>

    <?= $form->field($model, 'orderNumber'
//        ,
//        ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
//            'parts' => [
//                '{input-group-begin}' => '<div class="input-group">',
//                '{input-group-end}' => '</div>',
//                '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="outboundlistform-package-barcode-qty">0</div>'
//            ]
//        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('order-number')
    ])
    ?>

    <?= $form->field($model, 'barcode'
        ,
        ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="outboundlistform-package-barcode-qty">0</div>'
            ]
        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('barcode')
    ])
    ?>

    <?php ActiveForm::end(); ?>
    <div class="row" style="margin: 20px 1px">
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Содержимое листа'), ['data-url' => Url::toRoute('show-order-in-list'), 'class' => 'btn btn-success', 'id' => 'outboundlistform-show-order-in-list-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Упакован но не отсканирован в лист отгрузки'), ['data-url' => Url::toRoute('show-packed-order-but-not-scanned-to-list'), 'class' => 'btn btn-success', 'id' => 'outboundlistform-packed-not-scanned-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Все курьерки'), ['data-url' => Url::toRoute('show-kaspi-orders'), 'class' => 'btn btn-warning', 'id' => 'outboundlistform-show-kaspi-orders-bt', 'style' => 'margin-left:10px;']) ?>
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
    <div id="show-order-in-list-container" class="table-responsive"></div>
</div>