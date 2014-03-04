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
* Add edit Decent work template for the LRS admin
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');

$name = $this->objLanguage->languageText('mod_lrs_decent_work_name', 'award');
$value = $this->objLanguage->languageText('mod_lrs_decent_work_value', 'award');
$unit = $this->objLanguage->languageText('mod_lrs_decent_work_unit', 'award');
$source = $this->objLanguage->languageText('mod_lrs_decent_work_source', 'award');
$note = $this->objLanguage->languageText('mod_lrs_decent_work_note', 'award');
$msgTblHead = $this->objLanguage->languageText('mod_lrs_decent_work_tblhead', 'award');
$msgName = $this->objLanguage->languageText('mod_lrs_decent_work_msgName', 'award');
$msgValue = $this->objLanguage->languageText('mod_lrs_decent_work_msgValue', 'award');
$msgUnit = $this->objLanguage->languageText('mod_lrs_decent_work_msgUnit', 'award');
$msgSource = $this->objLanguage->languageText('mod_lrs_decent_work_msgSource', 'award');
$msgYear = $this->objLanguage->languageText('mod_lrs_decent_work_msgYear', 'award');

$category = $this->objdecentWorkCategory->getRow('id', $catId);

$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
if(isset($valId)) {
	$header->str = $this->objLanguage->languageText('mod_lrs_edit_decent_work_header','award');
} else {
	$header->str = $this->objLanguage->languageText('mod_lrs_add_decent_work_header','award');
}

$objaddeditForm = new form('lrsadmin', $this->uri(array('action'=>'editdecentWork', 'valId'=>$valId, 'catId'=>$catId)));
$objaddeditForm->addToForm($header->show());

$objaddeditTable = new htmlTable('lrsadmin');
$objaddeditTable->cellspacing = 2;

$objaddeditTable->startHeaderRow();
$objaddeditTable->addHeaderCell($msgTblHead. ':');
$objaddeditTable->addHeaderCell(ucwords($category['category']));
$objaddeditTable->endHeaderRow();

if(isset($valId))
{
	$valueRow = $this->objdecentWorkValues->getRow('id', $valId);
	$setName = $valueRow['label'];
	$setValue = $valueRow['value'];
	$setUnit = $valueRow['unit'];
	$setSource = $valueRow['source'];
	$setYear = $valueRow['year'];
	$setNote = $valueRow['notes'];
}
else
{
	$setName = '';
	$setValue = '';
	$setUnit = '';
	$setSource = '';
	$setYear = '';
	$setNote = '';
}

$txtname = new textinput('name', $setName);
$txtValue = new textinput('value', $setValue);
$txtUnit = new textinput('unit', $setUnit);
$txtSource = new textinput('source', $setSource);
$txtYear = new textinput('year', $setYear);
$txtNote = new textarea('note', $setNote);

$objaddeditTable->startRow();
$objaddeditTable->addCell($name. ':', NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtname->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($value. ':', NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtValue->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($unit. ':', NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtUnit->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($source. ':', NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtSource->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_add_category_year','award'), '30%', '','','odd');
$objaddeditTable->addCell($txtYear->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($note. ':', NULL, 'top', NULL, 'odd');
$objaddeditTable->addCell($txtNote->show());
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'decentworkadmin', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().' '.$btnCancel->show());
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$objaddeditForm->addToForm($objaddeditTable->show());
$objaddeditForm->addRule('name',$msgName,'required');
$objaddeditForm->addRule('value',$msgValue,'required');
$objaddeditForm->addRule('unit',$msgUnit,'required');
$objaddeditForm->addRule('source',$msgSource,'required');
$objaddeditForm->addRule('year',$msgYear,'required');


echo $objaddeditForm->show();
?>