<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$this->objCollOps    = $this->getObject('sahriscollectionsops');

// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');
        
$middleColumn = NULL;
$leftColumn = NULL;

// Add in a heading
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_sahriscollectionsman_objectsreportheader', 'sahriscollectionsman');
$header->type = 1;

$middleColumn .= $header->show();

$middleColumn .= $graph;

$leftColumn .= $this->leftMenu->show();
$leftColumn .= $this->objCollOps->menuBox();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
