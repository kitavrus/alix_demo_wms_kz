/**
 * Created by Igor on 15.01.2015.
 */

var accommodationManager = function () {

    var e = '';

    return {
        'onLoad': function() {

        }
    }
};

$(function () {

    var accommodationModel = new accommodationManager(),
        b = $('body'),
        codLenAccommodation = 0;

    var accommodationFormFromID = '#accommodationform-from';
    var accommodationFormToID = '#accommodationform-to';
    var currentOnFocus = $(accommodationFormFromID);

    accommodationModel.onLoad();

    b.on('change','#stock-accommodation-type',function() {

        var typeValue = $('#stock-accommodation-type').val();

        if(typeValue) {
            console.log(labelArray[typeValue].from);
        }

        $('#accommodationform-from').focus().select();

        errorBase.setForm($('#stock-accommodation-process-form'));

        //$('#error-listaccommodationform-from').remove();
        //$('#error-listaccommodationform-to').remove();
        //errorBase.hidden();

        //$('#error-container').html('');
    });

    /*
     * From
     * */
    b.on('submit','#stock-accommodation-process-form', function (e) {
        return false;
    });
    b.on('click',"#accommodationform-from, #accommodationform-to", function (e) {

      $(this).focus().select();
      errorBase.setForm($('#stock-accommodation-process-form'));
      errorBase.hidden();
      var messagesListElm = $('#messages-list'),
          messagesListBodyElm = $('#messages-list-body');

      messagesListElm.addClass('hidden');
      messagesListElm.removeClass('alert-info alert-success');
      //messagesListBodyElm.html('');

    });
    b.on('keyup',"#accommodationform-from, #accommodationform-to", function (e) {

        var codStr = $(this).val();

        if(codStr.substr(-2) == '##') {
            codLenAccommodation++;
        }

        if (e.which == 13 || (codStr.substr(-2) == '##' && codLenAccommodation == 3)) {
            codLenAccommodation = 0;
            var me = $(this),
                moveType = $('#stock-accommodation-type'),
                form = $('#stock-accommodation-process-form'),
                to = $('#accommodationform-to'),
                from = $('#accommodationform-from');

            console.info("-accommodationform-from-");
            console.info("Value : " + me.val());
            console.info("ID : " +me.attr('id'));
            console.info("Move type : " +moveType.val());

            errorBase.setForm(form);

            //$('#error-listaccommodationform-from').remove();
            //$('#error-listaccommodationform-to').remove();
            //errorBase.hidden();

            $.post('/stock/accommodation/move-from-to', form.serialize(),function (result) {

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                    play();
                } else {
                    errorBase.hidden();

                    if(me.attr('id') == 'accommodationform-from') {
                        console.log('++1');
                        to.focus().select();
                    } else {
                        from.focus().select();
                        to.val('');
                        console.log('++2');
                    }

                  var messagesListElm = $('#messages-list'),
                      messagesListBodyElm = $('#messages-list-body');

                    messagesListElm.addClass('hidden');
                    messagesListElm.removeClass('alert-info alert-success');
                    messagesListBodyElm.html('');

                    if(result.successMessages.length >= 1) {
                        messagesListBodyElm.html(result.successMessages);
                        messagesListElm.addClass('alert-success');
                        messagesListElm.removeClass('hidden');
                    }

                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });
        }

        e.preventDefault();

        return false;
    });

    // VOICE BEGIN

    function addFocusSelect(id) {
        $(id).focus().select();
    }

    b.on('click', accommodationFormFromID, function (e) {
        currentOnFocus = this;
    });

    b.on('click', accommodationFormToID, function (e) {
        currentOnFocus = this;
    });

    $(".key").click(function (e) {

        var key = $(this).text();

        //console.info($(currentOnFocus).attr('id'));
        //console.info($(this).text());
        addFocusSelect(currentOnFocus);

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


    // VOICE END





});