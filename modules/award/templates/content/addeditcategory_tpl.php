<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS Admin
*/

/**
* Decent work template for adding and editing the category for the LRS postlogin
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');

$msgTblHead = $this->objLanguage->languageText('mod_lrs_category_tbl_header','award');
$msgCategory = $this->objLanguage->languageText('mod_lrs_category_Rule','award');

//Create form
$objaddeditCatForm = new form('lrsadmin', $this->uri(array('action'=>'editcategory', 'catId'=>$catId, 'selected'=>'init_10')));


$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_edit_category_header','award');
$objaddeditCatForm->addToForm($header->show());
$objaddeditCatTable = new htmlTable('lrsadmin');
$objaddeditCatTable->cellspacing = 2;

if (isset($catId)) {
	$categoryRow = $this->objdecentWorkCategory->getRow('id', $catId);
	$setCategory = $categoryRow['category'];
} else {
	$setCategory = '';
}

$txtCategory = new textinput('category', $setCategory);

$objaddeditCatTable->startRow();
$objaddeditCatTable->addCell($msgTblHead, '30%');
$objaddeditCatTable->addCell($txtCategory->show());
$objaddeditCatTable->endRow();

$objaddeditCatTable->startRow();
$objaddeditCatTable->addCell("<br />");
$objaddeditCatTable->addCell("<br />");
$objaddeditCatTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'decentworkadmin', 'selected'=>'init_10'));
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddeditCatTable->startRow();
$objaddeditCatTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddeditCatTable->addCell("<br />");
$objaddeditCatTable->endRow();

$objaddeditCatForm->addToForm($objaddeditCatTable->show());
$objaddeditCatForm->addRule('category',$msgCategory,'required');

echo $objaddeditCatForm->show();
?>