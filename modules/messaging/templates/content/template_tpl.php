<?php
/* -------------------- template for messaging module ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package messaging
*/

/**
* Template for the messaging module
* Author Kevin Cyster
* */

// Load scriptaclous since we can no longer guarantee it is there
$scriptaculous = $this->getObject('scriptaculous', 'prototype');
$this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));

// select all checkbox js library
$headerParams = $this->getJavascriptFile('selectall.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);

// sort table js library
$headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);

// set up layout
if($mode == 'iframe'){
    // add messaging module js library
    $headerParams = $this->getJavascriptFile('messaging.js', 'messaging');
    $this->appendArrayVar('headerParams', $headerParams);

    $this->setVar('pageSuppressBanner', TRUE);
    $this->setVar('pageSuppressContainer', TRUE);
    $this->setVar('pageSuppressSearch', TRUE);
    $this->setVar('pageSuppressToolbar', TRUE);
    $this->setVar('suppressFooter', TRUE);
    $this->setVar('bodyParams', 'onload="javascript:jsHideLoading();"');
}elseif($mode == 'popup'){
    // add messaging module js library
    $headerParams = $this->getJavascriptFile('messaging.js', 'messaging');
    $this->appendArrayVar('headerParams', $headerParams);

    // add x js library (cross browser library)
    $headerParams = $this->getJavascriptFile('x.js', 'htmlelements');
    $this->appendArrayVar('headerParams', $headerParams);

    $this->setVar('pageSuppressBanner', TRUE);
    $this->setVar('pageSuppressContainer', TRUE);
    $this->setVar('pageSuppressSearch', TRUE);
    $this->setVar('pageSuppressToolbar', TRUE);
    $this->setVar('suppressFooter', TRUE);
}elseif($mode == 'textroom'){
    // add messaging module js library
    $headerParams = $this->getJavascriptFile('messaging.js', 'messaging');
    $this->appendArrayVar('headerParams', $headerParams);

    $this->setVar('pageSuppressSearch', TRUE);
    $this->setVar('pageSuppressToolbar', TRUE);
    $this->setVar('footerStr', '');
    $this->setLayoutTemplate('room_text_only_tpl.php');
    $this->setVar('bodyParams', 'onload="javascript:jsOnloadChat(\'standard\');" onunload="clearTimeout(chatTimer);"');
}elseif($mode == 'room'){
    // add messaging module js library
    $headerParams = $this->getJavascriptFile('messaging.js', 'messaging');
    $this->appendArrayVar('headerParams', $headerParams);

    $this->setVar('pageSuppressSearch', TRUE);
    $this->setVar('pageSuppressToolbar', TRUE);
    $this->setVar('footerStr', '');
    $this->setLayoutTemplate('room_tpl.php');
    $this->setVar('bodyParams', 'onload="javascript:jsOnloadChat(\'standard\');" onunload="clearTimeout(chatTimer);clearTimeout(userTimer);"');
}else{
    $this->setLayoutTemplate('layout_tpl.php');
}

echo $templateContent;
?>