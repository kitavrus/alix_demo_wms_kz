/**
 * Created by kitavrus on 03.02.15.
 */

$(function() {
    var b = $('body');

    b.on('click', '#outbound-print-bt', function(){
        console.info('init outbound-process');
        window.location.href = $(this).data('url-value');

    });

    b.on('click', '#item-lost-full-bt', function(){
        window.location.href = $(this).data('url-value');

    });

    b.on('click', '#item-lost-found-bt', function(){
        window.location.href = $(this).data('url-value');

    });

    b.on('click', '#print-stock-address-barcode-bt', function(){
        console.log('click');
        if($('#address').val().length > 0){
            var href = $(this).data('url-value');
            window.location.href = href + '?address='+ $('#address').val();
        }
    });

    b.on('click','#print-lost-list-bt',function(){

        var keys = $('#lost-grid').yiiGridView('getSelectedRows');

        if(keys.length < 1) {

           alert('Нужно выбрать хотябы одну запись');

        } else {

            var href = $(this).data('url-value');

            window.location.href = href + '?ids='+keys.join();
        }
    });

    b.on('click','#print-unalloc-list-bt',function(){

        var keys = $('#unalloc-grid').yiiGridView('getSelectedRows'),
                   href = $(this).data('url-value');

        if(keys.length < 1) {

            window.location.href = href + "?" + $('#unallocated-box-search-form').serialize();

        } else {
            window.location.href = href + '?ids='+keys.join();
        }
    });

    //b.on('click','#upload-outbound-order-load-bt',function() {
    //
    //    console.log('click #upload-outbound-order-load-bt');
    //    console.log( $(this).data('url'));
    //    console.log( $(this).data('unique-key'));
    //    console.log( $(this).data('client-id'));
    //
    //    var url = $(this).data('url'),
    //        uniqueKey = $(this).data('unique-key'),
    //        clientId = $(this).data('client-id');
    //
    //    $('#show-status-message').html(' [ Подождите ... ] ').show();
    //
    //    $.post(url,{'unique-key':uniqueKey,'client-id':clientId},function() {
    //
    //        $('#show-status-message').html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000 );
    //
    //        setTimeout(function(){
    //            window.location.href = '/outbound/default/index';
    //        },5000)
    //
    //    });
    //
    //});

});
