$(function() {
    console.info("Defacto Ecommerce scan form part-re-reserved-form js");

    var formID = '#partrereservedform';
    var pickListBarcodeID = formID+'-picklistbarcode';
    var employeeBarcodeID = formID+'-employeebarcode';
    var showOtherPlaceBtID = '.show-other-place-bt';
    var showItemsID = '#show-items';
    var showOtherProductAddressID = '#show-other-product-address';
    var pickingStockRowID = '#picking-stock-id-';
    var otherPlaceStockRowID = '#other-place-stock-id-';
    var pickingStockClass = '.picking-stock';
    var otherPlaceStockClass = '.other-place-stock';
    var reReservedBt = '.re-reserved-bt';
    var isReReservedBtClicked = 'N';

    resetForm();

    var b = $("body");

    b.on('submit',formID, function (e) {
            e.preventDefault();
            return false;
    });

    b.on('click',pickListBarcodeID+','+employeeBarcodeID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });

    // EMPLOYEE BARCODE
    b.on('keyup',employeeBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {
            console.info("employeeBarcodeID");
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
                    addFocusSelect(pickListBarcodeID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // PIKING LIST BARCODE
    b.on('keyup',pickListBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {
            console.info("pickListBarcodeID");
            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 'N' ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                    showReservedProducts('');
                    showOtherProductAddress('');
                } else {
                    errorBase.hidden();
                    showReservedProducts(result.result);
                    showOtherProductAddress('');
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // SHOW OTHER PLACE BT ID BUTTON
    b.on('click',showOtherPlaceBtID, function (e) {
        e.preventDefault();

        console.info("changeBtID");

        var me = $(this),
            productBarcode = me.data('product-barcode'),
            stockId = me.data('stock-id'),
            changeReason = $('#change-reason-'+stockId+"").val(),
            url = me.data('url')+"?stockId="+stockId+"&changeReason="+changeReason,
            form = $(formID);

        if(changeReason == '') {
            alert('Выберите причину замену товара: '+productBarcode);
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
                showOtherProductAddress(result.result);
                pickingStockRowIdActive(result.stockId);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

       // Re reserved product on stock BT ID BUTTON
    b.on('click',reReservedBt, function (e) {
        e.preventDefault();

        console.info("reReservedBt");

        var me = $(this),
            url = me.data('url');
            form = $(formID);

        me.text("Подолжите");
        if (isReReservedBtClicked == 'Y') {
            return;
        }
        isReReservedBtClicked = 'Y';


        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {
            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                isReReservedBtClicked = 'N';
                me.text("Заменено");
                me.removeClass('btn-warning');
                me.removeClass('re-reserved-bt');
                me.addClass('btn-success');
                alert('Товар успешно перерезервирован из выбранного короба');
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function pickingStockRowIdActive(stockId) {
        $(pickingStockClass).removeClass('color-violet');
        $(pickingStockRowID+stockId).addClass('color-violet');
    }

    function otherPlaceStockRowIDActive(stockId) {
        $(otherPlaceStockClass).removeClass('color-dark-olive-green');
        $(otherPlaceStockRowID+stockId).addClass('color-dark-olive-green');
    }

    function showReservedProducts(html) {
        $(showItemsID).html(html);
    }

    function showOtherProductAddress(html) {
        $(showOtherProductAddressID).html(html);
    }

    function resetForm() {
        $(pickListBarcodeID).val('');
    }
});