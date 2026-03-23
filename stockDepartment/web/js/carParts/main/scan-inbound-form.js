
$(function() {

    var formID = '#scaninboundform';
    var orderNumberID = formID+'-ordernumberid';
    var transportedBoxBarcodeID = formID+'-transportedboxbarcode';
    //var productModelID = formID+'-productmodel';
    var productBarcodeID = formID+'-productbarcode';
    var productQtyID = formID+'-productqty';

    var countProductsInUnitID = '#count-product-in-unit';
    var countProductsInOrderID = '#count-products-in-order';
    var countProductModelInOrderID = '#count-product-model-in-order';

    var inboundItemsContainerID = '#inbound-items';
    var inboundShowOrderItemsBtID = formID+'-inbound-show-items-bt';
    var printDiffInOrderBtID = formID+'-print-diff-in-order-bt';
    var inboundCloseBtID = formID+'-inbound-close-bt';

    var labelProductBarcode = 'label[for=scaninboundform-productbarcode]';
    var oldTextLabelProductBarcode = $(labelProductBarcode).text();

    var init = function() {
        //hideButtonClearBox();
        hideButtonPrintDiff();
    };

    init();

    var b = $("body");

    b.on('submit',formID, function (e) {
        return false;
    });
    //
    //b.on('click',transportedBoxBarcodeID+", "+productBarcodeID,function(e) {
    b.on('click',transportedBoxBarcodeID+", "+productBarcodeID+", "+productQtyID,function(e) {
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
                addQtyToOrderLabel(result.accepted_qty+' из '+result.expected_qty);
                addFocusSelect(transportedBoxBarcodeID);
                //showButtonPrintDiff();
            }
        },'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. ВЫБОР НАКЛАДНОЙ");});
    });
    //
    b.on('keyup',transportedBoxBarcodeID, function (e) {
        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        console.info("-"+transportedBoxBarcodeID+"-");
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
                showQtyProductInUnit(result.qtyProductInUnit);
                //showButtonClearBox();
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. НАШ КОРОБ"); });
    });
    //
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
            addFocusSelect(transportedBoxBarcodeID);
            return false;
        }
        //if($(this).val() == 'CHANGE-ARTICLE') {
        //    me.val('');
        //    addFocusSelect(productModelID);
        //    return false;
        //}

        if($(this).val() == 'CLEAN-BOX') {
            me.val('');
            cleanTransportedBox(me.data('clean-url'),me);
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
                addQtyProductModelInOrderID(result.acceptedQtyInOrderItem+' из '+result.expectedQtyInOrderItem);
                addQtyToOrderLabel(result.acceptedQtyInOrder+' из '+result.expectedQtyInOrder);
                showQtyProductInUnit(result.qtyProductInUnit);
                //resetTextLabelProductBarcode();
            }
            addFocusSelect(me);

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });
    //
    b.on('keyup',productQtyID, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+productBarcodeID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addQtyProductModelInOrderID(result.acceptedQtyInOrderItem+' из '+result.expectedQtyInOrderItem);
                addQtyToOrderLabel(result.acceptedQtyInOrder+' из '+result.expectedQtyInOrder);
                showQtyProductInUnit(result.qtyProductInUnit);
                //resetTextLabelProductBarcode();
                addFocusSelect(productBarcodeID);
            }


        }, 'json')
            .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });
    //
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
    function cleanTransportedBox (url,me) {

        var form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addQtyProductModelInOrderID(result.acceptedQtyInOrderItem+' из '+result.expectedQtyInOrderItem);
                addQtyToOrderLabel(result.acceptedQtyInOrder+' из '+result.expectedQtyInOrder);
                showQtyProductInUnit(result.qtyProductInUnit);
                resetTextLabelProductBarcode();
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

    function addQtyProductModelInOrderID(qty) {
        $(countProductModelInOrderID).text(qty);
    }

    function showQtyProductInUnit(qty) {
        $(countProductsInUnitID).text(qty);
    }

    //function addTextLabelProductBarcode(text) {
    //    $(labelProductBarcode).html(oldTextLabelProductBarcode+" / <strong> "+text+"</strong>");
    //}

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
        $(transportedBoxBarcodeID).val('');
        //$(productModelID).val('');
        $(productBarcodeID).val('');
    }

    //
    //b.on('D_keyup',productModelID, function (e) {
    //
    //    e.preventDefault();
    //    if (e.which != 13) {
    //        return false;
    //    }
    //
    //    var me = $(this),
    //        form = $(formID);
    //
    //    errorBase.setForm(form);
    //    errorBase.hidden();
    //
    //    console.info("-"+productModelID+"-");
    //    console.info("Value : " + $(this).val());
    //
    //    var url = $(this).data('url');
    //
    //    $.post(url, form.serialize(),function (result) {
    //
    //        if (result.success == 'N') {
    //            errorBase.eachShow(result.errors);
    //            addFocusSelect(me);
    //        } else {
    //            errorBase.hidden();
    //            addFocusSelect(productBarcodeID);
    //            if(result.isEmptyProductBarcodeByModel) {
    //                addTextLabelProductBarcode("У артикла нет штрихкода товара. введите его.");
    //            }
    //            addQtyProductModelInOrderID(result.acceptedQtyModel+' из '+result.expectedQtyModel);
    //        }
    //
    //    }, 'json')
    //    .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    //});
    //
});