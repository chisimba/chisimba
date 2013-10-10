<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 2 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );

$middleColumn = NULL;
$leftColumn = NULL;
$rightColumn = NULL;

// Add in a heading
$header = new htmlHeading ( );
$header->str = $this->objLanguage->languageText ( 'mod_lifelog_lifelogof', 'lifelog' ) . " " . $this->objUser->fullName ( $this->objUser->userId() );
$header->type = 1;

$middleColumn .= $header->show();
$middleColumn .= $data;

if (! $this->objUser->isLoggedIn ()) {
    //$leftColumn .= $objImView->showUserMenu ();
} else {
    $leftColumn .= $this->leftMenu->show ();
}

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setLeftColumnContent ( $rightColumn );
echo $cssLayout->show ();
