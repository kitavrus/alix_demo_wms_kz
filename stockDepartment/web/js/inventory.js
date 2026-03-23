/**
 * Created by kitavrus on 15.09.2015.
 */
$(function() {

    var b = $('body');
    console.info('inventory js init');



    $("#inventoryform-secondary_address, #inventoryform-primary_address, #inventoryform-product_barcode").on('click', function (e) {
        $(this).select().focus();

    });


    //
    $("#inventoryform-secondary_address").on('keyup', function (e) {
        if (e.which == 13) {
            var me = $(this);
            console.info("-inventoryform-secondary_address-");
            console.info("Value : " + me.val());

            me.focus().select();

            var form = $('#stock-inventory-form');
            errorBase.setForm(form);

            $.post(me.data('url'), form.serialize(),function (response) {
                if (response.success == 0) {
                    errorBase.eachShow(response.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    console.info("OK");
                    $('#messages-list-body').html(response.startMessage);
                    $('#messages-list').removeClass('hidden');

                    $('#inventoryform-primary_address').addClass('hidden');
                    $('#inventoryform-product_barcode').addClass('hidden');

                    $('#inventoryform-primary_address').focus().select();
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });
        }
        e.preventDefault();
    });

    //
    $("#inventoryform-primary_address").on('keyup', function (e) {
        if (e.which == 13) {
            var me = $(this);
            console.info("-inventoryform-primary_address-");
            console.info("Value : " + me.val());

            me.focus().select();

            var form = $('#stock-inventory-form');
            errorBase.setForm(form);

            $.post(me.data('url'), form.serialize(),function (response) {
                if (response.success == 0) {
                    errorBase.eachShow(response.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#inventoryform-product_barcode').focus().select();
                    $('#inventoryform-count-product-in-box').html(response.countProductInBox);

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });
        }
        e.preventDefault();
    });

    //
    $("#inventoryform-product_barcode").on('keyup', function (e) {

        if (e.which == 13) {
            var me = $(this);
            console.info("-inventoryform-product_barcode-");
            console.info("Value : " + me.val());

            me.focus().select();

            if($(this).val() == 'CHANGEBOX') {
                me.val('');
                $('#inventoryform-primary_address').focus().select();
                errorBase.hidden();
                e.preventDefault();
                return false;
            }

            var form = $('#stock-inventory-form');
            errorBase.setForm(form);

            $.post(me.data('url'), form.serialize(),function (response) {
                if (response.success == 0) {
                    errorBase.eachShow(response.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#inventoryform-count-product-in-box').html(response.countProductInBox);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });
        }
        e.preventDefault();
    });

    //
    $("#inventoryform-clear-box-bt").on('click', function (e) {

        var me = $(this);
        console.info("-inventoryform-clear-box-bt-");
        if(confirm("Вы действительно хотите очистить короб?")){
            var form = $('#stock-inventory-form');
            errorBase.setForm(form);

            $.post(me.data('url'), form.serialize(), function (response) {
                if (response.success == 0) {
                    errorBase.eachShow(response.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#inventoryform-count-product-in-box').html(response.countProductInBox);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
    //                alert(errorThrown);
            });
        }
        e.preventDefault();
    });

    //
    $("#print-inventory-diff-list-bt").on('click', function () {
        var href = $(this).data('url');
        var form = $('#stock-inventory-form');
        form.attr('action',href);
        form.submit();
    });

    //
    $("#print-inventory-accepted-item-bt").on('click', function () {
        var href = $(this).data('url');
        var form = $('#stock-inventory-form');
        form.attr('action',href);
        form.submit();
    });

     //
    b.on('click',"#continue-inventory-bt", function (e) {
        console.info("-continue-inventory-bt-");

        $('#inventoryform-primary_address').removeClass('hidden');
        $('#inventoryform-product_barcode').removeClass('hidden');
        $('#messages-list-body').html('');
        $('#messages-list').addClass('hidden');
        $('#inventoryform-primary_address').focus().select();
    });

    //
    b.on('click',"#start-inventory-bt, #restart-inventory-bt", function (e) {

        var me = $(this);
        console.info(me.attr('id'));
        me.html('Пожалуйста подождите ...');

        if(confirm('Вы действительно хотите начать заново')) {

            var form = $('#stock-inventory-form');
            errorBase.setForm(form);

            $.post(me.data('url'), form.serialize(), function (response) {
                if (response.success == 0) {
                    errorBase.eachShow(response.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#messages-list-body').html('');
                    $('#messages-list').addClass('hidden');
                    $('#inventoryform-primary_address').removeClass('hidden');
                    $('#inventoryform-product_barcode').removeClass('hidden');

                    $('#inventoryform-primary_address').focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
    //                alert(errorThrown);
            });
        }
        e.preventDefault();
    });

    b.on('click',"#print-inventory-report-pdf-bt,#print-inventory-report-excel-bt,#print-inventory-report-accepted-excel-bt,#print-inventory-report-accepted-pdf-bt", function (e) {
        var href = $(this).data('url');
        var form = $('#stock-inventory-form');
        form.attr('action',href);
        form.submit();
    });

});