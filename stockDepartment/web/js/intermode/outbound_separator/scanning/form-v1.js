$(function() {
    var b = $('body');

    $("#outboundseparatorscanningform-box_barcode").focus().select();

    b.on('click', "#scanningform-outbound_separator_id, #scanningform-in_box_barcode, #scanningform-out_box_barcode, #scanningform-product_barcode", function (e) {
        $(this).focus().select();
    });

    // Select order
    b.on('change', "#scanningform-outbound_separator_id", function (e) {
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
                $('#order-info').html(result.orderInfo.countScanned+" из "+result.orderInfo.countNotScanned);
                $("#scanningform-in_box_barcode").focus().select();
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    // In Box Barcode
    b.on('keyup', "#scanningform-in_box_barcode", function (e) {

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
                    $('#in-box-barcode-info').html(result.inBoxInfo.countScanned);
                    $("#scanningform-out_box_barcode").focus().select();
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // Out Box Barcode
    b.on('keyup', "#scanningform-out_box_barcode", function (e) {

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
                    $('#out-box-barcode-info').html(result.outBoxInfo.total_in_box+" / "+result.outBoxInfo.out_box_not_scanned+" / "+result.outBoxInfo.out_box_scanned);
                    $("#show-picking-list-items").html(result.items);
                    $("#scanningform-product_barcode").focus().select();
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    // Product barcode
    b.on('keyup', "#scanningform-product_barcode", function (e) {

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
                    $('#out-box-barcode-info').html(result.outBoxInfo.total_in_box+" / "+result.outBoxInfo.out_box_not_scanned+" / "+result.outBoxInfo.out_box_scanned);
                    $('#in-box-barcode-info').html(result.inBoxInfo.countScanned);
                    $("#show-picking-list-items").html(result.items);
                    $('#order-info').html(result.orderInfo.countScanned+" из "+result.orderInfo.countNotScanned);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });
});