$(function() {
    console.info("Defacto E-commerce outbound list form");

    var formID = '#outboundlistform';
    var titleID = formID+'-title';
    var barcodeID = formID+'-barcode';

    var printBtID = formID+'-print-bt';
    var showOrdersInListBtID = formID+'-show-order-in-list-bt';
    var packageBarcodeQty = formID+'-package-barcode-qty';
    var packedNotScannedBtID = formID+'-packed-not-scanned-bt';
    var showOrderInListContainer = '#show-order-in-list-container';

    var b = $("body");

    addFocusSelect(barcodeID);

    b.on('submit',formID, function (e) {
        if($(_onPrintBoxLabelID).val() != 1) {
            e.preventDefault();
            return false;
        }
    });

    b.on('click',barcodeID+", "+titleID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });


    b.on('keyup',barcodeID, function (e) {
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
                    addFocusSelect(me);
                    showScannedQty(result.packageBarcodeQty);
                    showOrdersInList(result.result);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    // PRINT LIST
    b.on('click',printBtID, function (e) {

        //var form = $(formID),
        //    url = $(this).data('url');

        console.info("-"+printBtID+"-");
        console.info("Value : " + $(this).val());


        var me = $(this),
            url = me.data('validate-url'),
            urlToRedirect = me.data('url'),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(titleID);
            } else {
                errorBase.hidden();
                showScannedQty(0);
                window.location.href = urlToRedirect+'?title='+result.title;
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // SHOW SCANNED ORDER LIST
    b.on('click',showOrdersInListBtID, function (e) {
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
                showOrdersInList(result.result);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // SHOW PACKED BUT NOT SCANNED TO LIST
    b.on('click',packedNotScannedBtID, function (e) {
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
                showOrdersInList(result.result);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    function showOrdersInList(html) {
        $(showOrderInListContainer).html(html);
    }

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function showScannedQty(qty) {
        $(packageBarcodeQty).text(qty);
    }
});