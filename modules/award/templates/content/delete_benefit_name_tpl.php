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
* Delete benefit type list template for the LRS benefit type
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
$this->loadClass('link', 'htmlelements');

$objDeleteBenefitNameForm = new form('lrsbenefitname');

// Create delete icon
$param = array('action' => 'confirmdeletebenefitname', 'benefitTypeId'=>$benefitTypeId, 'benefitNameId'=>$benefitNameId);
$objDelIcon = &$this->newObject('geticon', 'htmlelements');
$deletephrase = $this->objLanguage->languageText('mod_lrsbenefittypes_deletebenefitname');
$deleteIcon = $objDelIcon->getDeleteIconWithConfirm($benefitNameId, $param, 'lrsbenefitnames', $deletephrase); 

$sql = "SELECT COUNT(benefit_nameId) AS count FROM tbl_lrs_benefits WHERE benefit_nameId = '$benefitNameId'";
$unitCount = $this->objBenefit->getArray($sql);
$count = current($unitCount);

$valueRow = $this->objBenefitName->getRow('id', $benefitNameId);
$setName = $valueRow['name'];

$rep = array('COUNT' => $count['count']);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 3;
$header->str = $setName."  ".$deleteIcon;
$objDeleteBenefitNameForm->addToForm($header->show());

$objaddeditHeadTable = new htmlTable('lrsbenefittypes');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->code2Txt("mod_lrsbenefittypes_delete_name_counts", $rep)."</i>");
$objaddeditHeadTable->endRow();

$linkDelete = $this->objLanguage->languageText("word_delete");
$location = $this->uri(array('action' => 'confirmdeletebenefitname', 'benefitTypeId'=>$benefitTypeId, 'benefitNameId'=>$benefitNameId), 'lrsbenefittypes');
$deletephrase = $this->objLanguage->languageText('mod_lrsbenefittypes_deletebenefitname');
$objConfirm = $this->newObject('confirm','utilities');
$objConfirm->setConfirm($linkDelete, $location, $deletephrase, NULL);

$linkBack = new link($this->uri(array('action'=>'viewbenefitname', 'benefitTypeId'=>$benefitTypeId), 'lrsbenefittypes'));
$linkBack->link = $this->objLanguage->languageText("word_back");

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<br />");
$objaddeditHeadTable->endRow();

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell($objConfirm->show().' / '.$linkBack->show());
$objaddeditHeadTable->endRow();

$objDeleteBenefitNameForm->addToForm($objaddeditHeadTable->show());

echo $objDeleteBenefitNameForm->show();
?>