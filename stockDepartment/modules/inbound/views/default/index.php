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
use stockDepartment\modules\inbound\assets\InboundAsset;
InboundAsset::register($this);
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $inboundForm stockDepartment\modules\inbound\models\InboundForm */
?>

<?php $this->title = Yii::t('inbound/titles', 'Inbound Orders'); ?>
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

    <?= $form->field($inboundForm, 'client_id', ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Client ID')]])->dropDownList(
        $clientsArray,
        ['prompt' => '',
            'id' => 'inbound-form-client-id',
            'class' => 'form-control input-lg',
        ]
    ); ?>

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
        ]
    ); ?>

    <?= $form->field($inboundForm, 'order_number',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inbound-form-order-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        [],
//		$orderNumberArray,
        ['prompt' => '',
            'id' => 'inbound-form-order-number',
            'class' => 'form-control input-lg',
        ]
    ); ?>

    <?= $form->field($inboundForm, 'box_barcode', [
        'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
        'parts' => [
            '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
            '{input-group-begin}' => '<div class="input-group">',
            '{input-group-end}' => '</div>',
            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>'
        ]
    ])->textInput(
        [
            'id' => 'inboundform-box_barcode',
            'class' => 'form-control input-lg',
        ]
    ); ?>

    <?= $form->field($inboundForm, 'product_barcode',
        ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Barcode')],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inboundform-box_barcode">' . Yii::t('inbound/forms', 'Product Barcode') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-product-in-box-by-one']) . '" id="clear-product-in-box-by-one-bt">' . Yii::t('inbound/buttons', 'Clear product in box') . '</span></div>'
            ]
//        ]
		])->textInput([
            'id' => 'inboundform-product_barcode',
            'class' => 'form-control input-lg',
        ]); ?>

    <?php ActiveForm::end(); ?>


    <div class="form-group">
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'style' => ' margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('get-list-differences'), 'class' => 'btn btn-success', 'id' => 'inbound-list-differences-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('inbound/buttons', 'Unallocated box'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'inbound-unallocated-list-bt', 'style' => 'margin-right:10px;']) ?>
    </div>
    <p id="note" style="font-weight: bold; text-align: center" data-on="0">

    </p>
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
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>