<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

// Set columns to 3
$cssLayout->setNumColumns(2);
$leftMenu = NULL;
$leftCol = NULL;
$middleColumn = NULL;

// check for a message
if(!empty($message))
{
	$middleColumn = $message;
}
$leftCol .= $objSideBar->show();


$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
// $cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>