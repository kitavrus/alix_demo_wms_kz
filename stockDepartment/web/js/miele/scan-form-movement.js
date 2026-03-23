$(function() {
    console.info("Miele scan form movement js");

    var formID = '#movementform';
    var employeeBarcodeID = formID+'-employee_barcode';
    var pickListBarcodeID = formID+'-pick_list_barcode';
    var productBarcodeID = formID+'-product_barcode';
    var fubBarcodeID = formID+'-fub_barcode';
    var boxBarcodeID = formID+'-to_box';
    var addressBarcodeID = formID+'-to_address';
    var printDiffListBtID = formID+'-diff-list-bt';

    var pickListBarcodeLabel = 'label[for=movementform-picking_list_barcode]';
    var pickListBarcodeLabelText = $(pickListBarcodeLabel).text();

    var boxBarcodeLabel = 'label[for=movementform-box_barcode]';
    var boxBarcodeLabelText = $(boxBarcodeLabel).text();

    console.info(employeeBarcodeID);

    var b = $("body");

    b.on('submit',formID, function (e) {
        if($(_onPrintBoxLabelID).val() != 1) {
            e.preventDefault();
            return false;
        }
    });

    b.on('click',addressBarcodeID+", "+employeeBarcodeID+", "+pickListBarcodeID+", "+boxBarcodeID+", "+productBarcodeID+", "+fubBarcodeID,function(e) {
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
                if (result.success == 0 ) {
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

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
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

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(fubBarcodeID);

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    // FUB PRODUCT BARCODE
    b.on('keyup',fubBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(boxBarcodeID);

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    // OUR BOX BARCODE
    b.on('keyup',boxBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(addressBarcodeID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    // OUR ADDRESS BARCODE
    b.on('keyup',addressBarcodeID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(productBarcodeID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // PRINT DIFF LIST
    b.on('click',printDiffListBtID, function (e) {

        var form = $(formID),
            url = $(this).data('url');

        console.info("-"+printDiffListBtID+"-");
        console.info("Value : " + $(this).val());

        window.location.href = url+'?'+form.serialize();
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }
});