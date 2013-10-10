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
$header->str = $this->objLanguage->languageText('mod_collectionsman_viewresheader', 'sahriscollectionsman');
$header->type = 1;

$middleColumn .= $header->show();
if(!isset($res) || empty($res)) {
    $middleColumn .= $this->objLanguage->languageText("mod_sahriscollectionsman_noresults", "sahriscollectionsman");
    $middleColumn .= $this->objCollOps->searchForm();
}
else {
    $middleColumn .= $this->objCollOps->searchForm();
    $middleColumn .= $this->objCollOps->formatSearchResults($res);
}
$leftColumn .= $this->leftMenu->show();
$leftColumn .= $this->objCollOps->menuBox();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
