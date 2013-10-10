<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->cssClass = 'error';
$header->str = $this->objLanguage->code2Txt('mod_context_unabletoentercontext', 'userextra', NULL, 'Unable to launch import facility');
$content=="";
$content.= $header->show();

$content.= '<p>'.$this->objLanguage->code2Txt('mod_context_unabletoenterinfo', 'userextra', NULL, 'It appears you have not joined the course, or been logged out of the course.').'</p>';



$str .= '<div id="browsecontextcontent"></div>';

$str .= $this->getJavaScriptFile('contextbrowser.js');

$content.= $str;



$link = new link ($this->uri(NULL, '_default'));
$link->link = $this->objLanguage->languageText('phrase_backhome', 'system', 'Back to home');

$content.= '<p><br />'.$link->show().'</p>';
// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent( $postLoginMenu->show());

// Add Right Column
$cssLayout->setMiddleColumnContent( $content);

//Output the content to the page
echo $cssLayout->show();


?>
?>