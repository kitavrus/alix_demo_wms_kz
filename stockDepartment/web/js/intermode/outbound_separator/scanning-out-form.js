$(function() {
    var b = $('body');

    b.on('submit','#outbound_separator_scanning_out_form_id', function (e) {
        return false;
    });


    b.on('click', "#outboundseparatorscanningoutform-product_barcode", function (e) {
        $(this).focus().select();
    });

    // Select order
    b.on('change', "#outboundseparatorscanningoutform-outbound_separator_id", function (e) {
        var me = $(this),
            url = me.data('url'),
            form = $('#outbound_separator_scanning_out_form_id');

        errorBase.setForm(form);

        $.post(url, form.serialize(), function (result) {
            if (result.success == "N") {
                errorBase.eachShow(result.errors);
                me.focus().select();
            } else {
                errorBase.hidden();
                console.log(result);
                $('#outbound-separator-info').html(result.orderInfo.countScanned+" из "+result.orderInfo.countNotScanned);
                 $("#outboundseparatorscanningoutform-product_barcode").focus().select();
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });

    // Product barcode
    b.on('keyup', "#outboundseparatorscanningoutform-product_barcode", function (e) {

        var me = $(this);

        if (e.which == 13) {
            var url = me.data('url'),
                form = $('#outbound_separator_scanning_out_form_id');

            errorBase.setForm(form);

            $.post(url, form.serialize(), function (result) {
                if (result.success == "N") {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    me.focus().select();
                    $('#outbound-separator-info').html(result.orderInfo.countScanned+" из "+result.orderInfo.countNotScanned);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });
});