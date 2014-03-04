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
}
else {
    $leftColumn .= $this->leftMenu->show();
}

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_recipes_cookbooklist', 'recipes');
$header->type = 1;

$middleColumn .= $header->show();
$middleColumn .= $this->objOps->listCookbooks($this->objUser->userId());

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
