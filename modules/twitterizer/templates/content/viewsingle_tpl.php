<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 2 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$this->objOps = $this->getObject ( 'tweetops' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objWashout = $this->getObject ( 'washout', 'utilities' );

$this->objDia = $this->getObject('jqdialogue', 'htmlelements');

$middleColumn = NULL;
$middleColumn .= $this->objOps->renderTopBoxen();

$leftColumn = NULL;
$rightColumn = NULL;

$middleColumn .= $this->objOps->renderOutputForBrowser($results);

$leftColumn .= $this->objOps->renderLeftBoxen();
$rightColumn .= $this->objOps->renderRightBoxen();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );

echo $cssLayout->show ();