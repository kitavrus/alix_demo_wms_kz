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
?>

<?php $this->title = Yii::t('inbound/titles', 'Inbound Orders'); ?>
<div class="order-process-form">

    <?php  echo Html::tag('span',
        Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>',
        [
            'class' => 'btn btn-danger pull-right hidden',
            'data-url' => Url::toRoute('confirm-order'),
            'style' => 'margin-top:-42px; float:right; font-size: 25px; margin-left:10px;',
            'id' => 'inbound-accept-bt'
        ]) ?>

    <?php echo Html::tag('span',
        Yii::t('inbound/buttons', 'Удалить ошибочные').'<span id="inbound-messages-clear-zero-qty"> </span>',
        [
            'class' => 'btn btn-warning pull-right hidden-',
            'data-url' => Url::toRoute('clear-zero-qty'),
            'style' => 'margin-top:-42px; float:right; font-size: 25px;',
            'id' => 'inbound-clear-zero-qty-bt'
        ]) ?>


    <?php $form = ActiveForm::begin([
            'id' => 'inbound-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>
    <div class="btn-group" data-toggle="buttons">
    <?= $form->field($inboundForm, 'withExtraLot')->radioList(
        [
            '0'=>'НЕ ПРИНИМАЕМ ПЛЮСЫ',
            '1'=>'ВНИМАНИЕ!!! принимаем плюсы',
        ],[
//        'class' => 'btn-group',
//        'data-toggle' => 'buttons',
        'item' => function($index,$label,$name,$checked,$value){
        return Html::radio($name,
            $checked,
            [
                'label' => $label,
                'value' => $value,
                'labelOptions' => ['class' => 'btn '.($index ? 'btn-danger' : 'btn-success').' active']
            ]);

    }]); ?>
    </div>
    <?= $form->field($inboundForm, 'client_id', ['labelOptions' => ['label' => false]])->hiddenInput(['id'=>'inbound-form-client-id'])?>

    <?= $form->field($inboundForm, 'party_number'
        ,
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
//                '{label}' => '<label for="inbound-form-party-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-party" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order-party">' . Yii::t('inbound/titles', 'In party') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        [],
        ['prompt' => '',
            'id' => 'inbound-form-party-number',
            'class' => 'form-control input-lg',
            'data-url' => '/wms/defacto/inbound-v2/get-in-process-inbound-orders-by-client-id'
        ]
    ); ?>

    <?= $form->field($inboundForm, 'order_number',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inbound-form-order-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" style="font-size: 33px; font-weight:bold;line-height:33px" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        [],
        ['prompt' => '',
            'id' => 'inbound-form-order-number',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/defacto/inbound-v2/get-scanned-product-by-id')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'box_barcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" style="font-size: 33px; font-weight:bold;line-height:33px" >' . Yii::t('inbound/titles', 'Products') . ': <strong class="label label-danger" style="font-size: 33px; font-weight:bold;line-height:33px; padding: 2px 4px !important;" id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>'
        ]
    ])->textInput(
        [
            'id' => 'inbound-form-box_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/defacto/inbound-v2/validate-scanned-box')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'client_box_barcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="inboundform-client_box_barcode">' . Yii::t('inbound/forms', 'ШК КОРОБА КЛИЕНТА') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon qty-lot-in-client-box-wrapper">Лотов в коробе: <span id="qty-lot-in-client-box-inbound" class="label label-danger qty-lot-in-client-box">0</span></div>'
//            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-client-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
        ]
    ])->textInput(
        [
            'id' => 'inbound-form-client_box_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/defacto/inbound-v2/validate-client-box-barcode')
        ]
    ); ?>

    <?= $form->field($inboundForm, 'product_barcode',
        ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Barcode')],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Product Barcode') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-product-in-box']) . '" id="clear-product-in-box-by-one-bt">' . Yii::t('inbound/buttons', 'Clear product in box') . '</span></div>'
            ]
//        ]
		])->textInput([
            'id' => 'inbound-form-product_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/defacto/inbound-v2/scan-product-in-box')
        ]); ?>

    <?php ActiveForm::end(); ?>


    <div class="form-group">

        <?= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-list-differences'), 'class' => 'btn btn-success', 'id' => 'inbound-list-differences-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Unallocated box'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'inbound-unallocated-list-bt', 'style' => 'margin-right:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Принятые короба'), ['data-url' => Url::toRoute('print-accepted-box'), 'class' => 'btn btn-primary', 'id' => 'inbound-accepted-list-bt', 'style' => 'margin-right:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Кол-во коробав'), ['data-url' => Url::toRoute('qty-accepted-box'), 'class' => 'btn btn-primary', 'id' => 'qty-accepted-box-bt', 'style' => 'margin-right:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Что в заказе'), ['data-url' => Url::toRoute('items-in-order'), 'class' => 'btn btn-primary', 'id' => 'items-in-order-bt', 'style' => 'margin-right:10px;']) ?>
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
    <h1 class="text-center" style="display:none;" id="show-message">123</h1>
    <div id="inbound-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?= Yii::t('inbound/forms', 'Product Barcode'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Product Model'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Короб клиента'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Кол-во в коробе(клиента)'); ?></th>
            </tr>
            <tbody id="inbound-item-body"></tbody>
        </table>
    </div>
</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>