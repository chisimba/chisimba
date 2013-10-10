<?php

$objModule = $this->newObject('modules', 'modulecatalogue');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objBlocks = $this->newObject('blocks', 'blocks');
$objDbBlocks = $this->newObject('dbblocksdata', 'blocks');

if ($objModule->checkIfRegistered('textblock')) {
    $objTextBlock = $this->newObject('dbtextblock', 'textblock');
}

$objLucene = $this->newObject('searchresults', 'search');
$objLink = $this->newObject('link', 'htmlelements');
$objTreeMenu = $this->newObject('cmstree', 'cmsadmin');
$objUser = $this->newObject('user', 'security');
$objLanguage = $this->newObject('language', 'language');
$objArticleBox = $this->newObject('articlebox', 'cmsadmin');
$objDbBlocks = $this->newObject('dbblocks', 'cmsadmin');

/* * ***************LEFT SIDE ************************************** */

// Navigation
$currentNode = $this->getParam('sectionid', NULL);

if (!isset($rss)) {
    $rss = '';
}

//Content ID if any
$contentId = $this->getParam('id', '');

$leftSide = $this->objLayout->getLeftMenu($currentNode, $rss, $contentId);
$leftSide .= '<div id="cmsleftblockscontainer">';

// Add blocks
$currentAction = $this->getParam('action', NULL);

switch ($currentAction) {
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
if (!empty($leftPageBlocks)) {
    foreach ($leftPageBlocks as $pbks) {
        $blockId = $pbks['blockid'];
        $blockToShow = $objDbBlocks->getBlock($blockId);


        $showToggle = TRUE;
        $showTitle = TRUE;

        $cssId = '';
        $cssClass = 'featurebox';

        //If a textblock is being used then check the show title fields
        if ($objModule->checkIfRegistered('textblock')) {
            $txtBlockArr = $objTextBlock->getBlock($blockId);

            if (isset($txtBlockArr['show_title']) && $txtBlockArr['show_title'] != '1') {
                $showToggle = FALSE;
                $showTitle = FALSE;
            }

            if (isset($txtBlockArr['css_id']) && $txtBlockArr['css_id'] != '') {
                $cssId = $txtBlockArr['css_id'];
            }

            if (isset($txtBlockArr['css_class']) && $txtBlockArr['css_class'] != '') {
                $cssClass = $txtBlockArr['css_class'];
            }
        }

        //TODO: Add support for hiding fields to core block module

        $leftSide .= $objBlocks->showBlock($blockToShow['blockname'], $blockToShow['moduleid'], NULL, 20, TRUE, $showToggle, 'default', $showTitle, $cssClass, $cssId);
    }
}

$leftSide .= '</div>';
/* * *************** END OF LEFT SIDE ****************************** */

if (!$this->getParam('query') == '') {

    $searchResults = $objLucene->displaySearchResults($this->getParam('query'), 'cms');
} else {
    $searchResults = '';
}

/* * *************** Right Side Content ****************************** */

$hasBlocks = FALSE;
$rightSide = '';

// Add right blocks
if (!empty($pageBlocks)) {
    $hasBlocks = TRUE;
    foreach ($pageBlocks as $pbks) {
        $blockId = $pbks['blockid'];
        $blockToShow = $objDbBlocks->getBlock($blockId);

        $showToggle = TRUE;
        $showTitle = TRUE;

        $cssId = '';
        $cssClass = 'featurebox';

        //If a textblock is being used then check the show title fields
        if ($objModule->checkIfRegistered('textblock')) {
            $txtBlockArr = $objTextBlock->getBlock($blockId);

            if (isset($txtBlockArr['show_title']) && $txtBlockArr['show_title'] != '1') {
                $showToggle = FALSE;
                $showTitle = FALSE;
            }

            if (isset($txtBlockArr['css_id']) && $txtBlockArr['css_id'] != '') {
                $cssId = $txtBlockArr['css_id'];
            }

            if (isset($txtBlockArr['css_class']) && $txtBlockArr['css_class'] != '') {
                $cssClass = $txtBlockArr['css_class'];
            }
        }

        //TODO: Add support for hiding fields to core block module

        $rightSide .= $objBlocks->showBlock($blockToShow['blockname'], $blockToShow['moduleid'], NULL, 20, TRUE, $showToggle, 'default', $showTitle, $cssClass, $cssId);
    }
}
if ($objModule) {
    
}

$cssLayout = $this->newObject('csslayout', 'htmlelements');
if ($hasBlocks) {
    $cssLayout->setNumColumns(3);
    $cssLayout->setRightColumnContent($rightSide);
} else {
    $cssLayout->setNumColumns(2);
}
$cssLayout->setLeftColumnContent($leftSide . '<br />');

$cssLayout->setMiddleColumnContent($this->getBreadCrumbs() . $this->getContent() . '<br />' . $searchResults);

echo $cssLayout->show();

$this->setVar('footerStr', $this->footerStr);
