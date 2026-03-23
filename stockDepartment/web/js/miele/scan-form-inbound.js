
$(function() {

    var formID = '#inboundform';
    var orderID = formID+'-order_id';
    var ourBoxBarcodeID = formID+'-our_box_barcode';
    var productBarcodeID = formID+'-product_barcode';
    var fabBarcodeID = formID+'-fab_barcode';
    var cleanOurBoxBtID =formID+'-clean-our-box-bt';
    var printDiffBtID = formID+'-print-diff-bt';

    var labelOrder = 'label[for=inboundform-order_id]';
    var oldTextLabelOrder = $(labelOrder).text();

    var labelOurBoxBarcode = 'label[for=inboundform-our_box_barcode]';
    var oldTextLabelOurBoxBarcode = $(labelOurBoxBarcode).text();

    var init = function() {
        hideButtonClearBox();
        hideButtonPrintDiff();
    };

    init();


    var b = $("body");

    b.on('submit',formID, function (e) {
        return false;
    });

    b.on('click',fabBarcodeID+", "+ourBoxBarcodeID+", "+productBarcodeID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });


    b.on('change',orderID,function(e) {

        if($(this).val() == '') {
            hideButtonPrintDiff();
            resetOrderLabel();
            return;
        }

        console.info("-"+orderID+"-");
        console.info("Value : " + $(this).val());

        var me = $(this),
            form = $(formID),
            url = $(this).data('url');


        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url,form.serialize(),function (result) {

            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addQtyToOrderLabel(result.accepted_qty+' из '+result.expected_qty);
                addFocusSelect(ourBoxBarcodeID);
                showButtonPrintDiff();
            }

        },'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. ВЫБОР НАКЛАДНОЙ");});
    });
    //
    b.on('keyup',ourBoxBarcodeID, function (e) {
        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        console.info("-"+ourBoxBarcodeID+"-");
        console.info("Value : " + $(this).val());

        var me = $(this),
            form = $(formID),
            url = $(this).data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {

            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
                hideButtonClearBox();
            } else {
                errorBase.hidden();
                addFocusSelect(productBarcodeID);
                showButtonClearBox();
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. НАШ КОРОБ"); });
    });

    b.on('keyup',productBarcodeID, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        if($(this).val() == 'CHANGEBOX') {
            me.val('');
            addFocusSelect(ourBoxBarcodeID);
            return false;
        }

        console.info("-"+productBarcodeID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addTextLabelOurBoxBarcode(result.qtyInBox);
                addQtyToOrderLabel(result.accepted_qty+' из '+result.expected_qty);
                if(result.isWaitFabBarcode == 1) {
                    addFocusSelect(fabBarcodeID);
                } else {
                    addFocusSelect(productBarcodeID);
                }
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });

    b.on('keyup',fabBarcodeID, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formID),
            url = $(this).data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+fabBarcodeID+"-");
        console.info("Value : " + $(this).val());

        $.post(url, form.serialize(),function (result) {

            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addFocusSelect(productBarcodeID);
                resetValueById(fabBarcodeID);
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. ФАБ НОМЕР"); });
    });

    b.on('click',cleanOurBoxBtID, function (e) {

        var form = $(formID),
            url = $(this).data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+cleanOurBoxBtID+"-");
        console.info("Value : " + $(this).val());


        $.post(url, form.serialize(),function (result) {

            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                addFocusSelect(productBarcodeID);
                addTextLabelOurBoxBarcode(result.qtyInBox);
                addQtyToOrderLabel(result.accepted_qty+' из '+result.expected_qty);
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. НАШ КОРОБ"); });
    });

    b.on('click',printDiffBtID, function (e) {

        var form = $(formID),
            url = $(this).data('url');

        console.info("-"+printDiffBtID+"-");
        console.info("Value : " + $(this).val());

        window.location.href = url+'?'+form.serialize();
    });

    function resetValueById(id) {
        $(id).val('');
    }

    function resetOrderLabel() {
        $(labelOrder).text(oldTextLabelOrder);
    }

    function addQtyToOrderLabel(qty) {
        $(labelOrder).text(oldTextLabelOrder+" / Всего: "+qty);
    }

    function addTextLabelOurBoxBarcode(qty) {
        $(labelOurBoxBarcode).text(oldTextLabelOurBoxBarcode+" / Всего: "+qty);
    }

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function showButtonClearBox() {  $(cleanOurBoxBtID).show(); }
    function hideButtonClearBox() {  $(cleanOurBoxBtID).hide(); }

    function showButtonPrintDiff() {  $(printDiffBtID).show(); }
    function hideButtonPrintDiff() {  $(printDiffBtID).hide();}

});