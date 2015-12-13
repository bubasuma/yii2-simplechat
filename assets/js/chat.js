/**
 * yii2-simplechat demo javascript.
 *
 * This is the JavaScript used by the demo page.
 *
 * @link https://github.com/bubasuma/yii2-simplechat
 * @copyright Copyright (c) 2015 bubasuma
 * @license http://opensource.org/licenses/BSD-3-Clause
 * @author Buba Suma <bubasuma@gmail.com>
 * @since 1.0
 */
(function ($) {
    var msgWrapper = document.getElementById('msg-wrap');
    var convWrapper = document.getElementById('conv-wrap');
    msgWrapper.scrollTop = msgWrapper.scrollHeight;
    $(document).ready(function () {
        var messages = $('#messages');
        var conversations = $(convWrapper);
        var scrollPos;
        var msgLoader = $('#msg-loader');
        var convLoader = $('#conv-loader');

        //read the current conversation once the chat is initialized
        var re = /contactId\s*=\s*(\d+)\s*/;
        var matches = location.href.match(re);
        var contactId = matches.length > 1 ? matches[1] : 0;

        // on messages initialized
        messages.on('initialized', function () {
            var options = messages.simpleChatMessages('data');
            if (options.contact.id == contactId) {
                //Avoids to repeat this fragment of code each time we select this conversation
                contactId = -1;
                // read all messages in the current conversation
                conversations.simpleChatConversations('read', contactId);
            }
        });

        // on scroll conversations content
        conversations.scroll(function () {
            // check whether not all history is loaded
            if (!conversations.data('loaded')) {
                // check whether the scroll is at the bottom
                if (convWrapper.scrollTop + conversations.innerHeight() >= convWrapper.scrollHeight) {
                    // load conversations
                    conversations.simpleChatConversations('load', 8);
                }
            }
        });

        // on scroll messages content
        $(msgWrapper).scroll(function () {
            // check whether the scroll is at the top  and not all history is loaded
            if (msgWrapper.scrollTop == 0 && !messages.data('loaded')) {
                // load messages
                messages.simpleChatMessages('load', 10);
            }
        });

        // on click conversation block
        conversations.on('click', '.conversation:not(.current)', function () {
            var $conv = $(this);
            // add class current to this conversation and remove from others
            $conv.addClass('current').siblings('.current').removeClass('current');
            //copy previous configuration
            var options = $.extend({}, messages.simpleChatMessages('data'));
            //destroy previous chat
            messages.simpleChatMessages('destroy');
            messages.removeData('loaded');
            //reinitialize the chat
            var contact = $conv.data('contact-info');
            messages.simpleChatMessages(
                options.user,
                contact,
                options.settings
            );

            var tempHandler1 = function () {
                // show loader
                $('.loading').show();
                // remove itself as handler
                messages.off('beforeSend.load', tempHandler1);
            };

            // register a handler on messages before load
            // this handler is executed once after it has been registered
            // Because it removes itself as handler at the end of its body
            messages.on('beforeSend.load', tempHandler1);

            var tempHandler2 = function () {
                // hide the loader
                $('.loading').hide();
                // remove itself as handler
                messages.off('complete.load', tempHandler2);
            };

            // register a handler on messages load completed
            // this handler is executed once after it has been registered
            // Because it removes itself as handler at the end of its body
            messages.on('complete.load', tempHandler2);

            var tempHandler3 = function () {
                // check whether the current conversation has unread messages
                if ($conv.is('.unread')) {
                    // read all messages in this conversations
                    conversations.simpleChatConversations('read', $conv.data('contact'));
                }
                // update the window state
                document.title = contact.profile.full_name;
                var re = /contactId\s*=(\s*\d+\s*)/;
                var url = location.href.replace(re, 'contactId=' + contact.id);
                window.history.replaceState(null, document.title, url);
                // remove itself as handler
                messages.off('success.load', tempHandler3);
            };
            // register a handler on messages load success
            // this handler is executed once after it has been registered
            // Because it removes itself as handler at the end of its body
            messages.on('success.load', tempHandler3);
            //reload messages
            messages.simpleChatMessages('reload');

        });

        // on click delete icon
        conversations.on('click', '.conversation .fa-times', function (e) {
            e.preventDefault();
            e.stopPropagation();
            conversations.simpleChatConversations('delete', $(this).parents('.conversation').data('contact'));
        });

        // on click read icon
        conversations.on('click', '.conversation .fa-circle', function (e) {
            e.preventDefault();
            e.stopPropagation();
            conversations.simpleChatConversations('read', $(this).parents('.conversation').data('contact'));
        });

        // on click unread icon
        conversations.on('click', '.conversation .fa-circle-o', function (e) {
            e.preventDefault();
            e.stopPropagation();
            conversations.simpleChatConversations('unread', $(this).parents('.conversation').data('contact'));
        });

        // on conversation unread successful
        conversations.on('success.unread', function (e, contactId, data) {
            // get the conversation
            var convToRead = conversations.simpleChatConversations('find', contactId, 'contact');
            if (data.count && convToRead.length) {
                // add unread class and change unread icon to read
                convToRead.addClass('unread')
                    .find('.fa-circle-o')
                    .removeClass('fa-circle-o')
                    .addClass('fa-circle')
                    .parent()
                    .attr('title', 'Mark as read');
                // update unread messages counter to 1
                convToRead.find('.msg-new').text('1');
            }
        });
        // on conversation read successful
        conversations.on('success.read', function (e, contactId, data) {
            // get the conversation
            var convToRead = conversations.simpleChatConversations('find', contactId, 'contact');
            if (data.count && convToRead.length) {
                // remove unread class and change read icon to unread
                convToRead.removeClass('unread')
                    .find('.fa-circle')
                    .removeClass('fa-circle')
                    .addClass('fa-circle-o')
                    .parent()
                    .attr('title', 'Mark as unread');
                // empty the unread messages counter
                convToRead.find('.msg-new').text('');
            }
        });

        // on conversation delete successful
        conversations.on('success.delete', function (e, contactId, data) {
            // get the conversation
            var convToRemove = conversations.simpleChatConversations('find', contactId, 'contact');
            if (data.count && convToRemove.length) {
                // remove conversation
                convToRemove.hide('slow', function () {
                    convToRemove.remove();
                });
                // check whether this conversation is the current
                // you can do it by comparing contactId to messages.simpleChatMessages('data').contact.id
                // or by checking whether convToRemove has class current
                if(convToRemove.is('.current')){
                    // remove messages from the messages container
                    messages.simpleChatMessages('empty');
                }
            }
        });

        // on click to send button
        $('#msg-send').click(function (e) {
            e.preventDefault();
            // submit message form
            $('#msg-form').trigger('submit');
        });

        // on messages before load
        messages.on('beforeSend.load', function (e, type) {
            // check whether we load history
            if (type == 'history') {
                //  remember the scroll height
                scrollPos = msgWrapper.scrollHeight;
                // show the loader
                msgLoader.prependTo($(msgWrapper)).show();
            }
        });

        // on messages load completed
        messages.on('complete.load', function (e, type) {
            // check whether we load history
            if (type == 'history') {
                // hide the loader
                msgLoader.hide();
                // scroll to previous scroll height
                msgWrapper.scrollTop = msgWrapper.scrollHeight - scrollPos;
            }
        });

        // on conversations before load
        conversations.on('beforeSend.load', function (e, type) {
            // check whether we load history
            if (type == 'history') {
                // show the loader
                convLoader.appendTo($(convWrapper)).show();
            }
        });

        // on conversations load completed
        conversations.on('complete.load', function (e, type) {
            // check whether we load history
            if (type == 'history') {
                // hide the loader
                convLoader.hide();
            }
        });

        // on message send successful
        messages.on('success.send', function (e, data) {
            // check whether we got empty array
            if (data.length == 0) {
                // reset form
                messages.simpleChatMessages('resetForm');
                // load new messages
                messages.simpleChatMessages('load', 'new');
                // load new conversations
                conversations.simpleChatConversations('load', 'new');
            }
            // else validation fails. You can retrieve error messages from data
        });

        conversations.on('success.load', function (e, type, data) {
            var options = conversations.simpleChatConversations('data');
            var activeChatOptions = messages.simpleChatMessages('data');
            var index, conv;
            // check whether we load history or new conversations
            if (type == 'history') {
                // set loaded attribute to true if all history is loaded
                if (data.count <= data.models.length) {
                    conversations.data('loaded', true);
                }
                // loop through data.models
                for (index = 0; index < data.models.length; index++) {
                    // object to inject to template
                    conv = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index,
                        is_current: activeChatOptions.contact.id == data.models[index].contact.id
                    };
                    //prepend conversation
                    conversations.simpleChatConversations('append', conv);
                }
            } else {
                // loop through data.models
                for (index = data.models.length - 1; index >= 0; index--) {
                    // object to inject to template
                    conv = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index,
                        is_current: activeChatOptions.contact.id == data.models[index].contact.id
                    };
                    // remove conversation if it existed before
                    var convToRemove = conversations.simpleChatConversations('find', conv.model.contact.id, 'contact');
                    if (convToRemove.length) {
                        convToRemove.remove();
                    }
                    // prepend conversation
                    conversations.simpleChatConversations('prepend', conv);
                }
            }
        });

        messages.on('success.load', function (e, type, data) {
            var when = false, options = messages.simpleChatMessages('data');
            var index, msg;
            // check whether we load history or new messages
            if (type == 'history') {
                // get the first date block
                var _top_when_text = false,
                    _top_when = messages.simpleChatMessages('find', '.msg-date').first();
                if (_top_when) {
                    when = _top_when_text = _top_when.find('strong').text();
                }
                // set loaded attribute to true if all history is loaded
                if (data.count <= data.models.length) {
                    messages.data('loaded', true);
                }
                // loop trough data.models object
                for (index = 0; index < data.models.length; index++) {
                    // check whether to insert date block
                    if (data.models[index]['when'] != when) {
                        if (when != _top_when_text) {
                            messages.simpleChatMessages('prepend', '<div class="alert alert-info msg-date"><strong>' + when + '</strong></div>');
                        }
                        when = data.models[index]['when'];
                    }
                    // object to inject to the template
                    msg = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index
                    };

                    if (when == _top_when_text) {
                        // insert message after the first date block from the top of the container
                        messages.simpleChatMessages('insert', msg, _top_when);
                    } else {
                        // prepend message to a container
                        messages.simpleChatMessages('prepend', msg);
                    }
                }
                // prepend the the date block
                if (when != _top_when_text) {
                    messages.simpleChatMessages('prepend', '<div class="alert alert-info msg-date"><strong>' + when + '</strong></div>');
                }
            } else {
                // get the last date block
                var _last_when_text = false,
                    _last_when = messages.simpleChatMessages('find', '.msg-date').last();
                if (_last_when) {
                    when = _last_when_text = _last_when.find('strong').text();
                }
                // loop trough data.models object
                for (index = data.models.length - 1; index >= 0; index--) {
                    // check whether to insert date block
                    if (data.models[index]['when'] != when) {
                        when = data.models[index]['when'];
                        if (when != _last_when_text) {
                            messages.simpleChatMessages('append', '<div class="alert alert-info msg-date"><strong>' + when + '</strong></div>');
                        }
                    }
                    // object to inject to the template
                    msg = {
                        options: options,
                        model: data.models[index],
                        key: data.keys[index],
                        index: index
                    };
                    // append the message
                    messages.simpleChatMessages('append', msg);
                }
                // scroll down the messages container
                if (data.models.length > 0) {
                    msgWrapper.scrollTop = msgWrapper.scrollHeight;
                }
            }

        });

        // load new messages every 10 seconds
        setInterval(function () {
            messages.simpleChatMessages('load', 'new');
        }, 10000);

        // load new conversations every 15 seconds
        setInterval(function () {
            conversations.simpleChatConversations('load', 'new');
        }, 15000);

    });
})(jQuery);
