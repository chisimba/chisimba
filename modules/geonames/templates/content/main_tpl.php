<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;
$rightSideColumn = '<h2>Search:</h2>'.$this->objGeoOps->searchForm();
$leftCol = NULL;
$middleColumn = NULL;

// check for a message
if(!empty($message))
{
	echo $message;
}
$leftCol .= $objSideBar->show();

$middleColumn .= $this->objGeoOps->uploadDataFile();

//Show as KML -> simplemap

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>