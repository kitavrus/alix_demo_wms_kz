/**
 * Created by kitavrus on 03.02.15.
 */

$(function() {
    var b = $('body');
	
	$("#scanning-form-print-box-label-bt").hide();

    b.on('click', "#scanningform-box_kg, #scanningform-employee_barcode, #scanningform-picking_list_barcode, #scanningform-box_barcode, #scanningform-product_barcode", function (e) {
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
                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();


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
                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

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
                   // $('#countdown').attr('data-timer', result.cdTimer);
                   // if (result.cdTimer != 0) {
                  //      console.info('-init timer-');
                  //      initCountdown(result.cdTimer);
                 //   }
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });
    // BOX BARCODE
    b.on('keyup', "#scanningform-box_barcode", function (e) {
        if (e.which == 13) {
            var me = $(this),
                url = me.data('url'),
                form = $('#scanning-form');
            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#scanningform-product_barcode').focus().select();
                    $('#scanningform-product_barcode').val('');
                    $('#count-product-in-box').html(result.countInBox);
                    $('#outbound-item-body').html(result.stockArrayByPL);
                    //if(result.clientBox != '') {
                    //    me.val(result.clientBox);
                    //}
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });
    // PRODUCT BARCODE
    b.on('keyup', "#scanningform-product_barcode", function (e) {
        var me = $(this);
        if (e.which == 13) {
            if ($(this).val() == 'CHANGEBOX') {
                me.val('');
                $('#scanningform-box_barcode').focus().select();
                e.preventDefault();
                return false;
            }
			
			if(me.val() == 'готово') {
                $("#scanning-form-print-box-label-bt").show();
                me.val('');
                e.preventDefault();
                return false;
            }

            var url = me.data('url'),
                form = $('#scanning-form');

            errorBase.setForm(form);
            me.focus().select();

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

    b.on('click','#scanning-form-differences-list-bt',function() {
        var href = $(this).data('url');
        window.location.href = href + '?plids=' + $('#scanningform-picking_list_barcode_scanned').val();
    });

    b.on('click', '#clear-box-scanning-outbound-bt', function () {

        var href = $(this).data('url-value'),
            form = $('#scanning-form');

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

    b.on('click', '#scanning-form-print-box-label-bt', function () {

        if (confirm('Вы действительно хотите распечатать этикетки')) {
            var plids = $('#scanningform-picking_list_barcode_scanned').val(),
                href = $(this).data('url'),
                hrefValidate = $(this).data('validate-url');

            console.log(href);
            console.log(hrefValidate);

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

});
