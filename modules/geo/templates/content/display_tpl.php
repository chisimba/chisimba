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
$path = $this->objOps->makeMapMarkers($choices, $lat, $lon);

$middleColumn .= $this->objOps->makeGMap($lat, $lon, $path, $zoom);
$radius = 5;

$middleColumn .= $this->objOps->placeSearchForm();
//$rightSideColumn = $this->objOps->addPlaceForm();
//$objWikipedia = $this->objOps->getWikipedia($lon, $lat, $radius);
// parse wikipedia data
//$wik = $this->objMongo->mongoWikipedia($objWikipedia);
//var_dump($wik);

//$objFlickr    = $this->objOps->getFlickr($lon, $lat, $radius);
// parse Flickr data
//$this->objMongo->mongoFlickr($objFlickr);
                
$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
//$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>
