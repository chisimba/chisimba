<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$middleColumn = NULL;
$leftCol = NULL;

if ($this->objUser->isLoggedIn()) {
	$leftMenu = $this->newObject('usermenu', 'toolbar');
	$leftCol .= $leftMenu->show();
}
//$leftCol .= "sign up here...";
$middleColumn .= $this->objLanguage->languageText("mod_mailmannews_joinerror", "mailmannews"); 
$middleColumn .= $this->objMailmanSignup->subsBox();
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
echo $cssLayout->show();

?>