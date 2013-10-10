<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
$this->objCollOps    = $this->getObject('sahriscollectionsops');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_collectionsman_uploadcsv', 'sahriscollectionsman');
$header->type = 1;

$middleColumn .= $header->show();

$middleColumn .= $uploadform;
$leftColumn .= $this->leftMenu->show();
$leftColumn .= $this->objCollOps->menuBox();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
