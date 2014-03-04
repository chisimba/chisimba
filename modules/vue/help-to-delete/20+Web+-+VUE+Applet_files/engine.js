/*
 * Copyright 2005 Joe Walker
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Declare an object to which we can add real functions.
 */
if (dwr == null) var dwr = {};
if (dwr.engine == null) dwr.engine = (function () {
    var DWRe = {};

    /**
     * Set an alternative error handler from the default alert box.
     * @see getahead.org/dwr/browser/engine/errors
     */
    DWRe.setErrorHandler = function(handler) {
      DWRe._errorHandler = handler;
    };

    /**
     * Set an alternative warning handler from the default alert box.
     * @see getahead.org/dwr/browser/engine/errors
     */
    DWRe.setWarningHandler = function(handler) {
      DWRe._warningHandler = handler;
    };

    /**
     * Setter for the text/html handler - what happens if a DWR request gets an HTML
     * reply rather than the expected Javascript. Often due to login timeout
     */
    DWRe.setTextHtmlHandler = function(handler) {
      DWRe._textHtmlHandler = handler;
    }

    /**
     * Set a default timeout value for all calls. 0 (the default) turns timeouts off.
     * @see getahead.org/dwr/browser/engine/errors
     */
    DWRe.setTimeout = function(timeout) {
      DWRe._timeout = timeout;
    };

    /**
     * The Pre-Hook is called before any DWR remoting is done.
     * @see getahead.org/dwr/browser/engine/hooks
     */
    DWRe.setPreHook = function(handler) {
      DWRe._preHook = handler;
    };

    /**
     * The Post-Hook is called after any DWR remoting is done.
     * @see getahead.org/dwr/browser/engine/hooks
     */
    DWRe.setPostHook = function(handler) {
      DWRe._postHook = handler;
    };

    /**
     * Custom headers for all DWR calls
     * @see getahead.org/dwr/????
     */
    DWRe.setHeaders = function(headers) {
      DWRe._headers = headers;
    };

    /**
     * Custom parameters for all DWR calls
     * @see getahead.org/dwr/????
     */
    DWRe.setParameters = function(parameters) {
      DWRe._parameters = parameters;
    };

    /** XHR remoting type constant. See DWRe.set[Rpc|Poll]Type() */
    DWRe.XMLHttpRequest = 1;

    /** XHR remoting type constant. See DWRe.set[Rpc|Poll]Type() */
    DWRe.IFrame = 2;

    /** XHR remoting type constant. See DWRe.setRpcType() */
    DWRe.ScriptTag = 3;

    /**
     * Set the preferred remoting type.
     * @param newType One of DWRe.XMLHttpRequest or DWRe.IFrame or DWRe.ScriptTag
     * @see getahead.org/dwr/browser/engine/options
     */
    DWRe.setRpcType = function(newType) {
      if (newType != DWRe.XMLHttpRequest && newType != DWRe.IFrame && newType != DWRe.ScriptTag) {
        DWRe._handleError(null, { name:"dwr.engine.invalidRpcType", message:"RpcType must be one of dwr.engine.XMLHttpRequest or dwr.engine.IFrame or dwr.engine.ScriptTag" });
        return;
      }
      DWRe._rpcType = newType;
    };

    /**
     * Which HTTP method do we use to send results? Must be one of "GET" or "POST".
     * @see getahead.org/dwr/browser/engine/options
     */
    DWRe.setHttpMethod = function(httpMethod) {
      if (httpMethod != "GET" && httpMethod != "POST") {
        DWRe._handleError(null, { name:"dwr.engine.invalidHttpMethod", message:"Remoting method must be one of GET or POST" });
        return;
      }
      DWRe._httpMethod = httpMethod;
    };

    /**
     * Ensure that remote calls happen in the order in which they were sent? (Default: false)
     * @see getahead.org/dwr/browser/engine/ordering
     */
    DWRe.setOrdered = function(ordered) {
      DWRe._ordered = ordered;
    };

    /**
     * Do we ask the XHR object to be asynchronous? (Default: true)
     * @see getahead.org/dwr/browser/engine/options
     */
    DWRe.setAsync = function(async) {
      DWRe._async = async;
    };

    /**
     * Does DWR poll the server for updates? (Default: false)
     * @see getahead.org/dwr/browser/engine/options
     */
    DWRe.setActiveReverseAjax = function(activeReverseAjax) {
      if (activeReverseAjax) {
        // Bail if we are already started
        if (DWRe._activeReverseAjax) return;
        DWRe._activeReverseAjax = true;
        DWRe._poll();
      }
      else {
        // Can we cancel an existing request?
        if (DWRe._activeReverseAjax && DWRe._pollReq) DWRe._pollReq.abort();
        DWRe._activeReverseAjax = false;
      }
      // TODO: in iframe mode, if we start, stop, start then the second start may
      // well kick off a second iframe while the first is still about to return
      // we should cope with this but we don't
    };

    /**
     * Set the preferred polling type.
     * @param newPollType One of DWRe.XMLHttpRequest or DWRe.IFrame
     * @see getahead.org/dwr/browser/engine/options
     */
    DWRe.setPollType = function(newPollType) {
      if (newPollType != DWRe.XMLHttpRequest && newPollType != DWRe.IFrame) {
        DWRe._handleError(null, { name:"dwr.engine.invalidPollType", message:"PollType must be one of dwr.engine.XMLHttpRequest or dwr.engine.IFrame"  });
        return;
      }
      DWRe._pollType = newPollType;
    };

    /**
     * The default message handler.
     * @see getahead.org/dwr/browser/engine/errors
     */
    DWRe.defaultErrorHandler = function(message, ex) {
      DWRe._debug("Error: " + ex.name + ", " + ex.message, true);

      if (message == null || message == "") alert("A server error has occured. More information may be available in the console.");
      // Ignore NS_ERROR_NOT_AVAILABLE if Mozilla is being narky
      else if (message.indexOf("0x80040111") != -1) DWRe._debug(message);
      else alert(message);
    };

    /**
     * The default warning handler.
     * @see getahead.org/dwr/browser/engine/errors
     */
    DWRe.defaultWarningHandler = function(message, ex) {
      DWRe._debug(message);
    };

    /**
     * For reduced latency you can group several remote calls together using a batch.
     * @see getahead.org/dwr/browser/engine/batch
     */
    DWRe.beginBatch = function() {
      if (DWRe._batch) {
        DWRe._handleError(null, { name:"dwr.engine.batchBegun", message:"Batch already begun" });
        return;
      }
      DWRe._batch = DWRe._createBatch();
    };

    /**
     * Finished grouping a set of remote calls together. Go and execute them all.
     * @see getahead.org/dwr/browser/engine/batch
     */
    DWRe.endBatch = function(options) {
      var batch = DWRe._batch;
      if (batch == null) {
        DWRe._handleError(null, { name:"dwr.engine.batchNotBegun", message:"No batch in progress" });
        return;
      }
      DWRe._batch = null;
      if (batch.map.callCount == 0) return;

      // The hooks need to be merged carefully to preserve ordering
      if (options) DWRe._mergeBatch(batch, options);

      // In ordered mode, we don't send unless the list of sent items is empty
      if (DWRe._ordered && DWRe._batchesLength != 0) {
        DWRe._batchQueue[DWRe._batchQueue.length] = batch;
      }
      else {
        DWRe._sendData(batch);
      }
    };

    /** @deprecated */
    DWRe.setPollMethod = function(type) { DWRe.setPollType(type); };
    DWRe.setMethod = function(type) { DWRe.setRpcType(type); };
    DWRe.setVerb = function(verb) { DWRe.setHttpMethod(verb); };

    //==============================================================================
    // Only private stuff below here
    //==============================================================================

    /** The original page id sent from the server */
    DWRe._origScriptSessionId = "F67442A5C6119C17817FCA222D1AD78E";

    /** The session cookie name */
    DWRe._sessionCookieName = "JSESSIONID"; // JSESSIONID

    /** Is GET enabled for the benefit of Safari? */
    DWRe._allowGetForSafariButMakeForgeryEasier = "false";

    /** The script prefix to strip in the case of scriptTagProtection. */
    DWRe._scriptTagProtection = "throw 'allowScriptTagRemoting is false.';";

    /** The default path to the DWR servlet */
    DWRe._defaultPath = "/confluence/plugins/servlet/builder/dwr";

    /** The read page id that we calculate */
    DWRe._scriptSessionId = null;

    /** The function that we use to fetch/calculate a session id */
    DWRe._getScriptSessionId = function() {
      if (DWRe._scriptSessionId == null) {
        DWRe._scriptSessionId = DWRe._origScriptSessionId + Math.floor(Math.random() * 1000);
      }
      return DWRe._scriptSessionId;
    };

    /** A function to call if something fails. */
    DWRe._errorHandler = DWRe.defaultErrorHandler;

    /** For debugging when something unexplained happens. */
    DWRe._warningHandler = DWRe.defaultWarningHandler;

    /** A function to be called before requests are marshalled. Can be null. */
    DWRe._preHook = null;

    /** A function to be called after replies are received. Can be null. */
    DWRe._postHook = null;

    /** An map of the batches that we have sent and are awaiting a reply on. */
    DWRe._batches = {};

    /** A count of the number of outstanding batches. Should be == to _batches.length unless prototype has messed things up */
    DWRe._batchesLength = 0;

    /** In ordered mode, the array of batches waiting to be sent */
    DWRe._batchQueue = [];

    /** What is the default rpc type */
    DWRe._rpcType = DWRe.XMLHttpRequest;

    /** What is the default remoting method (ie GET or POST) */
    DWRe._httpMethod = "POST";

    /** Do we attempt to ensure that calls happen in the order in which they were sent? */
    DWRe._ordered = false;

    /** Do we make the calls async? */
    DWRe._async = true;

    /** The current batch (if we are in batch mode) */
    DWRe._batch = null;

    /** The global timeout */
    DWRe._timeout = 0;

    /** ActiveX objects to use when we want to convert an xml string into a DOM object. */
    DWRe._DOMDocument = ["Msxml2.DOMDocument.6.0", "Msxml2.DOMDocument.5.0", "Msxml2.DOMDocument.4.0", "Msxml2.DOMDocument.3.0", "MSXML2.DOMDocument", "MSXML.DOMDocument", "Microsoft.XMLDOM"];

    /** The ActiveX objects to use when we want to do an XMLHttpRequest call. */
    DWRe._XMLHTTP = ["Msxml2.XMLHTTP.6.0", "Msxml2.XMLHTTP.5.0", "Msxml2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"];

    /** Are we doing comet or polling? */
    DWRe._activeReverseAjax = false;

    /** What is the default polling type */
    DWRe._pollType = DWRe.XMLHttpRequest;
    //DWRe._pollType = DWRe.IFrame;

    /** The iframe that we are using to poll */
    DWRe._outstandingIFrames = [];

    /** The xhr object that we are using to poll */
    DWRe._pollReq = null;

    /** How many milliseconds between internal comet polls */
    DWRe._pollCometInterval = 200;

    /** How many times have we re-tried to poll? */
    DWRe._pollRetries = 0;
    DWRe._maxPollRetries = 0;

    /** Do we do a document.reload if we get a text/html reply? */
    DWRe._textHtmlHandler = null;

    /** If you wish to send custom headers with every request */
    DWRe._headers = null;

    /** If you wish to send extra custom request parameters with each request */
    DWRe._parameters = null;

    /** Undocumented interceptors - do not use */
    DWRe._postSeperator = "\n";
    DWRe._defaultInterceptor = function(data) {return data;}
    DWRe._urlRewriteHandler = DWRe._defaultInterceptor;
    DWRe._contentRewriteHandler = DWRe._defaultInterceptor;
    DWRe._replyRewriteHandler = DWRe._defaultInterceptor;

    /** Batch ids allow us to know which batch the server is answering */
    DWRe._nextBatchId = 0;

    /** A list of the properties that need merging from calls to a batch */
    DWRe._propnames = [ "rpcType", "httpMethod", "async", "timeout", "errorHandler", "warningHandler", "textHtmlHandler" ];

    /** Do we stream, or can be hacked to do so? */
    DWRe._partialResponseNo = 0;
    DWRe._partialResponseYes = 1;
    DWRe._partialResponseFlush = 2;

    /**
     * @private Send a request. Called by the Javascript interface stub
     * @param path part of URL after the host and before the exec bit without leading or trailing /s
     * @param scriptName The class to execute
     * @param methodName The method on said class to execute
     * @param func The callback function to which any returned data should be passed
     *       if this is null, any returned data will be ignored
     * @param vararg_params The parameters to pass to the above class
     */
    DWRe._execute = function(path, scriptName, methodName, vararg_params) {
      var singleShot = false;
      if (DWRe._batch == null) {
        DWRe.beginBatch();
        singleShot = true;
      }
      var batch = DWRe._batch;
      // To make them easy to manipulate we copy the arguments into an args array
      var args = [];
      for (var i = 0; i < arguments.length - 3; i++) {
        args[i] = arguments[i + 3];
      }
      // All the paths MUST be to the same servlet
      if (batch.path == null) {
        batch.path = path;
      }
      else {
        if (batch.path != path) {
          DWRe._handleError(batch, { name:"dwr.engine.multipleServlets", message:"Can't batch requests to multiple DWR Servlets." });
          return;
        }
      }
      // From the other params, work out which is the function (or object with
      // call meta-data) and which is the call parameters
      var callData;
      var lastArg = args[args.length - 1];
      if (typeof lastArg == "function" || lastArg == null) callData = { callback:args.pop() };
      else callData = args.pop();

      // Merge from the callData into the batch
      DWRe._mergeBatch(batch, callData);
      batch.handlers[batch.map.callCount] = {
        exceptionHandler:callData.exceptionHandler,
        callback:callData.callback
      };

      // Copy to the map the things that need serializing
      var prefix = "c" + batch.map.callCount + "-";
      batch.map[prefix + "scriptName"] = scriptName;
      batch.map[prefix + "methodName"] = methodName;
      batch.map[prefix + "id"] = batch.map.callCount;
      for (i = 0; i < args.length; i++) {
        DWRe._serializeAll(batch, [], args[i], prefix + "param" + i);
      }

      // Now we have finished remembering the call, we incr the call count
      batch.map.callCount++;
      if (singleShot) DWRe.endBatch();
    };

    /** @private Poll the server to see if there is any data waiting */
    DWRe._poll = function(overridePath) {
      if (!DWRe._activeReverseAjax) return;

      var batch = DWRe._createBatch();
      batch.map.id = 0; // TODO: Do we need this??
      batch.map.callCount = 1;
      batch.isPoll = true;
      if (navigator.userAgent.indexOf("Gecko/") != -1) {
        batch.rpcType = DWRe._pollType;
        batch.map.partialResponse = DWRe._partialResponseYes;
      }
      else if (document.all) {
        batch.rpcType = DWRe.IFrame;
        batch.map.partialResponse = DWRe._partialResponseFlush;
      }
      else {
        batch.rpcType = DWRe._pollType;
        batch.map.partialResponse = DWRe._partialResponseNo;
      }
      batch.httpMethod = "POST";
      batch.async = true;
      batch.timeout = 0;
      batch.path = (overridePath) ? overridePath : DWRe._defaultPath;
      batch.preHooks = [];
      batch.postHooks = [];
      batch.errorHandler = DWRe._pollErrorHandler;
      batch.warningHandler = DWRe._pollErrorHandler;
      batch.handlers[0] = {
        callback:function(pause) {
          DWRe._pollRetries = 0;
          setTimeout(DWRe._poll, pause);
        }
      };

      // Send the data
      DWRe._sendData(batch);
      if (batch.rpcType == DWRe.XMLHttpRequest) {
      // if (batch.map.partialResponse != DWRe._partialResponseNo) {
        DWRe._checkCometPoll();
      }
    };

    /** Try to recover from polling errors */
    DWRe._pollErrorHandler = function(msg, ex) {
      // if anything goes wrong then just silently try again (up to 3x) after 10s
      DWRe._pollRetries++;
      DWRe._debug("Reverse Ajax poll failed (pollRetries=" + DWRe._pollRetries + "): " + ex.name + " : " + ex.message);
      if (DWRe._pollRetries < DWRe._maxPollRetries) {
        setTimeout(DWRe._poll, 10000);
      }
      else {
        DWRe._debug("Giving up.");
      }
    };

    /** @private Generate a new standard batch */
    DWRe._createBatch = function() {
      var batch = {
        map:{
          callCount:0,
          page:window.location.pathname + window.location.search,
          httpSessionId:DWRe._getJSessionId(),
          scriptSessionId:DWRe._getScriptSessionId()
        },
        charsProcessed:0, paramCount:0,
        headers:[], parameters:[],
        isPoll:false, headers:{}, handlers:{}, preHooks:[], postHooks:[],
        rpcType:DWRe._rpcType,
        httpMethod:DWRe._httpMethod,
        async:DWRe._async,
        timeout:DWRe._timeout,
        errorHandler:DWRe._errorHandler,
        warningHandler:DWRe._warningHandler,
        textHtmlHandler:DWRe._textHtmlHandler
      };
      if (DWRe._preHook) batch.preHooks.push(DWRe._preHook);
      if (DWRe._postHook) batch.postHooks.push(DWRe._postHook);
      var propname, data;
      if (DWRe._headers) {
        for (propname in DWRe._headers) {
          data = DWRe._headers[propname];
          if (typeof data != "function") batch.headers[propname] = data;
        }
      }
      if (DWRe._parameters) {
        for (propname in DWRe._parameters) {
          data = DWRe._parameters[propname];
          if (typeof data != "function") batch.parameters[propname] = data;
        }
      }
      return batch;
    }

    /** @private Take further options and merge them into */
    DWRe._mergeBatch = function(batch, overrides) {
      var propname, data;
      for (var i = 0; i < DWRe._propnames.length; i++) {
        propname = DWRe._propnames[i];
        if (overrides[propname] != null) batch[propname] = overrides[propname];
      }
      if (overrides.preHook != null) batch.preHooks.unshift(overrides.preHook);
      if (overrides.postHook != null) batch.postHooks.push(overrides.postHook);
      if (overrides.headers) {
        for (propname in overrides.headers) {
          data = overrides.headers[propname];
          if (typeof data != "function") batch.headers[propname] = data;
        }
      }
      if (overrides.parameters) {
        for (propname in overrides.parameters) {
          data = overrides.parameters[propname];
          if (typeof data != "function") batch.map["p-" + propname] = "" + data;
        }
      }
    };

    /** @private What is our session id? */
    DWRe._getJSessionId =  function() {
      var cookies = document.cookie.split(';');
      for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i];
        while (cookie.charAt(0) == ' ') cookie = cookie.substring(1, cookie.length);
        if (cookie.indexOf(DWRe._sessionCookieName + "=") == 0) {
          return cookie.substring(11, cookie.length);
        }
      }
      return "";
    }

    /** @private Check for reverse Ajax activity */
    DWRe._checkCometPoll = function() {
      for (var i = 0; i < DWRe._outstandingIFrames.length; i++) {
        var text = "";
        var iframe = DWRe._outstandingIFrames[i];
        try {
          text = DWRe._getTextFromCometIFrame(iframe);
        }
        catch (ex) {
          DWRe._handleWarning(iframe.batch, ex);
        }
        if (text != "") DWRe._processCometResponse(text, iframe.batch);
      }
      if (DWRe._pollReq) {
        var req = DWRe._pollReq;
        var text = req.responseText;
        DWRe._processCometResponse(text, req.batch);
      }

      // If the poll resources are still there, come back again
      if (DWRe._outstandingIFrames.length > 0 || DWRe._pollReq) {
        setTimeout(DWRe._checkCometPoll(), DWRe._pollCometInterval);
      }
    };

    /** @private Extract the whole (executed an all) text from the current iframe */
    DWRe._getTextFromCometIFrame = function(frameEle) {
      var body = frameEle.contentWindow.document.body;
      if (body == null) return "";
      var text = body.innerHTML;
      // We need to prevent IE from stripping line feeds
      if (text.indexOf("<PRE>") == 0 || text.indexOf("<pre>") == 0) {
        text = text.substring(5, text.length - 7);
      }
      return text;
    };

    /** @private Some more text might have come in, test and execute the new stuff */
    DWRe._processCometResponse = function(response, batch) {
      if (batch.charsProcessed == response.length) return;
      if (response.length == 0) {
        batch.charsProcessed = 0;
        return;
      }

      var firstStartTag = response.indexOf("//#DWR-START#", batch.charsProcessed);
      if (firstStartTag == -1) {
        // DWRe._debug("No start tag (search from " + batch.charsProcessed + "). skipping '" + response.substring(batch.charsProcessed) + "'");
        batch.charsProcessed = response.length;
        return;
      }
      // if (firstStartTag > 0) {
      //   DWRe._debug("Start tag not at start (search from " + batch.charsProcessed + "). skipping '" + response.substring(batch.charsProcessed, firstStartTag) + "'");
      // }

      var lastEndTag = response.lastIndexOf("//#DWR-END#");
      if (lastEndTag == -1) {
        // DWRe._debug("No end tag. unchanged charsProcessed=" + batch.charsProcessed);
        return;
      }

      // Skip the end tag too for next time, remembering CR and LF
      if (response.charCodeAt(lastEndTag + 11) == 13 && response.charCodeAt(lastEndTag + 12) == 10) {
        batch.charsProcessed = lastEndTag + 13;
      }
      else {
        batch.charsProcessed = lastEndTag + 11;
      }

      var exec = response.substring(firstStartTag + 13, lastEndTag);

      DWRe._receivedBatch = batch;
      DWRe._eval(exec);
      DWRe._receivedBatch = null;
    };

    /** @private Actually send the block of data in the batch object. */
    DWRe._sendData = function(batch) {
      batch.map.batchId = DWRe._nextBatchId++;
      DWRe._batches[batch.map.batchId] = batch;
      DWRe._batchesLength++;
      batch.completed = false;

      for (var i = 0; i < batch.preHooks.length; i++) {
        batch.preHooks[i]();
      }
      batch.preHooks = null;
      // Set a timeout
      if (batch.timeout && batch.timeout != 0) {
        batch.interval = setInterval(function() { DWRe._abortRequest(batch); }, batch.timeout);
      }
      // Get setup for XMLHttpRequest if possible
      if (batch.rpcType == DWRe.XMLHttpRequest) {
        if (window.XMLHttpRequest) {
          batch.req = new XMLHttpRequest();
        }
        // IE5 for the mac claims to support window.ActiveXObject, but throws an error when it's used
        else if (window.ActiveXObject && !(navigator.userAgent.indexOf("Mac") >= 0 && navigator.userAgent.indexOf("MSIE") >= 0)) {
          batch.req = DWRe._newActiveXObject(DWRe._XMLHTTP);
        }
      }

      var prop, request;
      if (batch.req) {
        // Proceed using XMLHttpRequest
        if (batch.async) {
          batch.req.onreadystatechange = function() { DWRe._stateChange(batch); };
        }
        // If we're polling, record this for monitoring
        if (batch.isPoll) {
          DWRe._pollReq = batch.req;
          // In IE XHR is an ActiveX control so you can't augment it like this
          // however batch.isPoll uses IFrame on IE so were safe here
          batch.req.batch = batch;
        }
        // Workaround for Safari 1.x POST bug
        var indexSafari = navigator.userAgent.indexOf("Safari/");
        if (indexSafari >= 0) {
          var version = navigator.userAgent.substring(indexSafari + 7);
          if (parseInt(version, 10) < 400) {
            if (DWRe._allowGetForSafariButMakeForgeryEasier == "true") batch.httpMethod = "GET";
            else DWRe._handleWarning(batch, { name:"dwr.engine.oldSafari", message:"Safari GET support disabled. See getahead.org/dwr/server/servlet and allowGetForSafariButMakeForgeryEasier." });
          }
        }
        batch.mode = batch.isPoll ? DWRe._ModePlainPoll : DWRe._ModePlainCall;
        request = DWRe._constructRequest(batch);
        try {
          batch.req.open(batch.httpMethod, request.url, batch.async);
          try {
            for (prop in batch.headers) {
              var value = batch.headers[prop];
              if (typeof value == "string") batch.req.setRequestHeader(prop, value);
            }
            if (!batch.headers["Content-Type"]) batch.req.setRequestHeader("Content-Type", "text/plain");
          }
          catch (ex) {
            DWRe._handleWarning(batch, ex);
          }
          batch.req.send(request.body);
          if (!batch.async) DWRe._stateChange(batch);
        }
        catch (ex) {
          DWRe._handleError(batch, ex);
        }
      }
      else if (batch.rpcType != DWRe.ScriptTag) {
        // Proceed using iframe
        var idname = batch.isPoll ? "dwr-if-poll-" + batch.map.batchId : "dwr-if-" + batch.map["c0-id"];
        batch.div = document.createElement("div");
        batch.div.innerHTML = "<iframe src='javascript:void(0)' frameborder='0' style='width:0px;height:0px;border:0;' id='" + idname + "' name='" + idname + "'></iframe>";
        document.body.appendChild(batch.div);
        batch.iframe = document.getElementById(idname);
        batch.iframe.batch = batch;
        batch.mode = batch.isPoll ? DWRe._ModeHtmlPoll : DWRe._ModeHtmlCall;
        if (batch.isPoll) DWRe._outstandingIFrames.push(batch.iframe);
        request = DWRe._constructRequest(batch);
        if (batch.httpMethod == "GET") {
          batch.iframe.setAttribute("src", request.url);
          // document.body.appendChild(batch.iframe);
        }
        else {
          batch.form = document.createElement("form");
          batch.form.setAttribute("id", "dwr-form");
          batch.form.setAttribute("action", request.url);
          batch.form.setAttribute("target", idname);
          batch.form.target = idname;
          batch.form.setAttribute("method", batch.httpMethod);
          for (prop in batch.map) {
            var value = batch.map[prop];
            if (typeof value != "function") {
              var formInput = document.createElement("input");
              formInput.setAttribute("type", "hidden");
              formInput.setAttribute("name", prop);
              formInput.setAttribute("value", value);
              batch.form.appendChild(formInput);
            }
          }
          document.body.appendChild(batch.form);
          batch.form.submit();
        }
      }
      else {
        batch.httpMethod = "GET"; // There's no such thing as ScriptTag using POST
        batch.mode = batch.isPoll ? DWRe._ModePlainPoll : DWRe._ModePlainCall;
        request = DWRe._constructRequest(batch);
        batch.script = document.createElement("script");
        batch.script.id = "dwr-st-" + batch.map["c0-id"];
        batch.script.src = request.url;
        document.body.appendChild(batch.script);
      }
    };

    DWRe._ModePlainCall = "/call/plaincall/";
    DWRe._ModeHtmlCall = "/call/htmlcall/";
    DWRe._ModePlainPoll = "/call/plainpoll/";
    DWRe._ModeHtmlPoll = "/call/htmlpoll/";

    /** @private Work out what the URL should look like */
    DWRe._constructRequest = function(batch) {
      // A quick string to help people that use web log analysers
      var request = { url:batch.path + batch.mode, body:null };
      if (batch.isPoll == true) {
        request.url += "ReverseAjax.dwr";
      }
      else if (batch.map.callCount == 1) {
        request.url += batch.map["c0-scriptName"] + "." + batch.map["c0-methodName"] + ".dwr";
      }
      else {
        request.url += "Multiple." + batch.map.callCount + ".dwr";
      }
      // Play nice with url re-writing
      var sessionMatch = location.href.match(/jsessionid=([^?]+)/);
      if (sessionMatch != null) {
        request.url += ";jsessionid=" + sessionMatch[1];
      }

      var prop;
      if (batch.httpMethod == "GET") {
        // Some browsers (Opera/Safari2) seem to fail to convert the callCount value
        // to a string in the loop below so we do it manually here.
        batch.map.callCount = "" + batch.map.callCount;
        request.url += "?";
        for (prop in batch.map) {
          if (typeof batch.map[prop] != "function") {
            request.url += encodeURIComponent(prop) + "=" + encodeURIComponent(batch.map[prop]) + "&";
          }
        }
        request.url = request.url.substring(0, request.url.length - 1);
      }
      else {
        // PERFORMANCE: for iframe mode this is thrown away.
        request.body = "";
        for (prop in batch.map) {
          if (typeof batch.map[prop] != "function") {
            request.body += prop + "=" + batch.map[prop] + DWRe._postSeperator;
          }
        }
        request.body = DWRe._contentRewriteHandler(request.body);
      }
      request.url = DWRe._urlRewriteHandler(request.url);
      return request;
    };

    /** @private Called by XMLHttpRequest to indicate that something has happened */
    DWRe._stateChange = function(batch) {
      var toEval;

      if (batch.completed) {
        DWRe._debug("Error: _stateChange() with batch.completed");
        return;
      }

      var req = batch.req;
      try {
        if (req.readyState != 4) return;
      }
      catch (ex) {
        DWRe._handleWarning(batch, ex);
        // It's broken - clear up and forget this call
        DWRe._clearUp(batch);
        return;
      }

      try {
        var reply = req.responseText;
        reply = DWRe._replyRewriteHandler(reply);
        var status = req.status; // causes Mozilla to except on page moves

        if (reply == null || reply == "") {
          DWRe._handleWarning(batch, { name:"dwr.engine.missingData", message:"No data received from server" });
        }
        else if (status != 200) {
          DWRe._handleError(batch, { name:"dwr.engine.http." + status, message:req.statusText });
        }
        else {
          var contentType = req.getResponseHeader("Content-Type");
          if (!contentType.match(/^text\/plain/) && !contentType.match(/^text\/javascript/)) {
            if (contentType.match(/^text\/html/) && typeof batch.textHtmlHandler == "function") {
              batch.textHtmlHandler();
            }
            else {
              DWRe._handleWarning(batch, { name:"dwr.engine.invalidMimeType", message:"Invalid content type: '" + contentType + "'" });
            }
          }
          else {
            // Comet replies might have already partially executed
            if (batch.isPoll && batch.map.partialResponse == DWRe._partialResponseYes) {
              DWRe._processCometResponse(reply, batch);
            }
            else {
              if (reply.search("//#DWR") == -1) {
                DWRe._handleWarning(batch, { name:"dwr.engine.invalidReply", message:"Invalid reply from server" });
              }
              else {
                toEval = reply;
              }
            }
          }
        }
      }
      catch (ex) {
        DWRe._handleWarning(batch, ex);
      }

      DWRe._callPostHooks(batch);

      // Outside of the try/catch so errors propogate normally:
      DWRe._receivedBatch = batch;
      if (toEval != null) toEval = toEval.replace(DWRe._scriptTagProtection, "");
      DWRe._eval(toEval);
      DWRe._receivedBatch = null;

      DWRe._clearUp(batch);
    };

    /** @private Called by the server: Execute a callback */
    DWRe._remoteHandleCallback = function(batchId, callId, reply) {
      var batch = DWRe._batches[batchId];
      if (batch == null) {
        DWRe._debug("Warning: batch == null in remoteHandleCallback for batchId=" + batchId, true);
        return;
      }
      // Error handlers inside here indicate an error that is nothing to do
      // with DWR so we handle them differently.
      try {
        var handlers = batch.handlers[callId];
        if (!handlers) {
          DWRe._debug("Warning: Missing handlers. callId=" + callId, true);
        }
        else if (typeof handlers.callback == "function") handlers.callback(reply);
      }
      catch (ex) {
        DWRe._handleError(batch, ex);
      }
    };

    /** @private Called by the server: Handle an exception for a call */
    DWRe._remoteHandleException = function(batchId, callId, ex) {
      var batch = DWRe._batches[batchId];
      if (batch == null) { DWRe._debug("Warning: null batch in remoteHandleException", true); return; }
      var handlers = batch.handlers[callId];
      if (handlers == null) { DWRe._debug("Warning: null handlers in remoteHandleException", true); return; }
      if (ex.message == undefined) ex.message = "";
      if (typeof handlers.exceptionHandler == "function") handlers.exceptionHandler(ex.message, ex);
      else if (typeof batch.errorHandler == "function") batch.errorHandler(ex.message, ex);
    };

    /** @private Called by the server: The whole batch is broken */
    DWRe._remoteHandleBatchException = function(ex, batchId) {
      var searchBatch = (DWRe._receivedBatch == null && batchId != null);
      if (searchBatch) {
        DWRe._receivedBatch = DWRe._batches[batchId];
      }
      if (ex.message == undefined) ex.message = "";
      DWRe._handleError(DWRe._receivedBatch, ex);
      if (searchBatch) {
        DWRe._receivedBatch = null;
        DWRe._clearUp(DWRe._batches[batchId]);
      }
    };

    /** @private Called by the server: Reverse ajax should not be used */
    DWRe._remotePollCometDisabled = function(ex, batchId) {
      DWRe.setActiveReverseAjax(false);
      var searchBatch = (DWRe._receivedBatch == null && batchId != null);
      if (searchBatch) {
        DWRe._receivedBatch = DWRe._batches[batchId];
      }
      if (ex.message == undefined) ex.message = "";
      DWRe._handleError(DWRe._receivedBatch, ex);
      if (searchBatch) {
        DWRe._receivedBatch = null;
        DWRe._clearUp(DWRe._batches[batchId]);
      }
    };

    /** @private Called by the server: An IFrame reply is about to start */
    DWRe._remoteBeginIFrameResponse = function(iframe, batchId) {
      if (iframe != null) DWRe._receivedBatch = iframe.batch;
      DWRe._callPostHooks(DWRe._receivedBatch);
    };

    /** @private Called by the server: An IFrame reply is just completing */
    DWRe._remoteEndIFrameResponse = function(batchId) {
      DWRe._clearUp(DWRe._receivedBatch);
      DWRe._receivedBatch = null;
    };

    /** @private This is a hack to make the context be this window */
    DWRe._eval = function(script) {
      if (script == null) return null;
      if (script == "") { DWRe._debug("Warning: blank script", true); return null; }
      // DWRe._debug("Exec: [" + script + "]", true);
      return eval(script);
    };

    /** @private Called as a result of a request timeout */
    DWRe._abortRequest = function(batch) {
      if (batch && !batch.completed) {
        clearInterval(batch.interval);
        DWRe._clearUp(batch);
        if (batch.req) batch.req.abort();
        DWRe._handleError(batch, { name:"dwr.engine.timeout", message:"Timeout" });
      }
    };

    /** @private call all the post hooks for a batch */
    DWRe._callPostHooks = function(batch) {
      if (batch.postHooks) {
        for (var i = 0; i < batch.postHooks.length; i++) {
          batch.postHooks[i]();
        }
        batch.postHooks = null;
      }
    }

    /** @private A call has finished by whatever means and we need to shut it all down. */
    DWRe._clearUp = function(batch) {
      if (!batch) { DWRe._debug("Warning: null batch in dwr.engine._clearUp()", true); return; }
      if (batch.completed == "true") { DWRe._debug("Warning: Double complete", true); return; }

      // IFrame tidyup
      if (batch.div) batch.div.parentNode.removeChild(batch.div);
      if (batch.iframe) {
        // If this is a poll frame then stop comet polling
        for (var i = 0; i < DWRe._outstandingIFrames.length; i++) {
          if (DWRe._outstandingIFrames[i] == batch.iframe) {
            DWRe._outstandingIFrames.splice(i, 1);
          }
        }
        batch.iframe.parentNode.removeChild(batch.iframe);
      }
      if (batch.form) batch.form.parentNode.removeChild(batch.form);

      // XHR tidyup: avoid IE handles increase
      if (batch.req) {
        // If this is a poll frame then stop comet polling
        if (batch.req == DWRe._pollReq) DWRe._pollReq = null;
        delete batch.req;
      }

      if (batch.map && batch.map.batchId) {
        delete DWRe._batches[batch.map.batchId];
        DWRe._batchesLength--;
      }

      batch.completed = true;

      // If there is anything on the queue waiting to go out, then send it.
      // We don't need to check for ordered mode, here because when ordered mode
      // gets turned off, we still process *waiting* batches in an ordered way.
      if (DWRe._batchQueue.length != 0) {
        var sendbatch = DWRe._batchQueue.shift();
        DWRe._sendData(sendbatch);
      }
    };

    /** @private Generic error handling routing to save having null checks everywhere */
    DWRe._handleError = function(batch, ex) {
      if (typeof ex == "string") ex = { name:"unknown", message:ex };
      if (ex.message == null) ex.message = "";
      if (ex.name == null) ex.name = "unknown";
      if (batch && typeof batch.errorHandler == "function") batch.errorHandler(ex.message, ex);
      else if (DWRe._errorHandler) DWRe._errorHandler(ex.message, ex);
      DWRe._clearUp(batch);
    };

    /** @private Generic error handling routing to save having null checks everywhere */
    DWRe._handleWarning = function(batch, ex) {
      if (typeof ex == "string") ex = { name:"unknown", message:ex };
      if (ex.message == null) ex.message = "";
      if (ex.name == null) ex.name = "unknown";
      if (batch && typeof batch.warningHandler == "function") batch.warningHandler(ex.message, ex);
      else if (DWRe._warningHandler) DWRe._warningHandler(ex.message, ex);
      DWRe._clearUp(batch);
    };

    /**
     * @private Marshall a data item
     * @param batch A map of variables to how they have been marshalled
     * @param referto An array of already marshalled variables to prevent recurrsion
     * @param data The data to be marshalled
     * @param name The name of the data being marshalled
     */
    DWRe._serializeAll = function(batch, referto, data, name) {
      if (data == null) {
        batch.map[name] = "null:null";
        return;
      }

      switch (typeof data) {
      case "boolean":
        batch.map[name] = "boolean:" + data;
        break;
      case "number":
        batch.map[name] = "number:" + data;
        break;
      case "string":
        batch.map[name] = "string:" + encodeURIComponent(data);
        break;
      case "object":
        if (data instanceof String) batch.map[name] = "String:" + encodeURIComponent(data);
        else if (data instanceof Boolean) batch.map[name] = "Boolean:" + data;
        else if (data instanceof Number) batch.map[name] = "Number:" + data;
        else if (data instanceof Date) batch.map[name] = "Date:" + data.getTime();
        else if (data && data.join) batch.map[name] = DWRe._serializeArray(batch, referto, data, name);
        else batch.map[name] = DWRe._serializeObject(batch, referto, data, name);
        break;
      case "function":
        // We just ignore functions.
        break;
      default:
        DWRe._handleWarning(null, { name:"dwr.engine.unexpectedType", message:"Unexpected type: " + typeof data + ", attempting default converter." });
        batch.map[name] = "default:" + data;
        break;
      }
    };

    /** @private Have we already converted this object? */
    DWRe._lookup = function(referto, data, name) {
      var lookup;
      // Can't use a map: getahead.org/ajax/javascript-gotchas
      for (var i = 0; i < referto.length; i++) {
        if (referto[i].data == data) {
          lookup = referto[i];
          break;
        }
      }
      if (lookup) return "reference:" + lookup.name;
      referto.push({ data:data, name:name });
      return null;
    };

    /** @private Marshall an object */
    DWRe._serializeObject = function(batch, referto, data, name) {
      var ref = DWRe._lookup(referto, data, name);
      if (ref) return ref;

      // This check for an HTML is not complete, but is there a better way?
      // Maybe we should add: data.hasChildNodes typeof "function" == true
      if (data.nodeName && data.nodeType) {
        return DWRe._serializeXml(batch, referto, data, name);
      }

      // treat objects as an associative arrays
      var reply = "Object_" + DWRe._getObjectClassName(data) + ":{";
      var element;
      for (element in data) {
        if (typeof data[element] != "function") {
          batch.paramCount++;
          var childName = "c" + DWRe._batch.map.callCount + "-e" + batch.paramCount;
          DWRe._serializeAll(batch, referto, data[element], childName);

          reply += encodeURIComponent(element) + ":reference:" + childName + ", ";
        }
      }

      if (reply.substring(reply.length - 2) == ", ") {
        reply = reply.substring(0, reply.length - 2);
      }
      reply += "}";

      return reply;
    };

    /** @private Returns the classname of supplied argument obj */
    DWRe._errorClasses = { "Error":Error, "EvalError":EvalError, "RangeError":RangeError, "ReferenceError":ReferenceError, "SyntaxError":SyntaxError, "TypeError":TypeError, "URIError":URIError };
    DWRe._getObjectClassName = function(obj) {
      // Try to find the classname by stringifying the object's constructor
      // and extract <class> from "function <class>".
      if (obj && obj.constructor && obj.constructor.toString)
      {
        var str = obj.constructor.toString();
        var regexpmatch = str.match(/function\s+(\w+)/);
        if (regexpmatch && regexpmatch.length == 2) {
          return regexpmatch[1];
        }
      }

      // Now manually test against the core Error classes, as these in some
      // browsers successfully match to the wrong class in the
      // Object.toString() test we will do later
      if (obj && obj.constructor) {
        for (var errorname in DWRe._errorClasses) {
          if (obj.constructor == DWRe._errorClasses[errorname]) return errorname;
        }
      }

      // Try to find the classname by calling Object.toString() on the object
      // and extracting <class> from "[object <class>]"
      if (obj) {
        var str = Object.prototype.toString.call(obj);
        var regexpmatch = str.match(/\[object\s+(\w+)/);
        if (regexpmatch && regexpmatch.length==2) {
          return regexpmatch[1];
        }
      }

      // Supplied argument was probably not an object, but what is better?
      return "Object";
    };

    /** @private Marshall an object */
    DWRe._serializeXml = function(batch, referto, data, name) {
      var ref = DWRe._lookup(referto, data, name);
      if (ref) return ref;

      var output;
      if (window.XMLSerializer) output = new XMLSerializer().serializeToString(data);
      else if (data.toXml) output = data.toXml;
      else output = data.innerHTML;

      return "XML:" + encodeURIComponent(output);
    };

    /** @private Marshall an array */
    DWRe._serializeArray = function(batch, referto, data, name) {
      var ref = DWRe._lookup(referto, data, name);
      if (ref) return ref;

      var reply = "Array:[";
      for (var i = 0; i < data.length; i++) {
        if (i != 0) reply += ",";
        batch.paramCount++;
        var childName = "c" + DWRe._batch.map.callCount + "-e" + batch.paramCount;
        DWRe._serializeAll(batch, referto, data[i], childName);
        reply += "reference:";
        reply += childName;
      }
      reply += "]";

      return reply;
    };

    /** @private Convert an XML string into a DOM object. */
    DWRe._unserializeDocument = function(xml) {
      var dom;
      if (window.DOMParser) {
        var parser = new DOMParser();
        dom = parser.parseFromString(xml, "text/xml");
        if (!dom.documentElement || dom.documentElement.tagName == "parsererror") {
          var message = dom.documentElement.firstChild.data;
          message += "\n" + dom.documentElement.firstChild.nextSibling.firstChild.data;
          throw message;
        }
        return dom;
      }
      else if (window.ActiveXObject) {
        dom = DWRe._newActiveXObject(DWRe._DOMDocument);
        dom.loadXML(xml); // What happens on parse fail with IE?
        return dom;
      }
      else {
        var div = document.createElement("div");
        div.innerHTML = xml;
        return div;
      }
    };

    /** @param axarray An array of strings to attempt to create ActiveX objects from */
    DWRe._newActiveXObject = function(axarray) {
      var returnValue;
      for (var i = 0; i < axarray.length; i++) {
        try {
          returnValue = new ActiveXObject(axarray[i]);
          break;
        }
        catch (ex) { /* ignore */ }
      }
      return returnValue;
    };

    /** @private Used internally when some message needs to get to the programmer */
    DWRe._debug = function(message, stacktrace) {
      var written = false;
      try {
        if (window.console) {
          if (stacktrace && window.console.trace) window.console.trace();
          window.console.log(message);
          written = true;
        }
        else if (window.opera && window.opera.postError) {
          window.opera.postError(message);
          written = true;
        }
      }
      catch (ex) { /* ignore */ }

      if (!written) {
        var debug = document.getElementById("dwr-debug");
        if (debug) {
          var contents = message + "<br/>" + debug.innerHTML;
          if (contents.length > 2048) contents = contents.substring(0, 2048);
          debug.innerHTML = contents;
        }
      }
    };

    return DWRe;
})();

