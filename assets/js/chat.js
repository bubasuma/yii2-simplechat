(function ($) {
    var msgWrapper = document.getElementById('msg-wrap');
    var convWrapper = document.getElementById('conv-wrap');
    msgWrapper.scrollTop = msgWrapper.scrollHeight;
    $(document).ready(function() {
        var messages = $('#messages');
        var conversations = $(convWrapper);
        var scrollPos ;
        var msgLoader = $('#msg-loader');
        var convLoader = $('#conv-loader');
        conversations.scroll(function() {
            if (!conversations.data('loaded')) {
                if (convWrapper.scrollTop + conversations.innerHeight() >= convWrapper.scrollHeight){
                    conversations.simpleChatConversations('load', 8);
                }
            }
        });

        $(msgWrapper).scroll(function() {
            if(msgWrapper.scrollTop == 0 && !messages.data('loaded')) {
                messages.simpleChatMessages('load',10);
            }
        });

        $(conversations).on('click','.conversation:not(.current)', function () {
            $(this).addClass('current').siblings('.current').removeClass('current');


            //copy previous configuration
            var options = $.extend({}, messages.simpleChatMessages('data'));
            //destroy previous chat
            messages.simpleChatMessages('destroy');
            messages.removeData('loaded');
            //reinitialize the chat

            var contact = $(this).data('contact-info');
            messages.simpleChatMessages(
                options.user,
                contact,
                $.extend({}, options.settings, {
                    loadUrl: $(this).data('load-url'),
                    sendUrl: $(this).data('send-url')
                })
            );

            var tempHandler1 = function () {
                $('.loading').show();
                messages.off('beforeSend.load',tempHandler1);
            };
            messages.on('beforeSend.load',tempHandler1);

            var tempHandler2 = function () {
                $('.loading').hide();
                messages.off('complete.load',tempHandler2);
            };
            messages.on('complete.load',tempHandler2);
            //reload the chat
            messages.simpleChatMessages('reload');
            //replace url
            document.title = contact.profile.full_name;
            window.history.replaceState(null, document.title, $(this).data('load-url'));

        });

        $('#msg-send').click(function(e) {
            e.preventDefault();
            $('#msg-form').trigger('submit');
        });

        messages.on('beforeSend.load', function (e, type) {
            if(type == 'history'){
                scrollPos = msgWrapper.scrollHeight;
                msgLoader.prependTo($(msgWrapper)).show();
            }
        });

        messages.on('complete.load', function (e, type) {
            if(type == 'history'){
                msgLoader.hide();
                msgWrapper.scrollTop = msgWrapper.scrollHeight - scrollPos;
            }
        });

        conversations.on('beforeSend.load', function (e, type) {
            if(type == 'history'){
                convLoader.appendTo($(convWrapper)).show();
            }
        });

        conversations.on('complete.load', function (e, type) {
            if(type == 'history'){
                convLoader.hide();
            }
        });

        messages.on('success.send',function(e, data) {
            if(data.length == 0){
                messages.simpleChatMessages('resetForm');
                messages.simpleChatMessages('load','new');
                conversations.simpleChatConversations('load','new');
            }
        });

        conversations.on('success.load',function(e, type, data) {
            var options = conversations.simpleChatConversations('data');
            var activeChatOptions = messages.simpleChatMessages('data');
            var index, conv;
            if(type == 'history'){
                if(data.count <= data.models.length){
                    conversations.data('loaded',true);
                }
                for(index = 0; index < data.models.length; index++) {
                    conv = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index,
                        is_current: activeChatOptions.contact.id == data.models[index].contact.id
                    };
                    conversations.simpleChatConversations('append', conv);
                }
            }else{
                for(index = data.models.length - 1; index >= 0; index--) {
                    conv = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index,
                        is_current: activeChatOptions.contact.id == data.models[index].contact.id
                    };
                    var convToRemove= conversations.simpleChatConversations('find', conv.model.contact.id, 'contact');
                    if(convToRemove.length){
                        convToRemove.remove();
                    }
                    conversations.simpleChatConversations('prepend', conv);
                }
            }
        });

        messages.on('success.load',function(e, type, data) {
            var when = false, options = messages.simpleChatMessages('data');
            var index, msg;
            if(type == 'history'){
                var _top_when_text = false,
                    _top_when = messages.simpleChatMessages('find','.msg-date').first();
                if(_top_when){
                    when = _top_when_text = _top_when.find('strong').text();
                }
                if(data.count <= data.models.length){
                    messages.data('loaded',true);
                }
                for(index = 0; index < data.models.length; index++) {
                    if(data.models[index]['when'] != when){
                        if(when != _top_when_text){
                            messages.simpleChatMessages('prepend','<div class="alert alert-info msg-date"><strong>' + when + '</strong></div>');
                        }
                        when = data.models[index]['when'];
                    }
                    msg = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index
                    };

                    if(when == _top_when_text){
                        messages.simpleChatMessages('insert', msg, _top_when);
                    }else{
                        messages.simpleChatMessages('prepend', msg);
                    }
                }
                if(when != _top_when_text){
                    messages.simpleChatMessages('prepend','<div class="alert alert-info msg-date"><strong>' + when + '</strong></div>');
                }
            }else{
                var _last_when_text = false,
                    _last_when = messages.simpleChatMessages('find','.msg-date').last();
                if(_last_when){
                    when = _last_when_text = _last_when.find('strong').text();
                }

                for(index = data.models.length - 1; index >= 0; index--) {
                    if(data.models[index]['when'] != when){
                        when = data.models[index]['when'];
                        if(when != _last_when_text){
                            messages.simpleChatMessages('append','<div class="alert alert-info msg-date"><strong>' + when + '</strong></div>');
                        }
                    }
                    msg = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index
                    };

                    messages.simpleChatMessages('append', msg);
                }
                if(data.models.length > 0){
                    msgWrapper.scrollTop = msgWrapper.scrollHeight;
                }
            }

        });

        //comment this line for live chat
        var newMessages = setInterval(function () {
            messages.simpleChatMessages('load','new');
        }, 30000);

        var newConversations = setInterval(function () {
            conversations.simpleChatConversations('load','new');
        }, 60000);

    });
})(jQuery);
