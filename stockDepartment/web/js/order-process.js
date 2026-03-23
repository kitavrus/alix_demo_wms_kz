/**
 * Created by Igor on 01.08.14.
 */



$(function () {

    $('#order-process-steps-form').on('submit', function (e) {

//       if ( $('#on-submit-form').val() == '0' ) {
        return false;
//       }

//       return true;
    });

    /*
     * Scan box barcode
     * */
    /*$("#order-process-steps-box-barcode").on('keyup',function (e) {*/
    $("#orderprocess-box_barcode").on('keyup', function (e) {
        /*
         * TODO Добавить проверку
         * TODO 1 + Существует ли этот короб у нас на складе
         * TODO 2 + Если ли в этом коробе уже товары из другого магазина
         * */
        if (e.which == 13) {
            console.info("-order-process-steps-box-barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this);
            var form = $('#order-process-steps-form');
            errorBase.setForm(form);

            $.post('/order/order-process/validate-scanned-box', $('#order-process-steps-form').serialize(),function (response) {
                if (response.errors) {
                    errorBase.eachShow(response.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $("#orderprocess-product_barcode").focus().select();
                    /*$(me).parent().find('.input-group-addon').html(response.countProductInBox);*/
                    $('#count-product-in-box').html(response.countProductInBox);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });

        }

        e.preventDefault();
    });

    /*
     * Scan product barcode in box
     * */
    $("#orderprocess-product_barcode").on('keyup', function (e) {
        /*
         * TODO Добавить проверку
         * TODO 1 ? Существует ли этот товар у нас на складе
         * TODO 2 + Существует ли этот товар у этого клиента
         */
        if (e.which == 13) {
            var me = $(this);
            console.info("-order-process-steps-product-barcode-");
            console.info("Value : " + me.val());

            me.focus().select();

            var form = $('#order-process-steps-form');
            errorBase.setForm(form);

            $.post('/order/order-process/set-status-scanned', $('#order-process-steps-form').serialize(),function (response) {
                if (response.errors) {
                    errorBase.eachShow(response.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#count-product-in-box').html(response.countProductInBox);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });
        }
        e.preventDefault();
    });


    $('#orderprocess-store_id').on('change', function (e) {
        $('#orderprocess-box_barcode').focus().select();

    });

    $('#order-process-steps-form-print-label-bt').on('click', function (e) {
        var client_id = $('#orderprocess-client_id').val(),
            store_id = $('#orderprocess-store_id').val(),
            box_barcode = $('#orderprocess-box_barcode').val(),
            href = $('#order-process-steps-form-print-label-bt').data('href');

        if (box_barcode && store_id) {
            window.location.href = href + '/box_barcode/' + box_barcode + '/store_id/' + store_id;
        }
        return false;
    });

    $('#order-process-steps-form-print-box-label-bt').on('click', function (e) {
        var client_id = $('#orderprocess-client_id').val(),
            store_id = $('#orderprocess-store_id').val(),
            href = $('#order-process-steps-form-print-box-label-bt').data('href');

        if (store_id) {
            window.location.href = href + '/store_id/' + store_id;
        }
        return false;
    });


});