/**
 * Created by Kitavrus on 22.04.15.
 */

$(function() {

    var b = $('body');
   $('#confirmcrossdockform-cross_dock_barcode').focus();


    console.info('init cross-dock');

    b.on('change', '#cross-dock-form-client-id', function(){
        var client_id = $(this).val(),
            e = $('#cross-dock-form-order-number'),
            dataOptions = '';

        if(client_id) {

            $.post('get-cross-dock-orders-by-client-id', {'client_id': client_id}).done(function (result) {

                e.html('');

                $.each(result.dataOptions, function (key, value) {
                    dataOptions = '<option value="' + key + '">' + value + '</option>' + dataOptions;
                });

                e.append(dataOptions);
                e.focus().select();

            }).fail(function () {
                console.log("server error");
            });

        }

    } );

    b.on('click', '#cross-dock-print-bt', function(){
        var client_id = $('#cross-dock-form-client-id').val(),
            party_number = $('#cross-dock-form-order-number').val(),
            href = $(this).data('url');

           if(client_id.length  < 1){
               alert('Выберите клиента');
               return false;
           }

           if(party_number.length < 1) {
               alert('Выберите заказ');
               return false;
           }

        window.location.href = href + '?client_id=' + client_id + '&party_number=' + party_number;
        return true;
    });

    b.on('click', '#cross-dock-confirm-bt', function(){
       if(confirm('Вы точно хотите подтвердить этот лист?')){
           var data = [];
           $.each(b.find('input.acc-qty'), function (key, value) {
               var inputValue = $(value).val();
               var isNum = inputValue / inputValue;

               if(inputValue.length < 1){
                   data =[];
                   alert('Не все поля заполнены');
                   return false;
               }
               if(!isNum){
                   data =[];
                   alert('Поле "количество" должно быть числом');
                   return false;
               }
               data[key]=[$(value).data('id'), inputValue];

       });
           if(data.length > 0){
               $('#loading-modal').modal('show');
               $.post('apply-qty', {'data': data}, function (result) {

               }, 'html').fail(function () {
                   $('#loading-modal').modal('hide');
                   console.log("server error");
               });
           }
       }

        return false;
    });


    
    //b.on('submit', '#cross-dock-confirm-form', function(e) {
    //    e.preventDefault();
    //});

    /*
     * Confirm cross dock picking list
     * */
    //b.on('keyup', '#confirmcrossdockform-cross_dock_barcode', function (e) {
    //    if (e.which == 13) {
    //
    //        var me = $(this);
    //        me.focus().select();
    //
    //        var form = $('#cross-dock-confirm-form');
    //
    //        errorBase.setForm(form);
    //
    //        $.post('confirm-cross-dock', form.serialize(),function (result) {
    //            //if (result.success == 0 ) {
    //            //    errorBase.eachShow(result.errors);
    //            //    me.focus().select();
    //            //} else {
    //               // errorBase.hidden();
    //                $('#result-table-body').html(result);
    //                //$('#count-products-in-order').html(result.countScannedProductInOrder+' / '+result.expected_qty);
    //                //
    //                //
    //                //$('#accepted-qty-'+result.dataScannedProductByBarcode.rowId).html(result.dataScannedProductByBarcode.countValue);
    //                //$('#row-'+result.dataScannedProductByBarcode.rowId).removeClass('alert-danger alert-success');
    //                //$('#row-'+result.dataScannedProductByBarcode.rowId).addClass(result.dataScannedProductByBarcode.colorRowClass);
    //                //
    //                //$('#count-products-in-party').html(result.acceptedQtyParty +' / '+result.expectedQtyParty);
    //          //  }
    //        }, 'html').fail(function (xhr, textStatus, errorThrown) {
    //            alert(errorThrown);
    //        });
    //    }
    //});

});