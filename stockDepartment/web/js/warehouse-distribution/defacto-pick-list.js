/**
 * Created by KitavrusAdmin on 30.08.2017.
 */



//$(function(){
//    $( "#key-1" ).bind( "tap", tapHandler );
//
//    function tapHandler( event ){
//        $( event.target ).css("font-size", "30px");
//    }
//});


$(function () {
    console.info(' init DeFacto Pick List Scan ');
    var b = $('body');
    var formId = '#pick-list-scan-form';
    var pickListBarcodeId = '#picklistscanform-picklistbarcode';
    var lotBarcodeId = '#picklistscanform-lotbarcode';
    var currentOnFocus = $(pickListBarcodeId);


    addFocusSelect(pickListBarcodeId);


    b.on('submit', '#pick-list-scan-form', function (e) {
        return false;
    });

    b.on('keyup', pickListBarcodeId, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formId),
            url = me.data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(), function (result) {

            console.info(result);

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
                play();
            } else {
                errorBase.hidden();
                addFocusSelect(lotBarcodeId);
            }
//
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });

    });

    b.on('keyup', lotBarcodeId, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formId),
            url = me.data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        $.post(url, form.serialize(), function (result) {

            console.info(result);

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                play();
            } else {
                errorBase.hidden();
            }
            addFocusSelect(me);
//
        }, 'json').fail(function (xhr, textStatus, errorThrown) {

        });
    });

    function addFocusSelect(id) {
        $(id).focus().select();
    }


    b.on('click', pickListBarcodeId, function (e) {
        //e.preventDefault();
        currentOnFocus = this;
    });

    b.on('click', lotBarcodeId, function (e) {
        //e.preventDefault();
        currentOnFocus = this;
    });

     $(".key").click(function (e) {

        var key = $(this).text();

        console.info($(currentOnFocus).attr('id'));
        console.info($(this).text());
        addFocusSelect(currentOnFocus);

        //alert($(this).text());

        if( key != 'del' && key != 'enter' ) {
            $(currentOnFocus).val($(currentOnFocus).val() + $(this).text());
        }

        if( key == 'enter') {
            var ev = $.Event("keyup", {which: 13, keyCode: 13});
            $(currentOnFocus).trigger(ev);
        }

        if( key == 'del' ){
             $(currentOnFocus).val( $(currentOnFocus).val().substr(0, $(currentOnFocus).val().length - 1) ).focus();
        }
    });




    /* ----------------------------------------------------------- */
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

    loadSoundFile('/error.m4a');
});