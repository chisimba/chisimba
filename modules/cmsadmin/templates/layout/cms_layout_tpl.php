<?php

//Working on blocks for CMSAdmin so that users can personalize it (WebParts will adopt this).

/*$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objBlocks =  $this->newObject('blocks', 'blocks');
$objLucene =  $this->newObject('searchresults', 'search');
$objModule =  $this->newObject('modules', 'modulecatalogue');
$objLink =  $this->newObject('link', 'htmlelements');
$objTreeMenu = $this->newObject('cmstree', 'cmsadmin');
$objUser = $this->newObject('user', 'security');
$objLanguage = $this->newObject('language', 'language');
$objArticleBox = $this->newObject('articlebox', 'cmsadmin');
$objDbBlocks = $this->newObject('dbblocks', 'cmsadmin');*/
$cssLayout = $this->getObject('csslayout', 'htmlelements');

/*****************LEFT SIDE ***************************************/

// Navigation
$leftSide = $this->_objUtils->getNav();


/***************** END OF LEFT SIDE *******************************/

if(!$this->getParam('query') == ''){

    $searchResults = $objLucene->displaySearchResults($this->getParam('query'), 'cms');
} else {
    $searchResults = '';
}

/***************** Right Side Content *******************************/

/*
$hasBlocks = FALSE;
$rightSide = '';

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




if($hasBlocks){
    $cssLayout->setNumColumns(3);
    $cssLayout->setRightColumnContent($rightSide);
} else {
    $cssLayout->setNumColumns(2);
}
*/

$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($leftSide);
$cssLayout->setMiddleColumnContent($this->getContent().'<br />'.$searchResults);

echo $cssLayout->show();

$this->setVar('footerStr', $this->footerStr);