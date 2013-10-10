<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$this->objOps       = $this->getObject('eventsops');

$middleColumn = NULL;
$leftColumn = NULL;
$rightColumn = NULL;

$middleColumn .= $this->objOps->locationHeader();
$middleColumn .= $this->objOps->geoLocationForm();

$leftColumn .= $this->objOps->browseEventsBox();
$rightColumn .= $this->objOps->showWelcomeBox();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();