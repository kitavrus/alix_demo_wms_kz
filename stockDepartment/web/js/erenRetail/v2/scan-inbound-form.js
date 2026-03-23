
$(function() {

    var formID = '#inboundform';
    var datamatrixID = formID+'-datamatrix';
    var stockID = formID+'-stockid';
    var withDatamatrixID = formID+'-withdatamatrix';
    var productBarcodeID = formID+'-productbarcode';
	var acceptBtID = '#inbound-accept-bt';
	
	$(acceptBtID).hide();

    $('#inbound-form-box_barcode, #inbound-form-product_barcode'+","+datamatrixID).on('click',function(){
        $(this).focus().select();
    });

    $('#inbound-form-party-number').on('change',function() {

        var party_id = $(this).val(),
            inbound = $('#inbound-form-order-number'),
            url = $(this).data('url');
            dataOptions = '';

        if(party_id) {

            $.post(url, {'party_id': party_id}).done(function (result) {

                inbound.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                });

                inbound.append(dataOptions);
                inbound.focus().select();

                $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);

            }).fail(function () {
                console.log("server error");
            });

        } else {
            //inboundManager.hideByOrderAll();
        }
    });


    $('#inbound-form-order-number').on('change',function() {
        var inbound_id = $(this).val(),
            url = $(this).data('url');

        if(inbound_id) {

            $.post(url,
                {'inbound_id': $(this).val()}
            ).done(function (result) {

                $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
                $('#inbound-item-body').html(result.items);
                /*                        $('#countdown').attr('data-timer', result.cdTimer);
										if(result.cdTimer != 0){
											console.info ('-init timer-');
											initCountdown(result.cdTimer);
										}*/

                $('#inbound-form-box_barcode').focus().select();

//                        inboundManager().showByOrder();

            }).fail(function () {
                console.log("server error");
            });
        }
    });

    /**
	 * Scan box barcode
	 * */
    $("#inbound-form-box_barcode").on('keyup',  function (e) {
        if (e.which == 13) {

            console.info("-inbound-form-box_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this),
                form = $('#inbound-process-form'),
                url = $(this).data('url');

            errorBase.setForm(form);
            me.focus().select();
            var data = form.serialize();
            $.post(url, data,function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                    play();
                } else {
                    errorBase.hidden();
                    $("#inbound-form-product_barcode").focus().select();
                    $('#count-product-in-box').html(result.countProductInBox);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        }

        e.preventDefault();
    });

    /**
	 * Scan product barcode in box
	 * */
    $("#inbound-form-product_barcode").on('keyup', function (e) {
        /*
		 * TODO Добавить проверку
		 * TODO 1 ? Существует ли этот товар у нас на складе
		 * TODO 2 + Существует ли этот товар у этого клиента
		 */
        if (e.which == 13) {

            var me = $(this),
//                    countdown = $('#countdown'),
                url = $(this).data('url');
            console.info("-inbound-process-steps-product-barcode-");
            console.info("Value : " + me.val());

            me.focus().select();

            if(me.val() == 'CHANGEBOX') {
                $('#inbound-form-box_barcode').focus().select();
                me.val('');
                return true;
            }
			
            if(me.val() == 'готово') {
                $(acceptBtID).show();
                me.val('');
                return true;
            }

            var form = $('#inbound-process-form');
            var data = form.serialize();
            errorBase.setForm(form);

            $.post(url, data,function (result) {
                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                    play();
                } else {
                    errorBase.hidden();
                    $('#count-product-in-box').html(result.countProductInBox);
                    $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
                    $(stockID).val(result.stockId);

                    $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                    $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                    $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                    $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);


                    if ($("input[name='InboundForm[withDatamatrix]']:checked").val() === "0") {
                        addFocusSelect(productBarcodeID);
                    } else {
                        addFocusSelect(datamatrixID);
                    }

                }
            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });
        }
        e.preventDefault();
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }
    function resetFields() {
        $(productBarcodeID).val('');
    }

    $(datamatrixID).on('keyup', function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }
        var me = $(this),
            form = $('#inbound-process-form');

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+datamatrixID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {
            if (result.success == "0") {
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

    /**
	 * Click on List differences
	 * */
    $('#inbound-list-differences-bt').on('click',function() {
        var href = $(this).data('url'),
            printType = $('#inbound-process-form').data('printtype');
        if(printType == 'pdf'){
            window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
        } else if (printType == 'html'){
            //window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
            autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#inbound-form-order-number').val(), '0', 2500);
        }

    });

    /**
	 * Click on Unallocated list
	 * */
    $('#inbound-unallocated-list-bt').on('click',function() {
        var href = $(this).data('url'),
            printType = $('#inbound-process-form').data('printtype');
        if(printType == 'pdf'){
            window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
        } else if (printType == 'html'){
            //window.location.href = href + '?inbound_id=' + $('#inbound-form-order-number').val();
            autoPrintAllocatedListHtml(href + '?inbound_id=' + $('#inbound-form-order-number').val());
        }

    });

    $('#clear-product-in-box-by-one-bt').on('click',function() {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#inbound-form-box_barcode').val(),
            form = $('#inbound-process-form');

        console.info(boxBorderValue);
        errorBase.setForm(form);

        $.post(href, form.serialize(), function (result) {

            errorBase.hidden();

            //console.info(result);
            //console.info(result.errors);
            //console.info(result.errors.length);

            if (result.success == 0 ) {
                errorBase.eachShow(result.errors);
            } else {
                errorBase.hidden();

                console.info(result.countProductInBox);
                console.info(result.countScannedProductInOrder);
                console.info('#accepted-qty-'+result.dataScannedProductByBarcode.rowId);
                console.info('#row-'+result.dataScannedProductByBarcode.rowId);

                $('#count-product-in-box').html(result.countProductInBox);
                //$('#count-products-in-order').html(result.countScannedProductInOrder);
                $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                $('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
                $('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
                $('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);

                $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
            }

        }, 'json').fail(function (xhr, textStatus, errorThrown) {
        });

    });


    $('#clear-box-bt').on('click',function() {

        var href = $(this).data('url-value'),
            boxBorderValue = $('#inbound-form-box_barcode').val(),
            form = $('#inbound-process-form');

        console.info(boxBorderValue);

        errorBase.setForm(form);

        if( confirm('Вы действительно хотите очистить короб') ) {
            $.post(href, form.serialize(), function (result) {

                errorBase.hidden();

                if (result.success == 0 ) {
                    errorBase.eachShow(result.errors);
                } else {
                    errorBase.hidden();
                    $('#count-product-in-box').html('0');
                    //$('#count-products-in-order').html(result.countScannedProductInOrder);
                    $('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);

                    $.each(result.dataScannedProductByBarcode, function (key, value) {
                        console.info(value);
                        console.info(key);

                        $('#accepted-qty-'+value.rowId).html(value.countValue);
                        $('#row-'+value.rowId).removeClass('alert-danger alert-success');
                        $('#row-'+value.rowId).addClass(value.colorRowClass);


                    });

                    $('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {

            });
        } else {

        }
    });

    $('#inbound-accept-bt').on('click',function() {
        var messages_processText = $('#inbound-messages-process'),
            form = $('#inbound-process-form'),
            url = $(this).data('url');


        if(confirm('Вы уверены, что хотите закрыть накладную')) {

                $(messages_processText).html(' Подождите, идет обработка, не закрывайте браузер или вкладку ...');
                $.post(url,  form.serialize()).done(function (result) {
                    /* TODO Потом сделать вывод сообщений через bootstrap Modal   */

                    var alertMessage = '';

                    $.each(result.messages, function (key, value) {
                        if ( value.length) {
                            alertMessage += value+'\n';
                        }
                    });

                    alert(alertMessage);

                    $('#inbound-form-product_barcode').val('');
                    $('#inbound-form-box_barcode').val('');
                    $('#count-products-in-order').html('0/0');
                    $('#count-product-in-box').html('0');
                    $('#inbound-item-body').html('');

                    $(messages_processText).html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000,function() {
                        $(messages_processText).html('');
                        window.location.href = '/intermode/inbound/scanning';
                    } );

                }).fail(function () {
                    console.log("server error");
                });
        }
    });

    /* --------------------- START PLAY VOICE-------------------------------------- */
    var audioCtx;
    try {
        // создаем аудио контекст
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    } catch(e) {
        alert('Opps.. Your browser do not support audio API');
    }

    //var context = new window.AudioContext(); //
    // переменные для буфера, источника и получателя
    var buffer, source, destination;
    // функция для подгрузки файла в буфер
    var loadSoundFile = function (url) {
        if(buffer) {
            return;
        }

        // делаем XMLHttpRequest (AJAX) на сервер
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'arraybuffer'; // важно
        xhr.onload = function (e) {
            // декодируем бинарный ответ
            audioCtx.decodeAudioData(this.response,
                function (decodedArrayBuffer) {
                    // получаем декодированный буфер
                    buffer = decodedArrayBuffer;
                    //play();

                }, function (e) {
                    console.log('Error decoding file', e);
                });
        };
        xhr.send();
    };
    // функция начала воспроизведения
    var play = function () {
        // создаем источник
        source = audioCtx.createBufferSource();
        // подключаем буфер к источнику
        source.buffer = buffer;
        // дефолтный получатель звука
        destination = audioCtx.destination;
        // подключаем источник к получателю
        source.connect(destination);
        // воспроизводим
        source.start(0);
    };
    // функция остановки воспроизведения
    //    var stop = function () {
    //        source.stop(0);
    //    };

    //loadSoundFile('/error.m4a');
    loadSoundFile('/sounddrom.mp3');
    // VOICE END
    /* --------------------- END PLAY VOICE-------------------------------------- */

});