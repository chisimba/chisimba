<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objSF = $this->getObject('sisforms');
// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;
$leftCol = NULL;
$middleColumn = NULL;
if(isset($message)) {
    $tomsg = $this->getObject('timeoutmessage', 'htmlelements');
    $tomsg->message = $message;
    $middleColumn .=  $tomsg->show()."<br />";
}
$rightSideColumn = $objSF->parentMenu(TRUE);

$leftCol .= $objSideBar->show();
$middleColumn .=  $this->objUser->userName();

$middleColumn .= $objSF->profileForm($this->objUser->userName(), FALSE);

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>
