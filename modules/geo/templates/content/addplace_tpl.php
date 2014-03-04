<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$this->objOps = $this->getObject('geoops');

// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;
$leftCol = NULL;
$middleColumn = NULL;

// check for a message
if(!empty($message))
{
	echo $message;
}
if(!isset($zoom)) {
	$zoom = 14;
}
$leftCol .= $this->objOps->showWelcomeBox();

$middleColumn .= $this->objOps->geoLocationForm($editparams = NULL, $eventform = FALSE);
//$middleColumn .= $this->objOps->addPlaceForm();
                
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
//$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>
