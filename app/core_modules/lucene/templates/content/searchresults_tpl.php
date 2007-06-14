<?php
//search results template
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
// Set columns to 3
$cssLayout->setNumColumns(2);
$leftCol = NULL;
$rightSideColumn = NULL;
$middleColumn = NULL;

$leftMenu = $this->newObject('usermenu', 'toolbar');
// get all the left column blocks
$this->objUser = $this->getObject('user', 'security');
if($this->objUser->isLoggedIn())
{
	$leftCol .= $leftMenu->show();
}
else {
	$objLogin = &$this->getObject('logininterface', 'security');
    $objRegister = $this->getObject('block_register', 'security');
    $objFeatureBox = $this->getObject('featurebox', 'navigation');
    $leftCol .= $objFeatureBox->show($this->objLanguage->languageText("word_login", "system") , $objLogin->renderLoginBox('lucene') . "<br />" . $objRegister->show());
}

$middleColumn = NULL;
$middleColumn .= $searchResults;
//dump the cssLayout to screen
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
//$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>