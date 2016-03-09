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
    $.fn.yiiSimpleChatConversations = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.yiiSimpleChatConversations');
            return false;
        }
    };

    var view = null;

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
        }
    };

    var defaults = {
        loadUrl: '',
        loadMethod: 'GET',
        unreadMethod: 'PATCH',
        readMethod: 'PATCH',
        deleteMethod: 'DELETE',
        limit: 10,
        templateUrl: '',
        itemCssClass:'conversation'
    };

    var methods = {
        init: function (user, current, options) {
            return this.each(function () {
                var $chat = $(this);
                if ($chat.data('yiiSimpleChatConversations')) {
                    return;
                }
                $chat.data('yiiSimpleChatConversations', {
                    settings: $.extend({}, defaults, options || {}),
                    user: user,
                    current: $.extend({}, {deleteUrl: null, unreadUrl: null, readUrl: null}, current || {}),
                    status: 0  // load status, 0: pending load, 1: loading
                });
                $chat.trigger(events.init);
            });
        },
        load: function (args) {
            var $chat = $(this);
            var widget = $chat.data('yiiSimpleChatConversations');
            var data = {
                type: loadTypes.down,
                limit: widget.settings.limit
            };
            if(typeof args == 'number'){
                data['limit'] = args;
            }else if(typeof args == 'string'){
                data['type'] = args;
            }else {
                data = $.extend({}, data, args || {});
            }
            var $conversation = find($chat, data['type'] == loadTypes.up ?'first':'last');
            if($conversation){
                data['key'] = $conversation.data('key');
            }
            if(widget.status == 0) {
                $.ajax({
                    url: widget.settings.loadUrl,
                    type: widget.settings.loadMethod,
                    dataType: 'JSON',
                    data: data,
                    beforeSend: function (xhr, settings) {
                        widget.status = 1;
                        $chat.trigger(events.load.beforeSend, [data['type'], xhr,settings]);
                    },
                    complete: function (xhr, textStatus) {
                        widget.status = 0;
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

        unread: function (settings) {
            var $chat = $(this);
            var widget = $chat.data('yiiSimpleChatConversations');
            $.ajax($.extend({}, {
                dataType: 'JSON',
                url: widget.current.unreadUrl,
                type: widget.settings.unreadMethod
            }, settings || {}));
        },

        read: function (settings) {
            var $chat = $(this);
            var widget = $chat.data('yiiSimpleChatConversations');
            $.ajax($.extend({}, {
                dataType: 'JSON',
                url: widget.current.readUrl,
                type: widget.settings.readMethod
            }, settings || {}));
        },

        delete: function (settings) {
            var $chat = $(this);
            var widget = $chat.data('yiiSimpleChatConversations');
            $.ajax($.extend({}, {
                dataType: 'JSON',
                url: widget.current.deleteUrl,
                type: widget.settings.deleteMethod
            }, settings || {}));
        },

        append: function (data) {
            var $chat = $(this);
            var widget = $chat.data('yiiSimpleChatConversations');
            if(typeof data == 'object'){
                $chat.append(tmpl(widget.settings.templateUrl, data));
            }else{
                $chat.append(data);
            }
        },

        prepend: function (data) {
            var $chat = $(this);
            var widget = $chat.data('yiiSimpleChatConversations');
            if(typeof data == 'object'){
                $chat.prepend(tmpl(widget.settings.templateUrl, data));
            }else{
                $chat.prepend(data);
            }
        },

        insert: function (data, selector, before) {
            var $chat = $(this);
            var widget = $chat.data('yiiSimpleChatConversations');
            var $elem = $chat.find(selector);
            var $conversation = null;
            if(typeof data == 'object'){
                $conversation = tmpl(widget.settings.templateUrl, data);
            }else{
                $conversation = data;
            }
            if(before){
                $elem.before($conversation);
            }else{
                $elem.after($conversation);
            }
        },

        widget: function () {
            return this.data('yiiSimpleChatConversations');
        },

        destroy: function () {
            return this.each(function () {
                var $chat = $(this);
                $chat.removeData('yiiSimpleChatConversations');
            });
        },

        find: function (id, dataAttr) {
            var $chat = $(this);
            return find($chat, id, dataAttr);
        }
    };



    var find = function ($chat, id, dataAttr) {
        var widget = $chat.data('yiiSimpleChatConversations');
        if(typeof id == 'number' || typeof dataAttr != 'undefined'){
            dataAttr = typeof dataAttr == 'undefined' ? 'key' : dataAttr;
            return $chat.find('[data-' + dataAttr +'=' + id + ']');
        } else if(id == 'last') {
            return $chat.find('.' + widget.settings.itemCssClass).last();
        } else if(id == 'first') {
            return $chat.find('.' + widget.settings.itemCssClass).first();
        }else{
            return $chat.find(id);
        }
    };

    var tmpl = function (url, data){
        if(null == view){
            view = twig({
                id: 'conversation',
                href: url,
                async: false
            })
        }
        return view.render(data);
    }

})(jQuery);
