<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;

if(!$this->objUser->isLoggedIn()) {
    $leftColumn .= $this->objOps->showSignInBox();
    $leftColumn .= $this->objOps->showSignUpBox();
    $middleColumn .= "You need to be logged in to add a recipe";
}
else {
    $leftColumn .= $this->leftMenu->show();
    $middleColumn .= $this->objOps->addRecipeForm();
}

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
