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
<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',\common\modules\client\models\Client::CLIENT_ERENRETAIL, $clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
        'name' => 'DatamatrixForm[client_id]',
    ]
); ?>
<h1><?= $this->title ?></h1>

<div class="order-process-form">
    <?php $form = ActiveForm::begin([
            'id' => 'datamatrix-process-form',
            'enableClientValidation' => false,
            'validateOnChange' => false,
            'validateOnSubmit' => false,
            'options' => [
                'data-printType' => \Yii::$app->params['printType']
            ]
        ]
    ); ?>

    <?php echo $form->field($inboundForm, 'client_id', ['labelOptions' => ['label' => false]])->hiddenInput(['id'=>'datamatrix-form-client-id'])?>

    <?= $form->field($inboundForm, 'party_number',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
//                '{label}' => '<label for="datamatrix-form-party-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-party" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order-party">' . Yii::t('inbound/titles', 'In party') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        $partyNumberArray,
        ['prompt' => '',
            'id' => 'datamatrix-form-party-number',
            'class' => 'form-control input-lg',
            'data-url' => '/wms/erenRetail/datamatrix/get-in-process-inbound-orders-by-client-id'
        ]
    ); ?>

    <?= $form->field($inboundForm, 'order_number',
        ['template' => "{label}\n{input-group-begin}{counter}{input}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="datamatrix-form-order-number">' . Yii::t('inbound/forms', 'Order') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-products-in-order" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-order">' . Yii::t('inbound/titles', 'In order') . ': </span></div>',

            ]
        ]
    )->dropDownList(
        [],
        ['prompt' => '',
            'id' => 'datamatrix-form-order-number',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/erenRetail/datamatrix/get-scanned-product-by-id')
        ]
    ); ?>

<!--    --><?//= $form->field($inboundForm, 'box_barcode', [
//        'template' => "{label}\n{input-group-begin}{counter}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
//        'parts' => [
//            '{label}' => '<label for="datamatrixform-box_barcode">' . Yii::t('inbound/forms', 'Box Barcode') . '</label>',
//            '{input-group-begin}' => '<div class="input-group">',
//            '{input-group-end}' => '</div>',
//            '{counter}' => '<div class="input-group-addon" >' . Yii::t('inbound/titles', 'Products') . ': <strong id="count-product-in-box" >0</strong>&nbsp;&nbsp;&nbsp;&nbsp;<span class="in-box">' . Yii::t('inbound/titles', 'In box') . ': </span></div>',
//            '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ><span class="btn btn-success btn-xs" data-url-value="' . Url::toRoute(['clear-box']) . '" id="clear-box-bt">' . Yii::t('inbound/buttons', 'Clear Box') . '</span></div>'
//        ]
//    ])->textInput(
//        [
//            'id' => 'datamatrix-form-box_barcode',
//            'class' => 'form-control input-lg',
//            'data-url' => Url::to('/wms/erenRetail/datamatrix/validate-scanned-box')
//        ]
//    ); ?>

    <?= $form->field($inboundForm, 'product_barcode',
        ['labelOptions' => ['label' => Yii::t('inbound/forms', 'Product Barcode')],
            'template' => "{label}\n{input-group-begin}{input}{button-right}{input-group-end}\n{hint}\n{error}\n",
            'parts' => [
                '{label}' => '<label for="datamatrixform-box_barcode">' . Yii::t('inbound/forms', 'Product Barcode') . '</label>',
                '{input-group-begin}' => '<div class="input-group">',
                '{input-group-end}' => '</div>',
                '{button-right}' => '<div class="input-group-addon" style="background-color: none; border: none; border-radius: none;" ></div>'
            ]
//        ]
		])->textInput([
            'id' => 'datamatrix-form-product_barcode',
            'class' => 'form-control input-lg',
            'data-url' => Url::to('/wms/erenRetail/datamatrix/scan-product-in-box')
        ]); ?>

    <?php ActiveForm::end(); ?>

<!--    <div class="form-group">-->
<!--        --><?//= Html::tag('span', Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>
<!--        --><?//= Html::tag('span', Yii::t('inbound/buttons', 'List differences'), ['data-url' => Url::toRoute('print-list-differences'), 'class' => 'btn btn-success', 'id' => 'inbound-list-differences-bt', 'style' => 'margin-left:10px;']) ?>
<!--        --><?//= Html::tag('span', Yii::t('inbound/buttons', 'Unallocated box'), ['data-url' => Url::toRoute('print-unallocated-list'), 'class' => 'btn btn-primary', 'id' => 'inbound-unallocated-list-bt', 'style' => 'margin-right:10px;']) ?>
<!--    </div>-->
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
    <div id="inbound-items" class="table-responsive" style="display: none">
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
<!--	        --><?//= Html::tag('span', Yii::t('inbound/buttons', 'Accept').'<span id="inbound-messages-process"> </span>', ['class' => 'btn btn-danger pull-right', 'data-url' => Url::toRoute('confirm-order'), 'style' => ' margin-left:10px;', 'id' => 'inbound-accept-bt']) ?>
</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>

<script type="application/javascript">
    $(function(){
	    $(document).ready(function() {
		    $(window).keydown(function(event){
			    if(event.keyCode == 13) {
				    event.preventDefault();
				    return false;
			    }
		    });
	    });
	    $(document).on("beforeSubmit", "#datamatrix-process-form", function () {
		    // send data to actionSave by ajax request.
		    return false; // Cancel form submitting.
	    });

        $('#datamatrix-form-product_barcode').on('click',function(){
            $(this).focus().select();
        });

        $('#datamatrix-form-party-number').on('change',function() {

            var party_id = $(this).val(),
                inbound = $('#datamatrix-form-order-number'),
                dataOptions = '';

            if(party_id) {

                $.post('/wms/erenRetail/datamatrix/get-in-process-inbound-orders-by-party-id', {'party_id': party_id}).done(function (result) {

                    inbound.html('');

                    $.each(result.dataOptions, function (key, value) {
                        dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                    });

                    inbound.append(dataOptions);
                    inbound.focus().select();

                    $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);

                }).fail(function () {
                    console.log("server error");
                });

            } else {
                //inboundManager.hideByOrderAll();
            }
        });


        $('#datamatrix-form-order-number').on('change',function() {
            var inbound_id = $(this).val(),
                url = $(this).data('url');

            if(inbound_id) {

                $.post(url,
                    {'inbound_id': $(this).val()}
                ).done(function (result) {

                        $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
                        $('#inbound-item-body').html(result.items);
/*                        $('#countdown').attr('data-timer', result.cdTimer);
                        if(result.cdTimer != 0){
                            console.info ('-init timer-');
                            initCountdown(result.cdTimer);
                        }*/

                        $('#datamatrix-form-box_barcode').focus().select();

//                        inboundManager().showByOrder();

                    }).fail(function () {
                        console.log("server error");
                    });
            }
        });

        /*
         * Scan box barcode
         * */
        // $("#datamatrix-form-box_barcode").on('keyup',  function (e) {
        //     if (e.which == 13) {
		//
        //         console.info("-datamatrix-form-box_barcode-");
        //         console.info("Value : " + $(this).val());
		//
        //         var me = $(this),
        //             form = $('#datamatrix-process-form'),
        //             url = $(this).data('url');
		//
        //         errorBase.setForm(form);
        //         me.focus().select();
        //         var data = 'DatamatrixForm[client_id]='+$('#main-form-client-id').val() + "&" + form.serialize();
        //         $.post(url, data,function (result) {
        //             if (result.success == 0 ) {
        //                 errorBase.eachShow(result.errors);
        //                 me.focus().select();
        //             } else {
        //                 errorBase.hidden();
        //                 $("#datamatrix-form-product_barcode").focus().select();
        //                 $('#count-product-in-box').html(result.countProductInBox);
        //             }
		//
        //         }, 'json').fail(function (xhr, textStatus, errorThrown) {
		//
        //         });
        //     }
		//
        //     e.preventDefault();
        // });

        /*
         * Scan product barcode in box
         * */
       $("#datamatrix-form-product_barcode").on('keyup', function (e) {
            /*
             * TODO Добавить проверку
             * TODO 1 ? Существует ли этот товар у нас на складе
             * TODO 2 + Существует ли этот товар у этого клиента
             */
            if (e.which == 13) {

                var me = $(this),
//                    countdown = $('#countdown'),
                    url = $(this).data('url');
                console.info("-inbound-process-steps-product-barcode-");
                console.info("Value : " + me.val());

                me.focus().select();

                // if(me.val() == 'CHANGEBOX') {
                //     $('#datamatrix-form-box_barcode').focus().select();
                //     me.val('');
                //     return true;
                // }

/*                if(countdown.attr('data-on') == 0){
                    countdown.attr('data-on', 1);
                    setCountdown(countdown.attr('data-timer'));
                }*/

                var form = $('#datamatrix-process-form');
                var data = 'DatamatrixForm[client_id]='+$('#main-form-client-id').val() + "&" + form.serialize();
                errorBase.setForm(form);

                $.post(url, data,function (result) {
                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                        me.focus().select();
                    } else {
                        errorBase.hidden();
                        $('#count-product-in-box').html(result.countProductInBox);
                        $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);


                        $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                        $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                        $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                        $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                    }
                }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
                });
            }
            e.preventDefault();
        });

        /*
         * Click on List differences
         * */
       $('#inbound-list-differences-bt').on('click',function() {
            var href = $(this).data('url'),
                printType = $('#datamatrix-process-form').data('printtype');
            if(printType == 'pdf'){
                window.location.href = href + '?inbound_id=' + $('#datamatrix-form-order-number').val();
            } else if (printType == 'html'){
                //window.location.href = href + '?inbound_id=' + $('#datamatrix-form-order-number').val();
                autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#datamatrix-form-order-number').val(), '0', 2500);
            }

        });

        /*
         * Click on Unallocated list
         * */
        $('#inbound-unallocated-list-bt').on('click',function() {
            var href = $(this).data('url'),
                printType = $('#datamatrix-process-form').data('printtype');
            if(printType == 'pdf'){
                window.location.href = href + '?inbound_id=' + $('#datamatrix-form-order-number').val();
            } else if (printType == 'html'){
                //window.location.href = href + '?inbound_id=' + $('#datamatrix-form-order-number').val();
                autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#datamatrix-form-order-number').val());
            }

        });

        $('#clear-product-in-box-by-one-bt').on('click',function() {

            var href = $(this).data('url-value'),
                boxBorderValue = $('#datamatrix-form-box_barcode').val(),
                form = $('#datamatrix-process-form');

            console.info(boxBorderValue);
            errorBase.setForm(form);

            $.post(href, form.serialize(), function (result) {

                errorBase.hidden();

                //console.info(result);
                //console.info(result.errors);
                //console.info(result.errors.length);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();

                    console.info(result.countProductInBox);
                    console.info(result.countScannedProductInOrder);
                    console.info('#accepted-qty-'+result.dataScannedProductByBarcode.rowId);
                    console.info('#row-'+result.dataScannedProductByBarcode.rowId);

                    $('#count-product-in-box').html(result.countProductInBox);
                    //$('#count-products-in-order').html(result.countScannedProductInOrder);
                    $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                    $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                    $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                    $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                    $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });

        });


        $('#clear-box-bt').on('click',function() {

            var href = $(this).data('url-value'),
                boxBorderValue = $('#datamatrix-form-box_barcode').val(),
                form = $('#datamatrix-process-form');

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
                        //$('#count-products-in-order').html(result.countScannedProductInOrder);
                        $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                        $.each(result.dataScannedProductByBarcode, function (key, value) {
                            console.info(value);
                            console.info(key);

                            $('#accepted-qty-'+value.rowId).html(value.countValue);
                            $('#row-'+value.rowId).removeClass('alert-danger alert-success');
                            $('#row-'+value.rowId).addClass(value.colorRowClass);


                        });

                        $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                    }

                }, 'json').fail(function (xhr, textStatus, errorThrown) {

                });
            } else {

            }
        });

        $('#inbound-accept-bt').on('click',function() {
            var client_idValue = $('#main-form-client-id').val(),
                order_numberValue = $('#datamatrix-form-order-number').val(),
                messages_processText = $('#inbound-messages-process'),
                form = $('#datamatrix-process-form'),
                url = $(this).data('url');


            if(confirm('Вы уверены, что хотите закрыть накладную')) {

                console.info(client_idValue);
                console.info(order_numberValue);

                if (client_idValue && order_numberValue) {

//                    stopCountdown();

                    $(messages_processText).html(' Подождите, идет обработка, не закрывайте браузер или вкладку ...');
                    var data = 'DatamatrixForm[client_id]='+$('#main-form-client-id').val() + "&" + form.serialize();
                    $.post(url,  data).done(function (result) {
                        /* TODO Потом сделать вывод сообщений через bootstrap Modal   */

                        var alertMessage = '';

                        $.each(result.messages, function (key, value) {
                            if ( value.length) {
                                alertMessage += value+'\n';
                            }
                        });

                        alert(alertMessage);

                        $('#datamatrix-form-product_barcode').val('');
                        $('#datamatrix-form-box_barcode').val('');
                        $('#count-products-in-order').html('0/0');
                        $('#count-product-in-box').html('0');
                        $('#inbound-item-body').html('');

                        $(messages_processText).html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000,function() {
                            $(messages_processText).html('');
                            window.location.href = '/wms/erenRetail/datamatrix/index';
                        } );

                        //$("#datamatrix-form-order-number option:selected").remove();

                        var client_id = $('#main-form-client-id').val(),
                            inbound = $('#datamatrix-form-order-number'),
                            partyInbound = $('#datamatrix-form-party-number'),
                            a = {},
                            dataOptions = '';

                        inbound.html('');
                        partyInbound.html('');
                        $('#count-products-in-party').html('0/0');

                        if(client_id) {

//                                $.post('/inbound/default/get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {
//
//                                    a = inbound;
//                                    if(result.type == 'party-inbound') {
//                                        a = partyInbound;
//                                    }
//
//                                    a.html('');
//
//                                    $.each(result.dataOptions, function (key, value) {
//                                        dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
//                                    });
//
//                                    a.append(dataOptions);
//                                    a.focus().select();
//
//                                }).fail(function () {
//                                    console.log("server error");
//                                });
//                            me.loadParentOrder();

                        } else {
//                            me.hideByOrderAll();
                        }

                    }).fail(function () {
                        console.log("server error");
                    });

                } else {
                    //inboundModel.hideByOrderAll();
                }

            }
        });

    });
</script>