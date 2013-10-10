<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;
$rightSideColumn = NULL;
$leftCol = NULL;
$middleColumn = NULL;

// $leftCol .= $objSideBar->show();

$middleColumn .= $this->objGeoRssOps->setupMap();
$middleColumn .= '<div id="map"></div>
    <textarea id="gml" style="width:100%" rows="1"></textarea>';

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>
