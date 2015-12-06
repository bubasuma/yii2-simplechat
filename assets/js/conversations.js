/**
 * yii2-simplechat conversations widget.
 *
 * This is the JavaScript widget used by the bubasuma\simplechat\ConversationWidget widget.
 *
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
(function ($) {
    $.fn.simpleChatConversations = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.simpleChatConversations');
            return false;
        }
    };

    var loadTypes  = {
        down:'history',
        up:'new'
    };

    var events = {
        init: 'initialized',
        load:{
            beforeSend: 'beforeSend.load',
            complete: 'complete.load',
            error: 'error.load',
            success: 'success.load'
        },
        read:{
            beforeSend: 'beforeSend.read',
            complete: 'complete.read',
            error: 'error.read',
            success: 'success.read'
        },
        unread:{
            beforeSend: 'beforeSend.unread',
            complete: 'complete.unread',
            error: 'error.unread',
            success: 'success.unread'
        },
        delete:{
            beforeSend: 'beforeSend.delete',
            complete: 'complete.delete',
            error: 'error.delete',
            success: 'success.delete'
        }

    };

    var defaults = {
        loadUrl: '',
        loadType: 'POST',
        unreadUrl: '',
        unreadType: 'PATCH',
        readUrl: '',
        readType: 'PATCH',
        deleteUrl: '',
        deleteType: 'DELETE',
        loadParam: 'key',
        limit: 10
    };

    var methods = {
        init: function (user,options) {
            return this.each(function () {
                var $chat = $(this);
                if ($chat.data('simpleChatConversations')) {
                    return;
                }
                var settings = $.extend({}, defaults, options || {});
                $chat.data('simpleChatConversations', {
                    settings: settings,
                    user: user,
                    status: 0  // load status, 0: pending load, 1: loading
                });
                $chat.trigger(events.init);
            });
        },
        load: function (args) {
            var $chat = $(this);
            var options = $chat.data('simpleChatConversations');
            var data = {
                type: loadTypes.down,
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
            if(options.status == 0) {
                $.ajax({
                    url: options.settings.loadUrl,
                    type: options.settings.loadType,
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

        unread: function (id) {
            var $chat = $(this);
            var options = $chat.data('simpleChatConversations');
            var url = options.settings.unreadUrl;
            if(-1 !== url.indexOf('?')){
                url += '&contactId=' + id;
            }else{
                url += '?contactId=' + id;
            }
            $.ajax({
                url: url,
                type: options.settings.unreadType,
                dataType: 'JSON',
                data: {contactId : id},
                beforeSend: function (xhr,settings) {
                    $chat.trigger(events.unread.beforeSend, [id, xhr,settings]);
                },
                complete: function (xhr,textStatus) {
                    $chat.trigger(events.unread.complete,[id, xhr,textStatus]);
                },
                success: function (res, textStatus, xhr) {
                    $chat.trigger(events.unread.success,[id, res, textStatus, xhr]);
                },
                error: function (xhr,textStatus,errorThrown) {
                    $chat.trigger(events.unread.error,[id, xhr,textStatus,errorThrown]);
                }
            });
        },

        read: function (id) {
            var $chat = $(this);
            var options = $chat.data('simpleChatConversations');
            var url = options.settings.readUrl;
            if(-1 !== url.indexOf('?')){
                url += '&contactId=' + id;
            }else{
                url += '?contactId=' + id;
            }
            $.ajax({
                url: url,
                type: options.settings.readType,
                dataType: 'JSON',
                data: {contactId : id},
                beforeSend: function (xhr,settings) {
                    $chat.trigger(events.read.beforeSend, [id, xhr,settings]);
                },
                complete: function (xhr,textStatus) {
                    $chat.trigger(events.read.complete,[id, xhr,textStatus]);
                },
                success: function (res, textStatus, xhr) {
                    $chat.trigger(events.read.success,[id, res, textStatus, xhr]);
                },
                error: function (xhr,textStatus,errorThrown) {
                    $chat.trigger(events.read.error,[id, xhr,textStatus,errorThrown]);
                }
            });
        },

        delete: function (id) {
            var $chat = $(this);
            var options = $chat.data('simpleChatConversations');
            var url = options.settings.deleteUrl;
            if(-1 !== url.indexOf('?')){
                url += '&contactId=' + id;
            }else{
                url += '?contactId=' + id;
            }
            $.ajax({
                url: url,
                type: options.settings.deleteType,
                dataType: 'JSON',
                data: {contactId : id},
                beforeSend: function (xhr,settings) {
                    $chat.trigger(events.delete.beforeSend, [id, xhr,settings]);
                },
                complete: function (xhr,textStatus) {
                    $chat.trigger(events.delete.complete,[id, xhr,textStatus]);
                },
                success: function (res, textStatus, xhr) {
                    $chat.trigger(events.delete.success,[id, res, textStatus, xhr]);
                },
                error: function (xhr,textStatus,errorThrown) {
                    $chat.trigger(events.delete.error,[id, xhr,textStatus,errorThrown]);
                }
            });
        },

        append: function (data) {
            var $chat = $(this);
            var options = $chat.data('simpleChatConversations');
            if(typeof data == 'object'){
                $chat.append(tmpl(options.settings.template,data));
            }else{
                $chat.append(data);
            }
        },

        prepend: function (data) {
            var $chat = $(this);
            var options = $chat.data('simpleChatConversations');
            if(typeof data == 'object'){
                $chat.prepend(tmpl(options.settings.template,data));
            }else{
                $chat.prepend(data);
            }
        },

        insert: function (data, selector, before) {
            var $chat = $(this);
            var options = $chat.data('simpleChatConversations');
            var $elem = $chat.find(selector);
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

        data: function () {
            return this.data('simpleChatConversations');
        },

        destroy: function () {
            return this.each(function () {
                var $chat = $(this);
                $chat.removeData('simpleChatConversations');
            });
        },

        find: function (id, dataAttr) {
            var $chat = $(this);
            return find($chat, id, dataAttr);
        }
    };



    var find = function ($chat, id, dataAttr) {
        var options = $chat.data('simpleChatConversations');
        if(typeof id == 'number' || typeof dataAttr != 'undefined'){
            dataAttr = typeof dataAttr == 'undefined' ? 'key' : dataAttr;
            return $chat.find('[data-' + dataAttr +'=' + id + ']');
        } else if(id == 'last') {
            return $chat.find(options.settings.selector).last();
        } else if(id == 'first') {
            return $chat.find(options.settings.selector).first();
        }else{
            return $chat.find(id);
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
