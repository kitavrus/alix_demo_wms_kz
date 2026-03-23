
$(function() {

    var formID = '#scaninboundform';
    var orderNumberID = formID+'-ordernumberid';
    var clientBoxBarcodeID = formID+'-clientboxbarcode';
    var ourBoxBarcodeID = formID+'-ourboxbarcode';
    var productBarcodeID = formID+'-productbarcode';
    var datamatrixID = formID+'-datamatrix';
    var stockID = formID+'-stockid';
    var withDatamatrixID = formID+'-withdatamatrix';

    var clientBoxProductExpected = '#client-box-prod-exp';
    var clientBoxProductAccepted = '#client-box-prod-acc';
    var ourBoxProductAccepted = '#our-box-prod-acc';
    var countProductsInOrderID = '#count-products-in-order';
    var productScannedInLot = '#product-scanned-in-lot';


    var inboundItemsContainerID = '#inbound-items';
    var inboundShowOrderItemsBtID = formID+'-inbound-show-items-bt';
    var printDiffInOrderBtID = formID+'-print-diff-in-order-bt';
    var inboundCloseBtID = formID+'-inbound-close-bt';
	var inboundCheckOrderBtID = formID+'-check-order-bt';

    var labelProductBarcode = 'label[for=scaninboundform-productbarcode]';
    var oldTextLabelProductBarcode = $(labelProductBarcode).text();

    var init = function() {
        hideButtonPrintDiff();
    };

    init();

    var b = $("body");

    b.on('submit',formID, function (e) {
        return false;
    });


    //
    //b.on('click',transportedBoxBarcodeID+", "+productBarcodeID,function(e) {
    b.on('click',clientBoxBarcodeID+", "+ourBoxBarcodeID+", "+productBarcodeID+", "+datamatrixID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });
    //
    b.on('change',orderNumberID,function(e) {

        if($(this).val() == '') {
            hideButtonPrintDiff();
            resetOrderLabel();
            return;
        }
        showButtonPrintDiff();

        console.info("-"+orderNumberID+"-");
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
                addQtyToOrderLabel(result.accepted_product_qty+' из '+result.expected_product_qty);
                addFocusSelect(ourBoxBarcodeID);
                //showButtonPrintDiff();
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

            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
                //hideButtonClearBox();
            } else {
                errorBase.hidden();
                addFocusSelect(productBarcodeID);
                addQtyToOrderLabel(result.accepted_product_qty+' из '+result.expected_product_qty);
                showProductScannedInOurBox(result.productAcceptedQty);

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

        if($(this).val() == 'CLEAN-BOX') {
            me.val('');
            cleanOurBox(me.data('clean-url'),me);
            $(datamatrixID).val("");
            return false;
        }
		
		if(me.val() == 'готово') {
            me.val('');
            $(inboundCloseBtID).show();
            return false;
        }

        console.info("-"+productBarcodeID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                addQtyToOrderLabel(result.accepted_product_qty+' из '+result.expected_product_qty);
                showProductScannedInOurBox(result.InOurBoxProductAcceptedQty);

                $(stockID).val(result.StockId);

                if ($("input[name='ScanInboundForm[withDatamatrix]']:checked").val() === "0") {
                    addFocusSelect(productBarcodeID);
                } else {
                    addFocusSelect(datamatrixID);
                }
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });

    b.on('keyup',datamatrixID, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+datamatrixID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {
            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addFocusSelect(productBarcodeID);
                $(datamatrixID).val('');
            }
        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });

    b.on('click',inboundShowOrderItemsBtID, function (e) {

        e.preventDefault();

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+inboundShowOrderItemsBtID+"-");

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                loadOrderItems(result.items);
            }

        }, 'json')
            .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });
	
	b.on('click',inboundCheckOrderBtID, function (e) {

        e.preventDefault();

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+inboundCheckOrderBtID+"-");

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                loadOrderItems(result.items);
            }

        }, 'json')
            .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });
	

    b.on('click',inboundCloseBtID, function (e) {

        e.preventDefault();

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+inboundCloseBtID+"-");

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                resetFields();
                resetOrderLabel();
                resetTextLabelProductBarcode();
                resetOrderItems();
                alert("Накладная успешно принята на склад");
            }

        }, 'json')
            .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });
    //
    b.on('click',printDiffInOrderBtID, function (e) {

        var me = $(this),
            form = $(formID),
            url = me.data('url');

        window.location.href = url+'?'+form.serialize();
    });
    //
    function cleanOurBox (url,me) {

        var form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                showProductScannedInOurBox(0);
                addQtyToOrderLabel(result.accepted_product_qty+' из '+result.expected_product_qty);
            }

        }, 'json')
            .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    }


    function resetOrderLabel() {
        $(countProductsInOrderID).text('0');
    }

    function addQtyToOrderLabel(qty) {
        $(countProductsInOrderID).text(qty);
    }

    //function addQtyProductModelInOrderID(qty) {
    //    $(countProductModelInOrderID).text(qty);
    //}

    //function showQtyProductInUnit(qty) {
    //    $(countProductsInUnitID).text(qty);
    //}

    function showClientBoxLotQty(qty) {
        $(clientBoxLotQty).text(qty);
    }

    function showClientBoxProductExpected(qty) {
        $(clientBoxProductExpected).text(qty);
    }

    function showClientBoxProductAccepted(qty) {
        $(clientBoxProductAccepted).text(qty);
    }

    //function showProductQtyInLot(qty) {
    //    $(productQtyInLot).text(qty);
    //}

    //function showProductQtyInLotExpected(qty) {
    //    $(productQtyInLotExpected).text(qty);
    //}
    //
    //function showProductQtyInLotAccepted(qty) {
    //    $(productQtyInLotAccepted).text(qty);
    //}

    function showProductScannedInLot(qty) {
        $(productScannedInLot).text(qty);
    }

    function showProductScannedInOurBox(qty) {
        $(ourBoxProductAccepted).text(qty);
    }

    function resetTextLabelProductBarcode() {
        $(labelProductBarcode).html(oldTextLabelProductBarcode);
    }

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function showButtonPrintDiff() {  $(printDiffInOrderBtID).show(); }
    function hideButtonPrintDiff() {  $(printDiffInOrderBtID ).hide(); }

    function loadOrderItems(html) {  $(inboundItemsContainerID).html(html); }
    function resetOrderItems() {  $(inboundItemsContainerID).html(''); }

    function resetFields() {
        // $(transportedBoxBarcodeID).val('');
        //$(productModelID).val('');
        $(productBarcodeID).val('');
    }
});