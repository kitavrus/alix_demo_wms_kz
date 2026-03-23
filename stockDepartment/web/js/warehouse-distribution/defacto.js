/**
 * Created by Ferze on 13.07.2015.
 */
var initCountdown = function (time) {
    var target = $('#countdown'),
        ts = (new Date()).getTime() + (time * 1000);

    target.countdown(ts, function (event) {
        var $this = $(this).html(event.strftime(''
            + '<b>Вы должны собрать этот заказ за: </b>'
            + '<span>%-H</span> час '
            + '<span>%M</span> мин '
            + '<span>%S</span> сек'));
    });
    pauseCountdown();

};

var stopCountdown = function () {
    var target = $('#countdown');
    if (target.text().length > 0) {
        target.countdown('stop');
        target.attr('data-on', 0);
    }

};

var pauseCountdown = function () {
    $('#countdown').countdown('pause');
};

var resumeCountdown = function () {
    $('#countdown').countdown('resume');
};

var setCountdown = function (time) {
    var ts = (new Date()).getTime() + (time * 1000);
    $('#countdown').countdown(ts);
};

function autoPrintAllocatedListHtml(url, orientation, timeout, type) {
    if (typeof jsPrintSetup != 'undefined') {
        //по умолчанию используем портретную ориентацию
        if (typeof orientation == 'undefined') {
            orientation = 0;
        }
        //таймаут
        if (typeof timeout == 'undefined') {
            timeout = 1500;
        }
        //тип: a4 или этикетки (a4 or BL)
        if (typeof type == 'undefined') {
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
        if (type == 'a4') {
            jsPrintSetup.setPrinter(A4p);
            jsPrintSetup.setOption('printBGColors', '1');
            jsPrintSetup.setOption('paperData', '9'); // A5 - 11 A4 - 9
            jsPrintSetup.setOption('paperSizeType', 'A4');
        } else if (type == 'BL') {
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

        $(window.frames['frame-print-alloc-list']).ready(function () {
            setTimeout(function () {
                jsPrintSetup.printWindow(window.frames['frame-print-alloc-list']);
                jsPrintSetup.setSilentPrint(0);
                jsPrintSetup.clearSilentPrint();
                //jsPrintSetup.refreshOptions();
            }, timeout);
        });

    }

    return false;
}
/*S: TODO MOVE TO CODE JS FILE */

/*
 * Set content and show popup
 * @param string Content be should showed
 * */
function ShowPopupContent(toShow) {
    $('#outbound-index-modal').
        modal('show')
        .find('#outbound-index-content')
        .html(toShow);
}

/*
 * Set errors in popup
 * @param string Content be should showed
 * */
function ShowPopupErrors(toShow) {
    $('#outbound-index-modal')
        .find('#outbound-index-errors')
        .html(toShow);
}

/*
 * Hide and clear content in popup
 * */
function HideClearPopup() {
    $('#outbound-index-modal').modal('hide');
    $('#outbound-index-errors').html('');
    $('#outbound-index-content').html('');
}

/*E: TODO MOVE TO CODE JS FILE */

function initClient() {

    var client_id = $('#main-form-client-id').val(),
        e = $('#outbound-form-parent-order-number'),
        dataOptions = '';

    if (client_id) {

        $.post('/wms/defacto/outbound/get-parent-order-number', {'client_id': client_id}).done(function (result) {

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
/*
 * OUTBOUND: START
 * */
$(function () {
    console.info('-init-defacto-outbound-');
    var b = $('body'),
        outboundRoute = '/wms/defacto/outbound/';

    /*
     * Start if change main client drop-down
     * */
    b.on('change', '#main-form-client-id', function () {

        console.log('change #main-form-client-id');

        var me = $(this),
            url = me.data('url');

        window.location.href = url + '?id=' + me.val();
    });

    b.on('change', '#outbound-form-parent-order-number', function (event) {

        var me = $(this),
            clientID = $('#main-form-client-id').val(),
            parentOrderNumber = me.val(),
            url = outboundRoute + 'get-sub-order-grid';

        url = url + '?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber + '&type=1';//+'&_pjax=#pjax-grid-view-order-item-container';

        console.info('-change-outbound-form-parent-order-number');

        $.get(url).done(function (result) {

            $('#grid-orders-container').html(result);

        }).fail(function () {

            console.log("server error");

        });
    });

    b.on('click', '#print-picking-outbound-print-bt', function () {

        var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows'),
            printType = $('#outbound-process-form').data('printtype');

        console.info(keys);
        console.info(keys.length);

        if (keys.length < 1) {

            ShowPopupContent('Нужно выбрать хотябы одну заявку');

        } else {

            setTimeout(function () {

                var me = $("#outbound-form-parent-order-number"),
                    clientID = $('#main-form-client-id').val(),
                    parentOrderNumber = me.val(),
                    url = outboundRoute + 'get-sub-order-grid';

                url = url + '?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber + '&type=1';

                $.get(url).done(function (result) {
                    $('#grid-orders-container').html(result);
                }).fail(function () {
                    console.log("server error");
                });
            }, 2000);

            var href = $(this).data('url-value');

            if (printType == 'pdf') {
                window.location.href = href + '?ids=' + keys.join();
            } else if (printType == 'html') {
                // window.location.href =  href + '?ids='+keys.join();
                autoPrintAllocatedListHtml(href + '?ids=' + keys.join(), '0', 2500);
            }
        }
    });

    b.on('click', '#outbound-print-pick-list-bt', function () {

        var client_id = $('#main-form-client-id').val(),
            url = $(this).data('url'),
            orderUrl = '/wms/defacto/outbound/get-parent-order-number',
            dataOptions = '';

        if (client_id) {
            $.post(url, function (data) {
                $(this).addClass('focus');
                $('#container-outbound-layout').html(data);

                $.post(orderUrl, {'client_id': client_id}).done(function (result) {
                    var e = $('#outbound-form-parent-order-number');
                    e.html('');
                    $.each(result.dataOptions, function (key, value) {
                        dataOptions += '<option value="' + key + '">' + value + '</option>';

                    });
                    e.append(dataOptions);
                    e.focus().select();
                }).fail(function () {

                    console.log("server error");

                });
            });

        }
        $('#buttons-menu').find('.btn').removeClass('focus');

    });

    b.on('click', '#begin-end-picking-list-bt', function () {

        var me = $(this),
            url = me.data('url');
        console.log(url);
        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url, function (data) {
            me.addClass('focus');
            $('#container-outbound-layout').html(data);
        });

    });

    /*
     * S:
     * Begin end picking list form
     *
     * */

    b.on('submit', '#begin-end-pick-list-form', function (e) {
        return false;
    });


    b.on('keyup', "#beginendpicklistform-picking_list_barcode, #beginendpicklistform-employee_barcode", function (e) {

        if (e.which == 13) {

            console.info("-beginendpicklistform-picking_list_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#begin-end-pick-list-form'),
                url = $(this).data('url'),
                messagesListElm = $('#messages-list');
            messagesListBodyElm = $('#messages-list-body');

            errorBase.setForm(form);
            me.focus().select();

            messagesListElm.addClass('hidden');
            messagesListElm.removeClass('alert-info alert-success');
            messagesListBodyElm.html('');

            $.post(url, form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if (result.messagesInfo.length >= 1) {
                        messagesListBodyElm.html(result.messagesInfo);
                        messagesListElm.addClass('alert-info');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val(result.picking_list_barcode);
                        $('#beginendpicklistform-employee_barcode').val(result.employee_barcode);
                    }

                    if (result.messagesSuccess.length >= 1) {
                        messagesListBodyElm.html(result.messagesSuccess);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val('');
                        $('#beginendpicklistform-employee_barcode').val('');
                    }

                    if (me.attr('id') == 'beginendpicklistform-employee_barcode') {
                        $('#beginendpicklistform-picking_list_barcode').focus().select();
                    } else {
                        $('#beginendpicklistform-employee_barcode').focus().select();
                    }

                    if (result.step == 'end') {
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


    b.on('keyup', "#beginendpicklistform-employee_barcode_NOT_USED", function (e) {

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

            $.post('/outbound/default/begin-end-picking-handler', form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if (result.messagesInfo.length >= 1) {
                        messagesListBodyElm.html(result.messagesInfo);
                        messagesListElm.addClass('alert-info');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val(result.picking_list_barcode);
                        $('#beginendpicklistform-employee_barcode').val(result.employee_barcode);
                        $('#beginendpicklistform-picking_list_id').val(result.picking_list_id);
                        $('#beginendpicklistform-employee_id').val(result.employee_id);
                    }

                    if (result.messagesSuccess.length >= 1) {
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


    b.on('change', '#grid-view-order-items input[type="checkbox"]', function () {

        var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows');

        console.info(keys);

        console.info('-change->checkbox');
        var sum = 0;
        $.each(keys, function (key, value) {
            sum += parseInt($('#allocated-qty-cell-' + value).text());
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
    b.on('submit', '#scanning-process-form', function (e) {
        return false;
    });

    b.on('click', '#scanning-process-bt', function () {

        var me = $(this),
            url = me.data('url');


        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url, function (data) {

            me.addClass('focus');

            $('#container-outbound-layout').html(data);
        });
    });

    b.on('click', '#inbound-process-bt', function () {

        var me = $(this),
            url = me.data('url');

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url, function (data) {

            me.addClass('focus');

            $('#container-defacto-outbound-layout').html(data);
            inboundManager().onLoad();
            inboundManager().loadParentOrder();
        });
    });

    /*
     * Scanning employees barcode OR picking list barcode
     *
     * */

    b.on('click', "#scanningform-product_barcode,#scanningform-box_barcode,#scanningform-box_kg", function (e) {
        $(this).focus().select();
    });

    // EMPLOYEE BARCODE
    b.on('keyup', "#scanningform-employee_barcode", function (e) {

        if (e.which == 13) {
            var me = $(this),
                url = me.data('url'),
                form = $('#scanning-form');

            /*url = '<?= Url::toRoute('employee-barcode-scanning-handler'); ?>',*/

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {

//                console.info(result);

                if (result.success == 0) {
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
    b.on('keyup', "#scanningform-picking_list_barcode", function (e) {

        var me = $(this);

        if (e.which == 13) {

            var url = me.data('url'),
                form = $('#scanning-form');

            errorBase.setForm(form);

            if ($(this).val() == 'CHANGEBOX') {
                me.val('');
                $('#scanningform-box_barcode').focus().select();
                errorBase.hidden();
                e.preventDefault();
                return false;
            }

            $.post(url, form.serialize(), function (result) {

//                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

//                    console.info('OK->OK-> scanning-form-picking-list-barcode');

                    if ($('#' + me.val()).html() == undefined) {
                        var newElem = $('#messages-scanning-list').clone(false);
                        newElem.attr('id', me.val());
                        newElem.append(me.val()+" / "+result.storeName);
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
                    $('#order-exp-accept').html(result.exp_qty + ' / ' + result.accept_qty);
                    $('#countdown').attr('data-timer', result.cdTimer);
                    if (result.cdTimer != 0) {
                        console.info('-init timer-');
                        initCountdown(result.cdTimer);
                    }

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // BOX BARCODE
    b.on('keyup', "#scanningform-box_barcode", function (e) {

        if (e.which == 13) {

//            console.info("-scanning-form-box-barcode-");
//            console.info("Value : " + $(this).val());

            var me = $(this),
                url = me.data('url'),
                form = $('#scanning-form');


            /*url = '<?= Url::toRoute('box-barcode-scanning-handler'); ?>',*/

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {

//                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

//                    console.info('OK->OK-> scanning-form-box-barcode');

                    $('#scanningform-product_barcode').focus().select();
                    $('#scanningform-product_barcode').val('');
                    $('#count-product-in-box').html(result.countInBox);
                    $('#outbound-item-body').html(result.stockArrayByPL);

                    if(result.clientBox != '') {
                        me.val(result.clientBox);
                    }

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // PRODUCT BARCODE
    b.on('keyup', "#scanningform-product_barcode", function (e) {

        var me = $(this),
            countdown = $('#countdown');

        if (e.which == 13) {

//            console.info("-scanning-form-product-barcode-");
//            console.info("Value : " + $(this).val());

            if ($(this).val() == 'CHANGEBOX_NOT_USED') {
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

            if (countdown.attr('data-on') == 0) {
                countdown.attr('data-on', 1);
                setCountdown(countdown.attr('data-timer'));
            }
            $.post(url, form.serialize(), function (result) {

//                console.info(result);

                if (result.change_box == 'ok') {
                    me.val('');
                    $('#scanningform-box_barcode').focus().select();
                    e.preventDefault();
                    return false;
                }


                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

//                    console.info('OK->OK-> scanning-form-product-barcode');

                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#count-product-in-box').html(result.countInBox);
                    $('#order-exp-accept').html(result.exp_qty + ' / ' + result.accept_qty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();
    });

    // BOX KG
    b.on('keyup', "#scanningform-box_kg", function (e) {

        var me = $(this);

        if (e.which == 13) {

            console.info("-scanning-form-box-kg-");
//            console.info("Value : " + $(this).val());

            var url = me.data('url'),
                form = $('#scanning-form');

            errorBase.setForm(form);
            me.focus().select();
            errorBase.hidden();

            $.post(url, form.serialize(), function (result) {

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#scanningform-box_barcode').focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();
        return false;
    });

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


    b.on('click', '#clear-box-scanning-outbound-bt', function () {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#scanningform-box_barcode').val(),
            form = $('#scanning-form');

//        console.info(boxBorderValue);

        errorBase.setForm(form);

        if (confirm('Вы действительно хотите очистить короб')) {

            $.post(href, form.serialize(), function (result) {

                errorBase.hidden();

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#count-product-in-box').html('0');
                    $('#order-exp-accept').html(result.exp_qty + ' / ' + result.accept_qty);
                } else {
                    errorBase.hidden();
                    $('#outbound-item-body').html(result.stockArrayByPL);
                    $('#count-product-in-box').html('0');
                    $('#order-exp-accept').html(result.exp_qty + ' / ' + result.accept_qty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        } else {

        }
    });

    b.on('click', '#clear-product-in-box-by-one-scanning-outbound-bt', function () {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#scanningform-box_barcode').val(),
            form = $('#scanning-form');

//        console.info(boxBorderValue);

        errorBase.setForm(form);

        $.post(href, form.serialize(), function (result) {

            errorBase.hidden();


            if (result.success == 0) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();

                $('#count-product-in-box').html(result.countInBox);
                $('#outbound-item-body').html(result.stockArrayByPL);
                $('#order-exp-accept').html(result.exp_qty + ' / ' + result.accept_qty);

            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    /*
     * Click on List differences
     * */
    b.on('click', '#scanning-form-differences-list-bt', function () {
        var href = $(this).data('url'),
            printType = $('#scanning-form').data('printtype');
        if (printType == 'pdf') {
            window.location.href = href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val();
        } else if (printType == 'html') {
            autoPrintAllocatedListHtml(href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val());
        }
    });


    b.on('click', '#scanning-form-print-box-label-bt', function () {

        if (confirm('Вы действительно хотите распечатать этикетки')) {
            stopCountdown();
            var plids = $('#scanningform-picking_list_barcode_scanned').val(),
                href = $(this).data('url'),
                hrefValidate = $(this).data('validate-url');

            $.post(hrefValidate, {'plids': plids}, function (result) {

                if (result.runNext == 'ok') {

                    $('#scanningform-employee_barcode').val('');
                    $('#alert-picking-list').html('');
                    $('#scanningform-picking_list_barcode').val('');
                    $('#count-product-in-box').html('');
                    $('#scanningform-box_barcode').val('');
                    $('#scanningform-product_barcode').val('');
                    $('#scanningform-picking_list_barcode_scanned').val('');
                    $('#outbound-item-body').html('');
                    $('#order-exp-accept').html('0/0');


                    window.location.href = href + '?plids=' + plids;
                } else {
                    alert(result.message);
                }
            });
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
    b.on('click', '#download-outbound-order-api-bt', function () {

        /* var url = '<?= Url::toRoute('download-file-de-facto-api'); ?>',*/
        var me = $(this),
            url = me.data('url');


        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url, function (data) {

            me.addClass('focus');

            $('#container-defacto-outbound-layout').html(data);
            initClientApi();
        });

    });

    b.on('change', '#download-confirm-for-api-client-id', function () {

        console.info('-download-confirm-for-api-client-id');

        var client_id = $(this).val(),
            e = $('#download-confirm-for-api-order-number'),
            dataOptions = '';

        if (client_id) {

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

    b.on('change', '#download-confirm-for-api-order-number', function (event) {
        var me = $(this),
            clientID = $('#download-confirm-for-api-form').val(),
            parentOrderNumber = me.val(),
            url = outboundRoute + 'get-sub-order-grid';

        if (parentOrderNumber == '') {
            $('#download-confirm-for-api-grid-orders-container').html('');
            return false;
        }

        url = url + '?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber + '&type=2';//+'&_pjax=#pjax-grid-view-order-item-container';

        console.info('-change-outbound-form-parent-order-number');

        $.get(url).done(function (result) {

            $('#download-confirm-for-api-grid-orders-container').html(result);

        }).fail(function () {

            console.log("server error");

        });
    });

    b.on('click', '#download-confirm-outbound-print-bt', function () {

        var keys = $('#grid-view-order-items').yiiGridView('getSelectedRows'),
            href = $(this).data('url-value');

        setTimeout(
            function () {

                var me = $("#download-confirm-for-api-order-number"),
                    clientID = $('#download-confirm-for-api-form').val(),
                    parentOrderNumber = me.val(),
                    url = outboundRoute + 'get-sub-order-grid';

                if (parentOrderNumber == '') {
                    $('#download-confirm-for-api-grid-orders-container').html('');
                    return false;
                }

                url = url + '?OutboundOrderSearch[client_id]=' + clientID + '&OutboundOrderSearch[parent_order_number]=' + parentOrderNumber + '&type=2';//+'&_pjax=#pjax-grid-view-order-item-container';

                console.info('-change-outbound-form-parent-order-number');

                $.get(url).done(function (result) {

                    $('#download-confirm-for-api-grid-orders-container').html(result);

                }).fail(function () {

                    console.log("server error");

                });

            }, 2000
        );

        window.location.href = href + '?ids=' + keys.join();

//        }
    });

    /////////////////////////////
    b.on('click', '#upload-outbound-order-api-bt', function () {


        var me = $(this),
            url = me.data('url');

        /* '<?= Url::toRoute('upload-file-de-facto-api'); ?>' */

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url, function (data) {

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
    b.on('click', '.outbound-order-complete-bt', function () {

        if (confirm('Вы действительно хотите подтвердить выполнение заказа?')) {

            var me = $(this),
                url = me.data('url');

            $.post(url, function (data) {

                setTimeout(
                    function () {
                        var form = $("#outbound-orders-grid-search-form"),
                            url = outboundRoute + 'defacto-outbound-grid';
                        url = url + '?' + form.serialize();

                        window.location.href = url;

                    }, 1000
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
     * Start%
     * Resend API
     *
     * */
    b.on('click', '.outbound-order-resend-api-bt', function () {

        if (confirm('Вы действительно хотите повторно отправить данные по API?')) {

            var me = $(this),
                url = me.data('url');

            $.post(url, function (data) {

                setTimeout(
                    function () {
                        var form = $("#outbound-orders-grid-search-form"),
                            url = outboundRoute + 'defacto-outbound-grid';
                        url = url + '?' + form.serialize();

                        window.location.href = url;

                    }, 1000
                );

            });
        }
    });

    /*
     * End
     * Resend API
     *
     * */

});
/*
 * OUTBOUND: END
 * */


/*
 * INBOUND: START
 * */
var inboundManager = function () {

    var fld_box_barcode = $('#inbound-form-box_barcode'),
        fld_product_barcode = $('#inbound-form-product_barcode'),
        fld_party_number = $('#inbound-form-party-number'),
        fld_order_number = $('#inbound-form-order-number'),
        list_differences_bt = $('#inbound-list-differences-bt'),
        list_unallocated_bt = $('#inbound-unallocated-list-bt'),
        inbound_clear_zero_qty_bt = $('#inbound-clear-zero-qty-bt'),
        inbound_accepted_list_bt = $('#inbound-accepted-list-bt'),
        e_items = $('#inbound-items'),
        accept_bt = $('#inbound-accept-bt'),
        actionRoute = '/wms/defacto/inbound/',
        client_id = $('#main-form-client-id').val(),
        currentAction = [],
        currentActionKey = 'CURRENT-ACTION-KEY',
        me;

    return me = {
        'onLoad': function () {
            fld_order_number.val('');
            fld_box_barcode.val('');
            fld_product_barcode.val('');
            fld_party_number.val('');
            list_differences_bt.hide();
            list_unallocated_bt.hide();
            e_items.hide();
            accept_bt.hide();
            inbound_clear_zero_qty_bt.hide();
            inbound_accepted_list_bt.hide();

        },
        'showByOrder': function () {
            e_items.show();
            list_differences_bt.show();
            list_unallocated_bt.show();
            accept_bt.show();
            inbound_clear_zero_qty_bt.show();
            inbound_accepted_list_bt.show();
        },
        'hideByOrderAll': function () {

            fld_order_number.val('');
            fld_box_barcode.val('');
            fld_product_barcode.val('');
            fld_party_number.val('');
            list_differences_bt.hide();
            list_unallocated_bt.hide();
            e_items.hide();
            accept_bt.hide();
            inbound_clear_zero_qty_bt.hide();
            inbound_accepted_list_bt.hide();
        },
        'hideByOrder': function () {

            fld_box_barcode.val('');
            fld_product_barcode.val('');

            list_differences_bt.hide();
            list_unallocated_bt.hide();
            e_items.hide();
            accept_bt.hide();
            inbound_clear_zero_qty_bt.hide();
            inbound_accepted_list_bt.hide();
        },

        'loadParentOrder': function () {

            var partyInbound = $('#inbound-form-party-number'),
                orderInbound = $('#inbound-form-order-number'),
                dataOptions = '';

            if (client_id) {

                $.post(actionRoute + 'get-in-process-inbound-orders-by-client-id', {'client_id': client_id}).done(function (result) {
                    $('#inbound-form-client-id').val(client_id);
                    $.each(result.dataOptions, function (key, value) {
                        dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                    });
                    if (result.type == 'inbound') {
                        orderInbound.html('');
                        orderInbound.append(dataOptions);
                        orderInbound.focus().select();
                    } else if (result.type == 'party-inbound') {
                        partyInbound.html('');
                        partyInbound.append(dataOptions);
                        partyInbound.focus().select();
                    }


                }).fail(function () {
                    console.log("server error");
                });
            }
        },
        'handlerConfirmFormOnChangeClientID': function (event) {

            var client_id = $(this).val(),
                e = $('#download-for-api-order-number'),
                dataOptions = '';

            if (client_id) {

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

            var inbound_id = $(this).val(),
                url = $(this).data('url');

            if (inbound_id) {

                $.post(url,
                    {'inbound_id': $(this).val()}
                ).done(function (result) {

                        $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);
                        $('#inbound-item-body').html(result.items);
                        //$('#countdown').attr('data-timer', result.cdTimer);
                        //if(result.cdTimer != 0){
                        // console.info ('-init timer-');
                        // initCountdown(result.cdTimer);
                        //}

                        $('#inbound-form-box_barcode').focus().select();

                        inboundManager().showByOrder();

                    }).fail(function () {
                        console.log("server error");
                    });

            } else {
                inboundManager().hideByOrder();
            }
        },
        'handlerOnClickAcceptBT': function (event) {

            var client_idValue = $('#main-form-client-id').val(),
                order_numberValue = $('#inbound-form-order-number').val(),
                messages_processText = $('#inbound-messages-process'),
                form = $('#inbound-process-form'),
                url = $(this).data('url');

            if(currentAction[currentActionKey] == 1) {
                return;
            }

            currentAction[currentActionKey] = 1;

            if (confirm('Вы уверены, что хотите закрыть накладную')) {

                console.info(client_idValue);
                console.info(order_numberValue);

                if (client_idValue && order_numberValue) {

                    stopCountdown();

                    $(messages_processText).html(' Подождите, идет обработка, не закрывайте браузер или вкладку ...');
                    var data = 'InboundForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();
                    $.post(url, data).done(function (result) {
                        currentAction[currentActionKey] = 0;
                        /* TODO Потом сделать вывод сообщений через bootstrap Modal   */

                        var alertMessage = '';

                        $.each(result.messages, function (key, value) {
                            if (value.length) {
                                alertMessage += value + '\n';
                            }
                        });

                        alert(alertMessage);

                        $('#inbound-form-product_barcode').val('');
                        $('#inbound-form-box_barcode').val('');
                        $('#count-products-in-order').html('0/0');
                        $('#count-product-in-box').html('0');
                        $('#inbound-item-body').html('');

                        $(messages_processText).html(' [ ' + 'Данные успешно загружены ] ').fadeOut(5000, function () {
                            $(messages_processText).html('');
                        });

                        //$("#inbound-form-order-number option:selected").remove();

                        var client_id = $('#main-form-client-id').val(),
                            inbound = $('#inbound-form-order-number'),
                            partyInbound = $('#inbound-form-party-number'),
                            a = {},
                            dataOptions = '';

                        inbound.html('');
                        partyInbound.html('');
                        $('#count-products-in-party').html('0/0');

                        if (client_id) {

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
                            me.loadParentOrder();

                        } else {
                            me.hideByOrderAll();
                        }

                    }).fail(function () {
                        console.log("server error");
                        currentAction[currentActionKey] = 0;
                    });

                } else {
                    //inboundModel.hideByOrderAll();
                }

            }
        }
    }

};


$(function () {

    //return false;
    //console.info('inbound-process');

    var inboundModel = new inboundManager(),
        b = $('body');
        inProcess = [];
        inProcessKey = 'PROCESS-KEY';

    //inboundModel.onLoad();
    console.info('-init-defacto-inbound-');
    //console.info(inboundModel);

    $('#allocationlistform-box_barcode').focus().select();
    b.on('click', '#confirm-upload-bt', function () {
        if (confirm('Вы точно хотите загрузить данные в систему')) {
            $('#loading-modal').modal('show');
            $.post($(this).data('url'), {}, function (result) {
            }, 'html')
        }
    });

    $('#inbound-process-form').on('submit', function (e) {
        return false;
    });


    b.on('click', '#inbound-form-box_barcode, #inbound-form-product_barcode,#inbound-form-client_box_barcode', function () {
        $(this).focus().select();
    });

    b.on('change', '#inbound-form-order-number', inboundModel.handlerOnChangeOrderNumber);
    b.one('click', '#inbound-accept-bt', inboundModel.handlerOnClickAcceptBT);

    /*
     * Scan box barcode
     * */
    b.on('keyup', "#inbound-form-box_barcode", function (e) {
        if (e.which == 13) {

            console.info("-inbound-form-box_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#inbound-process-form'),
                url = $(this).data('url');

            errorBase.setForm(form);
            me.focus().select();
            var data = 'InboundForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();
            $.post(url, data, function (result) {
                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    //$("#inbound-form-product_barcode").focus().select();
                    $("#inbound-form-client_box_barcode").focus().select();
                    $('#count-product-in-box').html(result.countProductInBox);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    /*
     * Scan box barcode
     * */
    b.on('keyup', "#inbound-form-client_box_barcode", function (e) {

        if (e.which == 13) {

            console.info("-inbound-form-client_box_barcode-");
            console.info("Value : " + $(this).val());
            $("#qty-lot-in-client-box-inbound").html('0');
            var me = $(this),
                form = $('#inbound-process-form'),
                url = $(this).data('url');

            errorBase.setForm(form);
            me.focus().select();
            var data = 'InboundForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();
            $.post(url, data, function (result) {
                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else if (result.statusAccepted == 'y') {
                    //} else if(result.qtyLotInClientBox != -1) {
                    errorBase.hidden();
                    $("#qty-lot-in-client-box-inbound").html(result.qtyLotInClientBox);
                    $("#inbound-form-client_box_barcode").select();
                    if (result.statusAccepted == 'y') {
                        $("#inbound-form-product_barcode").val(result.lotBarcodeInClientBox);

                        var e = $.Event("keyup", {which: 13, backToClientBox: "n"});
                        for (var i = 0; i < result.qtyLotInClientBox; i++) {
                            setTimeout(function () {
                                $("#inbound-form-product_barcode").trigger(e);
                            }, 100);
                        }
                    }
                } else {
                    errorBase.hidden();
                    $("#qty-lot-in-client-box-inbound").html(result.qtyLotInClientBox);
                    $("#inbound-form-product_barcode").focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    /*
     * Scan product barcode in box
     * */
    b.on('keyup', "#inbound-form-product_barcode", function (e) {
        /*
         * TODO Добавить проверку
         * TODO 1 ? Существует ли этот товар у нас на складе
         * TODO 2 + Существует ли этот товар у этого клиента
         */
        if (e.which == 13) {

            var backToClientBox = 'n';
            if (e.hasOwnProperty('backToClientBox')) {
                backToClientBox = 'y';
            }

            var me = $(this),
            //countdown = $('#countdown'),
                url = $(this).data('url');
            console.info("-inbound-process-steps-product-barcode-");
            console.info("Value : " + me.val());

            me.focus().select();

            if (me.val() == 'CHANGEBOX') {
                $('#inbound-form-box_barcode').focus().select();
                me.val('');
                return true;
            }

            //if(countdown.attr('data-on') == 0){
            //    countdown.attr('data-on', 1);
            //    setCountdown(countdown.attr('data-timer'));
            //}

            var form = $('#inbound-process-form');
            var data = 'InboundForm[client_id]=' + $('#main-form-client-id').val() + "&" + form.serialize();
            errorBase.setForm(form);

            $.post(url, data, function (result) {
                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#count-product-in-box').html(result.countProductInBox);
                    $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);


                    //$('#accepted-qty-' + result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                    //$('#row-' + result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                    //$('#row-' + result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                    $('#count-products-in-party').html(result.acceptedQtyParty + ' / ' + result.expectedQtyParty);

                    if (backToClientBox == 'y') {
                        //$('#inbound-form-client_box_barcode').focus().select();
                        $('#inbound-form-box_barcode').focus().select();
                    }

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
    b.on('click', '#inbound-list-differences-bt', function () {
        var href = $(this).data('url'),
            printType = $('#inbound-process-form').data('printtype');
        if (printType == 'pdf') {
            window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
        } else if (printType == 'html') {
            //window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
            autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#inbound-form-order-number').val(), '0', 2500);
        }

    });

    /*
     * Click on Unallocated list
     * */
    b.on('click', '#inbound-unallocated-list-bt', function () {
        var href = $(this).data('url'),
            printType = $('#inbound-process-form').data('printtype');
        if (printType == 'pdf') {
            window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
        } else if (printType == 'html') {
            //window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
            autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#inbound-form-order-number').val());
        }

    });
    b.on('click', '#inbound-accepted-list-bt', function () {
        var href = $(this).data('url'),
            printType = $('#inbound-process-form').data('printtype');
        //alert("xxxxxxxxx");
        if (printType == 'pdf') {
            window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
        } else if (printType == 'html') {
            //window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
            autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#inbound-form-order-number').val());
        }

    });

    b.on('click', '#qty-accepted-box-bt', function () {

        var href = $(this).data('url'),
            form = $('#inbound-process-form');

        errorBase.setForm(form);

        $.post(href, form.serialize(), function (result) {

            errorBase.hidden();

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                $('#show-message').show().html("Кол-во коробов: " + result.qtyAcceptedBox).fadeOut(5000, function () {
                    $('#show-message').html('');
                });

            }
            console.info(result);
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });

    });

    b.on('click', '#items-in-order-bt', function () {

        var href = $(this).data('url'),
            form = $('#inbound-process-form');

        errorBase.setForm(form);

        $.post(href, form.serialize(), function (result) {

            errorBase.hidden();

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                $('#inbound-item-body').html(result.items);

            }
            console.info(result);
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });

    });

    //$("#print-register-np-bt").attr({target:"_blank",href:data.redirectToURL,'np-statement-id':data.npStatementID});
    //var a = window.open(data.redirectToURL, "_blank", "");
    //a.blur();

    b.on('click', '#clear-box-bt', function () {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#inbound-form-box_barcode').val(),
            form = $('#inbound-process-form');

        console.info(boxBorderValue);

        errorBase.setForm(form);

        if (confirm('Вы действительно хотите очистить короб')) {
            $.post(href, form.serialize(), function (result) {

                errorBase.hidden();

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();
                    $('#count-product-in-box').html('0');
                    //$('#count-products-in-order').html(result.countScannedProductInOrder);
                    $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);

                    //$.each(result.dataScannedProductByBarcode, function (key, value) {
                    //    console.info(value);
                    //    console.info(key);
                    //
                    //    $('#accepted-qty-' + value.rowId).html(value.countValue);
                    //    $('#row-' + value.rowId).removeClass('alert-danger alert-success');
                    //    $('#row-' + value.rowId).addClass(value.colorRowClass);
                    //
                    //
                    //});

                    $('#count-products-in-party').html(result.acceptedQtyParty + ' / ' + result.expectedQtyParty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        } else {

        }
    });

    b.on('click', '#clear-product-in-box-by-one-bt', function () {

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

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();

                console.info(result.countProductInBox);
                console.info(result.countScannedProductInOrder);
                //console.info('#accepted-qty-' + result.dataScannedProductByBarcode.rowId);
                //console.info('#row-' + result.dataScannedProductByBarcode.rowId);

                $('#count-product-in-box').html(result.countProductInBox);
                //$('#count-products-in-order').html(result.countScannedProductInOrder);
                $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);

                //$('#accepted-qty-' + result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                //$('#row-' + result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                //$('#row-' + result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                $('#count-products-in-party').html(result.acceptedQtyParty + ' / ' + result.expectedQtyParty);
            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    b.on('change', '#inbound-form-party-number', function () {

        var party_id = $(this).val(),
            inbound = $('#inbound-form-order-number'),
            dataOptions = '';

        if (party_id) {

            $.post('/inbound/default/get-in-process-inbound-orders-by-party-id', {'party_id': party_id}).done(function (result) {

                inbound.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                });

                inbound.append(dataOptions);
                inbound.focus().select();

                $('#count-products-in-party').html(result.acceptedQtyParty + ' / ' + result.expectedQtyParty);

            }).fail(function () {
                console.log("server error");
            });

        } else {
            //inboundManager.hideByOrderAll();
        }
    });

    console.info('-init-defacto-api-');

    $('#get-inbound-order-form, #get-cross-dock-order-form, #get-outbound-order-form').on('submit', function (e) {
        return false;
    });


    b.on('click', '#get-inbound-order-api-de-facto-bt, #get-outbound-order-api-de-facto-bt, #get-returns-order-api-de-facto-bt, #get-cross-dock-order-api-de-facto-bt', function () {

        if(inProcess[inProcessKey] == 1) {
            return;
        }

        var url = $(this).data('url'),
            loading = $(this).find('.loading');

        inProcess[inProcessKey] = 1;

        loading.text('Подождите, загружается ....').css({color: 'red'}).show();

        $.post(url, function (data) {
            $('#container-api-de-facto-layout').html(data);
            loading.text('Успешно загружено!').fadeOut(5000);
            inProcess[inProcessKey] = 0;
        }).fail(function () {
            loading.text('Во время загрузки произошла ошибка :(').fadeOut(9000);
            inProcess[inProcessKey] = 0;
        });

    });

//S: #INTBOUND
    b.on('click', '#get-inbound-order-submit-bt', function () {

        var url = $(this).data('url'),
            me = $(this),
            form = $('#get-inbound-order-form');

        errorBase.setForm(form);
        errorBase.hidden();

        $('#get-inbound-order-button-message').html(' Пожалуйста подождите, идет обработка запроса...').show();

        $.post(url, form.serialize()).done(function (result) {

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
                $('#get-inbound-order-button-message').html('');
            } else {
                errorBase.hidden();

                $('#get-inbound-order-button-message').html(' Данные успешно загружены').fadeOut(5000);
                $('#apidefactoform-invoice').val();

                $('#inbound-order-uploaded-grid').html(result.gridData);
            }

        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('click', '#yes-upload-inbound-data-bt', function () {
        console.info('click yes-upload-inbound-data-bt');

        var clientId = $(this).data('client-id'),
            url = $(this).data('url'),
            uniqueKey = $(this).data('unique-key');

        if (clientId && uniqueKey) {

            $('#show-status-message').html(' [ Подождите ... ] ').show();

            $.post(url, {'client_id': clientId, 'unique_key': uniqueKey})
                .done(function (result) {

                    $('#show-status-message').html(' [ ' + 'Данные успешно загружены ] ').fadeOut(5000);
                    $('#grid-view-inbound-order-items').fadeOut(7000);
                    $('#alert-message-inbound').fadeOut(7000);

                })
                .fail(function () {
                    console.log("server error");
                });
        }
    });


    function updateOrderItemList(inbound_id, url) {

        if (inbound_id) {
            $.post(url,
                {'inbound_id': inbound_id}
            ).done(function (result) {
                    $('#count-products-in-order').html(result.countScannedProductInOrder + ' / ' + result.expected_qty);
                    $('#inbound-item-body').html(result.items);
                }).fail(function () {
                    console.log("server error");
                });
        }

        return 0;
    }

    b.one('click', '#inbound-clear-zero-qty-bt', function () {

        console.info('click inbound-clear-zero-qty-bt');

        if(inProcess[inProcessKey] == 1) {
            return;
        }
        inProcess[inProcessKey] = 1;

        var url = $(this).data('url'),
            inboundId = $("#inbound-form-order-number").val(),
            updateOrderItemListUrl = $("#inbound-form-order-number").data('url');

        if (inboundId) {

            $('#inbound-messages-clear-zero-qty').html(' [ Подождите ... ] ').show();

            $.post(url, {'inbound_id': inboundId})
                .done(function (result) {
                    inProcess[inProcessKey] = 0;
                    $('#inbound-messages-clear-zero-qty').html(' [ ' + 'Нулевые успешно удалены ] ').fadeOut(5000);

                    updateOrderItemList(inboundId, updateOrderItemListUrl);

                    $('#inbound-accept-bt').removeClass('hidden');
                })
                .fail(function () {
                    console.log("server error");
                    inProcess[inProcessKey] = 0;
                });
        }
    });

//S: #OUTBOUND
    b.on('click', '#get-outbound-order-submit-bt', function () {

        console.info('click #get-outbound-order-submit-bt');

        var url = $(this).data('url'),
            me = $(this),
            form = $('#get-outbound-order-form');

        errorBase.setForm(form);
        errorBase.hidden();

        $('#get-outbound-order-button-message').html(' Пожалуйста подождите, идет обработка запроса...').show();

        $.post(url, form.serialize()).done(function (result) {

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
                $('#get-outbound-order-button-message').html('');
            } else {
                errorBase.hidden();

                $('#get-outbound-order-button-message').html(' Данные успешно загружены').fadeOut(5000);
                $('#apidefactoform-invoice').val('');

                $('#outbound-order-uploaded-grid').html(result.gridData);
            }

        }, 'json').fail(function () {
            console.log("server error");
        });

    });

    b.on('click', '#upload-outbound-order-load-bt', function () {

        console.log('click #upload-outbound-order-load-bt');
        console.log($(this).data('url'));
        console.log($(this).data('unique-key'));
        console.log($(this).data('client-id'));

        var url = $(this).data('url'),
            uniqueKey = $(this).data('unique-key'),
            clientId = $(this).data('client-id');

        $('#show-status-message').html(' [ Подождите ... ] ').show();

        $.post(url, {'unique-key': uniqueKey, 'client-id': clientId}, function () {

            $('#show-status-message').html(' [ ' + 'Данные успешно загружены ] ').fadeOut(5000);

            setTimeout(function () {
                window.location.href = '/wms/defacto/api-de-facto/index';
            }, 5000)

        });

    });

//S: #CROSS-DOCK
    console.info('-init defacto cross-dock-');

    b.on('submit', '#cross-dock-confirm-form', function (event) {
        event.preventDefault();
    });

    b.on('click', '#print-cross-dock-list-bt', function () {

        var url = $(this).data('url'),
            container = b.find("div.ajax-container");

        $.post(url).done(function (result) {
            container.html(result);
            $('#cross-dock-form-client-id').val($('#main-form-client-id').val()).trigger('change');

        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('click', '#confirm-cross-dock-list-bt', function () {

        var url = $(this).data('url'),
            container = b.find("div.ajax-container");

        $.post(url).done(function (result) {
            container.html(result);
            $('#confirmcrossdockform-cross_dock_barcode').focus();
        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('submit', '#cross-dock-add-new-item-form', function (event) {
        event.preventDefault();
    });

    b.on('click', '#cross-dock-add-new-item-bt', function () {
        var url = $(this).attr('data-url'),
            form = $('#cross-dock-add-new-item-form');

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(), function (result) {
            if (result.success == 0) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                window.location.href = '/wms/defacto/cross-dock/index';
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });


    b.on('click', '#add-new-item-cross-dock-bt', function () {

        var url = $(this).data('url'),
            container = b.find("div.ajax-container");

        $.post(url).done(function (result) {
            container.html(result);
            $('#cross-dock-form-client-id').val($('#main-form-client-id').val()).trigger('change');
        }).fail(function () {
            console.log("server error");
        });
    });

    b.on('click', '#create-cross-dock-form-bt', function () {

        var url = $(this).data('url'),
            container = b.find("div.ajax-container");

        $.post(url).done(function (result) {
            container.html(result);
            $('#cross-dock-form-client-id').val($('#main-form-client-id').val()).trigger('change');
        }).fail(function () {
            console.log("server error");
        });
    });

    b.on('click', '#save-create-cross-dock-form-bt', function () {
        if (confirm('Вы точно хотите создать новый кросс-док?')) {
            var data = [];
            var url = $(this).attr('data-url'),
                form = $('#create-cross-dock-form');
            $('#loading-modal-content').html('Идет обработка данных, пожалуйста подождите...');
            $('#loading-modal').modal('show');
            console.log(data);
            console.log(form.serialize());

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(), function (result) {
                if (result.success == '1') {
                    $('#loading-modal-content').html(result.message);

                    setTimeout(function () {
                        window.location.href = '/wms/defacto/cross-dock/index';
                    }, 3000);

                } else if (result.success == '0') {
                    $('#loading-modal').modal('hide');
                    errorBase.eachShow(result.errors);
                    console.log("server error");
                }
            }, 'json').fail(function () {
                $('#loading-modal').modal('hide');
                console.log("server error");
            });
        }

        return false;
    });

    b.on('click', '#preview-cross-dock-form-bt', function () {

        var url = $(this).attr('data-url'),
            form = $('#create-cross-dock-form');

        form.attr('action', url);
        form.submit();

        return false;
    });

    b.on('keyup', "#confirmcrossdockform-cross_dock_barcode", function (e) {
        console.log(e);
        if (e.which == 13) {
            var url = $(this).attr('data-url'),
                me = $(this),
                errorOut = $("p.help-block-error"),
                form = $('#cross-dock-confirm-form'),
                grid = $('#result-table-body');

            errorOut.html('');

            $.post(url, form.serialize(), function (result) {

                if (result.success == 0) {
                    errorOut.css('color', 'red').html(result.errors);
                    me.focus().select();
                } else {
                    grid.html(result.subTable);
                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    b.on('click', '#get-cross-dock-order-submit-bt', function () {

        var url = $(this).data('url'),
            me = $(this),
            form = $('#get-cross-dock-order-form');

        errorBase.setForm(form);
        errorBase.hidden();

        $('#get-cross-dock-order-button-message').html(' Пожалуйста подождите, идет обработка запроса...').show();

        $.post(url, form.serialize()).done(function (result) {

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
                $('#get-cross-dock-order-button-message').html('');
            } else {
                errorBase.hidden();

                $('#get-cross-dock-order-button-message').html(' Данные успешно загружены').fadeOut(5000);
                $('#apidefactoform-invoice').val();

                $('#cross-dock-order-uploaded-grid').html(result.gridData);
            }

        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('click', '#yes-upload-cross-dock-data-bt', function () {
        console.info('click yes-upload-cross-dock-data-bt');

        var clientId = $(this).data('client-id'),
            confirmUrl = $(this).data('url'),
            uniqueKey = $(this).data('unique-key');

        if (clientId && uniqueKey) {

            $('#show-status-message').html(' [ Подождите ... ] ').show();

            $.post(confirmUrl, {'client_id': clientId, 'unique_key': uniqueKey})
                .done(function (result) {

                    $('#show-status-message').html(' [ ' + 'Данные успешно загружены ] ').fadeOut(5000);
                    $('#grid-view-cross-dock-order-items').fadeOut(7000);
                    $('#alert-message-cross-dock').fadeOut(7000);

                })
                .fail(function () {
                    console.log("server error");
                });
        }
    });


    b.on('change', '#cross-dock-form-client-id', function () {
        var client_id = $(this).val(),
            e = $('#cross-dock-form-order-number'),
            dataOptions = '';

        if (client_id) {

            $.post('get-cross-dock-orders-by-client-id', {'client_id': client_id}).done(function (result) {

                e.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                });

                e.append(dataOptions);
                e.focus().select();
                $("#cross-dock-form-order-number [value='']").attr("selected", "selected");

            }).fail(function () {
                console.log("server error");
            });

        }

    });

    b.on('click', '#cross-dock-print-bt', function () {
        var client_id = $('#cross-dock-form-client-id').val(),
            party_number = $('#cross-dock-form-order-number').val(),
            printType = $('#cross-dock-process-form').data('printtype'),
            href = $(this).data('url');

        if (client_id.length < 1) {
            alert('Выберите клиента');
            return false;
        }

        if (party_number.length < 1) {
            alert('Выберите заказ');
            return false;
        }
        if (printType == 'pdf') {
            window.location.href = href + '?client_id=' + client_id + '&party_number=' + party_number;
        } else if (printType == 'html') {
            autoPrintAllocatedListHtml(href + '?client_id=' + client_id + '&party_number=' + party_number, '0', 2500);
        }

        return true;
    });

    b.on('click', '#cross-dock-confirm-bt', function () {
        if (confirm('Вы точно хотите подтвердить этот лист?')) {
            var data = [];

            /*            $.each(b.find('input.acc-qty'), function (key, value) {
             var inputValue = $(value).val();
             var isNum = inputValue / inputValue;

             if(inputValue.length < 1){
             data =[];
             alert('Не все поля заполнены');
             return false;
             }
             if(!isNum){
             data =[];
             alert('Поле "количество" должно быть числом');
             return false;
             }
             data[key]=[$(value).data('id'), inputValue];

             });*/

            /*if(data.length >= 1){*/

            var url = $(this).attr('data-url'),
                form = $('#cross-dock-apply-qty-form');
            $('#loading-modal-content').html('Идет обработка данных, пожалуйста подождите...');
            $('#loading-modal').modal('show');
            console.log(data);
            console.log(form.serialize());
            /*errorBase.setForm(form);*/
            $.post(url, form.serialize(), function (result) {
                /* $.post(url, {'data': data}, function (result) {*/
                if (result.success == '1') {
                    $('#loading-modal-content').html(result.message);
                    setTimeout(function () {

                        $('#confirmcrossdockform-cross_dock_barcode').val('');
                        $('#loading-modal').modal('hide');
                        $('#loading-modal-content').html('');
                        $('#result-table-body').html('');
                    }, 3000);
                } else if (result.success == '0') {
                    $('#loading-modal').modal('hide');
                    console.log("server error");
                }
            }, 'json').fail(function () {
                $('#loading-modal').modal('hide');
                console.log("server error");
            });


            /* }*/
        }

        return false;
    });

    b.on('click', '#cross-dock-apply-by-one-shop-bt', function () {
        if (confirm('Вы точно хотите подтвердить этот лист?')) {
            var data = [];
            var url = $(this).attr('data-url'),
                id = $(this).attr('data-id'),
                form = $('#cross-dock-apply-qty-form');
            $('#loading-modal-content').html('Идет обработка данных, пожалуйста подождите...');
            $('#loading-modal').modal('show');
            //console.log(data);
            //console.log(form.serialize());
            $.post(url, form.serialize(), function (result) {
                if (result.success == '1') {
                    $('#loading-modal-content').html(result.message);
                    setTimeout(function () {

                        //$('#confirmcrossdockform-cross_dock_barcode').val('');
                        $('#loading-modal').modal('hide');
                        $('#loading-modal-content').html('');
                        console.log("#row-cd-apply-" + id);
                        $("#row-cd-apply-" + id).remove();

                        if (result.messageStatus == 'end') {
                            $('#confirmcrossdockform-cross_dock_barcode').val('');
                            $('#result-table-body').html('');
                        }

                        /*                        var e = $.Event('keypress');
                         e.which = 13;
                         console.log(e);
                         $('#confirmcrossdockform-cross_dock_barcode').trigger(e);*/

                        //$('#confirmcrossdockform-cross_dock_barcode').keyup(13);
                        //$('#result-table-body').html('');
                    }, 1000);
                } else if (result.success == '0') {
                    $('#loading-modal').modal('hide');
                    console.log("server error");
                }
            }, 'json').fail(function () {
                $('#loading-modal').modal('hide');
                console.log("server error");
            });
        }

        return false;
    });


    //S: Print box barcode cross-doc
    b.on('click', '#print-box-barcode-bt', function () {

        var url = $(this).data('url'),
            container = b.find("div.ajax-container");

        $.post(url, {'client_id': $('#main-form-client-id').val()}).done(function (result) {
            container.html(result);
        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('change', '#print-box-barcode-cross-dock-order-number', function (event) {

        var me = $(this),
            clientID = $('#main-form-client-id').val(),
            parentOrderNumber = me.val(),
            url = me.data('url');// + 'get-sub-order-grid';

        console.info('-change-print-box-barcode-cross-dock-order-number');

        $.post(url, {'parent_order_number': parentOrderNumber, 'client_id': clientID}).done(function (result) {

            $('#grid-orders-container').html(result);

        }).fail(function () {

            console.log("server error");

        });
    });
    //E: Print box barcode cross-doc

    b.on('click', '#outbound-cross-dock-form-bt', function () {

        var url = $(this).data('url'),
            container = b.find("div.ajax-container");

        $.post(url, {'client_id': $('#main-form-client-id').val()}).done(function (result) {
            container.html(result);
            setTimeout(function () {
                $('#outboundcrossdockform-internal_barcode').focus().select();
            }, 500);
        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('keyup', "#outboundcrossdockform-internal_barcode", function (e) {

        if (e.which == 13) {
            var me = $(this),

                form = $('#outbound-cross-dock-form'),
                url = me.data('url');

            //$('#outbound-cross-dock-form-validate-type').val(1);

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == 0) {
                    console.info(result.errors);
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#outboundcrossdockform-to_point').focus().select();
                }
            });
        }
    });

    b.on('keyup', "#outboundcrossdockform-to_point", function (e) {

        if (e.which == 13) {
            var me = $(this),

                form = $('#outbound-cross-dock-form'),
                url = me.data('url');

            //$('#outbound-cross-dock-form-validate-type').val(2);

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == 0) {
                    console.info(result.errors);
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#outboundcrossdockform-box_barcode').focus().select();
                    $('#count-box-in-party-outbound-cross-dock-form').text(result.boxQty);
                    $('#store-name-outbound-cross-dock-form').text(result.storeName);
                }
            });
        }
    });

    b.on('keyup', "#outboundcrossdockform-box_barcode", function (e) {

        if (e.which == 13) {
            var me = $(this),

                form = $('#outbound-cross-dock-form'),
                url = me.data('url');

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == 0) {
                    console.info(result.errors);
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    //me.focus().select();
                    $("#outboundcrossdockform-box_size_code").focus().select();
                    $('#count-box-in-party-outbound-cross-dock-form').text(result.boxQty);
                    //$('#outboundcrossdockform-box_barcode').focus().select();
                }
            });
        }
    });

    b.on('click', "#outboundcrossdockform-box_size_code", function (e) {
        $("#outboundcrossdockform-box_size_code").focus().select();
    });

    // FOR MOBILE KEYBOARD BEGIN

    var OutboundCrossDockForm_internal_barcode = "#outboundcrossdockform-internal_barcode";
    var OutboundCrossDockForm_to_point = "#outboundcrossdockform-to_point";
    var OutboundCrossDockForm_box_barcode = "#outboundcrossdockform-box_barcode";
    var OutboundCrossDockForm_box_size_code = "#outboundcrossdockform-box_size_code";
    var currentOnFocusOutboundCrossDockForm = OutboundCrossDockForm_internal_barcode;

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    b.on('click', OutboundCrossDockForm_internal_barcode, function (e) {
        currentOnFocusOutboundCrossDockForm = this;
    });

    b.on('click', OutboundCrossDockForm_to_point, function (e) {
        currentOnFocusOutboundCrossDockForm = this;
    });

    b.on('click', OutboundCrossDockForm_box_barcode, function (e) {
        currentOnFocusOutboundCrossDockForm = this;
    });

    b.on('click', OutboundCrossDockForm_box_size_code, function (e) {
        currentOnFocusOutboundCrossDockForm = this;
    });


    b.on('click','.key1',function (e) {
    //$(".key1").click(function (e) {

        var key = $(this).text();
        //console.info(currentOnFocusOutboundCrossDockForm);

        addFocusSelect(currentOnFocusOutboundCrossDockForm);

        if( key != 'del' && key != 'enter' ) {
            $(currentOnFocusOutboundCrossDockForm).val($(currentOnFocusOutboundCrossDockForm).val() + $(this).text());
        }

        if( key == 'enter') {
            var ev = $.Event("keyup", {which: 13, keyCode: 13});
            $(currentOnFocusOutboundCrossDockForm).trigger(ev);
        }

        if( key == 'del' ){
            $(currentOnFocusOutboundCrossDockForm).val( $(currentOnFocusOutboundCrossDockForm).val().substr(0, $(currentOnFocusOutboundCrossDockForm).val().length - 1) ).focus();
        }
    });

    // FOR MOBILE KEYBOARD END

    /*
     *
     * */
    b.on('keyup', "#outboundcrossdockform-box_size_code", function (e) {

        if (e.which == 13) {
            var me = $(this),

                form = $('#outbound-cross-dock-form'),
                url = me.data('url');

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == 0) {
                    console.info(result.errors);
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $("#outboundcrossdockform-box_barcode").focus().select();
                    $('#outboundcrossdockform-box_size_code').val('');
                }
            });
        }
    });


    b.on('click', '#outbound-cross-dock-list-differences-bt', function () {

        var url = $(this).data('url'),
            form = $('#outbound-cross-dock-form');

        $.post(url, form.serialize()).done(function (result) {
            window.location.href = result.href;
        }).fail(function () {
            console.log("server error");
        });
    });

    // S:
    b.on('click', '#search-by-product-cross-dock-bt', function () {

        var url = $(this).data('url'),
            container = b.find("div.ajax-container");

        $.post(url, {'client_id': $('#main-form-client-id').val()}).done(function (result) {
            container.html(result);
        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('keyup', "#searchbyproductcrossdockform-internal_barcode", function (e) {

        if (e.which == 13) {

            var me = $(this),
                form = $('#outbound-cross-dock-form'),
                url = me.data('url');

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == 0) {
                    console.info(result.errors);
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#searchbyproductcrossdockform-product_barcode').focus().select();
                }
            });
        }
    });

    b.on('click', '#searchbyproductcrossdockform-product_barcode', function () {
        $(this).focus().select();
    });

    b.on('keyup', "#searchbyproductcrossdockform-product_barcode", function (e) {

        if (e.which == 13) {

            var me = $(this),
                form = $('#outbound-cross-dock-form'),
                url = me.data('url'),
                meValue = me.val();


            errorBase.setForm(form);

            if ($(this).val() == 'CHANGEBOX') {
                me.val('');
                $('#searchbyproductcrossdockform-scanned_product_barcodes').text('');
                $('#search-by-product-scanned-list').html('');
                $('#search-by-product-list').html('');
                errorBase.hidden();
                e.preventDefault();
                return false;
            }


            $.post(url, form.serialize(), function (result) {
                if (result.success == 0) {
                    console.info(result.errors);
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();
                    $('#search-by-product-list').html(result.html);
                    $('#searchbyproductcrossdockform-scanned_product_barcodes').text(result.scannedProducts);
                    $('#search-by-product-scanned-list').html(result.scannedProductsShowInGrid);
                }

                $('#searchbyproductcrossdockform-product_barcode').focus().select();
            });
        }
    });


    b.on('click', '#print-search-by-product-pdf-bt', function () {

        var me = $(this),
            form = $('#outbound-cross-dock-form'),
            internal_barcode = $('#searchbyproductcrossdockform-internal_barcode').val(),
            product_barcode = $('#searchbyproductcrossdockform-product_barcode').val(),
            url = me.data('url');
        window.location.href = url + '?SearchByProductCrossDockForm[internal_barcode]=' + internal_barcode + '&SearchByProductCrossDockForm[product_barcode]=' + product_barcode;
    });


    b.on('click', "#searchbyproductcrossdockform-product_barcode, #searchbyproductcrossdockform-internal_barcode, #searchbyproductcrossdockform-internal_barcode,#searchbyproductcrossdockform-product_barcode, #outboundcrossdockform-internal_barcode, #outboundcrossdockform-to_point, #outboundcrossdockform-box_barcode", function (e) {

        var me = $(this);
        me.focus().select();

    });
    // E:

    /*
     *CROSS DOCK: END
     */


    /*
     *RETURN ORDER: START
     */
    console.info('-init-defacto-return-order-');
    b.on('click', "#returnformnew-box_barcode, #returnformnew-order_number, #returnformnew-product_barcode", function (e) {
        $(this).focus().select();
    });

    /*
     *
     * */
    b.on('keyup', "#returnformnew-box_barcode", function (e) {

        if (e.which == 13) {

            console.info("-returnformnew-box_barcode");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#return-process-form'),
                url = me.data('url');

            errorBase.setForm(form);
            me.focus().select();

            $.post(url, form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    $('#returnformnew-box_barcode').focus().select();
                    //$('#count-product-in-box').html('0/0');
                } else {
                    errorBase.hidden();
                    $('#returnformnew-order_number').focus().select();
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
    });

    /*
     *
     * */
    b.on('keyup', "#returnformnew-order_number", function (e) {

        if (e.which == 13) {

            console.info("-returnformnew-order_number");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#return-process-form'),
                url = me.data('url');

            var messagesListElm = $('#messages-list'),
                messagesListBodyElm = $('#messages-list-body');

            messagesListElm.addClass('hidden');
            messagesListElm.removeClass('alert-info alert-success');
            messagesListBodyElm.html('');

            errorBase.setForm(form);
            me.focus().select();

            $('#message-return-order').html("Подождите, идет обработка запроса ...");

            $.post(url, form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    $('#returnformnew-order_number').focus().select();
                    $('#message-return-order').html('');
                    //$('#count-product-in-box').html('0/0');
                } else {
                    errorBase.hidden();

                    if (result.byProduct == 1) {
                        $('#returnformnew-product_barcode').focus().select();
                        $('#message-return-order').html("Начинайте сканировать содержимое короба");
                    } else {
                        $('#message-return-order').html("Можете распечатать этикетку на короб");
                    }

                    $('#returnformnew-by_product').val(result.byProduct);
                    $('#returnformnew-new_return_order_id').val(result.newReturnOrderID);
                    //$('#count-product-in-box').html(result.accepted_qty+' / '+result.expected_qty);
                    //$('#return-item-body').html(result.productList);

                    //$('#returnformnew-product_barcode').focus().select();


                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    /*
     *
     * */
    b.on('keyup', "#returnformnew-product_barcode", function (e) {

        if (e.which == 13) {

            console.info("-returnformnew-product_barcode");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#return-process-form'),
                url = me.data('url');

            errorBase.setForm(form);
            me.focus().select();

            $.post(url, form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    $('#accepted-product-list').html(result.items);
                    $('#count-product-in-box').html(result.accepted_qty + ' / ' + result.expected_qty);
                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    /*
     *
     * */
    b.on('click', "#clear-product-in-box-by-one-scanning-return-bt", function (e) {

        console.info("-clear-product-in-box-by-one-scanning-return-bt");
        console.info("Value : " + $(this).val());

        var me = $(this),
            form = $('#return-process-form'),
            url = me.data('url');


        errorBase.setForm(form);
        me.focus().select();

        $.post(url, form.serialize(), function (result) {

            console.info(result);

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
            } else {
                errorBase.hidden();
                $('#accepted-product-list').html(result.items);
                $('#count-product-in-box').html(result.accepted_qty + ' / ' + result.expected_qty);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    /*
     *
     * */
    b.on('click', "#clear-box-return-bt", function (e) {

        console.info("-clear-box-return-bt");
        console.info("Value : " + $(this).val());

        var me = $(this),
            form = $('#return-process-form'),
            url = me.data('url');

        errorBase.setForm(form);
        me.focus().select();

        $.post(url, form.serialize(), function (result) {

            console.info(result);

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
            } else {
                errorBase.hidden();

                $('#accepted-product-list').html(result.items);
                $('#count-product-in-box').html(result.accepted_qty + ' / ' + result.expected_qty);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    /*
     *
     * */
    b.on('click', '#return-form-print-box-label-bt', function () {

        if (confirm('Вы действительно хотите принять короб на склад')) {

            var returnOrderIDValue = $('#returnformnew-new_return_order_id').val(),
                boxBarcode = $('#returnformnew-box_barcode').val(),
                byProduct = $('#returnformnew-by_product').val(),
                me = $(this),
                href = me.data('url'),
                form = $('#return-process-form');

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(href, {'id': returnOrderIDValue, 'box-barcode': boxBarcode, 'by-product': byProduct}, function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();

                    $('#returnformnew-new_return_order_id').val('');
                    $('#returnformnew-box_barcode').val('');
                    $('#returnformnew-order_number').val('');
                    $('#message-return-order').html('');
                    window.location.href = result.labelUrl;
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });

        }
    });

    /*
     *RETURN ORDER: END
     * */

    /*
     * API v2 BEGIN
     * */
    b.on('click', '.defacto-marked-inbound-party-bt', function () {
        console.info('.defacto-marked-inbound-party-bt click');

        console.log(inProcess);
        if(inProcess[inProcessKey] == 1) {
            return;
        }

        console.info("THIS ID: "+inProcessKey);
        console.log(inProcess[inProcessKey] == 1);

        if (confirm('Груз действительно прибыл на склад?')) {
            inProcess[inProcessKey] = 1;

            var me = $(this),
                url = me.data('url');

            me.text("Загружается...");
            $.get(url, function (result) {

                console.info(result);

                if (!result.HasError) {
                    me.text(result.Message);
                } else {
                    alert(result.ErrorMessage);
                    me.text("Груз прибыл к нам на склад");
                }

                inProcess[inProcessKey] = 0;
                return false;
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    b.on('click', '.get-inbound-party-items-bt', function () {
        console.info('.get-inbound-party-items-bt click');
        var me = $(this),
            url = me.data('url'),
            partyNumber = me.data('value'),
            id = me.data('id');

        if(inProcess[inProcessKey] == 1) {
            return;
        }

        if (confirm('Вы действительно хотите загрузить накладную ' + partyNumber + ' на склад ?')) {
            me.text("Загружается...");
            inProcess[inProcessKey] = 1;
            $.get(url, function (result) {

                console.info(result);
                console.info("get-inbound-party-items-bt2");

                if (!result.HasError) {
                    alert(result.Message);
                    me.remove();
                } else {
                    me.text("Загружаем приходную накладную к нам в систему");
                    alert(result.ErrorMessage);
                }
                inProcess[inProcessKey] = 0;
                return false;
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    b.on('click', '.get-outbound-party-items-bt', function () {

        console.info('.get-outbound-party-items-bt click');

        var me = $(this),
            url = me.data('url'),
            partyNumber = me.data('value'),
            id = me.data('id');

        if(inProcess[inProcessKey] == 1) {
            return;
        }

        if (confirm('Вы действительно хотите загрузить накладную ' + partyNumber + ' на склад ?')) {
            inProcess[inProcessKey] = 1;
            me.text("Загружается...");
            $.get(url, function (result) {

                console.info(result);

                if (!result.isError) {
                    me.text(result.successMessage);
                    $("#get-outbound-party-items-save-bt-" + id).removeClass('hidden');
                    me.remove();
                } else {
                    alert(result.errorMessage);
                    me.text('Получаем данные от Дефакто');

                }

                inProcess[inProcessKey] = 0;

                return false;
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    //
    b.on('click', '.get-outbound-party-items-save-bt', function () {
        console.info('.get-outbound-party-items-save-bt click');
        var me = $(this),
            url = me.data('url'),
            partyNumber = me.data('value'),
            id = me.data('id');

        if(inProcess[inProcessKey] == 1) {
            return;
        }


        if (confirm('Вы действительно хотите загрузить накладную ' + partyNumber + ' на склад ?')) {
            inProcess[inProcessKey] = 1;
            me.text("Загружается...");
            $.get(url, function (result) {

                console.info(result);

                if (!result.isError) {
                    $("#get-outbound-party-items-save-bt-" + id).removeClass('hidden');
                    me.remove();
                    alert(result.successMessage);
                } else {
                    alert(result.errorMessage);
                    me.text('Создаем расходные накландые');
                }
                inProcess[inProcessKey] = 0;
                return false;
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    b.on('click', '.get-return-party-items-bt', function () { // ok
        console.info('.get-return-party-items-bt click');

        if(inProcess['GET-RETURN-PARTY-ITEMS-BT'] == 1) {
            return;
        }

        if (confirm('Возвраты действительно прибыл на склад?')) {

            inProcess['GET-RETURN-PARTY-ITEMS-BT'] = 1;

            var me = $(this),
                url = me.data('url'),
                $load = $(this).find(".loading");
            $load.text("/ Подождите, загружаются ... /");
            $.get(url, function (result) {
                $load.text("");
                console.info(result);

                if (!result.HasError) {
                    me.text(result.Message);
                } else {
                    alert(result.ErrorMessage);
                }

                //inProcess[inProcessKey] = 0;

                return false;
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    b.on('change', '#return-form-party-number', function (event) { // ok

        var me = $(this),
            returnOrderNumber = me.val(),
            url = me.data('url');

        console.info('-change-return-form-party-number');

        $.get(url, {id: returnOrderNumber}).done(function (resultQty) {

            $('#count-boxes-in-party').html(resultQty);
            $("#return-form-box_barcode").focus().select();
        }).fail(function () {

            console.log("server error");

        });
    });


    /*
     *
     * */
    b.on('keyup', "#return-form-box_barcode", function (e) {

        if (e.which == 13) {

            console.info("-return-form-box_barcode");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#return-process-form'),
                url = me.data('url');

            errorBase.setForm(form);
            me.focus().select();

            $.post(url, form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    $('#return-form-box_barcode').focus().select();
                } else {
                    errorBase.hidden();
                    $('#return-form-client_box_barcode').focus().select();
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
    });

    /*
     *
     * */
    b.on('keyup', "#return-form-client_box_barcode", function (e) {

        if (e.which == 13) {

            console.info("-return-form-client_box_barcode");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#return-process-form'),
                url = me.data('url');

            errorBase.setForm(form);
            me.focus().select();

            $.post(url, form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    $('#return-form-client_box_barcode').focus().select();
                } else {
                    errorBase.hidden();
                    $('#return-form-box_barcode').focus().select().html('');
                    $('#count-boxes-in-party-scanned').html(result.countBoxes);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
    });


    /*
     * Click on List differences
     * */
    b.on('click', '#return-list-differences-bt', function () {
        window.location.href = $(this).data('url') + '?id=' + $('#return-form-party-number').val();
    });

    /*
     * Click on List differences
     * */
    b.on('click', '#return-unallocated-list-bt', function () {
        window.location.href = $(this).data('url') + '?id=' + $('#return-form-party-number').val();
    });
    /*
     * Click on List accepted
     * */
    b.on('click', '#return-accepted-list-bt', function () {
        window.location.href = $(this).data('url') + '?id=' + $('#return-form-party-number').val();
    });

    b.on('click', '#return-accept-bt', function () { // ok
        console.info('.return-accept-bt');
        if (confirm('Вы действительно хотите прнять возвраты на склад?')) {
            var me = $(this),
                url = me.data('url') + '?id=' + $('#return-form-party-number').val();

            $.get(url, function (result) {

                console.info(result);

                if (!result.HasError) {
                    me.text(result.Message);
                } else {
                    alert(result.ErrorMessage);
                }

                return false;
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    /*
     * API v2 END
     * */
});
