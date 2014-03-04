<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objImView = $this->getObject ( 'viewer' );

$middleColumn = NULL;
$middleColumn .= $objImView->renderTopBoxen();
$leftColumn = NULL;
$rightColumn = NULL;

// Add in a heading
$header = new htmlHeading ( );
$header->str = $this->objLanguage->languageText ( 'mod_tribe_tribe', 'tribe' ) . " " . $this->objUser->fullName ( $this->objUser->userId() );
$header->type = 1;

//$middleColumn .= $header->show();
$middleColumn .= $cloud;

if (! $this->objUser->isLoggedIn ()) {
    //$leftColumn .= $objImView->showUserMenu ();
} else {
    $leftColumn .= $this->leftMenu->show ();
}

$leftColumn .= $objImView->renderLeftBoxen();
$rightColumn .= $objImView->renderRightBoxen($this->objUser->userid());

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setLeftColumnContent ( $rightColumn );
echo $cssLayout->show ();