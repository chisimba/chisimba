<?php
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$userMenu  = &$this->newObject('usermenu','toolbar');

// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_errors_taheader', 'errors');


// Add Post login menu to left column
$leftSideColumn ='';
$leftSideColumn = $userMenu->show();

$midcol = $header->show();

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

$midcol .= $objFeatureBox->show($this->objLanguage->languageText("mod_errors_mail", "errors"), $this->objLanguage->languagetext("mod_errors_tamail", "errors"));

$cssLayout->setMiddleColumnContent($midcol);

echo $cssLayout->show();