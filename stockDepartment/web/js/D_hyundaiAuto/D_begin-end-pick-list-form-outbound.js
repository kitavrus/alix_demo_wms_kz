
$(function(){

    console.info('-load begin-end-picking-list-outbound -');
    var formID = '#begin-end-pick-list-form';
    var pickingListID = '#beginendpicklistform-picking_list_barcode';
    var employeeID = '#beginendpicklistform-employee_barcode';
    var b = $('body');

    b.on('submit',formID, function (e) {
        return false;
    });

    b.on('click',pickingListID+", "+employeeID,function(e) {
        console.info($(this).attr('id'));
        $(this).focus().select();
    });


    b.on('keyup',pickingListID+", "+employeeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            console.info("-beginendpicklistform-picking_list_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $(formID),
                url = $(this).data('url'),
                messagesListElm = $('#messages-list');
            messagesListBodyElm = $('#messages-list-body');

            errorBase.setForm(form);
            me.focus().select();

            messagesListElm.addClass('hidden');
            messagesListElm.removeClass('alert-info alert-success');
            messagesListBodyElm.html('');

            $.post(url, form.serialize(),function (result) {

                console.info(result);

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();

                    if(result.messagesInfo.length >= 1) {
                        messagesListBodyElm.html(result.messagesInfo);
                        messagesListElm.addClass('alert-info');
                        messagesListElm.removeClass('hidden');

                        $(pickingListID).val(result.picking_list_barcode);
                        $(employeeID).val(result.employee_barcode);
                    }

                    if(result.messagesSuccess.length >= 1) {
                        messagesListBodyElm.html(result.messagesSuccess);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');

                        $(pickingListID).val('');
                        $(employeeID).val('');
                    }

                    if(me.attr('id')=='beginendpicklistform-employee_barcode') {
                        $(pickingListID).focus().select();
                    } else {
                        $(employeeID).focus().select();
                    }

                    if(result.step == 'end') {
                        $(pickingListID).val('');
                        $(employeeID).val('');
                        $(pickingListID).focus().select();
                    }

                }
//
            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }


    });
});