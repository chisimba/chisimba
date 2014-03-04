<?php

$DEBUG_LOG_DATA = "";

function DBG_VERBOSE() {
   return array_key_exists('SHELL', $_ENV);
}

function WARN($message) { DPRT($message); } // Figure something later

function DPRT($message) {
    global $DEBUG_LOG_DATA;
    if (DBG_VERBOSE() ) {
        // print rtrim($message)."\n";
    }
    error_log($message."\n");
    $DEBUG_LOG_DATA = $DEBUG_LOG_DATA . rtrim($message) . "\n";
}

function DPRTR($obj) {
    global $DEBUG_LOG_DATA;
    $message = print_r($obj, TRUE);
    DPRT($message);
}

function clearDebugLog() {
    global $DEBUG_LOG_DATA;
    $DEBUG_LOG_DATA = "";
}

function getDebugLog() {
    global $DEBUG_LOG_DATA;
    return $DEBUG_LOG_DATA;
}

function getDebugLogHTML() {
    global $DEBUG_LOG_DATA;
    return str_replace(array("<","\n"),array("&lt;","<br>\n"),$DEBUG_LOG_DATA);
}
// Return string suitable for putting in a <pre>
function getDebugLogPRE() {
    global $DEBUG_LOG_DATA;
    return str_replace("<","&lt;",$DEBUG_LOG_DATA);
}

function getDebugLogXML() {
    global $DEBUG_LOG_DATA;
    $x = str_replace("<","-lt-",$DEBUG_LOG_DATA);
    return str_replace("&","-amp-",$x);
}

function dumpDebugLog() {
/*
    global $DEBUG_LOG_DATA;
    $handle = fopen("cloudlog.txt", 'a');
    if ( $handle ) {
        fwrite($handle, date('r')."-------------\n".$DEBUG_LOG_DATA);
        fclose($handle);
    }
*/
}

?>
