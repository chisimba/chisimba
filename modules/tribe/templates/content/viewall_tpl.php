<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$this->leftMenu = $this->newObject ( 'usermenu', 'toolbar' );
$this->loadClass ( 'htmlheading', 'htmlelements' );
$objImView = $this->getObject ( 'viewer' );
$this->objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
$objWashout = $this->getObject ( 'washout', 'utilities' );

$this->objDia = $this->getObject('jqdialogue', 'jquery');
if(!isset($groupfail) || empty($groupfail)) {
    $groupfail = NULL;
    $message = NULL;
}

if($groupfail == 'TRUE') {
    $this->objDia->setTitle('Error!');
    $this->objDia->setContent($message);
    echo $this->objDia->show();
}
if($groupfail == 'FALSE') {
    $this->objDia->setTitle('Success!');
    $this->objDia->setContent($message);
    echo $this->objDia->show();
}

$middleColumn = NULL;
$middleColumn .= $objImView->renderTopBoxen();

$leftColumn = NULL;
$rightColumn = NULL;

$objPagination = $this->newObject ( 'pagination', 'navigation' );
$objPagination->module = 'tribe';
$objPagination->action = 'viewallajax';
$objPagination->id = 'tribe';
$objPagination->numPageLinks = $pages;
$objPagination->currentPage = $pages - 1;

$middleColumn .= '<br/>' . $objPagination->show ();
//$middleColumn .= $objImView->renderOutputForBrowser($msgs);
$userid = $this->objUser->userid();
if (! $this->objUser->isLoggedIn ()) {

   // $leftColumn .= $objImView->showUserMenu ();

} else {
    //$leftColumn .= $this->leftMenu->show ();
}

$leftColumn .= $objImView->renderLeftBoxen();
$rightColumn .= $objImView->renderRightBoxen($userid);

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();
