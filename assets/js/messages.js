/**
 * yii2-simplechat messages widget.
 *
 * This is the JavaScript widget used by the bubasuma\simplechat\MessageWidget widget.
 *
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
(function ($) {
    $.fn.simpleChatMessages = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.simpleChatMessages');
            return false;
        }
    };

    var loadTypes  = {
        up:'history',
        down:'new'
    };

    var events = {
        init: 'initialized',
        send:{
            beforeSend: 'beforeSend.send',
            complete: 'complete.send',
            error: 'error.send',
            success: 'success.send'
        },
        load:{
            beforeSend: 'beforeSend.load',
            complete: 'complete.load',
            error: 'error.load',
            success: 'success.load'
        }

    };

    var defaults = {
        loadUrl: '',
        loadMethod: 'POST',
        loadParam: 'key',
        sendUrl: false,
        sendMethod: false,
        limit: 10
    };

    var methods = {
        init: function (user,contact,options) {
            return this.each(function () {
                var $chat = $(this);
                if ($chat.data('simpleChatMessages')) {
                    return;
                }
                var settings = $.extend({}, defaults, options || {});
                $chat.data('simpleChatMessages', {
                    settings: settings,
                    user: user,
                    contact: contact,
                    status: 0  // status of the chat, 0: pending load, 1: loading
                });
                var $form = $(settings.form);
                $form.on('submit.simpleChatMessages', function (e) {
                    e.preventDefault();
                    methods.send.apply($chat);
                });

                if(settings.sendUrl){
                    $form.attr('action', settings.sendUrl)
                }
                if(settings.sendMethod){
                    $form.attr('method', settings.sendMethod)
                }
                $chat.trigger(events.init);
            });
        },


        resetForm: function () {
            var $chat = $(this);
            var options = $chat.data('simpleChatMessages');
            var $form = $chat.find(options.settings.form);
            $form.find('input, textarea, select').each(function () {
                var $input = $(this);
                $input.val('');
            });
        },

        send: function () {
            var $chat = $(this);
            var options = $chat.data('simpleChatMessages');
            var $form = $chat.find(options.settings.form);
            var url = $form.attr('action');
            if(-1 !== url.indexOf('?')){
                url += '&contactId=' + options.contact.id;
            }else{
                url += '?contactId=' + options.contact.id;
            }
            $.ajax({
                url: url,
                type: $form.attr('method'),
                dataType: 'JSON',
                data: $form.serialize(),
                beforeSend: function (xhr,settings) {
                    $chat.trigger(events.send.beforeSend, [xhr,settings]);
                },
                complete: function (xhr,textStatus) {
                    $chat.trigger(events.send.complete,[xhr,textStatus]);
                },
                success: function (data) {
                    $chat.trigger(events.send.success,[data]);
                },
                error: function (xhr,textStatus,errorThrown) {
                    $chat.trigger(events.send.error,[xhr,textStatus,errorThrown]);
                }
            });
        },

        reload: function () {
            var $chat = $(this);
            methods.resetForm.apply($chat);
            methods.empty.apply($chat);
            methods.load.apply($chat);
        },

        load: function (args) {
            var $chat = $(this);
            var options = $chat.data('simpleChatMessages');
            var data = {
                type: loadTypes.up,
                limit: options.settings.limit
            };
            if(typeof args == 'number'){
                data['limit'] = args;
            }else if(typeof args == 'string'){
                data['type'] = args;
            }else {
                data = $.extend({}, data, args || {});
            }

            var elem = find($chat, data['type'] == loadTypes.up ?'first':'last');
            if(elem){
                data[options.settings.loadParam] = elem.data(options.settings.loadParam);
            }

            var url = options.settings.loadUrl;
            if(-1 !== url.indexOf('?')){
                url += '&contactId=' + options.contact.id;
            }else{
                url += '?contactId=' + options.contact.id;
            }

            if(options.status == 0) {
                $.ajax({
                    url: url,
                    type: options.settings.loadMethod,
                    dataType: 'JSON',
                    data: data,
                    beforeSend: function (xhr, settings) {
                        options.status = 1;
                        $chat.trigger(events.load.beforeSend, [data['type'], xhr,settings]);
                    },
                    complete: function (xhr, textStatus) {
                        options.status = 0;
                        $chat.trigger(events.load.complete,[data['type'], xhr,textStatus]);
                    },
                    success: function (res, textStatus, xhr) {
                        $chat.trigger(events.load.success,[data['type'], res, textStatus, xhr]);
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        $chat.trigger(events.load.error,[data['type'], xhr,textStatus,errorThrown]);
                    }
                });
            }
        },

        empty: function () {
            var $chat = $(this);
            var options = $chat.data('simpleChatMessages');
            var $container = $chat.find(options.settings.container);
            $container.empty();
        },

        append: function (data) {
            var $chat = $(this);
            var options = $chat.data('simpleChatMessages');
            var $container = $chat.find(options.settings.container);
            if(typeof data == 'object'){
                $container.append(tmpl(options.settings.template,data));
            }else{
                $container.append(data);
            }
        },

        prepend: function (data) {
            var $chat = $(this);
            var options = $chat.data('simpleChatMessages');
            var $container = $chat.find(options.settings.container);
            if(typeof data == 'object'){
                $container.prepend(tmpl(options.settings.template,data));
            }else{
                $container.prepend(data);
            }
        },

        insert: function (data, selector, before) {
            var $chat = $(this);
            var options = $chat.data('simpleChatMessages');
            var $container = $chat.find(options.settings.container);
            var $elem = $container.find(selector);
            if(typeof data == 'object'){
                if(before){
                    $elem.before(tmpl(options.settings.template,data));
                }else{
                    $elem.after(tmpl(options.settings.template,data));
                }
            }else{
                if(before){
                    $elem.before(data);
                }else{
                    $elem.after(data);
                }
            }
        },

        destroy: function () {
            return this.each(function () {
                var $chat = $(this);
                var options = $chat.data('simpleChatMessages');
                var $form = $chat.find(options.settings.form);
                $form.off('.simpleChatMessages');
                $chat.removeData('simpleChatMessages');
            });
        },

        data: function () {
            return this.data('simpleChatMessages');
        },

        find: function (id) {
            var $chat = $(this);
            return find($chat, id);
        }
    };



    var find = function ($chat, id) {
        var options = $chat.data('simpleChatMessages');
        var $container = $chat.find(options.settings.container);
        if(typeof id == 'number'){
            return $container.find('[data-key=' + id + ']');
        } else if(id == 'last') {
            return $container.find(options.settings.selector).last();
        } else if(id == 'first') {
            return $container.find(options.settings.selector).first();
        }else{
            return $container.find(id);
        }
    };

    var tmlCache = {};
    var tmpl = function (str, data){
        var fn = /#[a-z0-9_-]+/i.test(str) ? tmlCache[str] = tmlCache[str] || tmpl($(str).html()) :
            new Function("obj",
                "var p=[],print=function(){p.push.apply(p,arguments);};" +
                "with(obj){p.push('" +
                str
                    .replace(/[\r\t\n]/g, " ")
                    .split("<%").join("\t")
                    .replace(/((^|%>)[^\t]*)'/g, "$1\r")
                    .replace(/\t=(.*?)%>/g, "',$1,'")
                    .split("\t").join("');")
                    .split("%>").join("p.push('")
                    .split("\r").join("\\'")
                + "');}return p.join('');");
        return data ? fn( data ) : fn;
    }

})(jQuery);
