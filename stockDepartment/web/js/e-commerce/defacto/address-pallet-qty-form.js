
$(function() {

    var formID = '#addresspalletqtyform';
    var placeAddressID = formID+'-placeaddress';
    var palletPlaceQtyID = formID+'-palletplaceqty';
    var countBoxInAddressLabelID = '#count-box-in-address';

    var init = function() {
        console.info('INIT place-to-address-form');
    };

    init();

    var b = $("body");

    b.on('submit',formID, function (e) {
        return false;
    });
    //
    b.on('click',placeAddressID+", "+palletPlaceQtyID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });
    //
    b.on('keyup',placeAddressID, function (e) {
        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        console.info("-"+placeAddressID+"-");
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

                setQtyBoxInAddress(result.qtyBoxInAddress);
                addFocusSelect(palletPlaceQtyID);
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. НАШ КОРОБ"); });
    });
    //
    b.on('keyup',palletPlaceQtyID, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+palletPlaceQtyID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addFocusSelect(placeAddressID);
                setQtyBoxInAddress('0');
                me.val('');
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });
    //
    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function setQtyBoxInAddress(qty) {
        $(countBoxInAddressLabelID).text(qty);
    }
});