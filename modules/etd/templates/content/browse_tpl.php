<?php
/**
* Template for browsing the Categories, authors, and titles.
* @package etd
*/

/**
* Template for browsing the Categories, authors, and titles.
*/

// Page layout
$this->setLayoutTemplate('etd_layout_tpl.php');

if(!isset($num)){
    $num = 2;
}

// View classes
$objViewBrowse = $this->getObject( 'viewbrowse', 'etd' );
$objViewBrowse->create($browseType);
$objViewBrowse->setNumCols($num);
$objViewBrowse->setAccess(FALSE);
$objViewBrowse->useSortTable();


if(isset($pageContentTitle) && !empty($pageContentTitle)){
    $objViewBrowse->setPageTitle($pageContentTitle);
}

// Show
$objLayer = $this->newObject('layer', 'htmlelements');
$objLayer->str = $objViewBrowse->show();
$objLayer->padding = '5px';
echo $objLayer->show();

?>