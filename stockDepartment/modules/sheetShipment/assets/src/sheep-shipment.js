/**
 * Created by KitavrusAdmin on 07.06.2017.
 */
$(function () {
    console.info("init Sheep Shipment module JS");

    var b = $('body'),
        codLenAccommodation = 0;

    b.on('submit', '#sheet-shipment-form', function (e) {
        return false;
    });

    b.on('click', "#sheetshipmentform-boxbarcode, #sheetshipmentform-placeaddress", function (e) {

     $(this).focus().select();
     errorBase.setForm($('#sheet-shipment-form'));
     errorBase.hidden();
     //var messagesListElm = $('#messages-list'),
     //messagesListBodyElm = $('#messages-list-body');
     //
     //messagesListElm.addClass('hidden');
     //messagesListElm.removeClass('alert-info alert-success');
     //messagesListBodyElm.html('');

     });

    b.on('keyup', "#sheetshipmentform-boxbarcode, #sheetshipmentform-placeaddress", function (e) {

        var codStr = $(this).val();

        if (codStr.substr(-2) == '##') {
            codLenAccommodation++;
        }

        if (e.which == 13 || (codStr.substr(-2) == '##' && codLenAccommodation == 3)) {
            codLenAccommodation = 0;
            var me = $(this),
                form = $('#sheet-shipment-form'),
                boxBarcode = $('#sheetshipmentform-boxbarcode'),
                placeAddress = $('#sheetshipmentform-placeaddress');

            errorBase.setForm(form);

            $.post('/sheetShipment/default/move', form.serialize(), function (result) {

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();


                    console.log(me.attr('id'));
                    console.log(boxBarcode.attr('id'));

                    if (me.attr('id') == boxBarcode.attr('id')) {
                        console.log('++1');
                        placeAddress.focus().select();
                    } else {
                        boxBarcode.focus().select();
                        placeAddress.val('');
                        console.log('++2');
                    }

                    var messagesListElm = $('#messages-list'),
                        messagesListBodyElm = $('#messages-list-body');

                    messagesListElm.addClass('hidden');
                    messagesListElm.removeClass('alert-info alert-success');
                    messagesListBodyElm.html('');

                    if (result.successMessages.length >= 1) {
                        messagesListBodyElm.html(result.successMessages);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');
                    }

                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });
        }

        e.preventDefault();

        return false;
    });
});