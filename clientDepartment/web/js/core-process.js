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

var ErrorManager = function () {
    var f; // This is form;
    var me; // This is this;
    var errorClasses = ['alert-success', 'alert-info', 'alert-warning', 'alert-danger']; // TODO Добавить возможность для разных полей ошибки разным цветом
    var errorFieldCount = 0; // TODO Добавить возможность для разных полей ошибки разным цветом
    return {
        'eachShow': function (errors) {
            $.each(errors, function (key, value) {
                if ($.isArray(value) && value.length) {
                    me.show(key, value);
                }
            });
        },
        'show': function (key, value) {
            f.find('.field-' + key).addClass('has-error').find('.help-block').html(value[0]);
            var newElem = $('#error-list').clone(false);
            newElem.attr('id', newElem.attr('id') + key);
            newElem.append(value[0]);
            newElem.removeClass('hidden');
            $(newElem).insertAfter('#error-base-line');
        },
        'hidden': function () {
            f.find('.has-error').removeClass('has-error').find('.help-block').html('');
        },
        'setForm': function (form) {
            f = form;
            me = this;
        }
    }
};

var errorBase = new ErrorManager();