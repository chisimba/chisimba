/*==================================================
 *  Metaweb Runway API
 *==================================================
 */

(function() {
    var useLocalResources = false;
    
    if (document.location.search.length > 0) {
        var params = document.location.search.substr(1).split("&");
        for (var i = 0; i < params.length; i++) {
            if (params[i] == "runway-use-local-resources") {
                useLocalResources = true;
            }
        }
    }
    
    var loadMe = function() {
        if (typeof window.Runway != "undefined") {
            return;
        }
    
        window.Runway = {
            loaded:     false,
            params:     { bundle: false /*!useLocalResources*/ },
            importers:  {},
            locales:    [ "en" ]
        };
    
        var javascriptFiles = [
            "runway.js",
            "dispatcher.js",
            "flash.js"
        ];
        var cssFiles = [
            "runway.css"
        ];
        
        var defaultClientLocales = ("language" in navigator ? navigator.language : navigator.browserLanguage).split(";");
        for (var l = 0; l < defaultClientLocales.length; l++) {
            var locale = defaultClientLocales[l];
            if (locale != "en") {
                var segments = locale.split("-");
                if (segments.length > 1 && segments[0] != "en") {
                    Runway.locales.push(segments[0]);
                }
                Runway.locales.push(locale);
            }
        }

        var paramTypes = { bundle:Boolean, js:Array, css:Array };
        if (typeof Runway_urlPrefix == "string") {
            Runway.urlPrefix = Runway_urlPrefix;
            if ("Runway_parameters" in window) {
                SimileAjax.parseURLParameters(Runway_parameters,
                                              Runway.params,
                                              paramTypes);
            }
        } else {
            var url = SimileAjax.findScript(document, "/runway-api.js");
            if (url == null) {
                Runway.error = new Error("Failed to derive URL prefix for Runway API code files");
                return;
            }
            Runway.urlPrefix = url.substr(0, url.indexOf("runway-api.js"));
        
            SimileAjax.parseURLParameters(url, Runway.params, paramTypes);
        }
        
        if (useLocalResources) {
            Runway.urlPrefix = "http://127.0.0.1:9191/runway/api/";
        }

        if (Runway.params.locale) { // ISO-639 language codes,
            // optional ISO-3166 country codes (2 characters)
            if (Runway.params.locale != "en") {
                var segments = Runway.params.locale.split("-");
                if (segments.length > 1 && segments[0] != "en") {
                    Runway.locales.push(segments[0]);
                }
                Runway.locales.push(Runway.params.locale);
            }
        }

        var scriptURLs = Runway.params.js || [];
        var cssURLs = Runway.params.css || [];
                
        /*
         *  Core scripts and styles
         */
        if (Runway.params.bundle) {
            scriptURLs.push(Runway.urlPrefix + "runway-bundle.js");
            cssURLs.push(Runway.urlPrefix + "runway-bundle.css");
        } else {
            SimileAjax.prefixURLs(scriptURLs, Runway.urlPrefix + "scripts/", javascriptFiles);
            SimileAjax.prefixURLs(cssURLs, Runway.urlPrefix + "styles/", cssFiles);
        }
        
        /*
         *  Localization
         */
        for (var i = 0; i < Runway.locales.length; i++) {
            scriptURLs.push(Runway.urlPrefix + "locales/" + Runway.locales[i] + "/locale.js");
        };
        
        if (Runway.params.callback) {
            window.SimileAjax_onLoad = function() {
                eval(Runway.params.callback + "()");
            }
        }

        SimileAjax.includeJavascriptFiles(document, "", scriptURLs);
        SimileAjax.includeCssFiles(document, "", cssURLs);
        Runway.loaded = true;
    };

    /*
     *  Load SimileAjax if it's not already loaded
     */
    if (typeof SimileAjax == "undefined") {
        window.SimileAjax_onLoad = loadMe;
        
        var url = useLocalResources ?
            "http://127.0.0.1:8888/ajax/api/simile-ajax-api.js?bundle=false" :
            "http://api.simile-widgets.org/ajax/2.2.1/simile-ajax-api.js";
            
        var createScriptElement = function() {
            var script = document.createElement("script");
            script.type = "text/javascript";
            script.language = "JavaScript";
            script.src = url;
            document.getElementsByTagName("head")[0].appendChild(script);
        }
        if (document.body == null) {
            try {
                document.write("<script src='" + url + "' type='text/javascript'></script>");
            } catch (e) {
                createScriptElement();
            }
        } else {
            createScriptElement();
        }
    } else {
        loadMe();
    }
})();
