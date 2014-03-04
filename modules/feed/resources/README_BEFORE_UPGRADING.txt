This version of SimplePie has been forked to support Proxy settings. SimplePie does not do this itself, for some reason. Until it does, it will be necessary to edit the SimplePie file to insert the proxy code. If you upgrade without doing this, proxy settings will not work. 

The lines concerned are:

680-687 in the current version (note that there will be comment blocks added so the line numbers will vary unless I edit this)

    // ----- Added by Derek Keats to support Proxy settings -----------------//
    var $useProxy=FALSE;
    var $useProxyAuth=FALSE;
    var $proxyUrl=FALSE;
    var $proxyPort=FALSE;
    var $proxyUser=FALSE;
    var $proxyPwd=FALSE;
    // ---- End added to support proxy --------------------------------------//


and 7625-7634

    // ------------- Added by DEREK KEATS to support proxy --------------------------//
    if ($this->useProxy) {
        curl_setopt($fp, CURLOPT_PROXY, USE_PROXY);
        curl_setopt($fp, CURLOPT_PROXY, $this->proxyUrl);
        curl_setopt($fp, CURLOPT_PROXYPORT, $this->proxyPort);
        if ($this->useProxyAuth) {
            curl_setopt ($fp, CURLOPT_PROXYUSERPWD, $this->proxyUser . ":" . $this->proxyPwd); 
        }
    }
    // ------------- End of added by DEREK KEATS to support proxy ------------------//

If the version being upgraded to supports proxies, then the wrapper class spie_class_inc.php in the feed module will also need to be modified accordingly.
