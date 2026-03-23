<?php

use stockDepartment\modules\wms\managers\erenRetail\re_print_outbound_datamatrix\ReOutboundDataMatrixFormAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;
ReOutboundDataMatrixFormAsset::register($this);
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

<div class="scanning-form">
<?php $form = ActiveForm::begin([
        'id' => 'scanning-form',
        'enableClientValidation' => false,
        'validateOnChange' => false,
        'validateOnSubmit' => false,
    ]
); ?>
<!--<div class="form-group" style="font-size: 30px;">-->
<!--	<strong ><strong id="scan-count">0</strong> из <strong id="exp-count">0</strong> / Номер заказа: <span id="order_number"></span>-->
<!--</div>-->

	<?= $form->field($model, 'box_barcode', [
    'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
    'parts' => [
        '{label}' => '<label for="scanningform-box_barcode">' . Yii::t('outbound/forms', 'Box Barcode') . '</label>',
        '{input-group-begin}' => '<div class="input-group">',
        '{input-group-end}' => '</div>',
        '{counter}' => '<div class="input-group-addon" style="font-size: 30px;">' . Yii::t('outbound/forms', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('outbound/forms', 'In box') . ': </span></div>',
        '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-scanning-outbound-bt">' . Yii::t('outbound/buttons', 'Clear Box') . '</span></div>'
    ]
])->textInput([
		'class' => 'form-control ext-large-input',
		'data-url' => Url::toRoute('box-barcode-scanning'),
		])->label(Yii::t('outbound/forms', 'Box Barcode')) ?>

	<?= $form->field($model, 'product_barcode')
			 ->textInput([
				 'class' => 'form-control ext-large-input',
				 'data-url' => Url::toRoute('product-barcode-scanning'),
			 ])
			 ->label(Yii::t('outbound/forms', 'Product Barcode')) ?>
<?php ActiveForm::end(); ?>

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
<div id="outbound-items" class="table-responsive">
    <table class="table">
        <tr>
            <th><?= Yii::t('outbound/forms', 'Product Barcode'); ?></th>
            <th><?= Yii::t('outbound/forms', 'Product Model'); ?></th>
            <th><?= Yii::t('outbound/forms', 'Box Barcode'); ?></th>
            <th><?= Yii::t('outbound/forms', 'Qty'); ?></th>
        </tr>
        <tbody id="outbound-item-body"></tbody>
    </table>
</div>