<?php
//no action! eek!
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('sidebar', 'navigation');
// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;

$rightSideColumn = NULL;
$middleColumn = NULL;
$leftCol = NULL;

if($this->objUser->isLoggedIn() == FALSE)
{
	$objLogin = & $this->getObject('logininterface', 'security');
	$objFeatureBox = $this->getObject('featurebox', 'navigation');
	$leftCol .= $objFeatureBox->show($this->objLanguage->languageText("word_login", "system"), $objLogin->renderLoginBox());
}
else {
	$leftMenu = &$this->newObject('usermenu', 'toolbar');
	$leftCol .= $leftMenu->show();
}

$middleColumn .= "<h1><em><center>" . $errmsg . "</center></em></h1>";
$middleColumn .= "<br />";

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>