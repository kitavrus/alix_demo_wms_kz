$(function() {
    console.info("Defacto Ecommerce scan form return outbound js");

    var formID = '#returnform';
    var employeeBarcodeID = formID+'-employeebarcode';
    var orderNumberID = formID+'-ordernumber';
    var boxBarcodeID = formID+'-boxbarcode';
    var productBarcodeID = formID+'-productbarcode';
    var returnProcessID = formID+'-returnprocess';

    var emptyBoxBtID = formID+'-empty-box-bt';
    var showBoxItemsBtID =  formID+'-show-box-items-bt';
    var showOrderItemsBtID = formID+'-show-order-items-bt';
    var completeBtID = formID+'-complete-bt';
    var orderInfoQtyID = '#order-info-qty';
    var boxInfoQtyID = '#box-info-qty';

    resetForm();

    var b = $("body");

    b.on('submit',formID, function (e) {
        if($(formID).val() != 1) {
            e.preventDefault();
            return false;
        }
    });

    b.on('click',employeeBarcodeID+", "+orderNumberID+","+boxBarcodeID+", "+productBarcodeID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });

    // EMPLOYEE BARCODE
    b.on('keyup',employeeBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            errorBase.setForm(form);
            errorBase.hidden();

            console.log(form);
            console.info(form.serialize());

            $.post(url, form.serialize(),function (result) {
                if (result.success == 'N' ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(orderNumberID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    // order number
    b.on('keyup',orderNumberID, function (e) {
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

                    addOrderInfoQty(result.expectedQty+' / '+result.acceptedQty);
                    addFocusSelect(boxBarcodeID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    // BOX BARCODE
    b.on('keyup',boxBarcodeID, function (e) {
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
                    addQtyInbox(result.qtyInbox);
                    addFocusSelect(productBarcodeID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // PRODUCT BARCODE
    b.on('keyup',productBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            if(me.val() == 'CHANGEORDER') {
                me.val('');
                addFocusSelect(orderNumberID);
                return false;
            }

            if(me.val() == 'CHANGEBOX') {
                me.val('');
                addFocusSelect(boxBarcodeID);
                return false;
            }

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 'N' ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(me);
                    addQtyInbox(result.qtyInbox);
                    addOrderInfoQty(result.expectedQty+' / '+result.acceptedQty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
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
                addFocusSelect(productBarcodeID);
                addQtyInbox(result.qtyInbox);
                addOrderInfoQty(result.expectedQty+' / '+result.acceptedQty);
                $('#show-items').html('');
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });
    // show order Items
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
    // show box Items
    b.on('click',showBoxItemsBtID, function (e) {
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

    // complete Button
    b.on('click',completeBtID, function (e) {
        e.preventDefault();

        if(confirm("Вы действительно закончили принимать возврат")) {
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
                    //$('#show-items').html(result.items);
                    resetForm();
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
       return false;
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function addOrderInfoQty(qty) {
        $(orderInfoQtyID).text(qty);
    }

    function addQtyInbox(qty) {
        $(boxInfoQtyID).text(qty);
    }
    function resetValueById(id) {
        $(id).val('');
    }

    function resetForm() {
        $(employeeBarcodeID).val('');
        $(orderNumberID).val('');
        $(boxBarcodeID).val('');
        $(productBarcodeID).val('');
        $('#show-items').html('');
    }
});