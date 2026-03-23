/**
 * Created by Igor on 09.01.2015.
 */
var initCountdown = function(time, start){
        var note = $('#note'),
            ts =  (new Date()).getTime() + (time*1000);

    if(typeof start == 'undefined'){
        start = true;
    }

    if(start == true){
        note.attr('data-on', '1');
    }

    if(note.data('on') == 0 ){

        $('#countdown').countdown({
            timestamp	: ts,
            callback	: function(days, hours, minutes, seconds){

                var message = "Вам нужно отсканировать этот заказ за: ";
                message += hours + " час(а), ";
                message += minutes + " минут, ";
                message += seconds + " секунд <br/>";
                note.html(message);
            }
        }, start);
    }

};
var stopCountdown = function(){
    $('#note').remove();
};

var inboundManager = function () {

    var fld_box_barcode = $('#inboundform-box_barcode'),
        fld_product_barcode = $('#inboundform-product_barcode'),
        fld_order_number = $('#inbound-form-order-number'),
        list_differences_bt = $('#inbound-list-differences-bt'),
        list_unallocated_bt = $('#inbound-unallocated-list-bt'),
        e_items = $('#inbound-items'),
        accept_bt = $('#inbound-accept-bt'),
        me;

    return me = {
        'onLoad': function() {
            console.info('-inboundManager onLoad-');
            fld_order_number.val('');
            fld_box_barcode.val('');
            fld_product_barcode.val('');

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

            list_differences_bt.hide();
            list_unallocated_bt.hide();
            e_items.hide();
            accept_bt.hide();
        },
        'hideByOrder':function() {

            fld_box_barcode.val('');
            fld_product_barcode.val('');

            list_differences_bt.hide();
            list_unallocated_bt.hide();
            e_items.hide();
            accept_bt.hide();
        },
        'handlerOnChangeClientID': function () {
            console.info('-handlerOnChangeClientID-');
            var client_id = $(this).val(),
                inbound = $('#inbound-form-order-number'),
                partyInbound = $('#inbound-form-party-number'),
                a = {},
                dataOptions = '';

            if(client_id) {

                $.post('/inbound/default/get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {

                    a = inbound;
                    if(result.type == 'party-inbound') {
                        a = partyInbound;
                    }

                    a.html('');

                    $.each(result.dataOptions, function (key, value) {
                        dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                    });

                    a.append(dataOptions);
                    //a.attr();

                    a.focus().select();

                    //console.info('#inbound-form-order-number [value=]"');

                }).fail(function () {
                    console.log("server error");
                });

            } else {
                me.hideByOrderAll();
            }
        },
        'handlerConfirmFormOnChangeClientID': function (event) {

            var client_id = $(this).val(),
                e = $('#download-for-api-order-number'),
                dataOptions = '';

            if(client_id) {

                $.post('/inbound/default/get-complete-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {

                    e.html('');

                    $.each(result.dataOptions, function (key, value) {
                        dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                    });

                    e.append(dataOptions);
                    e.focus().select();

                }).fail(function () {
                    console.log("server error");
                });

            } else {
                me.hideByOrderAll();
            }

        },
        'handlerOnChangeOrderNumber': function (event) {

            var inbound_id = $(this).val();

            if(inbound_id) {

                $.post('/inbound/default/get-scanned-product-by-id',
                    {'inbound_id': $(this).val()}
                ).done(function (result) {

                        $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
                        $('#inbound-item-body').html(result.items);
                        $('#note').attr('data-timer', result.cdTimer);
                        if(result.cdTimer != 0){
                            console.info ('-init timer-');
                            initCountdown(result.cdTimer, false);
                        }

                        $('#inboundform-box_barcode').focus().select();

                        me.showByOrder();

                    }).fail(function () {
                        console.log("server error");
                    });

            } else {
                me.hideByOrder();
            }
        },
        'handlerOnClickAcceptBT': function (event) {

            var client_idValue = $('#inbound-form-client-id').val(),
                order_numberValue = $('#inbound-form-order-number').val(),
                messages_processText = $('#inbound-messages-process');


            if(confirm('Вы уверены, что хотите закрыть накладную')) {

                console.info(client_idValue);
                console.info(order_numberValue);

                if (client_idValue && order_numberValue) {

                   stopCountdown();

                    $(messages_processText).html(' Подождите, идет обработка, не закрывайте браузер или вкладку ...');

                    $.post('/inbound/default/confirm-order',  $('#inbound-process-form').serialize()).done(function (result) {
                        /* TODO Потом сделать вывод сообщений через bootstrap Modal   */

                        var alertMessage = '';

                        $.each(result.messages, function (key, value) {
                            if ( value.length) {
                                alertMessage += value+'\n';
                            }
                        });

                        alert(alertMessage);

                        $('#inboundform-product_barcode').val('');
                        $('#inboundform-box_barcode').val('');
                        $('#count-products-in-order').html('0/0');
                        $('#count-product-in-box').html('0');
                        $('#inbound-item-body').html('');

                        $(messages_processText).html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000,function() {
                            $(messages_processText).html('');
                        } );

                        //$("#inbound-form-order-number option:selected").remove();

                        var client_id = $('#inbound-form-client-id').val(),
                            inbound = $('#inbound-form-order-number'),
                            partyInbound = $('#inbound-form-party-number'),
                            a = {},
                            dataOptions = '';

                            inbound.html('');
                            partyInbound.html('');
                            $('#count-products-in-party').html('0/0');

                        if(client_id) {

                            $.post('/inbound/default/get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {

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
                    // case "Microsoft XPS Document Writer":
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


    
    //return false;
    //console.info('inbound-process');

    var inboundModel = new inboundManager(),
        b = $('body');

    inboundModel.onLoad();

    console.info(inboundModel);

    $('#allocationlistform-box_barcode').focus().select();
    b.on('click', '#confirm-upload-bt', function(){
        if(confirm('Вы точно хотите загрузить данные в систему')){
            $('#loading-modal').modal('show');
            $.post($(this).data('url'), {}, function (result) {
            }, 'html')
        }
    });

    $('#inbound-process-form').on('submit', function (e) {
        return false;
    });


    b.on('click','#inboundform-box_barcode, #inboundform-product_barcode',function(){
        var me = $(this);
        me.focus().select();
    });

    //console.info(inboundModel.handlerOnChangeClientID);

    b.on('change','#inbound-form-client-id',inboundModel.handlerOnChangeClientID);

    b.on('change','#inbound-form-order-number',inboundModel.handlerOnChangeOrderNumber);
    b.on('click','#inbound-accept-bt',inboundModel.handlerOnClickAcceptBT);
    //b.on('change','#inbound-form-client-id',inboundModel.handlerOnChangeClientID);
/*    b.on('change','#inbound-form-order-number',inboundModel.handlerOnChangeOrderNumber);
    b.on('click','#inbound-accept-bt',inboundModel.handlerOnClickAcceptBT);
    b.on('change','#download-for-api-client-id',inboundModel.handlerConfirmFormOnChangeClientID);*/


    /*
     * Scan box barcode
     * */
    $("#inboundform-box_barcode").on('keyup', function (e) {
        /*
         * TODO Добавить проверку
         * TODO 1 + Существует ли этот короб у нас на складе
         * TODO 2 + Если ли в этом коробе уже товары из другого магазина
         * */
        if (e.which == 13) {

            console.info("inboundform-box-barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this);
            var form = $('#inbound-process-form');

            errorBase.setForm(form);
            me.focus().select();

            $.post('/inbound/default/validate-scanned-box', $('#inbound-process-form').serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $("#inboundform-product_barcode").focus().select();
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
    $("#inboundform-product_barcode").on('keyup', function (e) {
        /*
         * TODO Добавить проверку
         * TODO 1 ? Существует ли этот товар у нас на складе
         * TODO 2 + Существует ли этот товар у этого клиента
         */
        if (e.which == 13) {

            var me = $(this),
                timer = $('#note');
            console.info("-inbound-process-steps-product-barcode-");
            console.info("Value : " + me.val());

            me.focus().select();

            if(me.val() == 'CHANGEBOX') {
                $('#inboundform-box_barcode').focus().select();
                me.val('');
                return true;
            }
            if(timer.attr('data-on') == "0"){
                console.info("-start-timer-");
                initCountdown(timer.data('timer'));
            }

            var form = $('#inbound-process-form');

            errorBase.setForm(form);

            $.post('/inbound/default/scan-product-in-box', $('#inbound-process-form').serialize(),function (result) {
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
    b.on('click','#inbound-list-differences-bt',function() {
        var href = $(this).data('url'),
            printType = $('#inbound-process-form').data('printtype');
        if(printType == 'pdf'){
            window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
        } else if (printType == 'html'){
            //window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
            autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#inbound-form-order-number').val(), '0', 2500);
        }

    });

    /*
     * Click on Unallocated list
     * */
    b.on('click','#inbound-unallocated-list-bt',function() {
        var href = $(this).data('url'),
            printType = $('#inbound-process-form').data('printtype');
        if(printType == 'pdf'){
            window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
        } else if (printType == 'html'){
            //window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
            autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#inbound-form-order-number').val());
        }

    });

     //$("#print-register-np-bt").attr({target:"_blank",href:data.redirectToURL,'np-statement-id':data.npStatementID});
    //var a = window.open(data.redirectToURL, "_blank", "");
    //a.blur();

    b.on('click','#clear-box-bt',function() {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#inboundform-box_barcode').val(),
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

    b.on('click','#clear-product-in-box-by-one-bt',function() {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#inboundform-box_barcode').val(),
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
                $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    b.on('change','#inbound-form-party-number',function() {

        var party_id = $(this).val(),
            inbound = $('#inbound-form-order-number'),
            dataOptions = '';

        if(party_id) {

            $.post('/inbound/default/get-in-process-inbound-orders-by-party-id', {'party_id': party_id}).done(function (result) {

                inbound.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                });

                inbound.append(dataOptions);
                inbound.focus().select();
                $("#inbound-form-order-number [value='']").attr("selected", "selected");

                $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);

            }).fail(function () {
                console.log("server error");
            });

        } else {
            //inboundManager.hideByOrderAll();
        }
    });
});