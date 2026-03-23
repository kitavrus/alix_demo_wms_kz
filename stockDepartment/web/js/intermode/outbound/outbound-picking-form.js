
/**
 * OUTBOUND: START
 * */
$(function () {
    console.info('outbound picking list');
    var b = $('body');

    b.on('submit', '#begin-end-pick-list-form', function (e) {
        return false;
    });


    b.on('keyup', "#beginendpicklistform-picking_list_barcode, #beginendpicklistform-employee_barcode", function (e) {

        if (e.which == 13) {

            console.info("-beginendpicklistform-picking_list_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#begin-end-pick-list-form'),
                url = $(this).data('url'),
                messagesListElm = $('#messages-list');
            messagesListBodyElm = $('#messages-list-body');

            errorBase.setForm(form);
            me.focus().select();

            messagesListElm.addClass('hidden');
            messagesListElm.removeClass('alert-info alert-success');
            messagesListBodyElm.html('');

            $.post(url, form.serialize(), function (result) {

                console.info(result);

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if (result.messagesInfo.length >= 1) {
                        messagesListBodyElm.html(result.messagesInfo);
                        messagesListElm.addClass('alert-info');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val(result.picking_list_barcode);
                        $('#beginendpicklistform-employee_barcode').val(result.employee_barcode);
                    }

                    if (result.messagesSuccess.length >= 1) {
                        messagesListBodyElm.html(result.messagesSuccess);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');

                        $('#beginendpicklistform-picking_list_barcode').val('');
                        $('#beginendpicklistform-employee_barcode').val('');
                    }

                    if (me.attr('id') == 'beginendpicklistform-employee_barcode') {
                        $('#beginendpicklistform-picking_list_barcode').focus().select();
                    } else {
                        $('#beginendpicklistform-employee_barcode').focus().select();
                    }

                    if (result.step == 'end') {
                        $('#beginendpicklistform-picking_list_barcode').val('');
                        $('#beginendpicklistform-employee_barcode').val('');
                        $('#beginendpicklistform-picking_list_barcode').focus().select();
                    }

                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });
});