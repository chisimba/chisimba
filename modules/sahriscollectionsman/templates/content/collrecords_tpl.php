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
$header->str = $this->objLanguage->languageText('mod_sahriscollectionsman_collectionrecsheader', 'sahriscollectionsman');
$header->type = 1;

// $middleColumn .= $header->show();

$objPagination = $this->newObject ( 'pagination', 'navigation' );
$objPagination->module = 'sahriscollectionsman';
$objPagination->action = 'viewrecsajax&collid='.$collid;
$objPagination->id = 'sahriscollectionsman';
$objPagination->numPageLinks = $pages;
$objPagination->currentPage = $pages - 1;

$middleColumn .= $header->show () . '<br/>' . $objPagination->show ();

// $middleColumn .= $records;

$leftColumn .= $this->leftMenu->show();
$leftColumn .= $this->objCollOps->menuBox();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);
echo $cssLayout->show();
