<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS SIC
*/

/**
* Add Edit group list template for the LRS SIC
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the textarea class
$this->loadClass('textarea', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the tabbed box class
$this->loadClass('tabbedbox', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//Load the dropdown class
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');

$scriptRule = '<script type="text/javascript" src="modules/lrsadmin/resources/addRule.js"></script>';
//add to header
$this->appendArrayVar('headerParams', $scriptRule);

if(isset($sicSubGroupId))
{
	$objAddEditSubGroupForm = new form('lrssic', $this->uri(array('action'=>'editsubgroup', 'sicSubGroupId'=>$sicSubGroupId, 'selected'=>'init_10')));
}
else
{
	$objAddEditSubGroupForm = new form('lrssic', $this->uri(array('action'=>'addsubgroup', 'selected'=>'init_10')));
}

$description = $this->objLanguage->languageText('word_description');
$code = $this->objLanguage->languageText('word_code');
$notes = $this->objLanguage->languageText('word_notes');
$msgDesc = $this->objLanguage->languageText('mod_lrssic_desc_rule_majordiv', 'award');
$msgCode = $this->objLanguage->languageText('mod_lrssic_code_rule_majordiv', 'award');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
if (isset($sicSubGroupId)){
	$header->str = $this->objLanguage->languageText('mod_lrssic_edit_subgroup', 'award');
	$objAddEditSubGroupForm->addToForm($header->show());
}
else 
{
	$header->str = $this->objLanguage->languageText('mod_lrssic_add_subgroup', 'award');
	$objAddEditSubGroupForm->addToForm($header->show());
}


$objaddeditTable = new htmlTable('lrsic');
$objaddeditTable->cellspacing = 2;
$objaddeditTable->cellpadding = '2';
$objaddeditTable->width = '90%';

$objaddeditHeadTable = new htmlTable('lrsic');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrssic_tbl_head_subgroup', 'award')."</i>");
$objaddeditHeadTable->addCell(" ");
$objaddeditHeadTable->endRow();

if(isset($sicSubGroupId))
{
	$valueRow = $this->objDbSicSubGroups->getRow('id', $sicSubGroupId);
	$setDesc = $valueRow['description'];
	$setCode = $valueRow['code'];
	$setNote = $valueRow['notes'];
}
else
{
	$setDesc = '';
	$setCode = '';
	$setNote = '';
}

$txtDesc = new textarea('description', $setDesc);
$txtCode = new textinput('code', $setCode);
$txtNote = new textarea('notes', $setNote);

$objaddeditTable->startRow();
$objaddeditTable->addCell($description. ':', NULL, 'top', NULL, 'odd');
$objaddeditTable->addCell($txtDesc->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($code. ':', NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtCode->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($notes. ':', NULL, 'top', NULL, 'odd');
$objaddeditTable->addCell($txtNote->show());
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
//$btnSubmit->setOnClick("javascript:if((validate_form('input_description', '$msgDesc')) && (validate_form('input_code', '$msgCode'))) {document.getElementById('form_lrssic').submit()}");
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'selectsicsubgroup', 'sicGroupId'=>$sicGroupId, 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

//$majDivId = $this->objDbSicDivs->getRow('id', $sicDivId);
$txtHidden = new textinput('sicGroupId', $sicGroupId, 'hidden');

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddeditTable->addCell($txtHidden->show());
$objaddeditTable->endRow();

$objAddEditSubGroupForm->addRule('description',$this->objLanguage->languageText('mod_lrssic_desc_rule_majordiv','award'),'required');
$objAddEditSubGroupForm->addRule('code',$this->objLanguage->languageText('mod_lrssic_code_rule_majordiv','award'),'required');
$objAddEditSubGroupForm->addToForm($objaddeditHeadTable->show());
$objAddEditSubGroupForm->addToForm($objaddeditTable->show());

echo $objAddEditSubGroupForm->show();
?>