<?php
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
//OutboundAsset::register($this);
$this->title = Yii::t('wms/titles', 'Distribution || MACCOFFEE KZ')
?>
<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',$id,$clientsArray, [
        'prompt' => '',
        'id' => 'main-form-client-id',
        'class' => 'form-control input-lg',
        'data'=>['url'=>Url::to('/wms/default/route-form')],
        'readonly' => true,
    ]
); ?>
<div id="container-colins-process-form-layout" style="margin-top: 30px;"></div>
<div id="container-colins-layout" style="margin-top: 30px;"></div>

<span id="buttons-menu">
<!--    --><?//= Html::tag('span', Yii::t('outbound/buttons', 'Print allocation list [1]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/colins/outbound/scanning-box')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'scanning-box-colins-bt']) ?>
<!--    --><?//= Html::tag('span', Yii::t('outbound/buttons', 'TIR scanning process [2]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/colins/outbound/scanning-form')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'scanning-process-colins-bt']) ?>
<!--    --><?//= Html::tag('span', Yii::t('outbound/buttons', 'Inbound [3]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/koton/inbound/inbound-form')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'inbound-process-koton-bt']) ?>
    <?= Html::a(Yii::t('outbound/buttons', 'Inbound [3]'), Url::toRoute('/wms/maccoffeekz/inbound/index'),['class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;']) ?>
    <?= Html::a(Yii::t('outbound/buttons', 'Outbound [4]'), Url::toRoute('/wms/maccoffeekz/outbound-common/index'),['class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;']) ?>
</span>
<div id="container-colins-outbound-layout" style="margin-top: 50px;"></div>
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

    /*E: TODO MOVE TO CODE JS FILE */

    $(function() {

        /*   NEW BEGIN   */

        var b = $('body');




        b.on('click','#scanning-box-colins-bt',function() {

            //console.log('change #scanning-box-colins-bt');

            var me = $(this),
                url = me.data('url');

            //console.log(me.val());

            $.post(url,function(data) {
                $('#container-colins-outbound-layout').html(data);
                $("#allocationlistform-box_barcode").focus().select();
            });

        });

        b.on('click',"#allocationlistform-box_barcode", function (e) {
            $(this).focus().select();
        });

        b.on('keyup',"#allocationlistform-box_barcode", function (e) {

            if (e.which == 13) {
                console.log('keyup #allocationlistform-box_barcode');
                var me = $(this),
                    url = me.data('url'),
                    validateUrl = me.data('validation-url'),
                    meValue = me.val(),
                    form = $('#allocate-list-form'),
                    printType = form.data('printtype');

                errorBase.setForm(form);

                $.post(validateUrl,form.serialize(),function(dataResponse) {
                    if(!$.isEmptyObject(dataResponse)) {
                        errorBase.eachShow(dataResponse);
                    } else {
                        errorBase.hidden();
//                        console.info('before autoPrintAllocatedListHtml');
//                        autoPrintAllocatedListHtml(url + '?AllocationListForm[box_barcode]='+meValue);
                        //window.location.href = url + '?AllocationListForm[box_barcode]='+meValue;
                        if(printType == 'pdf'){
                            window.location.href = url + '?AllocationListForm[box_barcode]='+meValue;
                        } else if (printType == 'html'){
                            autoPrintAllocatedListHtml(url + '?AllocationListForm[box_barcode]='+meValue, '0', 7000);
                        }
                    }
                    me.focus().select();
                },'json');
            }

//        e.preventDefault();

            return false;
        });

        b.on('click','#scanning-process-colins-bt',function() {

            console.log('change #scanning-process-colins-bt');

            var me = $(this),
                url = me.data('url');

            console.log(me.val());

            $.post(url,function(data) {
                $('#container-colins-outbound-layout').html(data);
                $('#scanningcolinsform-employee_barcode').focus();
            });

        });


/*   NEW END   */



        b.on('change','#outbound-form-client-id',function() {

            console.info('-change-outbound-form-client-id');

            var client_id = $(this).val(),
                e = $('#outbound-form-parent-order-number'),
                dataOptions = '';

            if(client_id) {

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

            var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows');

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

                window.location.href = href + '?ids='+keys.join();
            }
        });

        b.on('click','#outbound-print-pick-list-bt',function() {

//        var url = '<?//= Url::toRoute('select-and-print-picking-list'); ?>//',
//            me = $(this);

            var me = $(this),
                url = me.data('url');

            $('#buttons-menu').find('.btn').removeClass('focus');

            $.post(url,function(data) {

                me.addClass('focus');

                $('#container-outbound-layout').html(data);
            });

        });

        b.on('click','#begin-end-picking-list-bt',function() {

//        var url = '<?//= Url::toRoute('begin-end-picking-handler'); ?>//',
//            me = $(this);

            var me = $(this),
                url = me.data('url');

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

//        var url = '<?//= Url::toRoute('scanning-form'); ?>//',
//            me = $(this);

            var me = $(this),
                url = me.data('url');

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

//    // BOX BARCODE
//    b.on('keyup',"#scanningcolinsform-box_barcode", function (e) {
//
//        if (e.which == 13) {
//
////            console.info("-scanning-form-box-barcode-");
////            console.info("Value : " + $(this).val());
//
//            var url = '<?//= Url::toRoute('colins/box-barcode-scanning-handler'); ?>//',
//                me = $(this),
//                form = $('#scanning-form');
//
//            errorBase.setForm(form);
//
//            $.post(url, form.serialize(),function (result) {
//
////                console.info(result);
//
//                if (result.success == 0 ) {
//                    errorBase.eachShow(result.errors);
//                    me.focus().select();
//                } else {
//                    errorBase.hidden();
//
////                    console.info('OK->OK-> scanning-form-box-barcode');
//
//                    $('#scanningcolinsform-product_barcode').focus().select();
//                    $('#scanningcolinsform-product_barcode').val('');
//                    $('#count-product-in-box').html(result.countInBox);
//                    $('#outbound-item-body').html(result.stockArrayByPL);
//
//                }
//            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//            });
//        }
//        e.preventDefault();
//    });

        // PRODUCT BARCODE
        b.on('keyup',"#scanningform-product_barcode", function (e) {

            var  me = $(this);

            if (e.which == 13) {

//            console.info("-scanning-form-product-barcode-");
//            console.info("Value : " + $(this).val());

                if($(this).val() == 'CHANGEBOX_NOT_USED') {
                    me.val('');
                    $('#scanningform-box_barcode').focus().select();
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
                        $('#order-exp-accept').html(result.allocated_qty+' / '+result.accept_qty);
                        /*$('#order-exp-accept').html(result.exp_qty+' / '+result.accept_qty);*/
                    } else {
                        errorBase.hidden();
                        $('#outbound-item-body').html(result.stockArrayByPL);
                        $('#count-product-in-box').html('0');
                        $('#order-exp-accept').html(result.allocated_qty+' / '+result.accept_qty);
                        /*$('#order-exp-accept').html(result.exp_qty+' / '+result.accept_qty);*/
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

        b.on('click','#clear-product-in-box-by-one-scanning-outbound-bt',function() {

            console.info('-wd view default _colinsProcessForm click clear-product-in-box-by-one-scanning-outbound-bt-');

            var href = $(this).data('url-value'),
                boxBorderValue = $('#scanningform-box_barcode').val(),
                form = $('#scanning-form');

            errorBase.setForm(form);

            $.post(href, form.serialize(), function (result) {

                errorBase.hidden();

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();

                    $('#count-product-in-box').html(result.countInBox);
                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#order-exp-accept').html(result.allocated_qty+' / '+result.accept_qty);

                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });

        });

//        /*
//         * Click on List differences
//         * */
//        b.on('click','#scanning-form-differences-list-bt',function() {
//            var href = $(this).data('url');
//
//            window.location.href = href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val();
//
//        });
        /*
         * Click on List differences
         * */
        b.on('click','#scanning-form-differences-list-bt',function() {
            var href = $(this).data('url'),
                printType = $('#scanning-form').data('printtype');
            if(printType == 'pdf'){
                window.location.href = href + '?order_shop=' + $('#scanningcolinsform-order_shop').val();
            } else if (printType == 'html'){
                autoPrintAllocatedListHtml(href + '?order_shop=' + $('#scanningcolinsform-order_shop').val());
            }

        });


        b.on('click','#scanning-form-print-box-label-bt',function() {

            if(confirm('Вы действительно хотите распечатать этикетки')) {

                var plids = $('#scanningform-picking_list_barcode_scanned').val();

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

                window.location.href = href + '?plids=' + plids ;
            }

        });

        b.on('click','#scanning-form-print-box-label-bt',function() {
            var printType = $('#scanning-form').data('printtype');
            if(confirm('Вы действительно хотите распечатать этикетки')) {

                //$('#scanning-colins-form-print-box-content-bt').removeClass('hidden');

                var plids = $('#scanningcolinsform-order_shop').val();

                $('#scanningform-employee_barcode').val('');
                // $('#alert-picking-list').html('');
                //$('#scanningform-picking_list_barcode').val('');
                $('#count-product-in-box').html('');
                $('#scanningcolinsform-box_barcode').val('');
                $('#scanningcolinsform-product_barcode').val('');
                //$('#scanningform-picking_list_barcode_scanned').val('');
                $('#outbound-item-body').html('');
                $('#order-exp-accept').html('0/0');

                var href = $(this).data('url');


                //window.location.href = href + '?order_shop=' + plids ;

                if(printType == 'pdf'){
                    window.location.href = href + '?order_shop=' + plids ;
                } else if (printType == 'html'){
                    //autoPrintAllocatedListHtml(href + '?order_shop=' + plids, '0', 1500, 'BL');
                    window.location.href = href + '?order_shop=' + plids ;
                }
            }

        });

//        b.on('click','#scanning-colins-form-print-box-content-bt',function() {
//
//            var plids = $('#scanningcolinsform-order_shop').val();
//
//                $('#scanningform-employee_barcode').val('');
//                // $('#alert-picking-list').html('');
//                //$('#scanningform-picking_list_barcode').val('');
//                $('#count-product-in-box').html('');
//                $('#scanningcolinsform-box_barcode').val('');
//                $('#scanningcolinsform-product_barcode').val('');
//                //$('#scanningform-picking_list_barcode_scanned').val('');
//                $('#outbound-item-body').html('');
//                $('#order-exp-accept').html('0/0');
//                $(this).addClass('hidden');
//
//                var href = $(this).data('url');
//
//            window.location.href = href + '?order_shop=' + plids ;
//
//        });

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

//        var url = '<?//= Url::toRoute('download-file-de-facto-api'); ?>//',
//            me = $(this);

            var me = $(this),
                url = me.data('url');

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

//        var url = '<?//= Url::toRoute('upload-file-de-facto-api'); ?>//',
            var me = $(this),
                url = me.data('url');

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
         *
         *  BEGIN COLINS
         *
         * */


        /*
         * Start if change main client drop-down
         * */
        b.on('change','#main-form-client-id',function() {

            console.log('change #main-form-client-id');

            var me = $(this),
                url = me.data('url');

            window.location.href = url+'?id='+me.val();
        });

        /*
         * End if change main client drop-down
         * */





        b.on('click','#outbound-process-colins-bt',function() {

            console.log('change #outbound-process-colins-bt');

            var me = $(this);
            window.location.href = me.data('url');

        });



        // EMPLOYEE BARCODE
        b.on('keyup',"#scanningcolinsform-employee_barcode", function (e) {

            if (e.which == 13) {

                var me = $(this),
                    url = me.data('url'),
                    form = $('#scanning-form');

                errorBase.setForm(form);

                $.post(url, form.serialize(),function (result) {


                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                        me.focus().select();
                    } else {
                        errorBase.hidden();

                        $('#scanningcolinsform-order_shop').focus().select();
                        $('#scanningcolinsform-box_barcode').val('');
                        $('#scanningcolinsform-product_barcode').val('');

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

        /*
         * Start if change shop order drop-down
         * */
        b.on('change','#scanningcolinsform-order_shop',function() {

            console.log('change #scanningcolinsform-order_shop');

            var me = $(this),
                url = me.data('url'),
                form = $('#scanning-form');

            console.log(me.val());

            errorBase.setForm(form);

            $.post(url, form.serialize(),function (result) {

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    $('#scanningcolinsform-box_barcode').focus().select();
                    $('#scanningcolinsform-box_barcode').val('');
                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#order-exp-accept').html(result.allocated_qty+' / '+result.accept_qty);
                    $('#order-shop-name').html(result.orderShopName);

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });

//      $('#scanningcolinsform-box_barcode').focus().select();

        });

        /*
         * Scanning employees barcode OR picking list barcode
         *
         * */

        b.on('click',"#scanningcolinsform-product_barcode,#scanningcolinsform-box_barcode", function (e) {
            $(this).focus().select();
        });

        // BOX BARCODE
        b.on('keyup',"#scanningcolinsform-box_barcode", function (e) {

            if (e.which == 13) {

                var me = $(this),
                    url = me.data('url'),
                    form = $('#scanning-form');

                console.log('keyup #scanningcolinsform-box_barcode');
                console.log(me.val());

                errorBase.setForm(form);

                $.post(url, form.serialize(),function (result) {

                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                        me.focus().select();
                    } else {
                        errorBase.hidden();

                        $('#scanningcolinsform-product_barcode').focus().select();
                        $('#scanningcolinsform-product_barcode').val('');
                        $('#count-product-in-box').html(result.countInBox);
//                    $('#outbound-item-body').html(result.stockArrayByPL);

                    }
                }, 'json').fail(function (xhr, textStatus, errorThrown) {
                });
            }
            e.preventDefault();
        });

        // PRODUCT BARCODE
        b.on('keyup',"#scanningcolinsform-product_barcode", function (e) {

            if (e.which == 13) {
                var me = $(this),
                    url = me.data('url'),
                    form = $('#scanning-form');

                console.log('keyup #scanningcolinsform-product_barcode');
                console.log(me.val());

                if(me.val() == 'CHANGEBOX') {
                    me.val('');
                   var contentHref = '/wms/colins/outbound/printing-box-content',
                       boxBarcode = $('#scanningcolinsform-box_barcode'),
                       orderNumber = $('#scanningcolinsform-order_shop'),
                       printType = form.data('printtype');
                    boxBarcode.focus().select();

                    if(printType == 'pdf'){
                        window.location.href = contentHref + '?box_barcode=' + boxBarcode.val()+'&order='+orderNumber.val();
                    } else if (printType == 'html'){
                        autoPrintAllocatedListHtml(contentHref + '?box_barcode=' + boxBarcode.val()+'&order='+orderNumber.val());
                    }

                    e.preventDefault();
                    return false;
                }

                errorBase.setForm(form);
                me.focus().select();

                $.post(url, form.serialize(),function (result) {

                    if(result.change_box == 'ok') {
                        me.val('');
                        $('#scanningcolinsform-box_barcode').focus().select();
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
                        $('#order-exp-accept').html(result.allocated_qty+' / '+result.accept_qty);
                    }

                }, 'json').fail(function (xhr, textStatus, errorThrown) {

                });
            }

            e.preventDefault();
        });


        b.on('submit','#allocate-list-form', function (e) {
            return false;
        });




        /* COLINS INBOUND
         *
         *  START
         *
         * */

    });

    var inboundManagerColins = function () {


        var fld_box_barcode = $('#inbound-form-box_barcode'),
            fld_product_barcode = $('#inbound-form-product_barcode'),
            fld_order_number = $('#colins-inbound-form-order-number'),
            fld_party_number = $('#inbound-form-party-number'),
            list_differences_bt = $('#inbound-list-differences-bt'),
            list_unallocated_bt = $('#inbound-unallocated-list-bt'),
            e_items = $('#inbound-items'),
            accept_bt = $('#inbound-accept-bt'),
            client_id = $('#main-form-client-id').val(),
            actionRoute = '/wms/colins/inbound/',
            me;

        return me = {
            'onLoad': function() {
                fld_order_number.val('');
                fld_box_barcode.val('');
                fld_product_barcode.val('');
                fld_party_number.val('');

                list_differences_bt.hide();
                list_unallocated_bt.hide();
                e_items.hide();
                accept_bt.hide();
            },
            'showByOrder':function() {
                e_items.show();
                list_differences_bt.show();
                list_unallocated_bt.show();
                accept_bt.show();
            },
            'hideByOrderAll':function() {

                fld_order_number.val('');
                fld_box_barcode.val('');
                fld_product_barcode.val('');
                fld_party_number.val('');

                list_differences_bt.hide();
                list_unallocated_bt.hide();
                e_items.hide();
                accept_bt.hide();
            },
            'hideByOrder':function() {

                fld_box_barcode.val('');
                fld_product_barcode.val('');
                fld_party_number.val('');

                list_differences_bt.hide();
                list_unallocated_bt.hide();
                e_items.hide();
                accept_bt.hide();
            },

            'loadParentOrder':function(){

                var partyInbound = $('#inbound-form-party-number'),
                    dataOptions = '';

                if(client_id) {

                    $.post(actionRoute+'get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {
                        partyInbound.html('');

                        $.each(result.dataOptions, function (key, value) {
                            dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                        });

                        partyInbound.append(dataOptions);
                        partyInbound.focus().select();

                    }).fail(function () {
                        console.log("server error");
                    });
                }
            },
//            'handlerOnChangeClientID': function (event) {
//
//                var client_id = $(this).val(),
//                    inbound = $('#colins-inbound-form-order-number'),
//                    partyInbound = $('#inbound-form-party-number'),
//                    a = {},
//                    dataOptions = '';
//
//                if(client_id) {
//
//                    $.post('get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {
//
//                        a = inbound;
//                        if(result.type == 'party-inbound') {
//                            a = partyInbound;
//                        }
//
//                        a.html('');
//
//                        $.each(result.dataOptions, function (key, value) {
//                            dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
//                        });
//
//                        a.append(dataOptions);
//                        a.focus().select();
//
//                    }).fail(function () {
//                        console.log("server error");
//                    });
//
//                } else {
//                    me.hideByOrderAll();
//                }
//            },
            //'handlerConfirmFormOnChangeClientID': function (event) {
            //
            //    var client_id = $(this).val(),
            //        e = $('#download-for-api-order-number'),
            //        dataOptions = '';
            //
            //    if(client_id) {
            //
            //        $.post('/inbound/default/get-complete-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {
            //
            //            e.html('');
            //
            //            $.each(result.dataOptions, function (key, value) {
            //                dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
            //            });
            //
            //            e.append(dataOptions);
            //            e.focus().select();
            //
            //        }).fail(function () {
            //            console.log("server error");
            //        });
            //
            //    } else {
            //        me.hideByOrderAll();
            //    }
            //
            //},
            //'handlerOnChangeOrderNumber': function (event) {
            //
            //    var inbound_id = $(this).val();
            //
            //    if(inbound_id) {
            //
            //        $.post('/inbound/default/get-scanned-product-by-id',
            //            {'inbound_id': $(this).val()}
            //        ).done(function (result) {
            //
            //                $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
            //                $('#inbound-item-body').html(result.items);
            //
            //                $('#inboundform-box_barcode').focus().select();
            //
            //                me.showByOrder();
            //
            //            }).fail(function () {
            //                console.log("server error");
            //            });
            //
            //    } else {
            //        me.hideByOrder();
            //    }
            //},
            'handlerOnClickAcceptBT': function (event) {

                var client_idValue = $('#main-form-client-id').val(),
                    order_numberValue = $('#inbound-form-party-number').val(),
                    messages_processText = $('#inbound-messages-process');

                if(confirm('Вы уверены, что хотите закрыть накладную')) {

                    console.info(client_idValue);
                    console.info(order_numberValue);

                    if (client_idValue && order_numberValue) {

                        $(messages_processText).html(' Подождите, идет обработка, не закрывайте браузер или вкладку ...');

                        $.post(actionRoute+'confirm-order',  $('#inbound-process-form').serialize()).done(function (result) {
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
                            $('#colins-count-products-in-order').html('0/0');
                            $('#count-product-in-box').html('0');
                            $('#inbound-item-body').html('');

                            $(messages_processText).html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000,function() {
                                $(messages_processText).html('');
                            } );

                            //$("#inbound-form-order-number option:selected").remove();

                            var client_id = $('#colins-inbound-form-client-id').val(),
                                inbound = $('#colins-inbound-form-order-number'),
                                partyInbound = $('#inbound-form-party-number'),
                                a = {},
                                dataOptions = '';

                            inbound.html('');
                            partyInbound.html('');
                            $('#count-products-in-party').html('0/0');

                            if(client_id) {

                                $.post('get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {

                                    a = inbound;
                                    if(result.type == 'party-inbound') {
                                        a = partyInbound;
                                    }

                                    a.html('');

                                    $.each(result.dataOptions, function (key, value) {
                                        dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                                    });

                                    a.append(dataOptions);
                                    a.focus().select();

                                }).fail(function () {
                                    console.log("server error");
                                });

                            } else {
                                me.hideByOrderAll();
                            }

                        }).fail(function () {
                            console.log("server error");
                        });

                    } else {
                        //inboundModel.hideByOrderAll();
                    }

                }
            }
        }

    };

    $(function () {

        var inboundModel = new inboundManagerColins(),
            actionRoute = '/wms/colins/inbound/',
            client_id = $('#main-form-client-id').val(),
            b = $('body');

            inboundModel.onLoad();


        $('#inbound-process-form').on('submit', function (e) {
            return false;
        });

        b.on('click','#inbound-form-box_barcode, #coins-inboundform-product_barcode',function(){
            var me = $(this);
            me.focus().select();
        });

        b.on('click','#inbound-process-colins-bt',function() {

            console.log('click inbound-process-colins-bt');

            var me = $(this),
                url = me.data('url');

            $.post(url,function(data) {
                $('#container-colins-outbound-layout').html(data);
                inboundModel.loadParentOrder();
                init();
            });

        });

        function init(){
            //b.on('change','#colins-inbound-form-client-id',inboundModel.handlerOnChangeClientID);

            //b.on('change','#colins-inbound-form-order-number',inboundModel.handlerOnChangeOrderNumber);

            b.on('click','#inbound-accept-bt',inboundModel.handlerOnClickAcceptBT);

            //b.on('change','#download-for-api-client-id',inboundModel.handlerConfirmFormOnChangeClientID);

            /*
             * Scan box barcode
             * */
            $("#inbound-form-box_barcode").on('keyup', function (e) {
                /*
                 * TODO Добавить проверку
                 * TODO 1 + Существует ли этот короб у нас на складе
                 * TODO 2 + Если ли в этом коробе уже товары из другого магазина
                 * */
                if (e.which == 13) {

                    console.info("colins-inboundform-box-barcode-");
                    console.info("Value : " + $(this).val());

                    var me = $(this);
                    var form = $('#inbound-process-form');

                    errorBase.setForm(form);
                    me.focus().select();

                    $.post(actionRoute+'validate-scanned-box', form.serialize(),function (result) {
                        if (result.success == 0 ) {
                            errorBase.eachShow(result.errors);
                            me.focus().select();
                        } else {
                            errorBase.hidden();
                            $("#inbound-form-product_barcode").focus().select();
                            $('#count-product-in-box').html(result.countProductInBox);
                        }

                    }, 'json').fail(function (xhr, textStatus, errorThrown) {

                    });
                }

                e.preventDefault();
            });

            /*
             * Scan product barcode in box
             * */
            $("#inbound-form-product_barcode").on('keyup', function (e) {
                /*
                 * TODO Добавить проверку
                 * TODO 1 ? Существует ли этот товар у нас на складе
                 * TODO 2 + Существует ли этот товар у этого клиента
                 */
                if (e.which == 13) {

                    var me = $(this);
                    console.info("colins-inbound-process-steps-product-barcode-");
                    console.info("Value : " + me.val());

                    me.focus().select();

                    if(me.val() == 'CHANGEBOX') {
                        $('#inbound-form-box_barcode').focus().select();
                        me.val('');
                        return true;
                    }


                    var form = $('#inbound-process-form');

                    errorBase.setForm(form);

                    $.post(actionRoute+'scan-product-in-box', $('#inbound-process-form').serialize(),function (result) {
                        if (result.success == 0 ) {
                            errorBase.eachShow(result.errors);
                            me.focus().select();
                        } else {
                            errorBase.hidden();
                            $('#count-product-in-box').html(result.countProductInBox);
                            $('#colins-count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);


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
            b.on('click','#inbound-list-differences-bt',function() {
                var href = $(this).data('url'),
                    printType = $('#inbound-process-form').data('printtype');
                if(printType == 'pdf'){
                    window.location.href = href + '?party_id=' + $('#inbound-form-party-number').val();
                } else if (printType == 'html'){
                    autoPrintAllocatedListHtml(href + '?party_id=' + $('#inbound-form-party-number').val());
                }



            });

            /*
             * Click on Unallocated list
             * */
            b.on('click','#inbound-unallocated-list-bt',function() {
                var href = $(this).data('url'),
                    printType = $('#inbound-process-form').data('printtype');
                if(printType == 'pdf'){
                    window.location.href = href + '?party_id=' + $('#inbound-form-party-number').val();
                } else if (printType == 'html'){
                    autoPrintAllocatedListHtml(href + '?party_id=' + $('#inbound-form-party-number').val());
                }

            });

            //$("#print-register-np-bt").attr({target:"_blank",href:data.redirectToURL,'np-statement-id':data.npStatementID});
            //var a = window.open(data.redirectToURL, "_blank", "");
            //a.blur();

//            b.on('click','#clear-box-bt',function() {
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
                            //$('#count-products-in-order').html(result.countScannedProductInOrder);
                            $('#colins-count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

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

//            b.on('click','#clear-product-in-box-by-one-bt',function() {
            $("#clear-product-in-box-by-one-bt").on('click',function() {

                console.info('stockDepartment\modules\wms\views\default\_colinsProcessForm.php');
                var href = $(this).data('url-value'),
                    boxBorderValue = $('#inbound-form-box_barcode').val(),
                    form = $('#inbound-process-form');

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
                        $('#colins-count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                        $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                        $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                        $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                        $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                    }

                }, 'json').fail(function (xhr, textStatus, errorThrown) {
                });

            });

//            b.on('change','#inbound-form-party-number',function() {
            $('#inbound-form-party-number').on('change',function() {

                var party_id = $(this).val();
                if(party_id) {
                    $.post(actionRoute + 'get-scanned-product-by-party-id',
                                    {'party_id': party_id}
                    ).done(function (result) {

                                        //$('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
                        $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                        $('#inbound-item-body').html(result.items);
                        $('#inbound-form-box_barcode').focus().select();
                        inboundModel.showByOrder();

                    }).fail(function () {
                        console.log("server error");
                    });

//                    $.post(actionRoute + 'get-in-process-inbound-orders-by-party-id', {'party_id': party_id}).done(function (result) {
//
//
//
//                    }).fail(function () {
//                        console.log("server error");
//                    });

                } else {
                    inboundModel.hideByOrderAll();
                }
            });

}

    });

</script>