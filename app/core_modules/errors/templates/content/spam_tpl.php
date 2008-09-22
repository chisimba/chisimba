<?php
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$userMenu  = &$this->newObject('usermenu','toolbar');
$objUser  = $this->getObject('user','security');
$link = $this->loadClass('href', 'htmlelements');

// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_errors_heading', 'errors');


// Add Post login menu to left column
$leftSideColumn ='';
if($objUser->isLoggedIn())
{
	$leftSideColumn = $userMenu->show();
}
else {
	$linkhome = new href($this->objConfig->getSiteRoot(), $this->objLanguage->languageText("word_home", 'system', 'Home'));
	$leftSideColumn = $linkhome->show();
}


$midcol = $header->show();

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

$midcol .= $objFeatureBox->show($this->objLanguage->languageText("word_error", "system"), $this->objLanguage->languagetext("mod_errors_spammeralert", "errors"));

$cssLayout->setMiddleColumnContent($midcol);

echo $cssLayout->show();
?>