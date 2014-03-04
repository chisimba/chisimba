<?php
$userMenu  = $this->newObject('usermenu','toolbar');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
// Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

// Add Post login menu to left column
$leftSideColumn ='';
$leftSideColumn = $userMenu->show();

$middleColumn = NULL;

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_beautifier_heading', 'beautifier');

$middleColumn = $header->show();

$themod = $this->getParam('mod');

$intro = $this->objLanguage->languageText('mod_beautifier_beautifying', 'beautifier');
$patience = $this->objLanguage->languageText('mod_beautifier_patience', 'beautifier');
$tail = $this->objLanguage->languageText('mod_beautifier_finished', 'beautifier');
$complete = $this->objLanguage->languageText('mod_beautifier_completemess', 'beautifier');

$middleColumn .=  $intro . "  <strong>" . $themod . "</strong> " . $patience . "  ... " . $tail;
$middleColumn .= "<br />";
$middleColumn .=  $complete;

//add left column
$cssLayout->setLeftColumnContent($leftSideColumn);
//add middle column
$cssLayout->setMiddleColumnContent($middleColumn);
echo $cssLayout->show();
?>