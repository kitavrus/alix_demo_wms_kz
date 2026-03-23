$(function () {
    console.info ('-main-init-');
    var body = $('body');

    body.on('click', '.btn-href', function(){
        window.location.href = $(this).data('url');
    });

    body.on('click', '.btn-ajax', function(){
        var url = $(this).data('url'),
            container = body.find("div.ajax-container");

        $.post(url).done(function (d) {

            container.html('');
            container.html(d);
            container.focus().select();

        }).fail(function () {
            console.log("server error");
        });

    });
});