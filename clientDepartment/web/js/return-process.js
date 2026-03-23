/**
 * Created by kitavrus on 18.03.15.
 */

$(function () {
    console.info('INIT Return JS');

    var b = $('body'),
        store = $("#returnform-store_id"),
        order = $("#returnform-inbound_order_number");

    if(store.val() && order.val()) {
        var url = order.data('url'),
            party_id = order.val();

        if(party_id) {
            $.post(url, {'party_id': party_id})
                .done(function (result) {
                    $('#inbound-item-body').html(result.items);
                })
                .fail(function () {
                    console.log("server error");
                });
        } else {
            $('#inbound-item-body').html('');
        }
    }


    b.on('change', '#returnform-store_id', function () {
        var me = $(this),
            url = me.data('url'),
            store_id = me.val()
            ;
        if(store_id) {
            $.post(url, {'store_id': store_id})
                .done(function (result) {

                    $('#returnform-inbound_order_number').html('');

                    $.each(result.data_options, function (key, value) {
                        $('#returnform-inbound_order_number').append('<option value="' + key + '">' + value + '</option>');
                    });
                })
                .fail(function () {
                    console.log("server error");
                });
        }
    });

    b.on('change', '#returnform-inbound_order_number', function () {
        var me = $(this),
            url = me.data('url'),
            party_id = me.val(),
            delete_button = $('#return-delete-inbound-order-bt');
        if(party_id) {
            $.post(url, {'party_id': party_id})
                .done(function (result) {
                    $('#inbound-item-body').html(result.items);
                })
                .fail(function () {
                    console.log("server error");
                });
        } else {
            $('#inbound-item-body').html('');
        }
        delete_button.attr('data-party_id', party_id);
        delete_button.removeClass('hidden');
    });

    b.on('click', '#return-delete-inbound-order-bt', function () {

        if (confirm('Вы действительно хотите удалить накладную ?')) {

            var me = $(this),
                url = me.data('url'),
                party_id = me.data('party_id');

            //console.log(party_id);

            if (party_id) {
                $.post(url, {'party_id': party_id})
                    .done(function (result) {
                        console.log("done");
                    })
                    .fail(function () {
                        console.log("server error");
                    });

            } else {
                alert("Пожалуйста выберите накладную");
            }
        }

    });

    b.on('click', '#return-delete-inbound-item-bt', function () {

        if (confirm('Вы действительно хотите удалить короб ?')) {

            var me = $(this),
                url = me.data('url'),
                item_id = me.data('item_id');

            if (item_id) {
                $.post(url, {'item_id': item_id})
                    .done(function (result) {
                        if(result.item_id > 0){
                            me.closest('tr').remove();
                        }

                    })
                    .fail(function () {
                        console.log("server error");
                    });

            } else {
                alert("Пожалуйста выберите короб");
            }
        }

    });


    b.on('click', '#return-process-form-generate-new-bt', function () {

        if (confirm('Вы действительно хотите сгенерировать новую накладную')) {

            var me = $(this),
                url = me.data('url'),
                store_id = $('#returnform-store_id').val();

            if (store_id) {
                $.post(url, {'store_id': store_id, 'generate_new': true})
                    .done(function (result) {

                        $('#returnform-inbound_order_number').html('');

                        $.each(result.data_options, function (key, value) {
                            $('#returnform-inbound_order_number').append('<option value="' + key + '">' + value + '</option>');
                        });
                    })
                    .fail(function () {
                        console.log("server error");
                    });

            } else {
                alert("Пожалуйста выберите магазин");
            }
        }

    });


    b.on('click', '#return-process-form-accept-inbound-order-bt', function () {

        if (confirm('Вы действительно хотите отправить накладную на обработку складу')) {

            var me = $(this),
                url = me.data('url'),
                store_id = $('#returnform-store_id').val(),
                party_id = $('#returnform-inbound_order_number').val();

            if (store_id && party_id) {

                $.post(url, {'store_id': store_id,'party_id':party_id})
                .done(function (result) {
                    if(result.errors == 0) {
                        $('#returnform-inbound_order_number').html('');

                        $.each(result.data_options, function (key, value) {
                            $('#returnform-inbound_order_number').append('<option value="' + key + '">' + value + '</option>');
                        });
                        window.location.href = '/returnOrder/default/index';
                    } else {
                        window.location.href = '/returnOrder/default/index?store_id='+result.store_id+'&party_id='+result.party_id;
                    }

                })
                .fail(function () {
                    console.log("server error");
                });

            } else {
                alert("Пожалуйста выберите магазин и накладную");
            }
        }

    });

    b.on('click', '#return-process-form-upload-submit-bt', function () {
        setTimeout(function(){
            if(!$('.field-returnform-file').hasClass('has-error')) {
                $('#return-process-form-upload-message').html('  <strong> Пожалуйста подождите, идет обработка файлов </strong>');
            }
        },1000);

    });
});