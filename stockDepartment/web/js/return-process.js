/**
 * Created by kitavrus on 18.03.15.
 */

$(function() {
   console.info('INIT Return JS');

    $(function() {
        var b = $('body');

        b.on('click',"#returnform-box_barcode, #returnform-order_number, #returnform-product_barcode", function (e) {
            $(this).focus().select();
        });

        /*
        *
        * */
        b.on('keyup',"#returnform-box_barcode", function (e) {

            if (e.which == 13) {

                console.info("-returnform-box_barcode");
                console.info("Value : " + $(this).val());

                var me = $(this),
                    form = $('#return-process-form');

                errorBase.setForm(form);
                me.focus().select();

                $.post('/returnOrder/default/box-barcode', form.serialize(),function (result) {

                    console.info(result);

                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                        $('#returnform-box_barcode').focus().select();
                        //$('#count-product-in-box').html('0/0');
                    } else {
                        errorBase.hidden();

                        $('#returnform-order_number').focus().select();

                    }
                }, 'json').fail(function (xhr, textStatus, errorThrown) {

                });
            }
        });


        b.on('keyup',"#returnform-order_number", function (e) {

            if (e.which == 13) {

                console.info("-returnform-order_number");
                console.info("Value : " + $(this).val());

                var me = $(this),
                    form = $('#return-process-form');

                var messagesListElm = $('#messages-list'),
                    messagesListBodyElm = $('#messages-list-body');

                messagesListElm.addClass('hidden');
                messagesListElm.removeClass('alert-info alert-success');
                messagesListBodyElm.html('');

                errorBase.setForm(form);
                me.focus().select();

                $('#message-return-order').html("Подождите, идет обработка запроса ...");

                $.post('/returnOrder/default/order-number', form.serialize(),function (result) {

                    console.info(result);

                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                        $('#returnform-order_number').focus().select();
                        $('#message-return-order').html('');
                        //$('#count-product-in-box').html('0/0');
                    } else {
                        errorBase.hidden();

                        $('#returnform-new_return_order_id').val(result.newReturnOrderID);
                        //$('#count-product-in-box').html(result.accepted_qty+' / '+result.expected_qty);
                        //$('#return-item-body').html(result.productList);

                        //$('#returnform-product_barcode').focus().select();
                        $('#message-return-order').html("Можете распечатать этикетку на короб");
                    }
//
                }, 'json').fail(function (xhr, textStatus, errorThrown) {

                });
            }

            e.preventDefault();
        });


       b.on('keyup',"#returnform-product_barcode", function (e) {

            if (e.which == 13) {

                console.info("-returnform-product_barcode");
                console.info("Value : " + $(this).val());

                var me = $(this),
                    form = $('#return-process-form');

                errorBase.setForm(form);
                me.focus().select();

                $.post('/returnOrder/default/scan-product-from-box', form.serialize(),function (result) {

                    console.info(result);

                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                        me.focus().select();
                    } else {
                        errorBase.hidden();

                        $('#return-item-body').html(result.productList);
                        $('#count-product-in-box').html(result.accepted_qty+' / '+result.expected_qty);
                    }
//
                }, 'json').fail(function (xhr, textStatus, errorThrown) {

                });
            }

            e.preventDefault();
        });



        b.on('click','#return-form-print-box-label-bt',function() {

            if(confirm('Вы действительно хотите принять короб на склад')) {

                var returnOrderIDValue = $('#returnform-new_return_order_id').val(),
                    boxBarcode = $('#returnform-box_barcode').val(),
                    me = $(this),
                    href = me.data('url'),
                    form = $('#return-process-form');

                errorBase.setForm(form);
                errorBase.hidden();

                $('#returnform-new_return_order_id').val('');
                $('#returnform-box_barcode').val('');
                $('#returnform-order_number').val('');
                $('#message-return-order').html('');

                window.location.href = href + '?id=' + returnOrderIDValue+'&box-barcode='+boxBarcode;
            }
        });
    });
});