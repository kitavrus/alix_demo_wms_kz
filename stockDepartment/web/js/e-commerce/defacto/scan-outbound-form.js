$(function() {
    console.info("Defacto Ecommerce scan form outbound js");

    var formID = '#outboundform';
    var employeeBarcodeID = formID+'-employee_barcode';
    var pickListBarcodeID = formID+'-pick_list_barcode';
    var packageBarcodeID = formID+'-package_barcode';
    var productBarcodeID = formID+'-product_barcode';
	var productQRID = formID+'-product_qrcode';
    var kgID = formID+'-kg';
    var _onPrintBoxLabelID = formID+'-_on_print_box_label';
    var printBoxLabelBTID = formID+'-print-box-label-for-order-bt';

    var clearBoxBtID = formID+'-clear-box-bt';
    var printDiffBtID = formID+'-diff-list-bt';
    var showPickingListItemsBtID = formID+'-show-picking-list-items-bt';

    var printCargoLabelBtID = formID+'-print-cargo-label';
    var printWaybillDocumentBtID = formID+'-print-waybill-document';

    var pickListLabel = '#pick-list-barcode-qty';

    var boxLabel = '#package-barcode-qty';


    console.info(employeeBarcodeID);


    resetForm();


    var b = $("body");

    b.on('submit',formID, function (e) {
        if($(_onPrintBoxLabelID).val() != 1) {
            e.preventDefault();
            return false;
        }
    });

    b.on('click',employeeBarcodeID+", "+pickListBarcodeID+","+packageBarcodeID+", "+productBarcodeID+", "+kgID+", "+productQRID,function(e) {
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
                if (result.success == 'N' ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(packageBarcodeID);
                    addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
    // PACKAGE BARCODE
    b.on('keyup',packageBarcodeID, function (e) {
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
                    addTextLabelBox(result.qtyProductInPackage);

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
                addFocusSelect(pickListBarcodeID);
                return false;
            }

            if(me.val() == 'CHANGEPACKAGE') {
                me.val('');
                addFocusSelect(packageBarcodeID);
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
                    addTextLabelBox(result.qtyProductInPackage);
                    addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
	
	
	    b.on('keyup',productQRID, function (e) {
        e.preventDefault();

        if (e.which == 13) {

            var me = $(this),
                url = me.data('url'),
                form = $(formID);

            if(me.val() == 'CHANGEORDER') {
                me.val('');
                addFocusSelect(pickListBarcodeID);
                return false;
            }

            if(me.val() == 'CHANGEPACKAGE') {
                me.val('');
                addFocusSelect(packageBarcodeID);
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
                    addTextLabelBox(result.qtyProductInPackage);
                    addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
	
	
	
    // CLEAR BOX
    b.on('click',clearBoxBtID, function (e) {
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
                addTextLabelBox(result.qtyProductInPackage);
                addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
                $('#show-picking-list-items').html('');
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });
    // show PickingList Items
    b.on('click',showPickingListItemsBtID, function (e) {
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
                $('#show-picking-list-items').html(result.items);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });
    // PRINT BOX LABEL
    b.one('click',printBoxLabelBTID, function (e) {
        e.preventDefault();

        var me = $(this),
            url = me.data('url'),
        //validateUrl = me.data('validate-url'),
            form = $(formID);

        //window.location.href = url+'?'+form.serialize();
        //return false;

        errorBase.setForm(form);
        errorBase.hidden();

        $(me).text("пожалуйста подождите");

        $.post(url, form.serialize(),function (result) {
            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                if(result.pathToCargoLabelFile.length < 1) {
                    alert("Сторона дефакто не работает");
                    return false;
                }


                $(printCargoLabelBtID).attr('href',result.pathToCargoLabelFile);
                $(printWaybillDocumentBtID).attr('href',result.pathToWaybillFile);

                //$(printCargoLabelBtID).attr('href','/'+result.pathToCargoLabelFile);
                //$(printWaybillDocumentBtID).attr('href','/'+result.pathToWaybillFile);

                //$(printCargoLabelBtID).attr('href','/ecommerce/defacto/outbound/print-cargo-label?id='+result.orderId);
                //$(printWaybillDocumentBtID).attr('href','/ecommerce/defacto/outbound/print-waybill?id='+result.orderId);


                $(me).hide();
                $(printCargoLabelBtID).show();
                $(printWaybillDocumentBtID).show();
                resetForm();

                return false;
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });
    // PRINT DIFF LIST
    b.on('click',printDiffBtID, function (e) {

        var form = $(formID),
            url = $(this).data('url');

        console.info("-"+printDiffBtID+"-");
        console.info("Value : " + $(this).val());

        window.location.href = url+'?'+form.serialize();
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    function addQtyToOrderLabel(qty) {
        $(pickListLabel).text(qty);
        //$(pickListLabel).text(pickListLabelText+" / Всего: "+qty);
    }

    function addTextLabelBox(qty) {
        $(boxLabel).text(qty);
        //$(boxLabel).text(boxLabelText+" / Всего: "+qty);
    }
    function resetValueById(id) {
        $(id).val('');
    }

    function resetForm() {
        $(employeeBarcodeID).val('');
        $(pickListBarcodeID).val('');
        $(packageBarcodeID).val('');
        $(productBarcodeID).val('');
    }
});