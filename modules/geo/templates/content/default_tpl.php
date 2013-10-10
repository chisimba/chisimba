<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

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
//$leftCol .= $objSideBar->show();

$middleColumn .= $this->objOps->getHTML5Loc();

$radius = 5;

//$objWikipedia = $this->objOps->getWikipedia($lon, $lat, $radius);
// parse wikipedia data
//$this->objMongo->mongoWikipedia($objWikipedia);

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
//$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>