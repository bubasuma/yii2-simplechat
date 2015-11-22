/**
 * @file chat.js
 *
 * Provides information about timezone
 * @author Buba Suma <buba@bigdropinc.com>
 *
 */

var Message = {

    init: function (cid,selectors,options) {
        var self = this;

        self.cid = cid;
        self.isLive = false;
        self.form = $(selectors.form);
        self.container = $(selectors.container);
        self.selector = selectors.message;
        self.options = options || {};
        self.options.loadUrl = options.loadUrl || '';
        self.loaded = false;
        self.loading = false;
        self.cache = {};

        self.form.on('submit', function () {
            self.send();
            return false;
        });

    },

    live: function (listenUrl) {

        var self = this;

        self.isLive = true;

        self.socket = io(listenUrl);

        self.socket.on('connect', function(){

            if( self.options.liveCallback != undefined){
                self.options.liveCallback(self,'connect');
            }

            self.socket.emit('load', {cid: self.cid});
        });

        self.socket.on('peopleInChat', function(data){

            if( self.options.liveCallback != undefined){
                self.options.liveCallback(self,'peopleInChat',data);
            }

            if(data.number < 2) {
                console.log('participate');
                self.socket.emit('participate', {user: self.options.data.user, cid: self.cid});
            }

        });

        self.socket.on('ready', function(data){
            if( self.options.liveCallback != undefined){
                self.options.liveCallback(self,'ready',data);
            }
        });

        self.socket.on('leave',function(data){
            if( self.options.liveCallback != undefined){
                self.options.liveCallback(self,'leave',data);
            }
        });

        self.socket.on('tooMany', function(data){
            if( self.options.liveCallback != undefined){
                self.options.liveCallback(self,'tooMany',data);
            }
        });

        self.socket.on('receive', function(data){
            console.log('receive');
            if( self.options.liveCallback != undefined){
                self.options.liveCallback(self,'receive',data.message);
            }
        });
    },

    firstMessage: function () {
        var self = this;
        return self.container.find(self.selector + ':first-child');
    },

    lastMessage: function () {
        var self = this;
        return self.container.find(self.selector + ':last-child');
    },

    send: function () {
        var self = this;
        var datatype = self.form.attr('data-type');
        $.ajax({
            url: self.form.attr('action'),
            type: self.form.attr('method'),
            dataType: datatype == undefined ? 'json':datatype,
            data: self.form.serialize(),
            beforeSend: function (xhr,settings) {
                return self.sendBeforeSendCallback(xhr,settings);
            },
            complete: function (xhr,textStatus) {
                self.sendCompleteCallback(xhr,textStatus);
            },
            success: function (data) {
                self.sendSuccessCallback(data);
            },
            error: function (xhr,textStatus,errorThrown) {
                self.sendErrorCallback(xhr,textStatus,errorThrown);
            }
        });
    },

    load: function (type,datatype) {
        var self = this;
        if(self.loading == false && self.loaded == false ) {
            self.loading = true;
            $.ajax({
                url: self.options.loadUrl,
                type: type == undefined ? 'post' : type,
                dataType: datatype == undefined ? 'json' : datatype,
                data: {
                    history: parseInt(self.firstMessage().data('utctime'))
                },
                beforeSend: function (xhr, settings) {
                    return self.loadBeforeSendCallback(xhr, settings);
                },
                complete: function (xhr, textStatus) {
                    self.loading = false;
                    self.loadCompleteCallback(xhr, textStatus);
                },
                success: function (data) {
                    if (data.length == 0) {
                        self.loaded = true;
                    } else {
                        self.loadSuccessCallback(data);
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    self.loadErrorCallback(xhr, textStatus, errorThrown);
                }
            });
        }
    },

    append: function (data) {
        var self = this;
        if(typeof(data) != 'string'){
            self.container.append(self.tmpl(self.options.templateId,data));
        }else{
            self.container.append(data);
        }
    },

    prepend: function (data) {
        var self = this;
        if(typeof(data) != 'string'){
            self.container.prepend(self.tmpl(self.options.templateId,data));
        }else{
            self.container.prepend(data);
        }
    },

    sendCompleteCallback: function (xhr,textStatus) {
        var self = this;
        if( self.options.sendCompleteCallback != undefined){
            self.options.sendCompleteCallback(self,xhr,textStatus);
        }
    },

    sendBeforeSendCallback: function (xhr,settings) {
        var self = this;
        if( self.options.sendBeforeSendCallback != undefined){
            return self.options.sendBeforeSendCallback(self,xhr,settings);
        }
        return true;

    },

    sendSuccessCallback: function (data) {
        var self = this;

        if( self.options.sendSuccessCallback != undefined){
            self.options.sendSuccessCallback(self,data);
        }

        if(self.isLive === true){
            console.log("send", data);
            self.socket.emit('send', {message: data, cid: self.cid});
        }
    },

    sendErrorCallback: function (textStatus,errorThrown) {
        var self = this;
        if( self.options.sendErrorCallback != undefined){
            self.options.sendErrorCallback(self,xhr,textStatus,errorThrown);
        }
    },

    loadCompleteCallback: function (xhr,textStatus) {
        var self = this;
        if( self.options.loadCompleteCallback != undefined){
            self.options.loadCompleteCallback(self,xhr,textStatus);
        }
    },

    loadBeforeSendCallback: function (xhr,settings) {
        var self = this;
        if( self.options.loadBeforeSendCallback != undefined){
            return self.options.loadBeforeSendCallback(self,xhr,settings);
        }
        return true;
    },

    loadSuccessCallback: function (data) {
        var self = this;
        if( self.options.loadSuccessCallback != undefined){
            self.options.loadSuccessCallback(self,data);
        }
    },

    loadErrorCallback: function (textStatus,errorThrown) {
        var self = this;
        if( self.options.loadErrorCallback != undefined){
            self.options.loadErrorCallback(self,xhr,textStatus,errorThrown);
        }
    },

    tmpl : function (str, data){
        var self = this;

        var fn = !/\W/.test(str) ?
            self.cache[str] = self.cache[str] || self.tmpl(document.getElementById(str).innerHTML) :

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


};
