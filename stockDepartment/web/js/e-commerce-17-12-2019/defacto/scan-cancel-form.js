$(function() {
    console.info("defacto e-commerce  cancel form");

    var formID = '#cancelform';
    var outboundOrderNumberID = formID+'-outboundordernumber';
    var cancelReasonID = formID+'-cancelreason';

    var cancelBtID = formID+'-cancel-bt';


    addFocusSelect(outboundOrderNumberID);


    var b = $("body");

    b.on('submit',formID, function (e) {
        e.preventDefault();
        return false;
    });

    b.on('click',outboundOrderNumberID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });

    // Order number
    b.on('keyup',outboundOrderNumberID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 'N' ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(cancelReasonID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // CLEAR BOX
    b.on('click',cancelBtID, function (e) {
        e.preventDefault();

        var me = $(this),
            url = me.data('url'),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {
            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addFocusSelect(outboundOrderNumberID);
                alert("Успешно отменено");
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }
});