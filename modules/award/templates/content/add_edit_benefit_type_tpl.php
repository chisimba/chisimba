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
* Add Edit benefit type list template for the LRS benefit types
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

$objAddEditTypeForm = new form('lrsbenefittypes', $this->uri(array('action'=>'savebenefittype', 'selected'=>'init_10')));

$name = $this->objLanguage->languageText('phrase_benefit_name');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = (isset($benefitTypeId))? $this->objLanguage->languageText('mod_lrsbenefittypes_editbenefit', 'award') :
								 $this->objLanguage->languageText('mod_lrsbenefittypes_addbenefit', 'award');
	
$objAddEditTypeForm->addToForm($header->show());

$objaddeditTable = new htmlTable('lrsbenefitype');
$objaddeditTable->cellspacing = 2;
$objaddeditTable->width = '70%';

$objaddeditHeadTable = new htmlTable('lrsbenefittypes');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrsbenefittypes_addeditbenefittype', 'award')."</i>");
$objaddeditHeadTable->addCell("");
$objaddeditHeadTable->endRow();

if(isset($benefitTypeId)) {
	$valueRow = $this->objBenefitType->getRow('id', $benefitTypeId);
	$setName = $valueRow['name'];
	$hidTypeId = new textinput('benefitTypeId', $benefitTypeId, 'hidden');
	$txtTypeId = $hidTypeId->show();
} else {
	$setName = $txtTypeId = '';
}

$txtName = new textinput('benefitTypeName', $setName);

$objaddeditTable->startRow();
$objaddeditTable->addCell($name. ':', '30%', NULL, NULL, 'odd');
$objaddeditTable->addCell($txtName->show().$txtTypeId);
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewbenefittype', 'selected'=>'init_10'), 'award');
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
$objAddEditTypeForm->addRule('benefitTypeName', $this->objLanguage->languageText('mod_lrs_benefit_valrequired', 'award'), 'required');

$objAddEditTypeForm->addToForm($objaddeditHeadTable->show());
$objAddEditTypeForm->addToForm($objaddeditTable->show());

echo $objAddEditTypeForm->show();
?>