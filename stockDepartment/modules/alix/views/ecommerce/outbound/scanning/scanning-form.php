<?php
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\helpers\Html;

\stockDepartment\modules\alix\assets\ScanOutboundFormAsset::register($this);

// Если ScanningController::actionPackage редиректнул сюда с ?download=<orderNumber>,
// подгружаем Kaspi-накладную в скрытом iframe — браузер скачает PDF, страница не меняется.
$downloadOrderNumber = Yii::$app->request->get('download');

// ?info=ownpickup&orderNumber=… — Kaspi-заказ на самовывоз: PDF этикетки не будет,
// показываем оператору явное info-сообщение, чтобы он не ждал скачивание впустую.
$infoCode        = Yii::$app->request->get('info');
$infoOrderNumber = Yii::$app->request->get('orderNumber');
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
    <div style = " float:left; font-size: 25px;">Отгрузка</div>

<?= Html::tag('div',
Yii::t('outbound/buttons', 'Упакован'),
[
	'data-url' => Url::toRoute('package'),
	'data-validate-url' => Url::toRoute('validate-print-box-label'),
	'class' => 'btn btn-danger',
	'id' => 'outboundform-package-for-order-bt',
	'style' => 'margin-top:-42px; float:right; font-size: 25px; margin: 0px 5px 0px 5px'
]) ?>

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

    <?php ActiveForm::end(); ?>

    <div class="row" style="margin: 20px 1px">
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

<?php if (!empty($downloadOrderNumber)): ?>
    <iframe
        id="kaspi-label-iframe"
        src="<?= Url::toRoute(['/alix/ecommerce/outbound/scanning/kaspi-label', 'orderNumber' => $downloadOrderNumber]) ?>"
        style="display:none;"
        title="Kaspi waybill download"></iframe>
<?php endif; ?>

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

		// Info-плашка для own-pickup Kaspi-заказа (?info=ownpickup&orderNumber=…).
		// Показываем в существующем alert-info #messages-scanning-list, чтобы
		// оператор не ждал PDF этикетки, которого не будет.
		<?php if ($infoCode === 'ownpickup' && !empty($infoOrderNumber)): ?>
		(function() {
			var orderLabel = <?= \yii\helpers\Json::htmlEncode($infoOrderNumber) ?>;
			var $info = $('#messages-scanning-list');
			if (!$info.length) { return; }
			var msg = 'Заказ ' + orderLabel + ': самовывоз, Kaspi-этикетка не печатается. '
				+ 'Закрывайте заказ при выдаче покупателю (двухэтапный COMPLETED — '
				+ 'sendCompletionCode + confirmCompletionWithCode).';
			$('#messages-scanning-list-body').text(msg);
			$info.removeClass('hidden').show();
		})();
		<?php endif; ?>

		// Ошибка из скрытого iframe (actionKaspiLabel ⇒ renderKaspiLabelError):
		// сам iframe её отрендерит, но display:none — оператор не увидит.
		// Поэтому iframe шлёт postMessage, а мы показываем текст в существующем
		// alert-danger #error-list (#error-list body — это .alert внутри).
		window.addEventListener('message', function(event) {
			if (!event || !event.data || event.data.type !== 'kaspi-label-error') {
				return;
			}
			var $alert = $('#error-list');
			if (!$alert.length) {
				alert(event.data.message); // совсем fallback, если view изменили
				return;
			}
			$alert.removeClass('hidden').text(event.data.message).show();
			$('html, body').animate({scrollTop: $alert.offset().top - 20}, 200);
		}, false);
	});
</script>
