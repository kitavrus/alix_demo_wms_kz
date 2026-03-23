/**
 * Created by kitavrus on 18.03.15.
 */

$(function() {
   console.info('INIT Return JS');

    $(function() {
        var b = $('body');
        /*
         *
         * */
        b.on('click',"#returnformnew-box_barcode, #returnformnew-order_number, #returnformnew-product_barcode", function (e) {
            $(this).focus().select();
        });

        /*
        *
        * */
        b.on('keyup',"#returnformnew-box_barcode", function (e) {

            if (e.which == 13) {

                console.info("-returnformnew-box_barcode");
                console.info("Value : " + $(this).val());

                var me = $(this),
                    form = $('#return-process-form'),
                    url = me.data('url');

                errorBase.setForm(form);
                me.focus().select();

                $.post(url, form.serialize(),function (result) {

                    console.info(result);

                    if (result.success == 0 ) {
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
        b.on('keyup',"#returnformnew-order_number", function (e) {

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

                $.post(url, form.serialize(),function (result) {

                    console.info(result);

                    if (result.success == 0) {
                        errorBase.eachShow(result.errors);
                        $('#returnformnew-order_number').focus().select();
                        $('#message-return-order').html('');
                        //$('#count-product-in-box').html('0/0');
                    } else {
                        errorBase.hidden();

                        if(result.byProduct == 1) {
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
       b.on('keyup',"#returnformnew-product_barcode", function (e) {

            if (e.which == 13) {

                console.info("-returnformnew-product_barcode");
                console.info("Value : " + $(this).val());

                var me = $(this),
                    form = $('#return-process-form'),
                    url = me.data('url');

                errorBase.setForm(form);
                me.focus().select();

                $.post(url, form.serialize(),function (result) {

                    console.info(result);

                    if (result.success == 0 ) {
                        errorBase.eachShow(result.errors);
                        me.focus().select();
                    } else {
                        errorBase.hidden();

                        $('#accepted-product-list').html(result.items);
                        $('#count-product-in-box').html(result.accepted_qty+' / '+result.expected_qty);
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
        b.on('click',"#clear-product-in-box-by-one-scanning-return-bt", function (e) {

            console.info("-clear-product-in-box-by-one-scanning-return-bt");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#return-process-form'),
                url = me.data('url');


            errorBase.setForm(form);
            me.focus().select();

            $.post(url, form.serialize(),function (result) {

                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#accepted-product-list').html(result.items);
                    $('#count-product-in-box').html(result.accepted_qty+' / '+result.expected_qty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        });

        /*
         *
         * */
        b.on('click',"#clear-box-return-bt", function (e) {

            console.info("-clear-box-return-bt");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#return-process-form'),
                url = me.data('url');

            errorBase.setForm(form);
            me.focus().select();

            $.post(url, form.serialize(),function (result) {

                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    $('#accepted-product-list').html(result.items);
                    $('#count-product-in-box').html(result.accepted_qty+' / '+result.expected_qty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        });

        /*
         *
         * */
        b.on('click','#return-form-print-box-label-bt',function() {

            if(confirm('Вы действительно хотите принять короб на склад')) {

                var returnOrderIDValue = $('#returnformnew-new_return_order_id').val(),
                    boxBarcode = $('#returnformnew-box_barcode').val(),
                    byProduct = $('#returnformnew-by_product').val(),
                    me = $(this),
                    href = me.data('url'),
                    form = $('#return-process-form');

                errorBase.setForm(form);
                errorBase.hidden();

                $('#returnformnew-new_return_order_id').val('');
                $('#returnformnew-box_barcode').val('');
                $('#returnformnew-order_number').val('');
                $('#message-return-order').html('');

                window.location.href = href + '?id=' + returnOrderIDValue+'&box-barcode='+boxBarcode+'&by-product='+byProduct;
            }
        });
    });
});