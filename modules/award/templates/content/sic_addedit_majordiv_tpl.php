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
* Add Edit Major div list template for the LRS SIC
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


//If the user enters a new unit which already exists in the database an error message is displayed
$message = '';
$error = $this->getParam('error');
if(isset($error))
{
	$message = "<span class = 'error'>".$this->getParam('message')."</span>";
	echo $message;
	$majDivId = $this->getParam('majDivId');
}

if(isset($majDivId))
{
	$objAddEditMajorDivForm = new form('lrssic', $this->uri(array('action'=>'editmajdiv', 'majDivId'=>$majDivId, 'selected'=>'init_10')));
}
else
{
	$objAddEditMajorDivForm = new form('lrssic', $this->uri(array('action'=>'addmajdiv', 'selected'=>'init_10')));
}

$description = $this->objLanguage->languageText('word_description');
$code = $this->objLanguage->languageText('word_code');
$notes = $this->objLanguage->languageText('word_notes');
$msgDesc = $this->objLanguage->languageText('mod_lrssic_desc_rule_majordiv', 'award');
$msgCode = $this->objLanguage->languageText('mod_lrssic_code_rule_majordiv', 'award');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
if (isset($majDivId)){
	$header->str = $this->objLanguage->languageText('mod_lrssic_edit_majordiv', 'award');
	$objAddEditMajorDivForm->addToForm($header->show());
}
else 
{
	$header->str = $this->objLanguage->languageText('mod_lrssic_add_majordiv', 'award');
	$objAddEditMajorDivForm->addToForm($header->show());
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
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrssic_tbl_head_majordiv', 'award')."</i>");
$objaddeditHeadTable->addCell(" ");
$objaddeditHeadTable->endRow();

if(isset($majDivId))
{
	$valueRow = $this->objDbSicMajorDivs->getRow('id', $majDivId);
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
$objaddeditTable->addCell($description. ':', '30%', 'top', NULL, 'odd');
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
$location = $this->uri(array('action'=>'selectmajordiv', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$objAddEditMajorDivForm->addRule('description',$this->objLanguage->languageText('mod_lrssic_desc_rule_majordiv','award'),'required');
$objAddEditMajorDivForm->addRule('code',$this->objLanguage->languageText('mod_lrssic_code_rule_majordiv','award'),'required');
$objAddEditMajorDivForm->addToForm($objaddeditHeadTable->show());
$objAddEditMajorDivForm->addToForm($objaddeditTable->show());

echo $objAddEditMajorDivForm->show();
?>