/**
 * Created by kitavrus on 26.08.14.
 */
$(function () {
    $('#order-run-scanned-item-form').on('submit', function (e) {
        return false;
    });

    $("#tlorderitems-box_barcode").on('keyup', function (e) {
        /*
         * TODO Добавить проверку
         * TODO 1 + Существует ли этот короб у нас на складе
         * TODO 2 + Если ли в этом коробе уже товары из другого магазина
         * */
        if (e.which == 13) {
            console.info("-tlorderitems-box_barcode-");
            console.info("Value : " + $(this).val());

            var me = $(this);
            var form = $('#order-run-scanned-item-form');
            errorBase.setForm(form);

            $.post('set-status-scanned-box', form.serialize(),function (response) {
                if (response.errors) {
                    errorBase.eachShow(response.errors);
                } else {
                    errorBase.hidden();
                    $('#count-product-in-box').html(response.countProductInBox);
                }

                me.focus().select();

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
//                alert(errorThrown);
            });

        }

        e.preventDefault();
    });

});

/*S: TRANSPORT logistic */

$(function () {

    $('body').on('click', '#form-route-bt', function () {
        HideClearPopup();
        var url = $(this).data('url'),
            me = $(this);
        $.post(url,function(data) {

            if(data.status == 'more') {
                $('#delivery-proposal-index-modal').
                    modal('show')
                    .find('#delivery-proposal-index-content').html(data.html);

            } else if(data.status == 'one') {

                if(confirm('Вы точно хотите сформировать маршрут для этой заявки?')) {
                    var btText = me.text(),
                        div = $('#proposal-routes');

                    me.text('Маршруты формируются, пожалуйста подождите...');
                    console.info(me);
                    console.info(me.data('url-save'));
                    setTimeout(function(){
                        $.post(me.data('url-save'), {'id':me.data('id')}, function(result){
                            if(result.message == 'success'){
                                me.hide();
                                div.html(result.data);
                                $('html,body').stop().animate({ scrollTop: div.offset().top }, 1000);
                            } else {
                                me.text(result.message);
                                setTimeout(function(){
                                    me.text(btText);
                                }, 3000);
                            }
                        });
                    },1000);
                }
            }
        });
        return false;

/*        $('#delivery-proposal-index-modal').
            modal('show')
            .find('#delivery-proposal-index-content')
            .load($(this).data('url'));*/
        /*return false;*/

/*        if(confirm('Вы точно хотите сформировать маршрут для этой заявки?')) {
            var me = $(this),
                btText = me.text(),
                div = $('#proposal-routes');

            me.text('Маршруты формируются, пожалуйста подождите...');
            setTimeout(function(){
                $.post(me.data('url'), {'id':me.data('id')}, function(result){
                    if(result.message == 'success'){
                        me.hide();
                        div.html(result.data);
                        $('html,body').stop().animate({ scrollTop: div.offset().top }, 1000);
                    } else {
                        me.text(result.message);
                        setTimeout(function(){
                            me.text(btText);
                        }, 3000);
                    }

                });
            },1000);

        }*/

    });

    $('body').on('click', '#make-ready-invoice-bt', function () {
        if(confirm('Вы точно хотите пометить заявку как "Готова к выставлению счета?"')){
            var me = $(this),
                label = $('#ready-invoice-title');

            setTimeout(function(){
                $.post(me.data('url'), {'id':me.data('id')}, function(result){
                    if(result.success == '1'){
                        me.hide();
                        label.text(result.title);
                    } else {
                       alert(result.errors);
                    }

                });
            },500);

        }

    });

    $('.add-new-route-car-bt').on('click', function () {
        console.info('.add-route-car-bt CLICK');
        $('#add-new-route-car-modal').
            modal('show')
            .find('#modal-route-car-content')
            .load($(this).data('value'));

    });

    $('.add-route-bt').on('click', function () {
        console.info('.add-route-bt CLICK');
        $('#add-new-rout-modal').
            modal('show')
            .find('#modalContent')
            .load($(this).data('value'));

    });


    $('.update-route-car-bt').on('click',function(){
        var  id = $('#tldeliveryroutes-tl_delivery_proposal_route_car_id option:selected').val();

        $('#add-new-route-car-modal').
            modal('show')
            .find('#modal-route-car-content')
            .load($(this).data('value')+'?id='+id);

        console.info(id);
    });

/*    $('.add-route-bt').on('click', function () {
        console.info('.add-route-bt CLICK');
        $('#add-new-rout-modal').
            modal('show')
            .find('#modalContent')
            .load($(this).data('value'));

    });*/

    $('#print-ttn-btn').on('click', function () {
        console.info('#print-ttn-bt CLICK');
        var printType = $(this).data('printtype'),
            href =$(this).data('href');

        if(printType == 'pdf'){
            window.location.href = href;
        } else if (printType == 'html'){
            autoPrintTtn(href)
        }

    });


});


function addCarRouteSubmitForm($form) {

    $.post(
            $form.attr("action"), // serialize Yii2 form
            $form.serialize()
        )
        .done(function (result) {
            $form.parent().html(result.message);
            $('#add-new-route-car-modal').modal('hide');

//            modal.info(result.data_options);
            $('#tldeliveryroutes-tl_delivery_proposal_route_car_id').html('');
            $.each(result.data_options, function (key, value) {
                console.info(key);
                console.info(value);

                $('#tldeliveryroutes-tl_delivery_proposal_route_car_id').append('<option value="' + key + '">' + value + '</option>');
            });

        })
        .fail(function () {
            console.log("server error");
            $form.replaceWith('<button class="newType">Fail</button>').fadeOut()
        });

    return false;
}



function addRouteSubmitForm($form) {

    $.post(
            $form.attr("action"), // serialize Yii2 form
            $form.serialize()
        )
        .done(function (result) {
            $form.parent().html(result.message);
            $('#add-new-rout-modal').modal('hide');

            console.info(result.data_options);

            var fromValue = $('#tldeliveryproposal-route_from option:selected').val(),
                toValue = $('#tldeliveryproposal-route_to option:selected').val();


            $('#tldeliveryproposal-route_from, #tldeliveryproposal-route_to').html('');
            $.each(result.data_options, function (key, value) {
                console.info(key);
                console.info(value);

                $('#tldeliveryproposal-route_from, #tldeliveryproposal-route_to').append('<option value="' + key + '">' + value + '</option>');
            });

            $("#tldeliveryproposal-route_from [value='"+fromValue+"']").attr("selected", "selected");
            $("#tldeliveryproposal-route_to [value='"+toValue+"']").attr("selected", "selected");



        })
        .fail(function () {
            console.log("server error");
            $form.replaceWith('<button class="newType">Fail</button>').fadeOut()
        });

    return false;
}

function autoPrintTtn(url, timeout, orientation)
{
    if (typeof jsPrintSetup != 'undefined') {

        var printers = jsPrintSetup.getPrintersList().split(','), // get a list of installed printers
            A4p = '',
            BLp = '',
            printer = '';
        for (var k in printers) {

            printer = printers[k];

            switch (printer) {
                case "\\\\192.168.1.2\\Samsung M2070 Series":
                //case "\\\\192.168.1.6\\Xerox Phaser 3320":
                case "\\\\192.168.1.6\\Xerox Phaser 3320 (Копия 1)":
                //case "Microsoft XPS Document Writer":
                case "Xerox Phaser 3320":
                    A4p = printer;
                    break;
                case "\\\\192.168.1.4\\TSC TDP-244":
                //case "Microsoft XPS Document Writer":
                case "TSC TDP-244":
                    BLp = printer;
                    break;
            }

        }
        if(typeof timeout == 'undefined'){
            timeout = 2000;
        }

        if(typeof orientation == 'undefined'){
            orientation = 1;
        }

            jsPrintSetup.setPrinter(A4p);
            jsPrintSetup.setOption('printBGColors', '0');
            jsPrintSetup.setOption('paperSizeType','A4');

        jsPrintSetup.clearSilentPrint();
        jsPrintSetup.refreshOptions();
        jsPrintSetup.setSilentPrint(1); // 0 -show print settings dialog, 1 - not show print settings dialog
        jsPrintSetup.setOption('orientation', orientation); // 1 - kLandscapeOrientation 0 - kPortraitOrientation
        jsPrintSetup.setOption('shrinkToFit', 1);
        jsPrintSetup.setOption('headerStrLeft', '');
        jsPrintSetup.setOption('headerStrCenter', '');
        jsPrintSetup.setOption('headerStrRight', '');
        jsPrintSetup.setOption('footerStrLeft', '');
        jsPrintSetup.setOption('footerStrCenter', '');
        jsPrintSetup.setOption('footerStrRight', '');
        jsPrintSetup.setOption('marginTop', '0');
        jsPrintSetup.setOption('marginRight', '0');
        jsPrintSetup.setOption('marginBottom', '0');
        jsPrintSetup.setOption('marginLeft', '0');

        window.frames['print-ttn-frame'].location.href = url;

        $( window.frames['print-ttn-frame'] ).ready(function() {
            setTimeout(function(){
                jsPrintSetup.printWindow(window.frames['print-ttn-frame']);
                jsPrintSetup.setSilentPrint(0);
                jsPrintSetup.clearSilentPrint();
            },timeout);
        });

    }

    return false;
}

/*E: TRANSPORT logistic */