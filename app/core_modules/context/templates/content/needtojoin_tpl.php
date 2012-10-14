<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->cssClass = 'error';
$header->str = $this->objLanguage->code2Txt('mod_context_unabletoentercontext', 'context', NULL, 'Unable to enter [-context-]');

echo $header->show();

echo '<p>'.$this->objLanguage->code2Txt('mod_context_unabletoenterinfo', 'context', NULL, 'The [-context-] you tried to enter either does not exist, or is private with access restricted to members only.').'</p>';


$objNav = $this->getObject('contextadminnav', 'contextadmin');
$str = $this->objLanguage->languageText('word_browse', 'glossary', 'Browse').': '.$objNav->getAlphaListingAjax();

$str .= '<div id="browsecontextcontent"></div>';

$str .= $this->getJavaScriptFile('contextbrowser.js');

echo $str;



$link = new link ($this->uri(NULL, '_default'));
$link->link = $this->objLanguage->languageText('phrase_backhome', 'system', 'Back to home');

echo '<p><br />'.$link->show().'</p>';
?>