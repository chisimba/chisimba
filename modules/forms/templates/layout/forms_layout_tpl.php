<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objBlocks =  $this->newObject('blocks', 'blocks');
$objLucene =  $this->newObject('searchresults', 'search');
$objModule =  $this->newObject('modules', 'modulecatalogue');
$objLink =  $this->newObject('link', 'htmlelements');
$objTreeMenu = $this->newObject('cmstree', 'cmsadmin');
$objUser = $this->newObject('user', 'security');
$objLanguage = $this->newObject('language', 'language');
$objArticleBox = $this->newObject('articlebox', 'cmsadmin');
$objDbBlocks = $this->newObject('dbblocks', 'cmsadmin');

/*****************LEFT SIDE ***************************************/

// Navigation
$leftSide = '';

// Add blocks

$currentAction = $this->getParam('action', NULL);

switch($currentAction){
    case 'showsection':
        $sectionId = $this->getParam('id');
        $pageBlocks = $objDbBlocks->getBlocksForSection($sectionId);
        $leftPageBlocks = $objDbBlocks->getBlocksForSection($sectionId, 1);
        break;
    
    case 'showcontent':
    case 'showfulltext':
        $sectionId = $this->getParam('sectionid');
        $pageId = $this->getParam('id');
        $leftPageBlocks = $objDbBlocks->getBlocksForPage($pageId, $sectionId, 1);
        $pageBlocks = $objDbBlocks->getBlocksForPage($pageId, $sectionId);
        break;
    
    case 'home':
    case '':
        $leftPageBlocks = $objDbBlocks->getBlocksForFrontPage(1);
        $pageBlocks = $objDbBlocks->getBlocksForFrontPage();
        break;
}

// Add left blocks    
if(!empty($leftPageBlocks)) {
    foreach($leftPageBlocks as $pbks) {
        $blockId = $pbks['blockid'];
        $blockToShow = $objDbBlocks->getBlock($blockId);

        $leftSide .= $objBlocks->showBlock($blockToShow['blockname'], $blockToShow['moduleid']);
    }
}

/***************** END OF LEFT SIDE *******************************/

if(!$this->getParam('query') == ''){

    $searchResults = $objLucene->displaySearchResults($this->getParam('query'), 'cms');
} else {
    $searchResults = '';
}

/***************** Right Side Content *******************************/

$hasBlocks = FALSE;
$rightSide = '';

/*
// Add right blocks    
if(!empty($pageBlocks)) {
    $hasBlocks = TRUE;
    foreach($pageBlocks as $pbks) {
        $blockId = $pbks['blockid'];
        $blockToShow = $objDbBlocks->getBlock($blockId);

        $rightSide .= $objBlocks->showBlock($blockToShow['blockname'], $blockToShow['moduleid']);
    }
}
if ($objModule) {
	
}
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');
if($hasBlocks){
    $cssLayout->setNumColumns(3);
    $cssLayout->setRightColumnContent($rightSide);
} else {
    $cssLayout->setNumColumns(2);
}

$objQuery = $this->getObject('jquery', 'jquery');
$objQuery->loadSimpleTreePlugin();

$leftColumn = $this->getVar('leftContent');

$cssLayout->setLeftColumnContent($leftColumn.'<br />');

$middleColumn = $this->getVar('middleContent');

$cssLayout->setMiddleColumnContent($middleColumn);

echo $cssLayout->show();

$this->setVar('footerStr', $this->footerStr);
