<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 2 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$objImView = $this->getObject ( 'jbviewer' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objWashout = $this->getObject ( 'washout', 'utilities' );
//$this->objImOps = $this->getObject('imops', 'im');


$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading ( );
$header->str = $this->objLanguage->languageText ( 'mod_jabberblog_jabberblogof', 'jabberblog' ) . " " . $this->objUser->fullName ( $this->jposteruid );
$header->type = 1;

if(empty($msgs)) {
    $eheader = new htmlHeading ( );
    $eheader->str = $this->objLanguage->languageText ( 'mod_jabberblog_noresults', 'jabberblog' );
    $eheader->type = 3;
    $middleColumn .= $header->show () . '<br />' . $eheader->show();
}
else {
    $objImView = $this->getObject ( 'jbviewer' );
    $view = $objImView->renderOutputForBrowser ( $msgs );
    $middleColumn .= $header->show () . '<br />' . $view;
}

if (! $this->objUser->isLoggedIn ()) {
    $leftColumn .= $objImView->showUserMenu ();
} else {
    $leftColumn .= $this->leftMenu->show ();
}

$leftColumn .= $objImView->renderBoxen();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
echo $cssLayout->show ();