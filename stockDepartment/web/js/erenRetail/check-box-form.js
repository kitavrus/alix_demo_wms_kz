$(function() {
    console.info("Defacto B2B scan check box form outbound js");

    var formID = '#checkboxform';
    var employeeBarcodeID = formID+'-employeebarcode';
    var inventoryKeyID = formID+'-inventoryid';
    var placeAddressID = formID+'-placeaddress';
    var boxBarcodeID = formID+'-boxbarcode';
    var productBarcodeID = formID+'-productbarcode';
    var showProductsInBoxBtID = formID+'-show-products-in-box-bt';
    var emptyBoxBtID = formID+'-empty-box-bt';
    var showProductsInBoxContainerID = '#show-products-in-box-container';
    var showProductsInInventoryID = '#count-products-in-inventory';
    var showProductsInBoxID = '#count-products-in-box';



    /* Audio */
    var audioCtx;
    try {
        // создаем аудио контекст
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    } catch(e) {
        alert('Opps.. Your browser do not support audio API');
    }
    var buffer, source, destination;
    var loadSoundFile = function (url) {
        if(buffer) {
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'arraybuffer'; // важно
        xhr.onload = function (e) {
            audioCtx.decodeAudioData(this.response,
                function (decodedArrayBuffer) {
                    buffer = decodedArrayBuffer;

                }, function (e) {
                    console.log('Error decoding file', e);
                });
        };
        xhr.send();
    };

    var play = function () {
        source = audioCtx.createBufferSource();
        source.buffer = buffer;
        destination = audioCtx.destination;
        source.connect(destination);
        source.start(0);
    };
    loadSoundFile('/sounddrom.mp3');



    console.info(employeeBarcodeID);

    resetForm();
    addFocusSelect(employeeBarcodeID);

    //alert("xxxx");

    var b = $("body");

    b.on('submit',formID, function (e) {
        //if($(_onPrintBoxLabelID).val() != 1) {
        //    e.preventDefault();
        //    return false;
        //}
            e.preventDefault();
            return false;
    });

    b.on('click',employeeBarcodeID+", "+inventoryKeyID+", "+placeAddressID+", "+boxBarcodeID+", "+productBarcodeID,function(e) {
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
                    play();
                } else {
                    errorBase.hidden();
                    addFocusSelect(placeAddressID);
                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }
    });

    //
    b.on('change',inventoryKeyID,function(e) {

        showProductsInBox('');

        if($(this).val() == '') {
            return;
        }

        var me = $(this),
            form = $(formID),
            url = $(this).data('url');


        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url,form.serialize(),function (result) {

            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
                play();
            } else {
                errorBase.hidden();
                showProductsInBox('');
                showLabelInfoForInventoryKey(result.result.qtyScannedProductInInventory+' из '+result.result.qtyExpectedProductInInventory);
                showLabelInfoForBoxBarcode(result.result.qtyScannedProductInBox+' из '+result.result.qtyExpectedProductInBox);

            }
        },'json')
            .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. ВЫБОР НАКЛАДНОЙ");});
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
                    play();
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
                    play();
                } else {
                    errorBase.hidden();
                    addFocusSelect(productBarcodeID);
                    showProductsInBox(result.result.showProductsInBoxHTML);

                    showLabelInfoForInventoryKey(result.result.qtyScannedProductInInventory+' из '+result.result.qtyExpectedProductInInventory);
                    showLabelInfoForBoxBarcode(result.result.qtyScannedProductInBox+' из '+result.result.qtyExpectedProductInBox);

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
                    play();
                } else {
                    errorBase.hidden();
                    addFocusSelect(me);
                    showProductsInBox(result.result.showProductsInBoxHTML);

                    showLabelInfoForInventoryKey(result.result.qtyScannedProductInInventory+' из '+result.result.qtyExpectedProductInInventory);
                    showLabelInfoForBoxBarcode(result.result.qtyScannedProductInBox+' из '+result.result.qtyExpectedProductInBox);
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
                play();
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
                play();
            } else {
                errorBase.hidden();
                addFocusSelect(boxBarcodeID);
                showProductsInBox('');

                showLabelInfoForInventoryKey(result.result.qtyScannedProductInInventory+' из '+result.result.qtyExpectedProductInInventory);
                showLabelInfoForBoxBarcode(result.result.qtyScannedProductInBox+' из '+result.result.qtyExpectedProductInBox);
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

    function showLabelInfoForEmployee(){}
    function showLabelInfoForInventoryKey(html) {
        $(showProductsInInventoryID).html(html);
    }
    function showLabelInfoForPlaceAddress(){}
    function showLabelInfoForBoxBarcode(html) {
        $(showProductsInBoxID).html(html);
    }
    function showLabelInfoForProductBarcode(){}

});