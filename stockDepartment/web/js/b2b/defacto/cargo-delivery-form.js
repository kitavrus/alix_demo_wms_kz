$(function() {
    console.info("Defacto B2B cargo delivery form outbound js");

    var formID = '#cargodeliveryform';
    var keyCargoDeliveryID = formID+'-keycargodelivery';
    var selectOrderID = formID+'-selectorder';
    var boxBarcodeID = formID+'-boxbarcode';
    //var showProductsInBoxContainerID = '#show-products-in-box-container';
    var showShowBoxInOrderBtID = '#show-box-in-order-bt';
    var showAllScannedBoxBtID = '#show-all-scanned-box-bt';
    var printBtID = '#print-bt';
    var countBoxInOrderID = '#count-box-in-order';


    //resetForm();

    var b = $("body");

    b.on('submit',formID, function (e) {
        e.preventDefault();
        return false;
    });

    b.on('click',selectOrderID+", "+boxBarcodeID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });

    //
    b.on('change',selectOrderID,function(e) {

        if($(this).val() == '') {
            return;
        }

        var me = $(this),
            form = $(formID),
            url = $(this).data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        console.info(form.serialize());

        $.post(url,form.serialize(),function (result) {

            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
                ShowCountBoxInOrder('0 из 0');
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                ShowCountBoxInOrder(result.result.expectedBoxInOrder+' из '+result.result.scannedBoxInOrder);
            }
        },'json')
            .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. ВЫБОР НАКЛАДНОЙ");});
    });

    // EMPLOYEE BARCODE
    b.on('keyup',boxBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            if(me.val() == 'готово') {
                me.val('');
                //addFocusSelect(boxBarcodeID);
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
                    ShowCountBoxInOrder(result.result.expectedBoxInOrder+' из '+result.result.scannedBoxInOrder);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // SHOW PRODUCT LIST On BOX
    b.on('click',showShowBoxInOrderBtID, function (e) {
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
                //showProductsInBox(result.result);
                //addFocusSelect(me);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // EMPTY BOX BT
    b.on('click',showAllScannedBoxBtID, function (e) {
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
                addFocusSelect(boxBarcodeID);
                //showProductsInBox('');

                //showLabelInfoForInventoryKey(result.result.qtyScannedProductInInventory+' из '+result.result.qtyExpectedProductInInventory);
                //showLabelInfoForBoxBarcode(result.result.qtyScannedProductInBox+' из '+result.result.qtyExpectedProductInBox);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });


    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function resetValueById(id) {
        $(id).val('');
    }

    //function showProductsInBox(html) {
    //    $(showProductsInBoxContainerID).html(html);
    //}

    function resetForm() {
        $(employeeBarcodeID).val('');
        $(placeAddressID).val('');
        $(boxBarcodeID).val('');
    }

    function ShowCountBoxInOrder(qtyHtml) {
        $(countBoxInOrderID).html(qtyHtml);
    }
    //function showLabelInfoForInventoryKey(html) {
    //    $(showProductsInInventoryID).html(html);
    //}
    //function showLabelInfoForPlaceAddress(){}
    //function showLabelInfoForBoxBarcode(html) {
    //    $(showProductsInBoxID).html(html);
    //}
    //function showLabelInfoForProductBarcode(){}

});