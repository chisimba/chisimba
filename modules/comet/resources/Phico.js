/** Phico - Php in comet
 * @author  Andrea Giammarchi
 * @site    webreflection.blogspot.com
 * @date    2008/04/23
 * @version 0.1.5
 * @license Mit Style License
 */
(function(list, iframe){

    /**
     * Constructor with implicit factory design pattern.
     * @param   String      PHP script to call to start a Phico session
     * @param   Function    optional callback (assigned as onData method) that will be called to receive mixed data from server
     * @return  Phico       if called statically, will return a new Phico instance
     */
    Phico = iframe ?
    function(server, onData){   // IE and Opera version
        if(this instanceof Phico){
            var iframe  = document.createElement("iframe"),
                style   = iframe.style;
            style.position  = "absolute";
            style.visibility= "visible";
            style.display   = "block";
            style.left      =
            style.top       = "-10000px";
            style.width     =
            style.height    = "1px";
            iframe.src  = __index__.call(this, server, list.push({iframe:iframe, self:this}), onData);
        } else
            return  Phico.init(server, onData);
    }:
    function(server, onData){   // FireFox and Safari version
        if(this instanceof Phico){
            server = __index__.call(this, server, list.push({self:this}), onData);
            list[this.__index__].server = server;
        } else
            return  Phico.init(server, onData);
    };

    Phico.prototype = {

        /**
         * public properties
         */

        // constructor
        constructor:Phico,

        /**
         * public methods
         */

        /**
         * Connect to server starting to listen to its messages, if any.
         * @return  Phico      same instance that used connect method
         * @raise   Error       if server does not reply as expected,
         *                      this method will generate an error (asyncronously).
         */
        connect:iframe ?
        function(){ // IE and Opera
            var self        = list[this.__index__];
            attachEvent("onreadystatechange", self.onreadystatechange = function(){onreadystatechange.call(self.iframe);});
            attachEvent("onbeforeunload", self.onbeforeunload = function(){disconnect(self);});
            (document.body || document.documentElement).appendChild(self.iframe);
            this.connect    = connect;
            return  this;
        }:
        function(){ // FireFox and Safari
            var self        = list[this.__index__],
                xhr         = new XMLHttpRequest,
                length      = 1030,
                script      = /^<script[^>]*>parent\.(.+);<\/script><br\s*\/>$/,
                responseText;
            xhr.open("get", self.server, true);
            xhr.onreadystatechange = function(){
                if(xhr.readyState > 2){
                    if(xhr.status == 200){
                        responseText = xhr.responseText.substring(length);
                        length += responseText.length;
                        eval(responseText.replace(script, "$1"));
                    }
                    else
                        onreadystatechange.call({readyState:"loaded"});
                }
            };
            self.xhr        = xhr;
            addEventListener("beforeunload", self.onbeforeunload = function(){disconnect(self);}, false);
            setTimeout(function(){xhr.send(null);}, 10);
            this.connect    = connect;
            return  this;
        },

        /**
         * Close connection trying to avoid memory leaks and errors.
         * Once it is executed, instance will be no more usable.
         * (use a new instance to connect again, if it is necessary)
         */
        disconnect:function(){
            var self        = list[this.__index__];
            disconnect(self);
            list[this.__index__] = false;
            delete  this.__index__;
            delete  self.self;
            delete  self;
            this.disconnect    = connect;
            if(typeof CollectGarbage == "function")
                CollectGarbage();
        },

        /**
         * Receive server informations as data, where
         * data could be every kind of JSON compatible value:
         * Array, Boolean, Number, Object, String
         * @param   mixed       every kind of json compatible data
         */
        onData:function(data){}
    };

    /**
     * static public methods
     */

    /**
     * Explicit factory method to create a new Phico instance.
     * @param   String      PHP script to call to start a Phico session
     * @param   Function    optional callback (assigned as onData method) that will be called to receive mixed data from server
     * @return  Phico       if called statically, will return a new Phico instance
     */
    Phico.init = function(server, onData){
        return  new Phico(server, onData);
    };

    /**
     * Public method used by server.
     * This method should not be used by JavaScript, it is a bridge
     * between the server and the client itself.
     * @param   UInt32      private list index automatically assigned during connection.
     * @param   mixed       JSON compatible data that will be sent to instance onData method.
     * @raise   Error       if instance does not exists (has been deleted or is not known)
     */
    Phico.onData = function(__index__, data){
        var self = list[__index__];
        if(self)
            self.self.onData(data);
        else if(self !== false)
            throw new Error("Phico instance is not available");
    };

    /**
     * private  methods, temporary uncommented
     */
    var __index__ =
            function(server, __index__, onData){
                if(typeof onData == "function")
                    this.onData     = onData;
                this.__index__  = --__index__;
                return  server.concat(server.indexOf("?") < 0 ? "?" : "&", "Phico=", __index__, "&", Math.random());
            },
        connect =
            function(){
                return  this;
            },
        disconnect = iframe ?
            function(self){
                var iframe  = self.iframe;
                if(iframe && iframe.parentNode){
                    detachEvent("onreadystatechange", self.onreadystatechange);
                    detachEvent("onbeforeunload", self.onbeforeunload);
                    iframe.src = ".";
                    iframe.parentNode.removeChild(iframe);
                    delete  self.iframe;
                }
            }:
            function(self){
                var xhr = self.xhr;
                if(xhr){
                    removeEventListener("beforeunload", self.onbeforeunload, false);
                    xhr.onreadystatechange = function(){};
                    xhr.abort();
                    delete  self.xhr;
                }
            },
        onreadystatechange =
            function(){
                if(/loaded|complete/i.test(this.readyState))
                    throw new Error("Phico server is not available");
            };

    /**
     * private  variables description
     * @param   Array       private list of Phico instances
     * @param   Boolean     browser filter, true if browser is IE or Opera
     */
})([], /\b(msie|opera)\b/i.test(navigator.userAgent));

/**
 * Extra public static method,
 * basically the porting of ABC function into Phico library.
 * ABC, Ajax Basic Call, is the most simple Ajax interaction I know.
 * @param   String      page to use for Ajax Call
 * @param   Object      optional object with data to send, if you want to use POST,
 *                      or only an optional onLoad and/or onError callback that will
 *                      receive used XMLHttpRequest object, and elapsed time in milliseconds.
 * @example
 * -- GET Request
 * Phico.send("test.php?key=value");
 * Phico.send("test.php?key=value", {onLoad:function(xhr, elapsed){
 *     alert([xhr.responseText, elapsed]);
 * }});
 * -- POST Request
 * Phico.send("test.php", {key:"value"});
 * Phico.send("test.php?key=value", {key2:"value2", onLoad:function(){}, onError:function(){}});
 * // above example will be a POST call with a GET argument
 * // if you send an Array it will be sent in a PHP compatible way.
 * // key[]=value1&key[]=value2 ... etc
 */
Phico.send = (function(ABC){
    return  function(url, data, async, user, pass){
        var time    = new Date,
            abc     = ABC(),
            send    = [],
            key     = null;
        if(data){
            for(key in data)
                if(typeof data[key] !== "function" && data.hasOwnProperty(key)){
                    if(data[key] instanceof Array)
                        for(var i = 0; i < data[key].length; i++)
                            send.push(encodeURIComponent(key).concat("[]=", encodeURIComponent(data[key][i])));
                    else
                        send.push(encodeURIComponent(key).concat("=", encodeURIComponent(data[key])));
                }
            key = 0 < send.length ? send.join("&") : null;
        };
        abc.open(
            key !== null ? "POST" : "GET",
            url,
            async = async === undefined ? true : !!async,
            user !== undefined && user,
            pass !== undefined && pass
        );
        abc.setRequestHeader("If-Modified-Since", "Mon, 26 Jul 1997 05:00:00 GMT");
        abc.setRequestHeader("Cache-Control", "no-cache");
        abc.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        if(key !== null)
            abc.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        if(async)
            abc.onreadystatechange= function(){
                if(abc.readyState === 4 && data){
                    key = /^(2|3)[0-9]{2}$/.test(abc.status) ? "onLoad" : "onError";
                    if(typeof data[key] === "function")
                        data[key](abc, new Date - time);
                }
            };
        abc.send(key);
        return  abc;
    };
})(this.XMLHttpRequest ?
    function(){return new XMLHttpRequest;} :
    function(){return new ActiveXObject("Microsoft.XMLHTTP");}
);