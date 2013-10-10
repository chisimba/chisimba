<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$userid = $this->objUser->userId();
$this->loadClass ( 'htmlheading', 'htmlelements' );
$objImView = $this->getObject ( 'viewer' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objWashout = $this->getObject ( 'washout', 'utilities' );


$middleColumn = NULL;
$leftColumn = NULL;
$rightColumn = NULL;

$middleColumn .= $objImView->renderTopBoxen();

if(empty($msgs)) {
    $eheader = new htmlHeading ( );
    $eheader->str = $this->objLanguage->languageText ( 'mod_tribe_noresults', 'tribe' );
    $eheader->type = 3;
    $middleColumn .= '<br />' . $eheader->show();
}
else {
    $objImView = $this->getObject ( 'viewer' );
    $view = $objImView->renderOutputForBrowser ( $msgs );
    $middleColumn .= '<br />' . $view;
}

if (! $this->objUser->isLoggedIn ()) {

} else {

}

$leftColumn .= $objImView->renderLeftBoxen();
$rightColumn .= $objImView->renderRightBoxen($userid);

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();