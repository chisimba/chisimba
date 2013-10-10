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
* Edit gender template for the LRS Admin
* Author Brent van Rensburg
*/

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');

//Set form action
$formAction = $this->uri(array('action'=>'savegenderrow', 'genderId'=>$genderId,'selected'=>'init_10'));
//Create new form object
$objEditGenderRowForm = new form('editgraderow', $formAction);
$objEditGenderRowForm->displayType = '3';

$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrs_gender_edit', 'award');
$objEditGenderRowForm->addToForm($header->show());

//$script = '<script type="text/javascript" src="modules/lrsadmin/resources/addRule.js"></script>';
//$this->appendArrayVar('headerParams', $script);

$genderRow = $this->objBCEA->getRow('id', $genderId);

$txtType = new textinput("type", $genderRow['type']);
$txtBcea = new textinput("bcea", $genderRow['bcea']);
$txtComment = new textarea("comment", $genderRow['comment']);

//$msgType = $this->objLanguage->languageText('mod_lrs_gender_typerequired');
//$msgBenefit = $this->objLanguage->languageText('mod_lrs_gender_benrequired');
//$msgBCEA = $this->objLanguage->languageText('mod_lrs_gender_bcearequired');


$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewgender', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

//Create table for edit form
$objFormTable =& $this->newObject('htmltable', 'htmlelements');
$objFormTable->cellspacing = '2';
$objFormTable->cellpadding = '2';
$objFormTable->width = '90%';

$rep = array('CAT' => $genderRow['category']);

$objaddeditHeadTable = new htmlTable('lrsadmin');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->code2Txt("mod_lrs_gender_tbl_head_genderRow", 'award', $rep)."</i>");
$objaddeditHeadTable->addCell(" ");
$objaddeditHeadTable->endRow();

$objFormTable->startRow();
$objFormTable->addCell($this->objLanguage->languageText('mod_lrs_gender_type_of_benefit', 'award'). ':','','','','odd');
$objFormTable->addCell($txtType->show());
$objFormTable->endRow();

$objbenefitNames = new dropdown('benefit');
//$objbenefitNames->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));

$tblgenderNameIds = $this->objBCEA->getAll();
foreach ($tblgenderNameIds as $id) 
{
	$genderName = $this->objBenefitNames->getRow('id', $id['nameid']);
	$objbenefitNames->addOption($genderName['id'],$genderName['name']);
}
$objbenefitNames->setSelected($genderRow['nameid']);

$objFormTable->startRow();
$objFormTable->addCell($this->objLanguage->languageText('mod_lrs_gender_benefit', 'award'). ':','','top','','odd');
$objFormTable->addCell($objbenefitNames->show());
$objFormTable->endRow();

$objFormTable->startRow();
$objFormTable->addCell($this->objLanguage->languageText('mod_lrs_gender_bcea', 'award'). ':','','','','odd');
$objFormTable->addCell($txtBcea->show());
$objFormTable->endRow();

$objFormTable->startRow();
$objFormTable->addCell($this->objLanguage->languageText('mod_lrs_gender_comment', 'award'). ':','','top','','odd');
$objFormTable->addCell($txtComment->show());
$objFormTable->endRow();

$objSubmit = new button('submit'); 
$objSubmit->setToSubmit(); 
//$objSubmit->setOnClick("javascript:if((validate_form('input_type', '$msgType')) && (validate_dropdown('input_benefit', '$msgBenefit')) && (validate_form('input_bcea', '$msgBCEA'))) {document.getElementById('form_editgraderow').submit()}");
$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_submit") . ' ');

$objFormTable->startRow();
$objFormTable->addCell($objSubmit->show().'  '.$btnCancel->show());
$objFormTable->addCell("<br />");
$objFormTable->endRow();

//Add validation here
$objEditGenderRowForm->addRule('type', $this->objLanguage->languageText('mod_lrs_gender_typerequired', 'award'), 'required');
$objEditGenderRowForm->addRule('benefit', $this->objLanguage->languageText('mod_lrs_gender_benrequired', 'award'), 'required');
$objEditGenderRowForm->addRule('bcea', $this->objLanguage->languageText('mod_lrs_gender_bcearequired', 'award'), 'required');

//Add table to form
$objEditGenderRowForm->addToForm($objaddeditHeadTable->show());
$objEditGenderRowForm->addToForm($objFormTable->show());

echo $objEditGenderRowForm->show();
?>