<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 08.01.15
 * Time: 7:02
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */

\stockDepartment\modules\wms\assets\carParts\main\ScanInboundFormAsset::register($this);

?>
<?php $this->title = "Обработка приходной накладной для ГРУЗОВЫХ ХЮНДАЙ"; ?>
<h1><?= $this->title ?></h1>

<div class="scan-inbound-form">
    <?php $form = ActiveForm::begin([
            'id' => 'scaninboundform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
//            'options' => [
//                'data-printType' => \Yii::$app->params['printType']
//            ]
        ]
    ); ?>
    <?= Html::tag('span',
        Yii::t('inbound/buttons', 'Accept').'<span id="inbound-close-message-process"> </span>',
        ['class' => 'btn btn-danger pull-right',
            'data-url' => Url::toRoute('close-order'),
            'style' => 'margin-top:-42px; float:right; font-size: 25px;',
            'id' => 'scaninboundform-inbound-close-bt'])
    ?>

    <?= $form->field($inboundForm, 'orderNumberId',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="font-size: 42px;" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        $newAndInProcessOrders,
        ['prompt' => '',
            'class' => 'form-control ext-large-input',
            'data-url' => Url::to('select-order-number')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'transportedBoxBarcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="font-size: 42px; background-color: lightblue;"" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-unit" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'В таре') . ': </span></div>',
        ]
    ])->textInput(
        [
            'class' => 'form-control ext-large-input',
            'data-url' => Url::to('scan-transported-box-barcode')
        ]
    ); ?>

    <!--    --><?//= $form->field($inboundForm, 'productModel', [
    //        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
    //        'parts' => [
    //            '{input-group-begin}' => '<div class="input-group">',
    //            '{input-group-end}' => '</div>',
    //            '{counter}' => '<div class="input-group-addon" style="font-size: 42px; background-color: greenyellow;" >' . Yii::t('inbound/titles', 'Всего артирулов в накладной') . ': <strong id="count-product-model-in-order">0</strong></div>',
    //        ]
    //    ])->textInput(
    //        [
    //            'class' => 'form-control ext-large-input',
    //            'data-url' => Url::to('scan-model-barcode')
    //        ]
    //    ); ?>

    <?= $form->field($inboundForm, 'productBarcode',
        [
            'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="font-size: 42px; background-color: greenyellow;" >' . Yii::t('inbound/titles', 'Кол-во шк в накладной') . ': <strong id="count-product-model-in-order">0</strong></div>',
            ]
        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::to('scan-product-barcode'),
        'data-clean-url' => Url::to('clean-transported-box')
    ]); ?>

    <?= $form->field($inboundForm, 'productQty')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::to('add-product-qty'),
    ]); ?>

    <?= $form->field($inboundForm, 'conditionType')->dropDownList($conditionTypeArray,[
        'class' => 'form-control input-lg',
//        'data-url' => Url::to('add-product-qty'),
    ]); ?>

    <?php ActiveForm::end(); ?>

    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-diff-in-order'), 'class' => 'btn btn-success', 'id' => 'scaninboundform-print-diff-in-order-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Содержимое заказа'), ['data-url' => Url::toRoute('show-order-items'), 'class' => 'btn btn-primary', 'id' => 'scaninboundform-inbound-show-items-bt', 'style' => 'margin-right:10px;']) ?>
    </div>
    <div id="countdown" data-on="0"></div>
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
    <div id="inbound-items" class="table-responsive"></div>
</div>