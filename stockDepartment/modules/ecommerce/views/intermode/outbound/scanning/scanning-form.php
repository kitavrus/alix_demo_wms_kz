<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\app\modules\ecommerce\assets\intermode\ScanOutboundFormAsset::register($this);
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
    <div style = " float:left; font-size: 25px;">Отгрузка INTERMODE Ecommerce </div>

<?= Html::tag('div',
Yii::t('outbound/buttons', 'Упакован'),
[
	'data-url' => Url::toRoute('package'),
	'data-validate-url' => Url::toRoute('validate-print-box-label'),
	'class' => 'btn btn-danger',
	'id' => 'outboundform-package-for-order-bt',
	'style' => 'margin-top:-42px; float:right; font-size: 25px; margin: 0px 5px 0px 5px'
]) ?>

<?//= Html::tag('div',
//    Yii::t('outbound/buttons', 'Print box label'),
//    [
//        'data-url' => Url::toRoute('print-box-label'),
//        'data-validate-url' => Url::toRoute('validate-print-box-label'),
//        'class' => 'btn btn-danger',
//        'id' => 'outboundform-print-box-label-for-order-bt',
//        'style' => 'margin-top:-42px; float:right; font-size: 25px; margin: 0px 5px 0px 5px'
//    ]) ?>

<?//= Html::tag('a',
//    Yii::t('outbound/buttons', 'Этикетка на упаковку'),
//    [
//        'href' => '#',
//        'class' => 'btn btn-warning',
//        'target' => '_blank',
//        'id' => 'outboundform-print-cargo-label',
//        'style' => ' float:right; font-size: 25px; display:none; margin: 0px 5px 0px 5px'
//    ]) ?>
<!---->
<?//= Html::tag('a',
//    Yii::t('outbound/buttons', 'Вложение в заказ А4'),
//    [
//        'href' => '#',
//        'class' => 'btn btn-info',
//        'target' => '_blank',
//        'id' => 'outboundform-print-waybill-document',
//        'style' => ' float:right; font-size: 25px; display:none; margin: 0px 5px 0px 5px'
//    ]) ?>

    </h1>
<br />
<br />
<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'outboundform',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'employee_barcode')->textInput([
        'class' => 'form-control input-lg',
        'data-url' => Url::toRoute('employee-barcode-handler')
    ]); ?>

    <?= $form->field($model, 'pick_list_barcode'
        ,
        ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="pick-list-barcode-qty">0</div>'
            ]
        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('pick-list-barcode-handler')
    ])
    ?>

    <?= $form->field($model, 'package_barcode'
        ,
        ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="package-barcode-qty">0/0</div>'
            ]
        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('package-barcode')
    ])
    ?>

    <?= $form->field($model, 'product_barcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('product-barcode-handler')
    ])->label(Yii::t('outbound/forms', 'Product Barcode')) ?>

	<?= $form->field($model, 'product_qrcode'
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('product-qrcode-handler')
    ])->label(Yii::t('outbound/forms', 'QR код')) ?>

	<?= $form->field($model, 'stockId')->hiddenInput()->label(false)->error(false); ?>

<!--    --><?php //$model->kg = 1;  ?>
<!--    --><?//= $form->field($model, 'kg'
//    )->textInput([
//        'class' => 'form-control ext-large-input',
////        'data-url' => Url::toRoute('product-barcode-handler')
//    ])->label(Yii::t('outbound/forms', 'Вес заказа')) ?>
<!--    --><?php //$model->packageType = \common\ecommerce\constants\OutboundPackageType::PAKET1;  ?>
<!--    --><?//= $form->field($model, 'packageType')->dropDownList(\common\ecommerce\constants\OutboundPackageType::getAll(),[
////        'data-url' => Url::toRoute('product-barcode-handler')
//    ])->label(Yii::t('outbound/forms', 'Тип упаковки')) ?>

    <?php ActiveForm::end(); ?>

    <div class="row" style="margin: 20px 1px">
<!--        --><?//= Html::tag('span', Yii::t('outbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-diff-list'), 'class' => 'btn btn-success', 'id' => 'outboundform-diff-list-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Содержимое заказа'), ['data-url' => Url::toRoute('show-picking-list-items'), 'class' => 'btn btn-success', 'id' => 'outboundform-show-picking-list-items-bt', 'style' => 'margin-left:10px;']) ?>
        <?= Html::tag('span', Yii::t('outbound/buttons', 'Clear Box'), ['data-url' => Url::toRoute('empty-package'), 'class' => 'btn btn-warning pull-right', 'id' => 'outboundform-clear-box-bt', 'style' => 'margin-left:10px;']) ?>
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
    <div id="show-picking-list-items" class="table-responsive"></div>
</div>

<script type="application/javascript">
	$(function(){
		$('#outboundform-package-for-order-bt').on('click',function() {
			var orderNumberValue = $('#outboundform-pick_list_barcode').val(),
				url = $(this).data('url');
			if(confirm('Вы уверены, что хотите закрыть накладную')) {
				if (orderNumberValue) {
					console.info(url+"?orderNumber="+orderNumberValue)
					window.location.href = url+"?orderNumber="+orderNumberValue;
				}
			}
		});
	});
</script>