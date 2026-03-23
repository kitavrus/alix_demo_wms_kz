/**
 * Created by KitavrusAdmin on 05.04.2017.
 */


/**
 * Created by kitavrus on 18.03.15.
 */

$(function () {
    console.info('INIT Return TMP ORDER JS');

    var b = $('body');
    var $form = $('#return-tmp-orders-form');
    var $fieldTTN = $('#tmporderform-ttn');
   // var $qtyTTN = $('#qty-places-in-ttn');
    var $fieldOurBoxToStockBarcode = $('#tmporderform-our_box_to_stock_barcode');
    var $fieldClientBoxBarcode = $('#tmporderform-client_box_barcode');
    var $btPrintWithoutSecondaryAddress = $('#return-print-without-secondary-address-bt');
    var $btPrintWithSecondaryAddress = $('#return-print-with-secondary-address-bt');
    var $qtyPlacesInTtn = $('#qty-places-in-ttn');
    var $qtyScannedInTtn = $('#qty-scanned-in-ttn');

    var $accommodationReturnForm = $("#stock-accommodation-process-form");
    var $accommodationReturnFrom = $("#accommodationreturnform-from");
    var $accommodationReturnTo = $("#accommodationreturnform-to");

    b.on('click',".selected-on-click", function (e) {
        $(this).focus().select();
    });

    $accommodationReturnForm.on('submit', function (e) {
        return false;
    });

    /*
     *
     * */
    $fieldTTN.on('keyup', function (e) {

        if (e.which == 13) {

            console.info("keyup id : " + $fieldTTN.attr("id"));
            console.info("Value : " + $fieldTTN.val());

            var url = $fieldTTN.data('url');

            errorBase.setForm($form);
            $fieldTTN.focus().select();

            $.post(url, $form.serialize(), function (result) {
                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();

                    $qtyPlacesInTtn.html(result.qtyPlacesInTtn);
                    $qtyScannedInTtn.html(result.qtyScannedInTtn);

                    $fieldOurBoxToStockBarcode.focus().select();

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
        e.preventDefault();
    });

    /*
     *
     * */
    $fieldOurBoxToStockBarcode.on('keyup', function (e) {

        if (e.which == 13) {

            console.info("keyup id : " + $fieldOurBoxToStockBarcode.attr("id"));
            console.info("Value : " + $fieldOurBoxToStockBarcode.val());

            var url = $fieldOurBoxToStockBarcode.data('url');

            errorBase.setForm($form);
            $fieldOurBoxToStockBarcode.focus().select();

            $.post(url, $form.serialize(), function (result) {
                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();
                    $fieldClientBoxBarcode.focus().select();

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
        e.preventDefault();
    });

    /*
     *
     * */
    $fieldClientBoxBarcode.on('keyup', function (e) {

        if (e.which == 13) {

            console.info("keyup id : " + $fieldClientBoxBarcode.attr("id"));
            console.info("Value : " + $fieldClientBoxBarcode.val());

            var url = $fieldClientBoxBarcode.data('url');

            errorBase.setForm($form);
            $fieldClientBoxBarcode.focus().select();

            $.post(url, $form.serialize(), function (result) {
                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();
                    $fieldOurBoxToStockBarcode.focus().select();
                    $qtyScannedInTtn.html(result.qtyScannedInTtn);

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }
        e.preventDefault();
    });

    $btPrintWithoutSecondaryAddress.on('click',function() {
        window.location.href = $(this).data('url') + '?ttn=' + $fieldTTN.val();
    });

    $btPrintWithSecondaryAddress.on('click',function() {
        window.location.href = $(this).data('url') + '?ttn=' + $fieldTTN.val()
    });



    b.on('click',"#accommodationreturnform-from, #accommodationreturnform-to", function (e) {

        $(this).focus().select();
        errorBase.setForm($accommodationReturnForm);
        errorBase.hidden();
        var messagesListElm = $('#messages-list'),
            messagesListBodyElm = $('#messages-list-body');

        messagesListElm.addClass('hidden');
        messagesListElm.removeClass('alert-info alert-success');
        //messagesListBodyElm.html('');

    });
    //alert("sdfsdf");
    b.on('keyup',"#accommodationreturnform-from, #accommodationreturnform-to", function (e) {

        var codStr = $(this).val();

        if(codStr.substr(-2) == '##') {
            codLenAccommodation++;
        }

        if (e.which == 13 || (codStr.substr(-2) == '##' && codLenAccommodation == 3)) {
            codLenAccommodation = 0;
            var me = $(this);

            console.info("-accommodationform-from-");
            console.info("Value : " + me.val());
            console.info("ID : " +me.attr('id'));
            //console.info("Move type : " +moveType.val());

            errorBase.setForm($accommodationReturnForm);

            $.post('/returnOrder/tmp-order/move-from-to', $accommodationReturnForm.serialize(),function (result) {

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if(me.attr('id') == 'accommodationreturnform-from') {
                        console.log('++1');
                        $accommodationReturnTo.focus().select();
                    } else {
                        $accommodationReturnFrom.focus().select();
                        $accommodationReturnTo.val('');
                        console.log('++2');
                    }

                    var messagesListElm = $('#messages-list'),
                        messagesListBodyElm = $('#messages-list-body');

                    messagesListElm.addClass('hidden');
                    messagesListElm.removeClass('alert-info alert-success');
                    messagesListBodyElm.html('');

                    if(result.successMessages.length >= 1) {
                        messagesListBodyElm.html(result.successMessages);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');
                    }

                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();

        return false;
    });

});