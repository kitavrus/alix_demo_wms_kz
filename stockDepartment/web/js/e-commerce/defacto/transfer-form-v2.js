$(function() {
    console.info("Defacto Ecommerce scan form transfer v2 js");

    var formID = '#transferformv2';
    var pickingListBarcodeID = formID+'-pickinglistbarcode';
    var ourBoxBarcodeID = formID+'-ourboxbarcode';
    var lcBarcodeID = formID+'-lcbarcode';
    var productBarcodeID = formID+'-productbarcode';

    var showBoxItemsBtID = formID+'-show-box-items-bt';
    var showLCBoxItemsBtID = formID+'-show-lc-box-items-bt';
    var showOrderItemsBtID = formID+'-show-order-items-bt';
    var showScannedItemsBtID = formID+'-show-scanned-items-bt';
    var emptyBoxBtID = formID+'-empty-box-bt';
    var completeBtID = formID+'-complete-bt';

    var orderQtyID = '#order-qty';
    var lcBoxBarcodeQtyID = '#lc-box-barcode-qty';
    var ourBoxBarcodeQtyID = '#our-box-barcode-qty';
    var productQtyID = '#product-qty';
    var ifClickCompleteBt = 'N';

    //resetForm();
    $(completeBtID).hide();

    var b = $("body");

    b.on('submit',formID, function (e) {
        if($(formID).val() != 1) {
            e.preventDefault();
            return false;
        }
    });

    b.on('click',pickingListBarcodeID+", "+lcBarcodeID+","+ourBoxBarcodeID+","+productBarcodeID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });

    // ourBox BARCODE
    b.on('keyup',pickingListBarcodeID, function (e) {
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
                    resetScanned();
                    //setOrderBarcodeQty(result.expectedQty + ' / ' + result.acceptedQty);

                    addFocusSelect(lcBarcodeID);
                    $('#show-items').html('');
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // our box Barcode
    b.on('keyup',ourBoxBarcodeID, function (e) {
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

                    setOurBoxBarcodeQty(result.productExpectedQty + ' / ' + result.productAcceptedQty);
                    addFocusSelect(lcBarcodeID);
                    $('#show-items').html('');
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // lc Barcode
    b.on('keyup',lcBarcodeID, function (e) {
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

                    setOrderBarcodeQty(result.totalExpectedQty + ' / ' + result.totalAcceptedQty);
                    setOurBoxBarcodeQty(result.productExpectedQty + ' / ' + result.productAcceptedQty);
                    setLcBarcodeQty(result.qtyProductsInBox);

                    addFocusSelect(productBarcodeID);
                    $('#show-items').html('');
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

            if(me.val() == 'CHANGEBOX') {
                me.val('');
                addFocusSelect(ourBoxBarcodeID);
                resetScanned();
                return false;
            }

            if(me.val() == 'MOVEALL') {
                me.val('');
                moveAllProductFromOurBox(me.data('move-all-url'),me);
                return false;
            }

            if(me.val() == 'готово') {
                me.val('');
                $(completeBtID).show();
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
                    setOrderBarcodeQty(result.totalExpectedQty + ' / ' + result.totalAcceptedQty);
                    setProductBarcodeQty(result.productExpectedQty + ' / ' + result.productAcceptedQty);
                    setOurBoxBarcodeQty(result.productExpectedQtyInBox + ' / ' + result.productAcceptedQtyInBox);

                    setLcBarcodeQty(result.qtyProductsInBox);
                    addFocusSelect(me);
                    $('#show-items').html('');
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });


    function moveAllProductFromOurBox(url,me) {
        //
        var form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {
            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();

                setOrderBarcodeQty(result.totalExpectedQty + ' / ' + result.totalAcceptedQty);
                setOurBoxBarcodeQty(result.productExpectedQty + ' / ' + result.productAcceptedQty);
                setLcBarcodeQty(result.qtyProductsInBox);

                addFocusSelect(ourBoxBarcodeID);
                $('#show-items').html('');
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    }

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
                setOrderBarcodeQty(result.totalExpectedQty + ' / ' + result.totalAcceptedQty);
                setProductBarcodeQty('0 / 0');
                setOurBoxBarcodeQty(result.productExpectedQty + ' / ' + result.productAcceptedQty);
                setLcBarcodeQty(result.qtyProductsInBox);
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

    // show scanned Items
    b.on('click',showScannedItemsBtID, function (e) {
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

    // show LC box Items
    b.on('click',showLCBoxItemsBtID, function (e) {
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

    // complete order
    b.on('click',completeBtID, function (e) {
        e.preventDefault();

        if(ifClickCompleteBt == 'Y') {
            Alert("Вы уже нажали кнопку закрыть трансфер!!!");
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
            } else {
                errorBase.hidden();
                resetForm();
                alert("OK");
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function setOrderBarcodeQty(qty) {
        $(orderQtyID).text(qty);
    }

    function setProductBarcodeQty(qty) {
        $(productQtyID).text(qty);
    }

    function setLcBarcodeQty(qty) {
        $(lcBoxBarcodeQtyID).text(qty);
    }

    function setOurBoxBarcodeQty(qty) {
        $(ourBoxBarcodeQtyID).text(qty);
    }
    function resetValueById(id) {
        $(id).val('');
    }

    function resetForm() {
        resetValueById(pickingListBarcodeID);
        resetValueById(lcBarcodeID);
        resetValueById(productBarcodeID);
        $('#show-items').html('');
        $(completeBtID).hide();
    }

    function resetScanned() {
        setProductBarcodeQty('0 / 0');
        setLcBarcodeQty('0');
        setOurBoxBarcodeQty('0');
        $('#show-items').html('');
    }

});