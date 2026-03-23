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
use kartik\select2\Select2;
use stockDepartment\modules\wms\assets\MacCoffeeAsset;

MacCoffeeAsset::register($this);

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */
?>

<?php $this->title = Yii::t('inbound/titles', 'Inbound Orders'); ?>
<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList('client_id',$inboundForm->client_id, $clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
        'name' => 'InboundForm[client_id]',
    ]
); ?>
<h1><?= $this->title ?></h1>
<?= Html::tag('span', Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger btn-lg pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>
<div class="order-process-form">
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

<table width="100%" >
    <tr>
        <td colspan="2">
            <?php echo $form->field($inboundForm, 'client_id', ['labelOptions' => ['label' => false]])->hiddenInput(['id'=>'inbound-form-client-id'])?>
            <?= $form->field($inboundForm, 'party_number',
                ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
                    'parts' => [
        //                '{label}' => '<label for="inbound-form-party-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                        '{input-group-begin}' => '<div class="input-group">',
                        '{input-group-end}' => '</div>',
                        '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'BOXES') . ': <strong id="count-box-in-party" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order-party">' . Yii::t('inbound/titles', 'In party') . ': </span></div>',

                    ]
                ]
            )->dropDownList(
                $partyNumberArray,
                ['prompt' => '',
//                    'id' => 'inbound-form-party-number',
                    'class' => 'form-control input-lg',
                    'data-url' =>  Url::to('show-sub-orders-by-party-id')
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
    <?= $form->field($inboundForm, 'order_number',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inbound-form-order-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'BOXES') . ': <strong id="count-box-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',
            ]
        ]
    )->dropDownList(
        [],
        ['prompt' => '',
//            'id' => 'inbound-form-order-number',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('show-order-items-by-sub-order-id')
        ]
    ); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
    <?= $form->field($inboundForm, 'pallet_barcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
//            '{label}' => '<label for="inboundform-pallet_barcode">' . Yii::t('inbound/forms', 'Pallet') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'BOXES') . ': <strong id="count-box-in-pallet" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="on-pallet">' . Yii::t('inbound/titles', 'ON_PALLET') . ': </span></div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url="' . Url::toRoute(['clear-pallet-barcode']) . '" id="clear-pallet-barcode-bt">' . Yii::t('inbound/buttons', 'CLEAR_PALLET') . '</span></div>'
        ]
    ])->textInput(
        [
//            'id' => 'inbound-form-pallet',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('validate-pallet')
        ]
    ); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <?= $form->field($inboundForm, 'box_barcode', [
//                'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
//                'parts' => [
//                    '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
//                    '{input-group-begin}' => '<div class="input-group">',
//                    '{input-group-end}' => '</div>',
//                    '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
//                    '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>'
//                ]
            ])->textInput(
                [
//                    'id' => 'inbound-form-box_barcode',
                    'class' => 'form-control input-lg',
                    'data-url' => Url::to('validate-box-barcode')
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td width="50%">
    <?= $form->field($inboundForm, 'product_barcode',
        ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Barcode')],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
//                '{label}' => '<label for="inboundform-product_barcode">' . Yii::t('inbound/forms', 'Product Barcode') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url="' . Url::toRoute(['link-product-barcode-to-box']) . '" id="link-product-to-box-barcode-bt">' . Yii::t('inbound/buttons', 'Связать') . '</span>&nbsp;&nbsp;<span class="btn btn-danger btn-xs" data-url="' . Url::toRoute(['unlink-product-barcode-to-box']) . '" id="unlink-product-to-box-barcode-bt">' . Yii::t('inbound/buttons', 'Разделить') . '</span></div>'
            ]
//        ]
		])->textInput([
//            'id' => 'inbound-form-product_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('validate-product-barcode')
        ]); ?>
        </td>
        <td>
    <?= $form->field($inboundForm, 'product_name',
        ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Name')],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
//                '{label}' => '<label for="inboundform-product_name">' . Yii::t('inbound/forms', 'PRODUCT_NAME') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url="' . Url::toRoute(['link-product-name-to-box']) . '" id="link-product-to-box-name-bt">' . Yii::t('inbound/buttons', 'Связать') . '</span>&nbsp;&nbsp;<span class="btn btn-danger btn-xs" data-url="' . Url::toRoute(['unlink-product-name-to-box']) . '" id="unlink-product-to-box-name-bt">' . Yii::t('inbound/buttons', 'Разделить') . '</span></div>'
            ]
//        ]
		])->widget(Select2::className(),
        [
            'data' => \common\modules\client\models\Client::getActiveTMSItems(),
            'options' => [
                'placeholder' => Yii::t('transportLogistics/forms', 'Select product name'),
                'data-url' => Url::to('validate-product-name'),
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]
    )
?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <?= $form->field($inboundForm, 'qty_box_on_pallet', [
//                'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
//                'parts' => [
//                    '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
//                    '{input-group-begin}' => '<div class="input-group">',
//                    '{input-group-end}' => '</div>',
//                    '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
//                    '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>'
//                ]
            ])->textInput(
                [
//                    'id' => 'inbound-form-qty_box_on_pallet',
                    'class' => 'form-control input-lg',
                    'data-url' => Url::to('save-qty-box-on-pallet'),
                    'data-print-url' => Url::to('print-pallet-barcode')
                ]
            ); ?>
        </td>
    </tr>
</table>

    <?php ActiveForm::end(); ?>
    <div class="form-group">
<!--        --><?php //= Html::tag('span', Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>
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
    <div id="inbound-items" class="table-responsive">
        <table class="table">
            <tr>
                <th><?= Yii::t('inbound/forms', 'Product Barcode'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Product Model'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Expected Qty'); ?></th>
                <th><?= Yii::t('inbound/forms', 'Accepted Qty'); ?></th>
            </tr>
            <tbody id="inbound-item-body"></tbody>
        </table>
    </div>
</div>