$(function() {
    var b = $('body');

    $("#outboundseparatorscanningform-box_barcode").focus().select();

    b.on('click', "#outboundseparatorscanningform-outbound_separator_id, #outboundseparatorscanningform-box_barcode, #outboundseparatorscanningform-product_barcode", function (e) {
        $(this).focus().select();
    });

    // Select order
    b.on('change', "#outboundseparatorscanningform-outbound_separator_id", function (e) {
        var me = $(this),
            url = me.data('url'),
            form = $('#outbound_separator_scanning_form_id');

        errorBase.setForm(form);

        $.post(url, form.serialize(), function (result) {
            if (result.success == "N") {
                errorBase.eachShow(result.errors);
                me.focus().select();
            } else {
                errorBase.hidden();
                console.log(result);
                $('#outbound-separator-info').html(result.orderInfo.in_box_new+" / "+result.orderInfo.in_box_scanned+" / "+result.orderInfo.to_out_from_order);
                 $("#outboundseparatorscanningform-box_barcode").focus().select();
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    // Box Barcode
    b.on('keyup', "#outboundseparatorscanningform-box_barcode", function (e) {

        if (e.which == 13) {
            var me = $(this),
                url = me.data('url'),
                form = $('#outbound_separator_scanning_form_id');

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == "N") {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#box-barcode-info').html(result.boxInfo.total_product_in_box+" / -"+result.boxInfo.to_out_from_order+" / "+result.boxInfo.leave_order);
                    $("#outboundseparatorscanningform-product_barcode").focus().select();
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // Product barcode
    b.on('keyup', "#outboundseparatorscanningform-product_barcode", function (e) {

        var me = $(this);

        if (e.which == 13) {

            var url = me.data('url'),
                form = $('#outbound_separator_scanning_form_id');

            errorBase.setForm(form);

            if ($(this).val() == 'CHANGEBOX') {
                me.val('');
                $('#outboundseparatorscanningform-box_barcode').focus().select();
                errorBase.hidden();
                e.preventDefault();
                return false;
            }

            $.post(url, form.serialize(), function (result) {
                if (result.success == "N") {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    me.focus().select();
                    $('#box-barcode-info').html(result.boxInfo.total_product_in_box+" / -"+result.boxInfo.to_out_from_order+" / "+result.boxInfo.leave_order);
                    $('#outbound-separator-info').html(result.orderInfo.in_box_new+" / "+result.orderInfo.in_box_scanned+" / "+result.orderInfo.to_out_from_order);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });
});