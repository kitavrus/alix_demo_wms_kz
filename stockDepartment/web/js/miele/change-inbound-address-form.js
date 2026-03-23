
$(function () {

        var b = $('body'),
        codLenAccommodation = 0;


    b.on('change','#inboundchangeaddressform-type',function() {
        $('#inboundchangeaddressform-from').focus().select();
    });

    /*
     * From
     * */

    b.on('submit','#inboundchangeaddressform', function (e) {
        return false;
    });

    b.on('click',"#inboundchangeaddressform-from, #inboundchangeaddressform-to", function (e) {

        $(this).focus().select();
        errorBase.setForm($('#inboundchangeaddressform'));
        errorBase.hidden();
        var messagesListElm = $('#messages-list'),
            messagesListBodyElm = $('#messages-list-body');

        messagesListElm.addClass('hidden');
        messagesListElm.removeClass('alert-info alert-success');
        //messagesListBodyElm.html('');

    });

    b.on('keyup',"#inboundchangeaddressform-from, #inboundchangeaddressform-to", function (e) {

        var codStr = $(this).val();

        if(codStr.substr(-2) == '##') {
            codLenAccommodation++;
        }

        if (e.which == 13 || (codStr.substr(-2) == '##' && codLenAccommodation == 3)) {
            codLenAccommodation = 0;
            var me = $(this),
                moveType = $('#stock-accommodation-type'),
                form = $('#inboundchangeaddressform'),
                to = $('#inboundchangeaddressform-to'),
                from = $('#inboundchangeaddressform-from');

            console.info("-inboundchangeaddressform-from-");
            console.info("Value : " + me.val());
            console.info("ID : " +me.attr('id'));
            console.info("Move type : " +moveType.val());

            errorBase.setForm(form);


            $.post('/wms/miele/inbound/move-from-to', form.serialize(),function (result) {

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if(me.attr('id') == 'inboundchangeaddressform-from') {
                        console.log('++1');
                        to.focus().select();
                    } else {
                        from.focus().select();
                        to.val('');
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
//                alert(errorThrown);
            });
        }

        e.preventDefault();

        return false;
    });

});