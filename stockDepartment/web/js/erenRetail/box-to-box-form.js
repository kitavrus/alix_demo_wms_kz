
$(function() {

    var formID = '#boxtoboxform';
    var fromBoxID = formID+'-frombox';
    var toBoxID = formID+'-tobox';
    var successListID = $('#success-list');

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
	


    var init = function() {
        console.info('INIT box-to-box-form');
        addFocusSelect(fromBoxID);
    };

    init();

    var b = $("body");

    b.on('submit',formID, function (e) {
        return false;
    });
    //
    b.on('click',fromBoxID+", "+toBoxID,function(e) {
        console.info($(this).attr('id'));
        addFocusSelect(this);
    });
    //
    b.on('keyup',fromBoxID, function (e) {
        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        console.info("-"+fromBoxID+"-");
        console.info("Value : " + $(this).val());

        var me = $(this),
            form = $(formID),
            url = $(this).data('url');

        errorBase.setForm(form);
        errorBase.hidden();

        console.info(form.serialize());

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N' ) {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
				 play();
            } else {
                errorBase.hidden();
                addFocusSelect(toBoxID);
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка. НАШ КОРОБ"); });
    });
    //
    //
    b.on('keyup',toBoxID, function (e) {

        e.preventDefault();
        if (e.which != 13) {
            return false;
        }

        var me = $(this),
            form = $(formID);

        errorBase.setForm(form);
        errorBase.hidden();

        console.info("-"+toBoxID+"-");
        console.info("Value : " + $(this).val());

        var url = $(this).data('url');

        $.post(url, form.serialize(),function (result) {

            if (result.success == 'N') {
                errorBase.eachShow(result.errors);
                addFocusSelect(me);
				 play();
            } else {
                errorBase.hidden();
                addFocusSelect(fromBoxID);

                successListID.fadeIn( "fast",function(){
                    successListID.removeClass('hidden').text("Успешно перемещен");
                }).fadeOut(1000,function(){
                    successListID.addClass('hidden').text("");
                });
            }

        }, 'json')
        .fail(function (xhr, textStatus, errorThrown) { alert("Произошла неизвестная ошибка"); });
    });



    //
    function addFocusSelect(id) {
        $(id).focus().select();
    }
});