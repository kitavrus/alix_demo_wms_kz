<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\stockDepartment\modules\intermode\controllers\outboundSeparator\scanning\assets\FormAsset::register($this);
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
    <div style = " float:left; font-size: 25px;">Изъятие товаров из отгрузочной накладной</div>
    </h1>
<br />
<br />
<div class="scanning-form">
    <?php $form = ActiveForm::begin([
            'id' => 'outbound_separator_scanning_form_id',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
        ]
    ); ?>

    <?= $form->field($model, 'outbound_separator_id',
		[
			'template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
			'parts' => [
				'{input-group-begin}' => '<div class="input-group">',
				'{input-group-end}' => '</div>',
				'{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="order-info">0/0</div>'
			]
		]
    )->dropDownList($items,[
		'prompt' => '',
        'class' => 'form-control ext-large-input',
	    'data-url' => Url::toRoute('get-info-by-order')
    ]); ?>

    <?= $form->field($model, 'in_box_barcode'
        ,
        ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="in-box-barcode-info">0</div>'
            ]
        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('in-box-barcode')
    ])
    ?>

	<?= $form->field($model, 'out_box_barcode'
        ,
        ['template' => "{label}\n{input-group-begin}{button-right}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="font-size: 62px;" id="out-box-barcode-info">0/0</div>'
            ]
        ]
    )->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('out-box-barcode')
    ])
    ?>

    <?= $form->field($model, 'product_barcode')->textInput([
        'class' => 'form-control ext-large-input',
        'data-url' => Url::toRoute('product-barcode')
    ])->label(Yii::t('outbound/forms', 'Product Barcode')) ?>
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
    <div id="show-picking-list-items" class="table-responsive"></div>
</div>

<!--<script type="application/javascript">-->
<!--	$(function(){-->
<!--		$('#outboundform-package-for-order-bt').on('click',function() {-->
<!--			var orderNumberValue = $('#outboundform-pick_list_barcode').val(),-->
<!--				url = $(this).data('url');-->
<!--			if(confirm('Вы уверены, что хотите закрыть накладную')) {-->
<!--				if (orderNumberValue) {-->
<!--					console.info(url+"?orderNumber="+orderNumberValue)-->
<!--					window.location.href = url+"?orderNumber="+orderNumberValue;-->
<!--				}-->
<!--			}-->
<!--		});-->
<!--	});-->
<!--</script>-->