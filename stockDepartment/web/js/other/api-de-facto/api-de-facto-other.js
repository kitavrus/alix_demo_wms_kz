/**
 * Created by kitavrus on 03.02.15.
 */

$(function() {

    var b = $('body');

    console.info('init api-de-facto-other');

    $('#get-inbound-order-form, #get-cross-dock-order-form, #get-outbound-order-form').on('submit', function (e) {
        return false;
    });


    b.on('click','#get-inbound-order-api-de-facto-bt, #get-outbound-order-api-de-facto-bt, #get-cross-dock-order-api-de-facto-bt',function() {

        var url = $(this).data('url'),
            me = $(this);

        $('#buttons-menu').find('.btn').removeClass('focus');

        $.post(url,function(data) {

            me.addClass('focus');

            $('#container-api-de-facto-layout').html(data);
        });

    });

    //S: #INTBOUND
    b.on('click','#get-inbound-order-submit-bt',function() {

        var url = $(this).data('url'),
            me = $(this),
            form = $('#get-inbound-order-form');

        errorBase.setForm(form);
        errorBase.hidden();

        $('#get-inbound-order-button-message').html(' Пожалуйста подождите, идет обработка запроса...').show();

        $.post(url, form.serialize()).done(function (result) {

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
                $('#get-inbound-order-button-message').html('');
            } else {
                errorBase.hidden();

                $('#get-inbound-order-button-message').html(' Данные успешно загружены').fadeOut(5000);
                $('#apidefactoform-invoice').val();

                $('#inbound-order-uploaded-grid').html(result.gridData);
            }

        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('click','#yes-upload-inbound-data-bt',function() {
        console.info('click yes-upload-inbound-data-bt');

        var clientId = $(this).data('client-id'),
            uniqueKey = $(this).data('unique-key');

        if(clientId && uniqueKey) {

            $('#show-status-message').html(' [ Подождите ... ] ').show();

            $.post('/inbound/default/upload-file-confirm', {'client_id': clientId,'unique_key':uniqueKey})
                .done(function (result) {

                    $('#show-status-message').html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000 );
                    $('#grid-view-inbound-order-items').fadeOut( 7000 );
                    $('#alert-message-inbound').fadeOut( 7000 );

                })
                .fail(function () {
                    console.log("server error");
                });
        }
    });

    //S: #OUTBOUND
    b.on('click','#get-outbound-order-submit-bt',function() {

        console.info('click #get-outbound-order-submit-bt');

        var url = $(this).data('url'),
            me = $(this),
            form = $('#get-outbound-order-form');

        errorBase.setForm(form);
        errorBase.hidden();

        $('#get-outbound-order-button-message').html(' Пожалуйста подождите, идет обработка запроса...').show();

        $.post(url, form.serialize()).done(function (result) {

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
                $('#get-outbound-order-button-message').html('');
            } else {
                errorBase.hidden();

                $('#get-outbound-order-button-message').html(' Данные успешно загружены').fadeOut(5000);
                $('#apidefactoform-invoice').val('');

                $('#outbound-order-uploaded-grid').html(result.gridData);
            }

        },'json').fail(function () {
            console.log("server error");
        });

    });

    //S: #CROSS-DOCK
    b.on('click','#get-cross-dock-order-submit-bt',function() {

        var url = $(this).data('url'),
            me = $(this),
            form = $('#get-cross-dock-order-form');

        errorBase.setForm(form);
        errorBase.hidden();

        $('#get-cross-dock-order-button-message').html(' Пожалуйста подождите, идет обработка запроса...').show();

        $.post(url, form.serialize()).done(function (result) {

            if (result.success == 0) {
                errorBase.eachShow(result.errors);
                me.focus().select();
                $('#get-cross-dock-order-button-message').html('');
            } else {
                errorBase.hidden();

                $('#get-cross-dock-order-button-message').html(' Данные успешно загружены').fadeOut(5000);
                $('#apidefactoform-invoice').val();

                $('#cross-dock-order-uploaded-grid').html(result.gridData);
            }

        }).fail(function () {
            console.log("server error");
        });

    });

    b.on('click','#yes-upload-cross-dock-data-bt',function() {
        console.info('click yes-upload-cross-dock-data-bt');

        var clientId = $(this).data('client-id'),
            uniqueKey = $(this).data('unique-key');

        if(clientId && uniqueKey) {

            $('#show-status-message').html(' [ Подождите ... ] ').show();

            $.post('/other/api-de-facto/cross-dock-confirm', {'client_id': clientId,'unique_key':uniqueKey})
                .done(function (result) {

                    $('#show-status-message').html(' [ '+'Данные успешно загружены ] ').fadeOut( 5000 );
                    $('#grid-view-cross-dock-order-items').fadeOut( 7000 );
                    $('#alert-message-cross-dock').fadeOut( 7000 );

                })
                .fail(function () {
                    console.log("server error");
                });
        }
    });

});
