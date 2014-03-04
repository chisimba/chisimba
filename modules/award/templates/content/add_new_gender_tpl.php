<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS benefit types
*/

/**
* Add Edit benefit name list template for the LRS benefit types
* Author Brent van Rensburg
*/

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


$objAddGenderForm = new form('lrsadmin', $this->uri(array('action'=>'savenewgenderrow', 'selected'=>'init_10')));

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_add_new_gender_header', 'award');
$objAddGenderForm->addToForm($header->show());

$objaddeditTable = new htmlTable('lrsadmin');
$objaddeditTable->cellspacing = 2;
$objaddeditTable->width = '70%';

$objaddeditHeadTable = new htmlTable('lrsgend');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$message = '';
$error = $this->getParam('error');
if(isset($error))
{
	$message = "<span class = 'error'>".$this->getParam('message')."</span>";

	$objaddeditHeadTable->startRow();
	$objaddeditHeadTable->addCell($message);
	$objaddeditHeadTable->addCell("<br />");
	$objaddeditHeadTable->endRow();
}

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrs_add_new_gender_table_header', 'award')."</i>");
$objaddeditHeadTable->addCell(" ");
$objaddeditHeadTable->endRow();

$category = new textinput('category');

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_new_gender_category', 'award'), '30%', NULL, NULL, 'odd');
$objaddeditTable->addCell($category->show());
$objaddeditTable->endRow();

$benefitType = new textinput('benefitType');

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_new_gender_benefittype', 'award'), '30%', NULL, NULL, 'odd');
$objaddeditTable->addCell($benefitType->show());
$objaddeditTable->endRow();

$objbenefitName = new dropdown('benefitName');
//$objbenefitName->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one'));

$tblgenderNameIds = $this->objBCEA->getAll();
foreach ($tblgenderNameIds as $id) 
{
	$genderName = $this->objBenefitNames->getRow('id', $id['nameid']);
	$objbenefitName->addOption($genderName['id'],$genderName['name']);
}

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_new_gender_benefit', 'award'), NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($objbenefitName->show());
$objaddeditTable->endRow();

$txtBCEA = new textinput('bcea');

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_new_gender_bcea', 'award'), '30%', NULL, NULL, 'odd');
$objaddeditTable->addCell($txtBCEA->show());
$objaddeditTable->endRow();

$txtComment = new textinput('comment');

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_new_gender_comment', 'award'), NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtComment->show());
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewgender', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddeditTable->startRow();
$objaddeditTable->addCell("<br />");
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

//Add validation here
$objAddGenderForm->addRule('category', $this->objLanguage->languageText('mod_lrs_gender_catrequired', 'award'), 'required');
$objAddGenderForm->addRule('type', $this->objLanguage->languageText('mod_lrs_gender_typerequired', 'award'), 'required');
$objAddGenderForm->addRule('name', $this->objLanguage->languageText('mod_lrs_gender_benrequired', 'award'), 'required');
$objAddGenderForm->addRule('bcea', $this->objLanguage->languageText('mod_lrs_gender_bcearequired', 'award'), 'required');//, 'award'

$objAddGenderForm->addToForm($objaddeditHeadTable->show());
$objAddGenderForm->addToForm($objaddeditTable->show());

echo $objAddGenderForm->show();
?>