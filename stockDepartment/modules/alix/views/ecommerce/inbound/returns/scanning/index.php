<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $inboundForm stockDepartment\modules\intermode\controllers\ecommerce\inbound\domain\InboundForm */

$this->title = Yii::t('inbound/titles', 'Обработка возвратов');
?>
<h1><?= Html::encode($this->title) ?></h1>

<div class="order-process-form">
    <?php $form = ActiveForm::begin(
        [
            'id' => 'inbound-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>


<?= Html::activeHiddenInput(
    $inboundForm,
    'client_id',
    ['id' => 'main-form-client-id']
) ?>

    <!-- Номер заказа -->

    <?= $form->field(
        $inboundForm,
        'order_number',
        [
            'template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inbound-form-order-number">' . Yii::t(
                    'inbound/forms',
                    'Order number'
                ) . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t(
                    'inbound/titles',
                    'Products'
                ) . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t(
                            'inbound/titles',
                            'In order'
                        ) . ': </span></div>',
            ]
        ]
    )->dropDownList(
            $partyNumberArray,
            [
                'id' => 'inbound-form-order-number',
                'class' => 'form-control input-lg',
                'data-url' => Url::to(
                    ['/intermode/ecommerce/inbound/returns/scanning/get-scanned-product-by-id']
                ),
            ]
        ); ?>


    <!-- Штрихкод короба -->

    <?= $form->field(
        $inboundForm,
        'box_barcode',
        [
            'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inboundform-box_barcode">' . Yii::t(
                    'inbound/forms',
                    'Box Barcode'
                ) . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t(
                    'inbound/titles',
                    'Products'
                ) . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t(
                            'inbound/titles',
                            'In box'
                        ) . ': </span></div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(
                    ['clear-box']
                ) . '" id="clear-box-bt">' . Yii::t(
                            'inbound/buttons',
                            'Clear Box'
                        ) . '</span></div>'
            ]
        ]
    )->textInput(
            [
                'id' => 'inbound-form-box_barcode',
                'class' => 'form-control input-lg',
                'data-url' => Url::to(
                    '/intermode/ecommerce/inbound/returns/scanning/validate-scanned-box'
                )
            ]
        ); ?>

    <!-- Штрихкод товара -->

    <?= $form->field(
        $inboundForm,
        'product_barcode',
        [
            'labelOptions' => [
                'label' => Yii::t(
                    'inbound/forms',
                    'Product Barcode'
                )
            ],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="inboundform-box_barcode">' . Yii::t(
                    'inbound/forms',
                    'Product Barcode'
                ) . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ></div>'
            ]
        ]
    )->textInput(
            [
                'id' => 'inbound-form-product_barcode',
                'class' => 'form-control input-lg',
                'data-url' => Url::to(
                    '/intermode/ecommerce/inbound/returns/scanning/scan-product-in-box'
                )
            ]
        ); ?>

    <div class="form-group">
        <?= Html::tag(
            'span',
            Yii::t('inbound/buttons', 'List differences'),
            [
                'data-url' => Url::toRoute('print-list-differences'),
                'class' => 'btn btn-success',
                'id' => 'inbound-list-differences-bt'
            ]
        ) ?>

        <?= Html::tag(
            'span',
            Yii::t('inbound/buttons', 'Unallocated box'),
            [
                'data-url' => Url::toRoute('print-unallocated-list'),
                'class' => 'btn btn-primary',
                'id' => 'inbound-unallocated-list-bt',
                'style' => 'margin-left:10px;'
            ]
        ) ?>
    </div>

    <div id="countdown" data-on="0"></div>
    <div id="error-container">
        <div id="error-base-line"></div>
        <?= Alert::widget(
            [
                'options' => [
                    'id' => 'error-list',
                    'class' => 'alert-danger hidden',
                ],
                'body' => '',
            ]
        );
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
            <tbody id="inbound-item-body"><?php echo $items; ?></tbody>
        </table>
    </div>

    <?= Html::tag(
        'span',
        Yii::t(
            'inbound/buttons',
            'Accept'
        ) . '<span id="inbound-messages-process"> </span>',
        [
            'class' => 'btn btn-danger pull-right',
            'data-url' => Url::toRoute('confirm-order'),
            'style' => ' margin-left:10px;',
            'id' => 'inbound-accept-bt'
        ]
    ) ?>

</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>

    <?php ActiveForm::end(); ?>

<script type="application/javascript">
    $(function () {
	    $("#inbound-form-box_barcode").focus().select();
        // Выделение текста при клике
        $('#inbound-form-box_barcode, #inbound-form-product_barcode').on('click', function () {
            $(this).focus().select();
        });

        // Обработка Enter по box_barcode
        $('#inbound-form-box_barcode').on('keyup', function (e) {
            if (e.which == 13) {
                var me = $(this),
                    form = $('#inbound-process-form'),
                    url = $(this).data('url');

                errorBase.setForm(form);
                me.focus().select();

                var clientId = $('#main-form-client-id').val(); 
                var data = 'InboundForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();

                console.log('SCAN BOX — client_id:', clientId);
                console.log('data:', data);

                $.post(url, data, function (result) {
                    if (result.success == 0) {
                        errorBase.eachShow(result.errors);
                        me.focus().select();
                        play();
                    } else {
                        errorBase.hidden();
                        $("#inbound-form-product_barcode").focus().select();
                        $('#count-product-in-box').html(result.countProductInBox);
                    }
                }, 'json');
            }
            e.preventDefault();
        });

        // Обработка Enter по product_barcode
        $('#inbound-form-product_barcode').on('keyup', function (e) {
            if (e.which == 13) {
                var me = $(this),
                    url = $(this).data('url');

                me.focus().select();

                if (me.val() == 'CHANGEBOX') {
                    $('#inbound-form-box_barcode').focus().select();
                    me.val('');
                    return true;
                }

                var form = $('#inbound-process-form');
                var clientId = $('#main-form-client-id').val();
                var data = 'InboundForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();

                console.log('SCAN PRODUCT — client_id:', clientId);
                console.log('data:', data);

                errorBase.setForm(form);

                $.post(url, data, function (result) {
                    if (result.success == 0) {
                        errorBase.eachShow(result.errors);
                        me.focus().select();
                        play();
                    } else {
                        errorBase.hidden();
                        $('#count-product-in-box').html(result.countProductInBox);
                        $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);
                        $('#inbound-item-body').html(result.items);
                        $('#count-products-in-party').html(result.acceptedQtyParty + ' / ' + result.expectedQtyParty);
                    }
                }, 'json');
            }
            e.preventDefault();
        });

        // Кнопка: List differences
        $('#inbound-list-differences-bt').on('click', function () {
            var href = $(this).data('url'),
                printType = $('#inbound-process-form').data('printtype'),
                id = $('#inbound-form-order-number').val();

            if (!id) return;

            if (printType === 'pdf') {
                window.location.href = href + '?inbound_id=' + id;
            } else if (printType === 'html') {
                autoPrintAllocatedListHtml(href + '?inbound_id=' + id, '0', 2500);
            }
        });

        // Кнопка: Unallocated box
        $('#inbound-unallocated-list-bt').on('click', function () {
            var href = $(this).data('url'),
                printType = $('#inbound-process-form').data('printtype'),
                id = $('#inbound-form-order-number').val();

            if (!id) return;

            if (printType === 'pdf') {
                window.location.href = href + '?inbound_id=' + id;
            } else if (printType === 'html') {
                autoPrintAllocatedListHtml(href + '?inbound_id=' + id);
            }
        });

        // Кнопка - Очистить короб
        $('#clear-box-bt').on('click',function() {
            var href = $(this).data('url-value'),
                boxBorderValue = $('#inbound-form-box_barcode').val(),
                form = $('#inbound-process-form');

            console.info(boxBorderValue);

            errorBase.setForm(form);

            if( confirm('Вы действительно хотите очистить короб') ) {
                $.post(href, form.serialize(), function (result) {

                    errorBase.hidden();

                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                    } else {
                        errorBase.hidden();
                        $('#count-product-in-box').html('0');
                        $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

	                    $('#inbound-item-body').html(result.items);
                        $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                    }

                }, 'json').fail(function (xhr, textStatus, errorThrown) {
                });
            }
        });

        // Кнопка Удалить ШК из накладной
        $('#clear-product-in-box-by-one-bt').on('click',function() {
            var href = $(this).data('url-value'),
                boxBorderValue = $('#inbound-form-box_barcode').val(),
                form = $('#inbound-process-form');

            console.info(boxBorderValue);
            errorBase.setForm(form);

            $.post(href, form.serialize(), function (result) {
                errorBase.hidden();

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();

                    console.info(result.countProductInBox);
                    console.info(result.countScannedProductInOrder);

                    $('#count-product-in-box').html(result.countProductInBox);
                    $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
	                $('#inbound-item-body').html(result.items);
                    $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        });

        // Кнопка - Принять
        $('#inbound-accept-bt').on('click',function() {
            var client_idValue = $('#main-form-client-id').val(),
                order_numberValue = $('#inbound-form-order-number').val(),
                messages_processText = $('#inbound-messages-process'),
                form = $('#inbound-process-form'),
                url = $(this).data('url');

            if(confirm('Вы уверены, что хотите закрыть накладную')) {
                console.info(client_idValue);
                console.info(order_numberValue);

                if (client_idValue && order_numberValue) {
                    $(messages_processText).html(' Подождите, идет обработка, не закрывайте браузер или вкладку ...');
                    var data = 'InboundForm[client_id]='+$('#main-form-client-id').val() + "&" + form.serialize();
                    $.post(url,  data).done(function (result) {
                        /* TODO Потом сделать вывод сообщений через bootstrap Modal   */

                        var alertMessage = '';

                        $.each(result.messages, function (key, value) {
                            if ( value.length) {
                                alertMessage += value+'\n';
                            }
                        });

                        alert(alertMessage);

                        $('#inbound-form-product_barcode').val('');
                        $('#inbound-form-box_barcode').val('');
                        $('#count-products-in-order').html('0/0');
                        $('#count-product-in-box').html('0');
                        $('#inbound-item-body').html('');

                        $(messages_processText).html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000,function() {
                            $(messages_processText).html('');
                            window.location.href = '/wms/erenRetail/inbound/index';
                        } );

                        var client_id = $('#main-form-client-id').val(),
                            inbound = $('#inbound-form-order-number'),
                            partyInbound = $('#inbound-form-party-number'),
                            a = {},
                            dataOptions = '';

                        inbound.html('');
                        partyInbound.html('');
                        $('#count-products-in-party').html('0/0');
                    }).fail(function () {
                        console.log("server error");
                    });
                } 
            }
        });

	    var audioCtx;
	    try {
		    // создаем аудио контекст
		    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
	    } catch(e) {
		    alert('Opps.. Your browser do not support audio API');
	    }

	    // переменные для буфера, источника и получателя
	    var buffer, source, destination;
	    // функция для подгрузки файла в буфер
	    var loadSoundFile = function (url) {
		    if(buffer) {
			    return;
		    }

		    // делаем XMLHttpRequest (AJAX) на сервер
		    var xhr = new XMLHttpRequest();
		    xhr.open('GET', url, true);
		    xhr.responseType = 'arraybuffer'; // важно
		    xhr.onload = function (e) {
			    // декодируем бинарный ответ
			    audioCtx.decodeAudioData(this.response,
				    function (decodedArrayBuffer) {
					    // получаем декодированный буфер
					    buffer = decodedArrayBuffer;
					    //play();

				    }, function (e) {
					    console.log('Error decoding file', e);
				    });
		    };
		    xhr.send();
	    };
	    // функция начала воспроизведения
	    var play = function () {
		    // создаем источник
		    source = audioCtx.createBufferSource();
		    // подключаем буфер к источнику
		    source.buffer = buffer;
		    // дефолтный получатель звука
		    destination = audioCtx.destination;
		    // подключаем источник к получателю
		    source.connect(destination);
		    // воспроизводим
		    source.start(0);
	    };
	    loadSoundFile('/sounddrom.mp3');
    });
</script>