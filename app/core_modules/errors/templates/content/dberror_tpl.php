<?php
$objFeatureBox = $this->newObject('featurebox', 'navigation');
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
$devmsg = nl2br($devmsg);
$usrmsg = nl2br($usrmsg);

$blurb = $this->objLanguage->languagetext("mod_errors_blurb", "errors");
//$midcol .= $blurb;
$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_usrtitle", "errors"), $usrmsg);//'<div class="featurebox">' . nl2br($usrmsg) . '</div>';
$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_devtitle", "errors"), $devmsg);//'<div class="featurebox">' . nl2br($devmsg) . '</div>';

$cssLayout->setMiddleColumnContent($midcol);

echo $cssLayout->show();
?>