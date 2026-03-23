/**
 * Created by kitavrus on 11.04.2016.
 */
$(function() {

    /*
     * Номер партии/накладной Шаг первый
     * */
    $('#inboundform-party_number').on('change',function() {

        var party_id = $(this).val(),
            url = $(this).data('url'),
            inbound = $('#inboundform-order_number'),
            dataOptions = '';

        if(party_id) {

            $.post(url, {'party_id': party_id}).done(function (result) {

                inbound.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                });

                inbound.append(dataOptions);
                inbound.focus().select();

                $('#count-box-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);

            }).fail(function () {
                console.log("server error");
            });

        } else {
            //inboundManager.hideByOrderAll();
        }
    });

    /*
     * Заказ Шаг второй
     * */
    $('#inboundform-order_number').on('change',function() {
        var inbound_id = $(this).val(),
            url = $(this).data('url');

        if(inbound_id) {
            $.post(url,{'inbound_id': inbound_id})
                .done(function (result) {

                    $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
                    $('#inbound-item-body').html(result.items);
                    $('#inboundform-pallet_barcode').focus().select();
                })
                .fail(function () {
                    console.log("server error");
                });
        }
    });

    /*
     * Паллета Шаг третий
     * */
    $('#inboundform-pallet_barcode').on('keyup',  function (e) {

        if (e.which == 13) {

            var me = $(this),
                form = $('#inbound-process-form'),
                url = $(this).data('url');

            errorBase.setForm(form);
            me.focus().select();
            var data = form.serialize();

            $.post(url, data, function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#inboundform-box_barcode').focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
        e.preventDefault();
    });

    /*
     * Сканируем штрих код короба. Шаг четвертый
     * */
    $("#inboundform-box_barcode").on('keyup',  function (e) {

        if (e.which == 13) {

            console.info("-inboundform-box_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#inbound-process-form'),
                url = $(this).data('url');

            errorBase.setForm(form);
            me.focus().select();
            var data = form.serialize();

            $.post(url, data,function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if(result.existBoxBarcode) {
                        console.log('existBoxBarcode TRUE');
                        $("#inboundform-qty_box_on_pallet").focus().select();
                    } else {
                        console.log('existBoxBarcode FALSE');
                        $("#inboundform-product_barcode").focus().select();
                        $('#count-product-in-box').html(result.countProductInBox);
                    }


                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    /*
     * Scan product barcode
     * */
    $("#inboundform-product_barcode").on('keyup', function (e) {
        if (e.which == 13) {

            var me = $(this),
                url = $(this).data('url');
            console.info("-inbound-process-steps-product-barcode-");
            console.info("Value : " + me.val());

            me.focus().select();

            if(me.val() == 'CHANGEBOX') {
                $('#inboundform-pallet_barcode').focus().select();
                me.val('');
                return true;
            }

            var form = $('#inbound-process-form');
            var data = form.serialize();
            errorBase.setForm(form);

            $.post(url, data,function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#link-product-to-box-barcode-bt').removeClass('btn-xs').addClass('btn-xs-norm');
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
        e.preventDefault();
    });

    /*
     * Scan product name
     * */
    $("#inboundform-product_name").on('change', function (e) {

        var me = $(this),
            url = $(this).data('url');
        console.info("-inbound-process-steps-product-name-");
        console.info("Value : " + me.val());

        var form = $('#inbound-process-form');
        var data = form.serialize();
        errorBase.setForm(form);

        $.post(url, data,function (result) {
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                me.focus().select();
            } else {
                errorBase.hidden();
                $('#link-product-to-box-name-bt').removeClass('btn-xs').addClass('btn-xs-norm');
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });
    });

    /*
     * Задаем количество коробов на палете Шаг шесть
     * */
    $('#inboundform-qty_box_on_pallet').on('keyup',  function (e) {

        if (e.which == 13) {
            var me = $(this),
                url = $(this).data('url'),
                printUrl = $(this).data('print-url');
            console.info("-inbound-form-qty-box-on-pallet-");
            console.info("Value : " + me.val());

            var form = $('#inbound-process-form');
            var data = form.serialize();
            errorBase.setForm(form);

            $.post(url, data,function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    //$('#link-product-to-box-name-bt').removeClass('btn-xs').addClass('btn-xs-norm');
                    alert("кол-во коробов на паллете сохранено");
                    $('#inboundform-pallet_barcode').focus().select();
                    window.location.href = printUrl+'?pallet-barcode='+result.palletBarcode;
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
        e.preventDefault();
    });

    /*
     * Связать товари короб по штрихкоду
     * */
    $('#link-product-to-box-barcode-bt').on('click',function() {
        var me = $(this),
            url = me.data('url');
        console.log('#link-product-to-box-barcode');

        me.removeClass('btn-xs-norm').addClass('btn-xs');

        var form = $('#inbound-process-form');
        var data = form.serialize();
        errorBase.setForm(form);

        $.post(url, data,function (result) {
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                $('#inboundform-qty_box_on_pallet').focus().select();
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });
    });

    /*
     * Отвязать товари короб по штрихкоду
     * */
    $('#unlink-product-to-box-barcode-bt').on('click',function() {
        var me = $(this),
            url = me.data('url');
        console.log('#unlink-product-to-box-barcode');

        me.removeClass('btn-xs-norm').addClass('btn-xs');

        var form = $('#inbound-process-form');
        var data = form.serialize();
        errorBase.setForm(form);

        $.post(url, data,function (result) {
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });
    });

    /*
     * Связать товари короб по названию
     * */
    $('#link-product-to-box-name-bt').on('click',function() {
        var me = $(this),
            url = me.data('url');
        console.log('#link-product-to-box-name-bt');

        me.removeClass('btn-xs-norm').addClass('btn-xs');

        var form = $('#inbound-process-form');
        var data = form.serialize();
        errorBase.setForm(form);

        $.post(url, data,function (result) {
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                $('#inboundform-qty_box_on_pallet').focus().select();
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });
    });

    /*
     * Отвязать товари короб по названию
     * */
    $('#unlink-product-to-box-name-bt').on('click',function() {
        var me = $(this),
            url = me.data('url');
        console.log('#unlink-product-to-box-name-bt');

        me.removeClass('btn-xs-norm').addClass('btn-xs');

        var form = $('#inbound-process-form');
        var data = form.serialize();
        errorBase.setForm(form);

        $.post(url, data,function (result) {
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });
    });

    /*
     * Очитстить паллету
     * */
    $('#clear-pallet-barcode-bt').on('click',function() {
        var me = $(this),
            url = me.data('url');
        console.log('#clear-pallet-barcode-bt');

        var form = $('#inbound-process-form');
        var data = form.serialize();
        errorBase.setForm(form);

        $.post(url, data,function (result) {
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                alert('Паллета успеша очищена');
                $('#inboundform-box_barcode').focus().select();

            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });
    });

    /*
     *
     * */
    $('#inboundform-box_barcode, #inboundform-product_barcode, #inboundform-pallet_barcode, #inboundform-qty_box_on_pallet').on('click',function(){
        $(this).focus().select();
    });

    /*
    *
    *
    * */
    $('#inbound-accept-bt').on('click',function() {

        if(confirm('Вы уверены, что хотите закрыть накладную')) {

            var me = $(this),
                url = me.data('url');
            console.log('#inbound-accept-bt');

            var form = $('#inbound-process-form');
            var data = form.serialize();
            errorBase.setForm(form);

            $.post(url, data,function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();
                    alert("Накладная успешно принята принята");
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
    });
});