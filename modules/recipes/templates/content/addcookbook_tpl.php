<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');
    
if(isset($editparams) && !empty($editparams)) {
    $editparams = $editparams[0];
}
else {
    $editparams = NULL;
}
    
$middleColumn = NULL;
$leftColumn = NULL;
$leftColumn = $this->leftMenu->show();

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_recipes_welcome', 'recipes');
$header->type = 1;

$middleColumn .= $header->show();
$middleColumn .= $this->objOps->addCookbookForm($editparams);

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
