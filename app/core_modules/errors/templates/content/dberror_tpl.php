<?php
$userMenu  = &$this->newObject('usermenu','toolbar');
// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_errors_heading', 'errors');


// Add Post login menu to left column
$leftSideColumn ='';
$leftSideColumn = $userMenu->show();

$midcol = $header->show();

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

$this->href = $this->getObject('href', 'htmlelements');

$devmsg = urldecode($devmsg);
$usrmsg = urldecode($usrmsg);

$blurb = $this->objLanguage->languagetext("mod_errors_blurb", "errors");
$midcol .= $blurb;
$midcol .= "<br /><br />";
$midcol .= $devmsg;
$midcol .= $usrmsg;
$cssLayout->setMiddleColumnContent($midcol);

echo $cssLayout->show();
?>