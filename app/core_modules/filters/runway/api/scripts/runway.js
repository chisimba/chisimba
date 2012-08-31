/*======================================================================
 *  Runway
 *======================================================================
 */

Runway.swf = Runway.urlPrefix + "swf/runway";

Runway.flashVersion = {
    major: 10,
    minor: 0,
    revision: 12
};

Runway.themes = {
    "pristine" : {
        topColor: "white",
        bottomColor: "white"
    },
    "arctic" : {
        topColor: "#e8e8e8",
        bottomColor: "#e8e8ff"
    },
    "midnight" : {
        topColor: "black",
        bottomColor: "black"
    },
    "sunset" : {
        topColor: "#110022",
        bottomColor: "#220000"
    },
    "pitchblack" : {
        topColor: "black",
        bottomColor: "black"
    }
};

Runway.hasRightFlashVersion = function() {
    return Runway.Flash.hasVersionOrLater(Runway.flashVersion.major, Runway.flashVersion.minor, Runway.flashVersion.revision);
};

Runway.create = function(elmt, options) {
    return new Runway._Impl(elmt, options);
};

Runway.createOrShowInstaller = function(elmt, options) {
    if (Runway.hasRightFlashVersion()) {
        return Runway.create(elmt, options);
    } else {
        elmt.innerHTML = 
            '<div class="runway-noflash-message">' +
                'Flash Player version ' + 
                    [Runway.flashVersion.major, Runway.flashVersion.minor, Runway.flashVersion.revision].join(".") +
                    '<br/>' +
                'or later is needed to view this content.<br/>' +
                '<a href="http://get.adobe.com/flashplayer/">Download the latest Flash Player</a>.' +
            '</div>';
    }
    return null;
};

Runway._Impl = function(elmt, options) {
    this._elmt = elmt;
    this._options = options || {};
    this._installUI();
};

Runway._Impl.prototype._installUI = function() {
    this._flashObjectID = "runway" + new Date().getTime() + Math.floor(Math.random() * 1000000);
    
    var self = this;
    var onReady = function() {
        Runway.Dispatcher.release(arguments.callee);
        self._onReady();
    };
    
    var flashVars = [
        "onReady=" + Runway.Dispatcher.wrap(onReady)
    ];
    var eventHandlerNames = {
        "onSelect" : true,
        "onZoom" : true,
        "onTitleClick" : true,
        "onTitleMouseOver": true,
        "onTitleMouseOut": true,
        "onSubtitleClick" : true,
        "onSubtitleMouseOver": true,
        "onSubtitleMouseOut": true,
        "onSideSlideMouseOver": true,
        "onSideSlideMouseOut": true
    };
    for (var n in eventHandlerNames) {
        if (n in this._options) {
            flashVars.push(n + "=" + Runway.Dispatcher.wrap(this._options[n]));
        }
    }
    
    for (var n in this._options) {
        if (this._options.hasOwnProperty(n)) {
            if (n != "onReady" && !(n in eventHandlerNames)) {
                flashVars.push(n + "=" + this._options[n]);
            }
        }
    }
    
    this._elmt.innerHTML = Runway.Flash.generateObjectEmbedHTML(
        "src",                  Runway.swf,
        "width",                "100%",
        "height",               "100%",
        "align",                "middle",
        "id",                   this._flashObjectID,
        "quality",              "high",
        "bgcolor",              "#ffffff",
        "name",                 "Runway",
        "allowScriptAccess",    "always",
        "type",                 "application/x-shockwave-flash",
        "pluginspage",          "http://www.adobe.com/go/getflashplayer",
        "FlashVars",            flashVars.join("&")
    );
    
    this._flashObject = document[this._flashObjectID] || window[this._flashObjectID];
    
    var onMouseScroll = function(evt) {
        if (evt.stopPropagation) {
            evt.stopPropagation();
        }
        evt.cancelBubble = true;
        
          // prevent the default action
        if (evt.preventDefault) {
            evt.preventDefault();
        }
        evt.returnValue = false;
        return false;
    };
    if (SimileAjax.Platform.browser.isFirefox) {
        SimileAjax.DOM.registerEvent(this._elmt, "DOMMouseScroll", onMouseScroll);
    } else {
        SimileAjax.DOM.registerEvent(this._elmt, "mousewheel", onMouseScroll);
    }
};

Runway._Impl.prototype.getID = function() {
    return this._flashObjectID;
};

Runway._Impl.prototype.clearRecords = function() {
    this._flashObject.clearRecords();
};

Runway._Impl.prototype.addRecords = function(records) {
    this._flashObject.addRecords(records);
};

Runway._Impl.prototype.setRecords = function(records) {
    this._flashObject.setRecords(records);
};

Runway._Impl.prototype.setThemeName = function(themeName) {
    this._flashObject.setThemeName(themeName);
};

Runway._Impl.prototype.getProperty = function(name) {
    return this._flashObject.getProperty(name);
};

Runway._Impl.prototype.setProperty = function(name, value) {
    this._flashObject.setProperty(name, value);
};

Runway._Impl.prototype.select = function(index) {
    this._flashObject.select(index);
};

Runway._Impl.prototype.getSelectedIndex = function() {
    return this._flashObject.getSelectedIndex();
};

Runway._Impl.prototype.getSlideCount = function() {
    return this._flashObject.getSlideCount();
};

Runway._Impl.prototype._onReady = function() {
    if ("onReady" in this._options) {
        this._options["onReady"]();
    }
};
