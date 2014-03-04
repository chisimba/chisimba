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
* Decent work template for the LRS admin
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

//Create form
$objDecentWorkForm = new form('lrsadmin', $this->uri(array('action'=>'decentworkadmin', 'selected'=>'init_10')));

$objIcon = $this->newObject('geticon','htmlelements');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
if (!isset($currentYr)) {
	$currentYr = date('Y');
}
$header->str = $this->objLanguage->languageText('mod_lrs_decent_work_header', 'award').'  '.$currentYr;
$objDecentWorkForm->addToForm($header->show());

$name = $this->objLanguage->languageText('mod_lrs_decent_work_name', 'award');
$value = $this->objLanguage->languageText('mod_lrs_decent_work_value', 'award');
$unit = $this->objLanguage->languageText('mod_lrs_decent_work_unit', 'award');
$source = $this->objLanguage->languageText('mod_lrs_decent_work_source', 'award');
$note = $this->objLanguage->languageText('mod_lrs_decent_work_note', 'award');
$year = $this->objLanguage->languageText('mod_lrs_decent_work_year', 'award');
$delete = $this->objLanguage->languageText('mod_lrs_decent_work_delete', 'award');
$edit = $this->objLanguage->languageText('mod_lrs_decent_work_edit', 'award');

$class = '';

$categoryList = $this->objdecentWorkCategory->getAll("ORDER BY category ASC");
$objCategoryTable = &$this->newObject('htmltable','htmlelements');
$objCategoryHeadTable = &$this->newObject('htmltable','htmlelements');
$content = '';
foreach($categoryList as $catList)
{
	$objCategoryTable->init();
	$objCategoryTable->cellspacing = 2;
	$objCategoryHeadTable->init();
	$objCategoryHeadTable->cellspacing = 2;
	$categoryListHead = ucfirst(strtolower($catList['category']));
	$headerCat = $this->getObject('htmlheading','htmlelements');
	$headerCat->type = 4;
	$headerCat->str = $categoryListHead;
	
	$objCategoryHeadTable->startRow();
	$objCategoryHeadTable->addCell($headerCat->show(), '60%', 'bottom');
	//$objCategoryHeadTable->addCell("<br />");
	$objCategoryHeadTable->addCell($objIcon->getEditIcon($this->uri(array('action'=>'addeditcategory', 'catId'=>$catList['id'], 'selected'=>'init_10'), 'award')).'  '.$objIcon->getDeleteIconWithConfirm($catList['id'],array('action'=>'deletedecentcategory', 'catId'=>$catList['id']),'award'), NULL, 'middle', 'right');
	$objCategoryHeadTable->endRow();
	
	if(isset($currentYr)) {
		$valuesList = $this->objdecentWorkValues->getAll("WHERE categoryid = '{$catList['id']}' AND year = '$currentYr' ORDER BY label ASC");
	} else {
		$valuesList = $this->objdecentWorkValues->getAll("WHERE categoryid = '{$catList['id']}' ORDER BY label ASC");
	}
	
	$objCategoryTable->startHeaderRow('odd');
	$objCategoryTable->addHeaderCell($name, '15%');
	$objCategoryTable->addHeaderCell($value, '8%');
	$objCategoryTable->addHeaderCell($unit, '15%');
	$objCategoryTable->addHeaderCell($source, '8%');
	$objCategoryTable->addHeaderCell($year, '6%');
	$objCategoryTable->addHeaderCell($note, '15%');
	$objCategoryTable->addHeaderCell($edit, '5%');
	$objCategoryTable->addHeaderCell($delete, '5%');
	$objCategoryTable->endHeaderRow();
	if(count($valuesList) > 0)
	{
		foreach($valuesList as $valList)
		{
			$valListLabel = ucfirst(strtolower($valList['label']));
			$valListUnit = ucfirst(strtolower($valList['unit']));
			$valListSource = ucfirst(strtolower($valList['source']));
			
			$class = ($class=='odd')? 'even' : 'odd';
			$objCategoryTable->startRow($class);
			$objCategoryTable->addCell($valListLabel);
			$objCategoryTable->addCell($valList['value']);
			$objCategoryTable->addCell($valListUnit);
			$objCategoryTable->addCell($valListSource);
			$objCategoryTable->addCell($valList['year']);
			$objCategoryTable->addCell($valList['notes']);
			$objCategoryTable->addCell($objIcon->getEditIcon($this->uri(array('action'=>'editadddecentwork', 'valId'=>$valList['id'], 'catId'=>$catList['id'], 'selected'=>'init_10'))));
			$objCategoryTable->addCell($objIcon->getDeleteIconWithConfirm($valList['id'],array('action'=>'deletedecentrow', 'valId'=>$valList['id'], 'selected'=>'init_10'),'award'));
			$objCategoryTable->endRow();
		}
	}
	else
	{
		$message = "<i>".$this->objLanguage->languageText('mod_lrs_decent_work_no_records','award')."</i>";

		$objCategoryTable->startRow();
		$objCategoryTable->addCell($message);
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->addCell("<br />");
		$objCategoryTable->endRow();
	}
	
	$linkadd = new link($this->uri(array('action'=>'editadddecentwork', 'catId'=>$catList['id'], 'selected'=>'init_10'),'award'));
	$linkadd->link = $this->objLanguage->languageText('mod_lrs_add_decent_work_header', 'award');
	
	$objCategoryTable->startRow();
	$objCategoryTable->addCell($linkadd->show());
	$objCategoryTable->addCell("<br />");
	$objCategoryTable->addCell("<br />");
	$objCategoryTable->addCell("<br />");
	$objCategoryTable->addCell("<br />");
	$objCategoryTable->addCell("<br />");
	$objCategoryTable->addCell("<br />");
	$objCategoryTable->endRow();
	
	$content .= $objCategoryHeadTable->show().$objCategoryTable->show();
}

$objExitTable =& $this->newObject('htmltable', 'htmlelements');
$objExitTable->cellspacing = '2';
$objExitTable->cellpadding = '2';

$objyear = new textinput('updateYear', $currentYr, NULL, '4');

$btnUpdateYr = new button('submitYear');
$btnUpdateYr->setToSubmit();
$btnUpdateYr->setValue(' '.$this->objLanguage->languageText("word_update").' ');

$linkAddCat = new link($this->uri(array('action'=>'addeditcategory', 'selected'=>'init_10'), 'award'));
$linkAddCat->link = $this->objLanguage->languageText("mod_lrs_add_category_header", 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

if($categoryList == NULL)
{
	$msg = $this->objLanguage->languageText('mod_lrs_decent_work_add_content', 'award');
	$message = "<span class = 'noRecordsMessage'>$msg</span>";
	
	$objExitTable->startRow();
	$objExitTable->addCell($message);
	$objExitTable->addCell("<br />");
	$objExitTable->endRow();
}

$objExitTable->startRow();
$objExitTable->addCell("<br />");
$objExitTable->addCell("<br />");
$objExitTable->endRow();

$objExitTable->startRow();
$objExitTable->addCell("<i>".$this->objLanguage->languageText('mod_lrs_decent_work_select_year', 'award')."</i>");
$objExitTable->addCell("<br />");
$objExitTable->endRow();

$objExitTable->startRow();
$objExitTable->addCell($objyear->show().'  '.$btnUpdateYr->show());
$objExitTable->addCell("<br />");
$objExitTable->endRow();

$objExitTable->startRow();
$objExitTable->addCell("<br />");
$objExitTable->addCell("<br />");
$objExitTable->endRow();

$objExitTable->startRow();
$objExitTable->addCell($linkAddCat->show().' / '.$linkExit->show());
$objExitTable->endRow();

$objDecentWorkForm->addToForm($content);
$objDecentWorkForm->addToForm($objExitTable->show());

echo $objDecentWorkForm->show();
?>