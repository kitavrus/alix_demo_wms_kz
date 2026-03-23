$(function() {
    console.info("hyundaiTruck scan form outbound js");

    var formID = '#outboundform';
    var employeeBarcodeID = formID+'-employee_barcode';
    var pickListBarcodeID = formID+'-pick_list_barcode';
    var boxBarcodeID = formID+'-box_barcode';
    var productBarcodeID = formID+'-product_barcode';
	var productQtyID = formID+'-product_qty';
	
    //var fubBarcodeID = formID+'-fub_barcode';
    var _onPrintBoxLabelID = formID+'-_on_print_box_label';
    var printBoxLabelBTID = formID+'-print-box-label-for-order-bt'; // outboundform-print-box-label-for-order-bt

    var clearBoxBtID = formID+'-clear-box-bt';
    var printDiffBtID = formID+'-diff-list-bt';
    var showPickingListItemsBtID = formID+'-show-picking-list-items-bt';

    //var pickListLabel = 'label[for=outboundform-pick_list_barcode]';
    var pickListLabel = '#pick-list-barcode-qty';
    //var pickListLabelText = $(pickListLabel).text();

    //var boxLabel = 'label[for=outboundform-box_barcode]';
    var boxLabel = '#box-barcode-qty';
    //var boxLabelText = $(boxLabel).text();


    console.info(employeeBarcodeID);

    var b = $("body");

    b.on('submit',formID, function (e) {
        if($(_onPrintBoxLabelID).val() != 1) {
            e.preventDefault();
            return false;
        }
    });

    b.on('click',employeeBarcodeID+", "+pickListBarcodeID+", "+boxBarcodeID+", "+productBarcodeID+", "+productQtyID,function(e) {
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
                    addFocusSelect(boxBarcodeID);
                    addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);

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
                    addFocusSelect(productBarcodeID);
                    addTextLabelBox(result.qtyInBox);

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

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(me);
                    addTextLabelBox(result.qtyInBox);
                    addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });
	
	
    // PRODUCT Qty
    b.on('keyup',productQtyID, function (e) {
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

            errorBase.setForm(form);
            errorBase.hidden();

            $.post(url, form.serialize(),function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    addFocusSelect(me);
                } else {
                    errorBase.hidden();
                    addFocusSelect(me);
                    addTextLabelBox(result.qtyInBox);
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
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                //me.focus().select();
                addFocusSelect(me);
            } else {
                errorBase.hidden();
                addFocusSelect(productBarcodeID);
                addTextLabelBox(result.qtyInBox);
                addQtyToOrderLabel(result.accepted_qty+ ' из '+result.expected_qty);
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
            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();
                $('#show-picking-list-items').html(result.items);
            }
        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });
    });

    // PRINT BOX LABEL
    b.on('click',printBoxLabelBTID, function (e) {

        var me = $(this),
            url = me.data('url'),
        //validateUrl = me.data('validate-url'),
            form = $(formID);

        window.location.href = url+'?'+form.serialize();
        return false;
        /*
         errorBase.setForm(form);
         errorBase.hidden();

         $.post(validateUrl, form.serialize(),function (result) {
         if (result.success == 0 ) {
         errorBase.eachShow(result.errors);
         //me.focus().select();
         addFocusSelect(me);
         } else {
         errorBase.hidden();
         $(_onPrintBoxLabelID).val(1);
         form.submit();
         //window.location.href = url+'?id='+result.orderId;

         }
         }, 'json').fail(function (xhr, textStatus, errorThrown) {
         }); */
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
});