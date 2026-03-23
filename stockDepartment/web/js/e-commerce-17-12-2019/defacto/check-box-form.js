$(function() {
    console.info("Defacto Ecommerce scan check box form outbound js");

    var formID = '#checkboxform';
    var employeeBarcodeID = formID+'-employeebarcode';
    var inventoryKeyID = formID+'-inventorykey';
    var titleID = formID+'-title';
    var placeAddressID = formID+'-placeaddress';
    var boxBarcodeID = formID+'-boxbarcode';
    var productBarcodeID = formID+'-productbarcode';
    var showProductsInBoxBtID = formID+'-show-products-in-box-bt';
    var emptyBoxBtID = formID+'-empty-box-bt';
    var showProductsInBoxContainerID = '#show-products-in-box-container';

    console.info(employeeBarcodeID);

    resetForm();
    addFocusSelect(employeeBarcodeID);

    
    var b = $("body");

    b.on('submit',formID, function (e) {
        //if($(_onPrintBoxLabelID).val() != 1) {
        //    e.preventDefault();
        //    return false;
        //}
            e.preventDefault();
            return false;
    });

    b.on('click',employeeBarcodeID+", "+inventoryKeyID+","+titleID+", "+placeAddressID+", "+boxBarcodeID+", "+productBarcodeID,function(e) {
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

            $.post(url, form.serialize(),function (result) {
                if (result.success == 'N' ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(placeAddressID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    // PLACE BARCODE
    b.on('keyup',placeAddressID, function (e) {
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
                    addFocusSelect(boxBarcodeID);
                    //addFocusSelect(packageBarcodeID);
                    //addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
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
                    addFocusSelect(productBarcodeID);
                    showProductsInBox(result.result);

                    //addFocusSelect(productBarcodeID);
                    //addTextLabelBox(result.qtyProductInPackage);

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
                addFocusSelect(boxBarcodeID);
                return false;
            }

            if(me.val() == 'CHANGEPLACE') {
                me.val('');
                addFocusSelect(placeAddressID);
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
                    showProductsInBox(result.result);
                    //addTextLabelBox(result.qtyProductInPackage);
                    //addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // SHOW PRODUCT LIST On BOX
    b.on('click',showProductsInBoxBtID, function (e) {
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
                showProductsInBox(result.result);
                //addFocusSelect(me);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // EMPTY BOX BT
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
                addFocusSelect(boxBarcodeID);
                showProductsInBox('');
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

    function showProductsInBox(html) {
        $(showProductsInBoxContainerID).html(html);
    }

    function resetForm() {
        $(employeeBarcodeID).val('');
        //$(inventoryKeyID).val('');
        //$(titleID).val('');
        $(placeAddressID).val('');
        $(boxBarcodeID).val('');
        $(productBarcodeID).val('');
    }
});