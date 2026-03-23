/**
 * Created by Igor on 22.08.14.
 */

(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

$(function(){

    $('.add-route-bt').on('click',function(){
        console.info('.add-route-bt CLICK');
        $('#add-new-rout-modal').
             modal('show')
            .find('#modalContent')
            .load($(this).data('value'));

    });

    $('#update-review-bt').on('click',function(){
        console.info('#update-review-bt CLICK');
        $.post(
           'show-store-review-form',
            {'review-id':$(this).data('id')}
        )
            .done(function(result) {
                $('#update-review-bt').hide();
                $('.store-reviews-view').empty();
                $('.store-reviews-view').html(result);

            })
            .fail(function() {
            console.log("server error");
            });
    });

});

/*
* Add new store for route
*
* */
function addRouteSubmitForm($form) {

    $.post(
            $form.attr("action"), // serialize Yii2 form
            $form.serialize()
        )
        .done(function(result) {
            $form.parent().html(result.message);
            $('#add-new-rout-modal').modal('hide');

            console.info(result.data_options);

            var fromValue = $('#tldeliveryproposal-route_from option:selected').val(),
                toValue = $('#tldeliveryproposal-route_to option:selected').val();


            $('#tldeliveryproposal-route_from, #tldeliveryproposal-route_to').html('');
            $.each(result.data_options, function(key, value) {

                $('#tldeliveryproposal-route_from, #tldeliveryproposal-route_to').append('<option value="'+key+'">'+value+'</option>');
            });

            $("#tldeliveryproposal-route_from [value='"+fromValue+"']").attr("selected", "selected");
            $("#tldeliveryproposal-route_to [value='"+toValue+"']").attr("selected", "selected");

        })
        .fail(function() {
//            console.log("server error");
            alert('Server Error');
//            $form.replaceWith('<button class="newType">Fail</button>').fadeOut()
        });

    return false;
}