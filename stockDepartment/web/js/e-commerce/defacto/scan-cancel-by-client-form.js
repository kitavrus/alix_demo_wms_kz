$(function() {
    console.info("defacto e-commerce  cancel by client form");

    var formID = '#cancelbyclientform';
    var outboundOrderNumberID = formID+'-outboundordernumber';
    var boxAddressID = formID+'-boxaddress';

    var cancelBtID = formID+'-cancel-bt';
    var emptyBoxBtID = formID+'-empty-box-bt';
    var showOrderItemsBtID = formID+'-show-order-items-bt';
    var showAllOrderItemsBtID = formID+'-show-all-order-items-bt';
    var ifClickCompleteBt = 'N';

    addFocusSelect(outboundOrderNumberID);

    $(cancelBtID).hide();

    var b = $("body");

    b.on('submit',formID, function (e) {
        e.preventDefault();
        return false;
    });

    b.on('click',outboundOrderNumberID+", "+boxAddressID,function(e) {
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
                    addFocusSelect(boxAddressID);
                    $('#show-items').html(result.items);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // BOX ADDRESS PLACE
    b.on('keyup',boxAddressID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            if(me.val()=='готово' || me.val()=='ujnjdj') {
                $(cancelBtID).show();
                return;
            }

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 'N' ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(outboundOrderNumberID);
                    $('#show-items').html(result.items);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // show Items
    b.on('click',showOrderItemsBtID, function (e) {
        e.preventDefault();

        var me = $(this),
            url = me.data('url'),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {
            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                $('#show-items').html(result.items);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // show all Items
    b.on('click',showAllOrderItemsBtID, function (e) {
        e.preventDefault();

        var me = $(this),
            url = me.data('url'),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {
            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                $('#show-items').html(result.items);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // EMPTY BOX
    b.on('click',emptyBoxBtID, function (e) {
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
                $('#show-items').html('');
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // CLEAR BOX
    b.on('click',cancelBtID, function (e) {
        e.preventDefault();

        if(ifClickCompleteBt == 'Y') {
            Alert("Вы уже нажали кнопку отменить накладные!!!");
            return;
        }

        ifClickCompleteBt = 'Y';

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