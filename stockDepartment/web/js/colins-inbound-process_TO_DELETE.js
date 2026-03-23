/**
 * Created by Igor on 09.01.2015.
 */

var inboundManager = function () {


    var fld_box_barcode = $('#colins-inboundform-box_barcode'),
        fld_product_barcode = $('#colins-inboundform-product_barcode'),
        fld_order_number = $('#colins-inbound-form-order-number'),
        list_differences_bt = $('#colins-inbound-list-differences-bt'),
        list_unallocated_bt = $('#colins-inbound-unallocated-list-bt'),
        e_items = $('#colins-inbound-items'),
        accept_bt = $('#colins-inbound-accept-bt'),
        me;

    return me = {
        'onLoad': function() {
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
        'handlerOnChangeClientID': function (event) {

            var client_id = $(this).val(),
                inbound = $('#colins-inbound-form-order-number'),
                partyInbound = $('#colins-inbound-form-party-number'),
                a = {},
                dataOptions = '';

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
        },
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

            var client_idValue = $('#colins-inbound-form-client-id').val(),
                //order_numberValue = $('#inbound-form-order-number').val(),
                messages_processText = $('#colins-inbound-messages-process');

            if(confirm('Вы уверены, что хотите закрыть накладную')) {

                console.info(client_idValue);
                console.info(order_numberValue);

                if (client_idValue && order_numberValue) {

                    $(messages_processText).html(' Подождите, идет обработка, не закрывайте браузер или вкладку ...');

                    $.post('confirm-order',  $('#colins-inbound-process-form').serialize()).done(function (result) {
                        /* TODO Потом сделать вывод сообщений через bootstrap Modal   */

                        var alertMessage = '';

                        $.each(result.messages, function (key, value) {
                            if ( value.length) {
                                alertMessage += value+'\n';
                            }
                        });

                        alert(alertMessage);

                        $('#colins-inboundform-product_barcode').val('');
                        $('#colins-inboundform-box_barcode').val('');
                        $('#colins-count-products-in-order').html('0/0');
                        $('#colins-count-product-in-box').html('0');
                        $('#colins-inbound-item-body').html('');

                        $(messages_processText).html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000,function() {
                            $(messages_processText).html('');
                        } );

                        //$("#inbound-form-order-number option:selected").remove();

                        var client_id = $('#colins-inbound-form-client-id').val(),
                            inbound = $('#colins-inbound-form-order-number'),
                            partyInbound = $('#colins-inbound-form-party-number'),
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

    var inboundModel = new inboundManager(),
        b = $('body');

    inboundModel.onLoad();

    $('#colins-inbound-form-client-id').prop("disabled", true);

    var client_id = $('#colins-inbound-form-client-id').val(),
        //inbound = $('#colins-inbound-form-order-number'),
        partyInbound = $('#colins-inbound-form-party-number'),
        a = {},
        dataOptions = '';

    if(client_id) {

        $.post('get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {

            //a = inbound;
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
    
    $('#colins-inbound-process-form').on('submit', function (e) {
        return false;
    });

    b.on('click','#colins-inboundform-box_barcode, #coins-inboundform-product_barcode',function(){
        var me = $(this);
        me.focus().select();
    });


    //b.on('change','#colins-inbound-form-client-id',inboundModel.handlerOnChangeClientID);

    //b.on('change','#colins-inbound-form-order-number',inboundModel.handlerOnChangeOrderNumber);

    b.on('click','#colins-inbound-accept-bt',inboundModel.handlerOnClickAcceptBT);

    //b.on('change','#download-for-api-client-id',inboundModel.handlerConfirmFormOnChangeClientID);

    /*
     * Scan box barcode
     * */
    $("#colins-inboundform-box_barcode").on('keyup', function (e) {
        /*
         * TODO Добавить проверку
         * TODO 1 + Существует ли этот короб у нас на складе
         * TODO 2 + Если ли в этом коробе уже товары из другого магазина
         * */
        if (e.which == 13) {

            console.info("colins-inboundform-box-barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this);
            var form = $('#colins-inbound-process-form');

            errorBase.setForm(form);
            me.focus().select();

            $.post('validate-scanned-box', $('#colins-inbound-process-form').serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $("#colins-inboundform-product_barcode").focus().select();
                    $('#colins-count-product-in-box').html(result.countProductInBox);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    /*
     * Scan product barcode in box
     * */
    $("#colins-inboundform-product_barcode").on('keyup', function (e) {
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
                $('#colins-inboundform-box_barcode').focus().select();
                me.val('');
                return true;
            }


            var form = $('#colins-inbound-process-form');

            errorBase.setForm(form);

            $.post('scan-product-in-box', $('#colins-inbound-process-form').serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#colins-count-product-in-box').html(result.countProductInBox);
                    $('#colins-count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);


                    $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                    $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                    $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                    $('#colins-count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
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
    b.on('click','#colins-inbound-list-differences-bt',function() {
        var href = $(this).data('url');

        window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();

    });

    /*
     * Click on Unallocated list
     * */
    b.on('click','#colins-inbound-unallocated-list-bt',function() {
        var href = $(this).data('url');

        window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();

    });

     //$("#print-register-np-bt").attr({target:"_blank",href:data.redirectToURL,'np-statement-id':data.npStatementID});
    //var a = window.open(data.redirectToURL, "_blank", "");
    //a.blur();

    b.on('click','#colins-clear-box-bt',function() {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#colins-inboundform-box_barcode').val(),
            form = $('#colins-inbound-process-form');

        console.info(boxBorderValue);

        errorBase.setForm(form);

         if( confirm('Вы действительно хотите очистить короб') ) {

            $.post(href, form.serialize(), function (result) {

                    errorBase.hidden();

                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                    } else {
                        errorBase.hidden();
                        $('#colins-count-product-in-box').html('0');
                        //$('#count-products-in-order').html(result.countScannedProductInOrder);
                        $('#colins-count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                        $.each(result.dataScannedProductByBarcode, function (key, value) {
                            console.info(value);
                            console.info(key);

                            $('#accepted-qty-'+value.rowId).html(value.countValue);
                            $('#row-'+value.rowId).removeClass('alert-danger alert-success');
                            $('#row-'+value.rowId).addClass(value.colorRowClass);


                        });

                        $('#colins-count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                    }

                }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        } else {

        }
    });

    b.on('click','#colins-clear-product-in-box-by-one-bt',function() {

        console.info('TO_DELETE');
        var href = $(this).data('url-value'),
            boxBorderValue = $('#colins-inboundform-box_barcode').val(),
            form = $('#colins-inbound-process-form');

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

                $('#colins-count-product-in-box').html(result.countProductInBox);
                //$('#count-products-in-order').html(result.countScannedProductInOrder);
                $('#colins-count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                $('#colins-count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    b.on('change','#colins-inbound-form-party-number',function() {

        var party_id = $(this).val(),
            inbound = $('#colins-inbound-form-order-number'),
            dataOptions = '';

        if(party_id) {

            $.post('get-in-process-inbound-orders-by-party-id', {'party_id': party_id}).done(function (result) {

                inbound.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                });

                inbound.append(dataOptions);
                inbound.focus().select();

                $('#colins-count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);

            }).fail(function () {
                console.log("server error");
            });

        } else {
            //inboundManager.hideByOrderAll();
        }
    });
});