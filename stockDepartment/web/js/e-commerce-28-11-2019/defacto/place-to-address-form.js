
$(function() {

    var formID = '#boxtoplaceform';
    var fromPlaceAddressID = formID+'-fromplaceaddress';
    var toPlaceAddressID = formID+'-toplaceaddress';

    var init = function() {
        console.info('INIT place-to-address-form');
    };

    init();

    var b = $("body");

    b.on('submit',formID, function (e) {
        return false;
    });
    //
    b.on('click',fromPlaceAddressID+", "+toPlaceAddressID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });
    //
    b.on('keyup',fromPlaceAddressID, function (e) {
        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        console.info("-"+fromPlaceAddressID+"-");
        console.info("Value : " + $(this).val());

        var me = $(this),
            form = $(formID),
            url = $(this).data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        console.info(form.serialize());

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addFocusSelect(toPlaceAddressID);
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. НАШ КОРОБ"); });
    });
    //
    b.on('keyup',toPlaceAddressID, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+toPlaceAddressID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addFocusSelect(fromPlaceAddressID);
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });
    //
    function addFocusSelect(id) {
        $(id).focus().select();
    }
});