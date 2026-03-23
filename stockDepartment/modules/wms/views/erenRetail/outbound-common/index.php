<?php
use stockDepartment\modules\wms\assets\erenRetail\OutboundAsset;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\Url;
use yii\bootstrap\Modal;
//use stockDepartment\assets\OutboundAsset;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $orderNumberArray common\modules\inbound\models\InboundOrder */
/* @var $clientsArray common\modules\client\models\Client */
/* @var $outboundForm stockDepartment\modules\outbound\models\OutboundPickListForm */
OutboundAsset::register($this);
$this->title = Yii::t('outbound/titles', 'Отгрузочные накладные')
?>
<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',\common\modules\client\models\Client::CLIENT_ERENRETAIL, $clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
        'name' => 'InboundForm[client_id]',
    ]
); ?>
<h1><?= $this->title ?></h1>
<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Outbound print pick list [1]'), ['class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'outbound-print-pick-list-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Begin end picking process [2]'), ['class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'begin-end-picking-list-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Scanning process [3]'), ['class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'scanning-process-bt']) ?>
</span>

<div id="container-outbound-layout" style="margin-top: 50px;">

</div>
<iframe style="display: none" name="frame-print-alloc-list" src="#" width="468" height="468">
</iframe>

<script type="text/javascript">

    function autoPrintAllocatedListHtml(url, orientation, timeout, type)
    {
        if (typeof jsPrintSetup != 'undefined') {
            //по умолчанию используем портретную ориентацию
            if(typeof orientation == 'undefined'){
                orientation = 0;
            }
            //таймаут
            if(typeof timeout == 'undefined'){
                timeout = 1500;
            }
            //тип: a4 или этикетки (a4 or BL)
            if(typeof type == 'undefined'){
                type = 'a4';
            }
            var printers = jsPrintSetup.getPrintersList().split(','), // get a list of installed printers
                A4p = '',
                BLp = '',
                printer = '';
            for (var k in printers) {

                printer = printers[k];

                switch (printer) {
                    case "\\\\192.168.1.2\\Samsung M2070 Series":
                    //case "\\\\192.168.1.6\\Xerox Phaser 3320":
                    case "\\\\192.168.1.6\\Xerox Phaser 3320 (Копия 1)":
                    //case "Microsoft XPS Document Writer":
                    case "Xerox Phaser 3320":
                        A4p = printer;
                        break;
                    case "\\\\192.168.1.4\\TSC TDP-244":
                    //case "Microsoft XPS Document Writer":
                    case "TSC TDP-244":
                        BLp = printer;
                        break;
                }

            }
            if(type == 'a4'){
                jsPrintSetup.setPrinter(A4p);
                jsPrintSetup.setOption('printBGColors', '1');
                jsPrintSetup.setOption('paperData','9'); // A5 - 11 A4 - 9
                jsPrintSetup.setOption('paperSizeType','A4');
            } else if(type == 'BL') {
                jsPrintSetup.setPrinter(BLp);
                jsPrintSetup.setOption('printBGColors', '0');
            }
            jsPrintSetup.clearSilentPrint();
            jsPrintSetup.refreshOptions();
            //jsPrintSetup.setPrinter("Microsoft XPS Document Writer");
            // no print dialogue boxes needed
            jsPrintSetup.setSilentPrint(1); // 0 -show print settings dialog, 1 - not show print settings dialog
            jsPrintSetup.setOption('orientation', orientation); // 1 - kLandscapeOrientation 0 - kPortraitOrientation
            jsPrintSetup.setOption('headerStrLeft', '');
            jsPrintSetup.setOption('headerStrCenter', '');
            jsPrintSetup.setOption('headerStrRight', '');
            jsPrintSetup.setOption('footerStrLeft', '');
            jsPrintSetup.setOption('footerStrCenter', '');
            jsPrintSetup.setOption('footerStrRight', '');
            jsPrintSetup.setOption('marginTop', '0');
            jsPrintSetup.setOption('marginRight', '0');
            jsPrintSetup.setOption('marginBottom', '0');
            jsPrintSetup.setOption('marginLeft', '0');

            window.frames['frame-print-alloc-list'].location.href = url;

            $( window.frames['frame-print-alloc-list'] ).ready(function() {
                setTimeout(function(){
                    jsPrintSetup.printWindow(window.frames['frame-print-alloc-list']);
                    jsPrintSetup.setSilentPrint(0);
                    jsPrintSetup.clearSilentPrint();
                    //jsPrintSetup.refreshOptions();
                },timeout);
            });

        }

        return false;
    }
    /*S: TODO MOVE TO CODE JS FILE */

    /*
     * Set content and show popup
     * @param string Content be should showed
     * */
    function ShowPopupContent(toShow)
    {
        $('#outbound-index-modal').
            modal('show')
            .find('#outbound-index-content')
            .html(toShow);
    }

    /*
     * Set errors in popup
     * @param string Content be should showed
     * */
    function ShowPopupErrors(toShow)
    {
        $('#outbound-index-modal')
            .find('#outbound-index-errors')
            .html(toShow);
    }

    /*
     * Hide and clear content in popup
     * */
    function HideClearPopup()
    {
        $('#outbound-index-modal').modal('hide');
        $('#outbound-index-errors').html('');
        $('#outbound-index-content').html('');
    }

    function initClientId(){
        console.log ('check');

        var client_id = $('#outbound-form-client-id').val(),
            e = $('#outbound-form-parent-order-number'),
            dataOptions = '';

        if(client_id) {

            $.post('outbound-common/get-parent-order-number', {'client_id': client_id}).done(function (result) {

                e.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions += '<option value="' + key + '">' + value + '</option>';
                });

                e.append(dataOptions);
                e.focus().select();

            }).fail(function () {

                console.log("server error");

            });
        }
    }

    /*E: TODO MOVE TO CODE JS FILE */

$(function(){

    var b = $('body');
    /*
     * Start if change main client drop-down
     * */
    b.on('change','#main-form-client-id',function() {

        console.log('change #main-form-client-id');

        var me = $(this),
            url = me.data('url');

        window.location.href = url+'?id='+me.val();
    });

//    b.on('change','#outbound-form-client-id',function() {
//
//        console.info('-change-outbound-form-client-id');
//
//        var client_id = $(this).val(),
//            e = $('#outbound-form-parent-order-number'),
//            dataOptions = '';
//
//        if(client_id) {
//
//            $.post('/outbound/default/get-parent-order-number', {'client_id': client_id}).done(function (result) {
//
//                e.html('');
//
//                $.each(result.dataOptions, function (key, value) {
//                    dataOptions += '<option value="' + key + '">' + value + '</option>';
//                });
//
//                e.append(dataOptions);
//                e.focus().select();
//
//            }).fail(function () {
//
//                console.log("server error");
//
//            });
//        }
//
//    });



    b.on('change','#outbound-form-parent-order-number',function(event){

        var me = $(this),
            clientID = $('#outbound-form-client-id').val(),
            parentOrderNumber = me.val(),
            url = '<?= Url::toRoute('get-sub-order-grid'); ?>';

        url = url +'?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber+'&type=1';//+'&_pjax=#pjax-grid-view-order-item-container';

        console.info('-change-outbound-form-parent-order-number');

        $.get(url).done(function (result) {

            $('#grid-orders-container').html(result);

        }).fail(function () {

            console.log("server error");

        });
    });

    b.on('click','#print-picking-outbound-print-bt',function(){

        var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows'),
                    printType = $('#outbound-process-form').data('printtype');

        console.info(keys);
        console.info(keys.length);

        if(keys.length < 1) {

            ShowPopupContent('Нужно выбрать хотябы одну заявку');

        } else {

            setTimeout(function() {

                var me = $("#outbound-form-parent-order-number"),
                    clientID = $('#outbound-form-client-id').val(),
                    parentOrderNumber = me.val(),
                    url = '<?= Url::toRoute('get-sub-order-grid'); ?>';

                url = url +'?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber+'&type=1';

                $.get(url).done(function (result) {
                    $('#grid-orders-container').html(result);
                }).fail(function () {
                    console.log("server error");
                });
            },2000);
            var href = $(this).data('url-value');
            if(printType == 'pdf'){
                window.location.href =  href + '?ids='+keys.join();
            } else if (printType == 'html'){
               // window.location.href =  href + '?ids='+keys.join();
               autoPrintAllocatedListHtml(href + '?ids='+keys.join(), '0', 2500);
            }

        }
    });

    b.on('click','#outbound-print-pick-list-bt',function() {

        var url = '<?= Url::toRoute('select-and-print-picking-list'); ?>',
            me = $(this);

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url,function(data) {

            me.addClass('focus');

            $('#container-outbound-layout').html(data);
            initClientId();
        });

    });

    b.on('click','#begin-end-picking-list-bt',function() {

        var url = '<?= Url::toRoute('begin-end-picking-handler'); ?>',
            me = $(this);

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url,function(data) {

            me.addClass('focus');

            $('#container-outbound-layout').html(data);
        });

    });

    /*
    * S:
    * Begin end picking list form
    *
    * */

    b.on('submit','#begin-end-pick-list-form', function (e) {
        return false;
    });

    /*
     * Scanning employees barcode OR picking list barcode
     *
     * */
    b.on('keyup',"#beginendpicklistform-barcode_process-OLD-NOT_USED", function (e) {

         if (e.which == 13) {

            console.info("-beginendpicklistform-barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
             form = $('#begin-end-pick-list-form'),
             messagesListElm = $('#messages-list');
             messagesListBodyElm = $('#messages-list-body');

            errorBase.setForm(form);
            me.focus().select();

            messagesListElm.addClass('hidden');
            messagesListElm.removeClass('alert-info alert-success');
            messagesListBodyElm.html('');

            $.post('/outbound/default/begin-end-picking-handler', form.serialize(),function (result) {

                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if(result.messagesInfo.length >= 1) {
                        messagesListBodyElm.html(result.messagesInfo);
                        messagesListElm.addClass('alert-info');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val(result.picking_list_barcode);
                        $('#beginendpicklistform-employee_barcode').val(result.employee_barcode);
                        $('#beginendpicklistform-picking_list_id').val(result.picking_list_id);
                        $('#beginendpicklistform-employee_id').val(result.employee_id);
                    }

                    if(result.messagesSuccess.length >= 1) {
                        messagesListBodyElm.html(result.messagesSuccess);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val('');
                        $('#beginendpicklistform-employee_barcode').val('');
                        $('#beginendpicklistform-picking_list_id').val('');
                        $('#beginendpicklistform-employee_id').val('');
                    }
                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });


    b.on('keyup',"#beginendpicklistform-picking_list_barcode, #beginendpicklistform-employee_barcode", function (e) {

        if (e.which == 13) {

            console.info("-beginendpicklistform-picking_list_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#begin-end-pick-list-form'),
                messagesListElm = $('#messages-list');
                messagesListBodyElm = $('#messages-list-body');

            errorBase.setForm(form);
            me.focus().select();

            messagesListElm.addClass('hidden');
            messagesListElm.removeClass('alert-info alert-success');
            messagesListBodyElm.html('');

            $.post('/outbound/default/begin-end-picking-handler', form.serialize(),function (result) {

                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if(result.messagesInfo.length >= 1) {
                        messagesListBodyElm.html(result.messagesInfo);
                        messagesListElm.addClass('alert-info');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val(result.picking_list_barcode);
                        $('#beginendpicklistform-employee_barcode').val(result.employee_barcode);
                    }

                    if(result.messagesSuccess.length >= 1) {
                        messagesListBodyElm.html(result.messagesSuccess);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val('');
                        $('#beginendpicklistform-employee_barcode').val('');
                    }

                    if(me.attr('id')=='beginendpicklistform-employee_barcode') {
                        $('#beginendpicklistform-picking_list_barcode').focus().select();
                    } else {
                        $('#beginendpicklistform-employee_barcode').focus().select();
                    }

                    if(result.step == 'end') {
                        $('#beginendpicklistform-picking_list_barcode').val('');
                        $('#beginendpicklistform-employee_barcode').val('');
                        $('#beginendpicklistform-picking_list_barcode').focus().select();
                    }

                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });


    b.on('keyup',"#beginendpicklistform-employee_barcode_NOT_USED", function (e) {

        if (e.which == 13) {

            console.info("-beginendpicklistform-barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#begin-end-pick-list-form'),
                messagesListElm = $('#messages-list');
            messagesListBodyElm = $('#messages-list-body');

            errorBase.setForm(form);
            me.focus().select();

            messagesListElm.addClass('hidden');
            messagesListElm.removeClass('alert-info alert-success');
            messagesListBodyElm.html('');

            $.post('/outbound/default/begin-end-picking-handler', form.serialize(),function (result) {

                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if(result.messagesInfo.length >= 1) {
                        messagesListBodyElm.html(result.messagesInfo);
                        messagesListElm.addClass('alert-info');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val(result.picking_list_barcode);
                        $('#beginendpicklistform-employee_barcode').val(result.employee_barcode);
                        $('#beginendpicklistform-picking_list_id').val(result.picking_list_id);
                        $('#beginendpicklistform-employee_id').val(result.employee_id);
                    }

                    if(result.messagesSuccess.length >= 1) {
                        messagesListBodyElm.html(result.messagesSuccess);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val('');
                        $('#beginendpicklistform-employee_barcode').val('');
                        $('#beginendpicklistform-picking_list_id').val('');
                        $('#beginendpicklistform-employee_id').val('');
                    }
                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });



    b.on('change','#grid-view-order-items input[type="checkbox"]',function() {

        var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows');

        console.info(keys);

        console.info('-change->checkbox');
        var sum = 0;
        $.each(keys, function (key, value) {
            sum +=  parseInt($('#allocated-qty-cell-'+value).text());
        });
        console.info(sum);
        $('#sum-order').text(keys.length);
        $('#sum-reserved').text(sum);

    });

    /*
     * E:
     * Begin end picking list form
     *
     * */



    /*
     * S:
     * Scanning process form
     *
     * */
    b.on('submit','#scanning-process-form', function (e) {
        return false;
    });

    b.on('click','#scanning-process-bt',function() {

        var url = '<?= Url::toRoute('scanning-form'); ?>',
            me = $(this);

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url,function(data) {

            me.addClass('focus');

            $('#container-outbound-layout').html(data);
        });
    });

    /*
     * Scanning employees barcode OR picking list barcode
     *
     * */

    b.on('click',"#scanningform-product_barcode,#scanningform-box_barcode", function (e) {
        $(this).focus().select();
    });

     // EMPLOYEE BARCODE
    b.on('keyup',"#scanningform-employee_barcode", function (e) {

        if (e.which == 13) {

//            console.info("-scanningform-employee_barcode-");
//            console.info("Value : " + $(this).val());

            var url = '<?= Url::toRoute('employee-barcode-scanning-handler'); ?>',
                me = $(this),
                form = $('#scanning-form');

            errorBase.setForm(form);

            $.post(url, form.serialize(),function (result) {

//                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

//                    console.info('OK->OK-> scanning-form-employee-barcode');

                    $('#scanningform-picking_list_barcode').focus().select();
                    $('#scanningform-picking_list_barcode').val('');
                    $('#scanningform-picking_list_barcode_scanned').val('');
                    $('#scanningform-box_barcode').val('');
                    $('#scanningform-product_barcode').val('');

                    $('#alert-picking-list').html('');
                    $('#order-exp-accept').html('0/0');
                    $('#count-product-in-box').html('0');
                    $('#outbound-item-body').html('');

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // PICKING LIST BARCODE
    b.on('keyup',"#scanningform-picking_list_barcode", function (e) {

        var me = $(this);

        if (e.which == 13) {

//            console.info("-scanning-form-picking-list-barcode-");
//            console.info("Value : " + me.val());



            var url = '<?= Url::toRoute('picking-list-barcode-scanning-handler'); ?>',
                form = $('#scanning-form');

            errorBase.setForm(form);

            if($(this).val() == 'CHANGEBOX') {
                me.val('');
                $('#scanningform-box_barcode').focus().select();
                errorBase.hidden();
                e.preventDefault();
                return false;
            }

            $.post(url, form.serialize(),function (result) {

//                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

//                    console.info('OK->OK-> scanning-form-picking-list-barcode');

                    if($('#'+me.val()).html() == undefined ) {
                        var newElem = $('#messages-scanning-list').clone(false);
                        newElem.attr('id', me.val());
                        newElem.append(me.val());
                        newElem.removeClass('hidden');
//                        $(newElem).insertAfter('#alert-picking-list');
                        $(newElem).appendTo('#alert-picking-list');
                    }

                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#scanningform-picking_list_barcode').focus().select();
                    $('#scanningform-picking_list_barcode_scanned').val(result.plIds);
                    $('#scanningform-box_barcode').val('');
                    $('#scanningform-product_barcode').val('');
                    $('#count-product-in-box').html('0');
                    $('#order-exp-accept').html(result.exp_qty+' / '+result.accept_qty);

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // BOX BARCODE
    b.on('keyup',"#scanningform-box_barcode", function (e) {

        if (e.which == 13) {

//            console.info("-scanning-form-box-barcode-");
//            console.info("Value : " + $(this).val());

            var url = '<?= Url::toRoute('box-barcode-scanning-handler'); ?>',
                me = $(this),
                form = $('#scanning-form');

            errorBase.setForm(form);

            $.post(url, form.serialize(),function (result) {

//                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

//                    console.info('OK->OK-> scanning-form-box-barcode');

                    $('#scanningform-product_barcode').focus().select();
                    $('#scanningform-product_barcode').val('');
                    $('#count-product-in-box').html(result.countInBox);
                    $('#outbound-item-body').html(result.stockArrayByPL);

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // PRODUCT BARCODE
    b.on('keyup',"#scanningform-product_barcode", function (e) {

        var  me = $(this);

        if (e.which == 13) {

//            console.info("-scanning-form-product-barcode-");
//            console.info("Value : " + $(this).val());

            if($(this).val() == 'CHANGEBOX') {
                var contentHref = '/wms/koton/outbound-common/printing-box-content',
                    boxBarcode = $('#scanningform-box_barcode'),
                    pickingList = $('#scanningform-picking_list_barcode_scanned'),
                    printType = $('#scanning-form').data('printtype');
                boxBarcode.focus().select();

                if(printType == 'pdf'){
                    window.location.href = contentHref + '?box_barcode=' + boxBarcode.val()+'&picking_list='+pickingList.val();
                } else if (printType == 'html'){
                    autoPrintAllocatedListHtml(contentHref + '?box_barcode=' + boxBarcode.val()+'&picking_list='+pickingList.val());
                    //window.location.href = contentHref + '?box_barcode=' + boxBarcode.val()+'&picking_list='+pickingList.val();
                }

                e.preventDefault();
                return false;
            }

            var url = '<?= Url::toRoute('product-barcode-scanning-handler'); ?>',
                form = $('#scanning-form');

            errorBase.setForm(form);
            me.focus().select();

            $.post(url, form.serialize(),function (result) {

//                console.info(result);

                if(result.change_box == 'ok') {
                    me.val('');
                    $('#scanningform-box_barcode').focus().select();
                    e.preventDefault();
                    return false;
                }


                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

//                    console.info('OK->OK-> scanning-form-product-barcode');

                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#count-product-in-box').html(result.countInBox);
                    $('#order-exp-accept').html(result.exp_qty+' / '+result.accept_qty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();
    });

    b.on('click','#clear-box-scanning-outbound-bt',function() {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#scanningform-box_barcode').val(),
            form = $('#scanning-form');

//        console.info(boxBorderValue);

        errorBase.setForm(form);

        if( confirm('Вы действительно хотите очистить короб') ) {

            $.post(href, form.serialize(), function (result) {

                errorBase.hidden();

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#count-product-in-box').html('0');
                    $('#order-exp-accept').html(result.exp_qty+' / '+result.accept_qty);
                } else {
                    errorBase.hidden();
                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#count-product-in-box').html('0');
                    $('#order-exp-accept').html(result.exp_qty+' / '+result.accept_qty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        } else {

        }
    });

    b.on('click','#clear-product-in-box-by-one-scanning-outbound-bt',function() {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#scanningform-box_barcode').val(),
            form = $('#scanning-form');

//        console.info(boxBorderValue);

        errorBase.setForm(form);

        $.post(href, form.serialize(), function (result) {

            errorBase.hidden();


            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();

                $('#count-product-in-box').html(result.countInBox);
                $('#outbound-item-body').html(result.stockArrayByPL);
                $('#order-exp-accept').html(result.exp_qty+' / '+result.accept_qty);

            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    /*
     * Click on List differences
     * */
    b.on('click','#scanning-form-differences-list-bt',function() {
        var href = $(this).data('url'),
            printType = $('#scanning-form').data('printtype');

        if(printType == 'pdf'){
            window.location.href = href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val();
        } else if (printType == 'html'){
            //window.location.href = href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val();
            autoPrintAllocatedListHtml(href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val());
        }

    });

    b.on('click','#scanning-form-print-box-label-bt',function() {

        if(confirm('Вы действительно хотите распечатать этикетки')) {

            var plids = $('#scanningform-picking_list_barcode_scanned').val(),
                printType = $('#scanning-form').data('printtype');

            $('#scanningform-employee_barcode').val('');
            $('#alert-picking-list').html('');
            $('#scanningform-picking_list_barcode').val('');
            $('#count-product-in-box').html('');
            $('#scanningform-box_barcode').val('');
            $('#scanningform-product_barcode').val('');
            $('#scanningform-picking_list_barcode_scanned').val('');
            $('#outbound-item-body').html('');
            $('#order-exp-accept').html('0/0');

            var href = $(this).data('url');
            if(printType == 'pdf'){
                window.location.href = href + '?plids=' + plids ;
            } else if (printType == 'html'){
                window.location.href = href + '?plids=' + plids ;
                //autoPrintAllocatedListHtml(href + '?plids=' + plids, '0', 1500, 'BL');
            }

        }

    });

    /*
     * E:
     * Scanning process form
     *
     * */

    /*
     * Start:
     * Upload outbound order for DeFacto API
     *
     * */
    b.on('click','#download-outbound-order-api-bt',function() {

        var url = '<?= Url::toRoute('download-file-de-facto-api'); ?>',
            me = $(this);

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url,function(data) {

            me.addClass('focus');

            $('#container-outbound-layout').html(data);
        });

    });

    b.on('change','#download-confirm-for-api-client-id',function() {

        console.info('-download-confirm-for-api-client-id');

        var client_id = $(this).val(),
            e = $('#download-confirm-for-api-order-number'),
            dataOptions = '';

        if(client_id) {

            /*$.post('/outbound/default/get-parent-order-number-in-process', {'client_id': client_id}).done(function (result) {*/
            $.post('/outbound/default/get-parent-order-number', {'client_id': client_id}).done(function (result) {

                e.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions += '<option value="' + key + '">' + value + '</option>';
                });

                e.append(dataOptions);
                e.focus().select();

            }).fail(function () {

                console.log("server error");

            });
        }

    });

    b.on('change','#download-confirm-for-api-order-number',function(event){
        var me = $(this),
            clientID = $('#download-confirm-for-api-form').val(),
            parentOrderNumber = me.val(),
            url = '<?= Url::toRoute('get-sub-order-grid'); ?>';

        if(parentOrderNumber == '') {
            $('#download-confirm-for-api-grid-orders-container').html('');
            return false;
        }

        url = url +'?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber+'&type=2';//+'&_pjax=#pjax-grid-view-order-item-container';

        console.info('-change-outbound-form-parent-order-number');

        $.get(url).done(function (result) {

            $('#download-confirm-for-api-grid-orders-container').html(result);

        }).fail(function () {

            console.log("server error");

        });
    });

    b.on('click','#download-confirm-outbound-print-bt',function(){

            var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows'),
                href = $(this).data('url-value');

            setTimeout(

                function() {

                    var me = $("#download-confirm-for-api-order-number"),
                        clientID = $('#download-confirm-for-api-form').val(),
                        parentOrderNumber = me.val(),
                        url = '<?= Url::toRoute('get-sub-order-grid'); ?>';

                    if(parentOrderNumber == '') {
                        $('#download-confirm-for-api-grid-orders-container').html('');
                        return false;
                    }

                    url = url +'?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber+'&type=2';//+'&_pjax=#pjax-grid-view-order-item-container';

                    console.info('-change-outbound-form-parent-order-number');

                    $.get(url).done(function (result) {

                        $('#download-confirm-for-api-grid-orders-container').html(result);

                    }).fail(function () {

                        console.log("server error");

                    });

                },2000
            );

            window.location.href = href + '?ids='+keys.join();

//        }
    });

    /////////////////////////////
    b.on('click','#upload-outbound-order-api-bt',function() {

        var url = '<?= Url::toRoute('upload-file-de-facto-api'); ?>',
            me = $(this);

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url,function(data) {

            me.addClass('focus');

            $('#container-outbound-layout').html(data);
        });

    });

     /*
     * End:
     * Upload outbound order for DeFacto API
     *
     * */

    /*
    * Start%
    * Compete
    *
    * */
    b.on('click','.outbound-order-complete-bt',function() {

        if( confirm('Вы действительно хотите ') ) {

            var me = $(this),
                url = me.data('url');

            $.post(url, function (data) {

                setTimeout(
                    function() {

                        var me = $("#download-confirm-for-api-order-number"),
                            clientID = $('#download-confirm-for-api-form').val(),
                            parentOrderNumber = me.val(),
                            url = '<?= Url::toRoute('get-sub-order-grid'); ?>';

                        if(parentOrderNumber == '') {
                            $('#download-confirm-for-api-grid-orders-container').html('');
                            return false;
                        }

                        url = url +'?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber+'&type=2';//+'&_pjax=#pjax-grid-view-order-item-container';

                        console.info('-change-outbound-form-parent-order-number');

                        $.get(url).done(function (result) {
                            $('#download-confirm-for-api-grid-orders-container').html(result);
                        }).fail(function () {
                            console.log("server error");
                        });

                    },1000
                );

            });
        }
    });

    /*
     * End
     * Compete
     *
     * */


/*
     * Click print box kg list
     * */
    b.on('click', '#scanning-form-print-box-kg-bt', function () {

        console.info("-scanning-form-print-box-kg-bt-");

        var me = $(this),
            url = me.data('validate-url'),
            href = me.data('redirect-url'),
            form = $('#scanning-form');

        errorBase.setForm(form);
        me.focus().select();

        $.post(url, form.serialize(), function (result) {

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
            } else {
                errorBase.hidden();
                window.location.href = href + '?plids=' + result.plids;
            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

        return false;
    });

  });

</script>