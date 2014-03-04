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

$objAddEditNameForm = new form('lrsbenefittypes', $this->uri(array('action'=>'savebenefitname','selected'=>'init_10'),'award'));

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = (isset($benefitNameId))? $this->objLanguage->languageText('mod_lrsbenefittypes_editbenefitname', 'award') :
										$this->objLanguage->languageText('mod_lrsbenefittypes_addbenefitname', 'award');
$objAddEditNameForm->addToForm($header->show());

$objaddeditTable = new htmlTable('lrsbenefitype');
$objaddeditTable->cellspacing = 2;
$objaddeditTable->width = '70%';

$objaddeditHeadTable = new htmlTable('lrsbenefittypes');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrsbenefittypes_addeditbenefitname','award')."</i>");
$objaddeditHeadTable->addCell("");
$objaddeditHeadTable->endRow();

if(isset($benefitNameId)) {
	$valueRow = $this->objBenefitNames->getRow('id', $benefitNameId);
	$setName = $valueRow['name'];
	$setUnit = $valueRow['measure'];
	$setAggregate = $valueRow['aggregatetype'];
	$hidNameId = new textinput('benefitNameId', $benefitNameId, 'hidden');
	$txtHiddenId = $hidNameId->show();
} else {
	$txtHiddenId = $setName = $setUnit = $setAggregate = '';
	
}

$name = $this->objLanguage->languageText('phrase_benefit_name_edit');
$nameRule = $this->objLanguage->languageText('mod_lrsbenefittypes_name_required');
$typeRule = $this->objLanguage->languageText('mod_lrsbenefittypes_type_required');

$objbenefitType = new dropdown('benefitTypeId');
//$objbenefitType->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one'));

$tblbenefitType = $this->objBenefitType->getAll("ORDER BY name ASC");
foreach ($tblbenefitType as $type)  {
	$objbenefitType->addOption($type['id'],$type['name']);
}
$objbenefitType->setSelected($benefitTypeId);

$txtName = new textinput('benefitTypeName', $setName, null, '40');

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('phrase_benefit_type_edit'), '40%', NULL, NULL, 'odd');
$objaddeditTable->addCell($objbenefitType->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell($name, NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtName->show());
$objaddeditTable->endRow();

$txtUnit = new textinput('unit', $setUnit);

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrsbenefittypes_edit_unit_measurement','award'), NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtUnit->show().$txtHiddenId);
$objaddeditTable->endRow();

$objAggregateType = new dropdown('aggregateType');
$objAggregateType->addOption('value', $this->objLanguage->languageText('mod_lrs_lrsbenefittype_value','award'));
$objAggregateType->addOption('percentage', $this->objLanguage->languageText('mod_lrs_lrsbenefittype_percentage','award'));

$objAggregateType->setSelected($setAggregate);

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_lrsbenefittype_aggregate','award'), '30%', NULL, NULL, 'odd');
$objaddeditTable->addCell($objAggregateType->show());
$objaddeditTable->endRow();

$objBenchmark = new textarea('benchmark');

$objaddeditTable->startRow();
$objaddeditTable->addCell($this->objLanguage->languageText('mod_lrs_lrsbenefittype_benchmark','award'), '30%', NULL, NULL, 'odd');
$objaddeditTable->addCell($objBenchmark->show());
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewbenefitname', 'benefitTypeId'=>$benefitTypeId, 'selected'=>'init_10'));
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
$objAddEditNameForm->addRule('benefitTypeName', $this->objLanguage->languageText('mod_lrs_benefit_namerequired', 'award'), 'required');
$objAddEditNameForm->addRule('unit', $this->objLanguage->languageText('mod_lrsbenefittypes_unit_valrequired', 'award'), 'required');
$objAddEditNameForm->addToForm($objaddeditHeadTable->show());
$objAddEditNameForm->addToForm($objaddeditTable->show());

echo $objAddEditNameForm->show();
?>