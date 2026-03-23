/**
 * Created by kitavrus on 03.02.15.
 */

$(function() {
    var b = $('body');

    b.on('click', "#scanningform-box_kg", function (e) {
        $(this).focus().select();
    });

    // BOX KG
    b.on('keyup', "#scanningform-box_kg", function (e) {

        var me = $(this);

        if (e.which == 13) {

            console.info("-scanning-form-box-kg-");
//            console.info("Value : " + $(this).val());

            var url = me.data('url'),
                form = $('#scanning-form');

            errorBase.setForm(form);
            me.focus().select();
            errorBase.hidden();

            $.post(url, form.serialize(), function (result) {

                if (result.success == 0) {
                    errorBase.eachShow(result.errors);
                    me.focus().select();
                } else {
                    errorBase.hidden();
                    $('#scanningform-box_barcode').focus().select();
                }

            }, 'json').fail(function (xhr, textStatus, errorThrown) {
            });
        }

        e.preventDefault();
        return false;
    });

});
