<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);
$this->loadClass('htmlheading', 'htmlelements');
$objFeatureBox = $this->getObject('featurebox', 'navigation');
        

$leftColumn = NULL;
$middleColumn = NULL;

if($this->objUser->isloggedIn()) {
    // get the sidebar object
    $this->leftMenu = $this->newObject('usermenu', 'toolbar');
    $leftColumn .= $this->leftMenu->show();
}
else {
    $leftColumn .= $this->objReviewOps->showSignInBox();
    $leftColumn .= $this->objReviewOps->showSignUpBox();
}


$middleColumn .= $this->objReviewOps->middleContainer();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
