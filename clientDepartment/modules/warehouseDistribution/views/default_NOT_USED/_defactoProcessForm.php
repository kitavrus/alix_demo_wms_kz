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
?>

<?= Html::label(Yii::t('inbound/forms', 'Client ID')); ?>
<?= Html::dropDownList( 'client_id',$id,$clientsArray, [
        'prompt' => '',
        'id' => 'main-defacto-form-client-id',
        'class' => 'form-control input-lg',
        'readonly' => true,
    ]
); ?>

<div id="container-defacto-process-form-layout" style="margin-top: 30px;"></div>
<div id="container-defacto-layout" style="margin-top: 30px;"></div>

<span id="buttons-menu">
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Upload outbound order DeFacto API [1]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/defacto/outbound/upload-file-de-facto-api')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'upload-outbound-order-api-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Outbound print pick list [2]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/defacto/outbound/select-and-print-picking-list')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'outbound-print-pick-list-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Begin end picking process [3]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/defacto/outbound/begin-end-picking-handler')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'begin-end-picking-list-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Scanning process [4]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/defacto/outbound/scanning-form')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'scanning-process-bt']) ?>
    <?= Html::tag('span', Yii::t('outbound/buttons', 'Download  outbound order DeFacto API [5]'), ['data'=>['url'=>Url::toRoute('/warehouseDistribution/defacto/outbound/download-file-de-facto-api')],'class' => 'btn btn-primary btn-lg', 'style' => ' margin:10px;', 'id' => 'download-outbound-order-api-bt']) ?>
</span>
<div id="container-defacto-outbound-layout" style="margin-top: 50px;"></div>



<script type="text/javascript">

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

    $(function(){

        var b = $('body');

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

            var me = $(this),
                url = me.data('url');
                /*url = '<?= Url::toRoute('select-and-print-picking-list'); ?>'*/

            $('#buttons-menu').find('.btn').removeClass('focus');

            $.post(url,function(data) {

                me.addClass('focus');

                $('#container-defacto-outbound-layout').html(data);
            });

        });

        b.on('click','#begin-end-picking-list-bt',function() {

            var me = $(this),
                url = me.data('url');
                /*url = '<?= Url::toRoute('begin-end-picking-handler'); ?>',*/

            $('#buttons-menu').find('.btn').removeClass('focus');

            $.post(url,function(data) {

                me.addClass('focus');

                $('#container-defacto-outbound-layout').html(data);
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

            var me = $(this),
                url = me.data('url');
                /*url = '<?= Url::toRoute('scanning-form'); ?>',*/

            $('#buttons-menu').find('.btn').removeClass('focus');

            $.post(url,function(data) {

                me.addClass('focus');

                $('#container-defacto-outbound-layout').html(data);
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

                var me = $(this),
                    url = me.data('url'),
                    form = $('#scanning-form');

                /*url = '<?= Url::toRoute('employee-barcode-scanning-handler'); ?>',*/

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



                var url = me.data('url'),
                    form = $('#scanning-form');

                /*url = '<?= Url::toRoute('picking-list-barcode-scanning-handler'); ?>',*/

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

                var me = $(this),
                    url = me.data('url'),
                    form = $('#scanning-form');


                /*url = '<?= Url::toRoute('box-barcode-scanning-handler'); ?>',*/

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

                if($(this).val() == 'CHANGEBOX_NOT_USED') {
                    me.val('');
                    $('#scanningform-box_barcode').focus().select();
                    e.preventDefault();
                    return false;
                }

                var url = me.data('url'),
                    form = $('#scanning-form');

                /*url = '<?= Url::toRoute('product-barcode-scanning-handler'); ?>',*/

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
            var href = $(this).data('url');

            window.location.href = href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val();

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

           /* var url = '<?= Url::toRoute('download-file-de-facto-api'); ?>',*/
            var me = $(this),
                url = me.data('url');


            $('#buttons-menu').find('.btn').removeClass('focus');

            $.post(url,function(data) {

                me.addClass('focus');

                $('#container-defacto-outbound-layout').html(data);
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


            var me = $(this),
                url = me.data('url');

            /* '<?= Url::toRoute('upload-file-de-facto-api'); ?>' */

            $('#buttons-menu').find('.btn').removeClass('focus');

            $.post(url,function(data) {

                me.addClass('focus');

                $('#container-defacto-outbound-layout').html(data);
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


    });

</script>