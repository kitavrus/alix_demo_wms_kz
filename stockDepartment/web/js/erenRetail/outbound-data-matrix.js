/**
 * Created by kitavrus on 03.02.15.
 */

$(function() {

    console.info("-intermode outbound data matrix");

    var b = $('body');

    b.on('click', "#outbounddatamatrixform-box_barcode", function (e) {
        $(this).focus().select();
    });

    b.on('click', "#outbounddatamatrixform-product_datamatrix", function (e) {
        $(this).focus().select();
    });

    b.on('click', "#outbounddatamatrixform-product_barcode", function (e) {
        $(this).focus().select();
    });

    // BOX BARCODE
    b.on('keyup', "#outbounddatamatrixform-box_barcode", function (e) {

        var me = $(this);

        if (e.which == 13) {

            console.info("-outbound-data-matrix-form-box_barcode-");
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
                    $('#order_number').text(result.orderNumber);
                    $('#count-product-in-box').text(result.scanCountBox+" из "+result.expCountBox);
                    $('#exp-count').text(result.expCount);
                    $('#scan-count').text(result.scanCount);
                    $('#outbounddatamatrixform-product_barcode').focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();
        return false;
    });

    b.on('keyup', "#outbounddatamatrixform-product_barcode", function (e) {
        var me = $(this);

        if (e.which == 13) {
            console.info("-outbound-data-matrix-form-product-barcode-");

            var url = me.data('url'),
                form = $('#scanning-form');

            errorBase.setForm(form);
            me.focus().select();
            errorBase.hidden();

            if(me.val() == 'CHANGEBOX') {
                me.val('');
                $('#outbounddatamatrixform-box_barcode').focus().select();
                return false;
            }

            $.post(url, form.serialize(), function (result) {

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#outbounddatamatrixform-product_datamatrix').focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();
        return false;
    });

    b.on('keyup', "#outbounddatamatrixform-product_datamatrix", function (e) {

        var me = $(this);

        if (e.which == 13) {

            console.info("-outbound-data-matrix-form-product_datamatrix-");

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
                    $('#count-product-in-box').text(result.scanCountBox+" из "+result.expCountBox);
                    $('#exp-count').text(result.expCount);
                    $('#scan-count').text(result.scanCount);
                    $('#outbounddatamatrixform-product_barcode').focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();
        return false;
    });

    b.on('click', '#clear-box-scanning-outbound-bt', function () {

        var href = $(this).data('url-value'),
            form = $('#scanning-form');

        errorBase.setForm(form);

        $.post(href, form.serialize(), function (result) {

            errorBase.hidden();


            if (result.success == 0) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();

                $('#count-product-in-box').text(result.scanCountBox+" из "+result.expCountBox);
                $('#exp-count').text(result.expCount);
                $('#scan-count').text(result.scanCount);
                $('#outbounddatamatrixform-box_barcode').focus().select();
            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

});
