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
* Edit agreement template for the LRS Wages
* Edit the fields associated with the agree selected agreement
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
$this->loadClass('checkBox', 'htmlelements');

//database elements for if the agree id is set
$agree = $this->objAgree->getRow('id', $agreeId);
$setMonths = $agree['length'];
$setNOW = $agree['workers'];
$setAgreeNotes = $agree['notes'];
$setDate = date('Y-m-d',strtotime($agree['implementation']));
$wage = $this->objDbWages->getRow('agreeid', $agree['id']);
$setWageRate = $wage['weeklyrate'];
$setWageNotes = $wage['notes'];
$agreementType = $this->objDbAgreeType->getRow('id', $agree['typeid']);
$unit = $this->objDbUnit->getRow('id', $unitId);
$branchUnitId = $this->objUnitBranch->getRow('unitid', $unitId);
$branchInfo = $this->objDbBranch->getRow('id', $branchUnitId['id']);

$objeditAgreementForm = new form('lrsadmin', $this->uri(array('action'=>'editagreement', 'selected'=>'init_10')));

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_edit_Agreement_header', 'award');
$objeditAgreementForm->addToForm($header->show());

//Create a table for the header
$objeditAgreeTable = new htmlTable('lrsadmin');
$objeditAgreeTable->cellspacing = '2';
$objeditAgreeTable->cellpadding = '2';
$objeditAgreeTable->width = '90%';

$lblagreeName = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_name', 'award'), NULL);
$lblagreeType = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_type', 'award'), NULL);
$lblunitType = new label($this->objLanguage->languageText('mod_lrs_confirm_unit_Type', 'award'), NULL);
$lblagreeDate = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_Date', 'award'), NULL);
$lblagreeMonths = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_Month', 'award'), NULL);
$lblagreeNotes = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_Notes', 'award'), NULL);

$objagreeType = new dropdown('agreeType');

$tblAgree = $this->objDbAgreeType->getAll("ORDER BY name ASC");
$objagreeType->addFromDB($tblAgree, 'name', 'id');
$objagreeType->setSelected((int)$agreementType['id']);

$objeditAgreeTable->startHeaderRow();
$objeditAgreeTable->addHeaderCell($lblagreeName->show());
$objeditAgreeTable->addHeaderCell($agreeName);
$objeditAgreeTable->endHeaderRow();

$objeditAgreeTable->startRow();
$objeditAgreeTable->addCell($lblagreeType->show(),'','','','odd');
$objeditAgreeTable->addCell($objagreeType->show());
$objeditAgreeTable->endRow();

$objdatePick = $this->getObject('datepicker','htmlelements');
$objdatePick->setDefaultDate($setDate);
if(isset($agreeId))
{
	$objdatePick->setDefaultDate($setDate);
}

$objeditAgreeTable->startRow();
$objeditAgreeTable->addCell($lblagreeDate->show(),'','','','odd');
$objeditAgreeTable->addCell($objdatePick->show());
$objeditAgreeTable->endRow();

$benefit = $this->objBenefit->getRow('agreeId', $agreeId);
$benefitVal = $benefit['value'];

$txtHOW = new textinput('how', $benefitVal);

$objeditAgreeTable->startRow();
$objeditAgreeTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_HOW', 'award'),'','','','odd');
$objeditAgreeTable->addCell($txtHOW->show());
$objeditAgreeTable->endRow();

$txtmonths = new textinput('months', $setMonths);

$objeditAgreeTable->startRow();
$objeditAgreeTable->addCell($lblagreeMonths->show(),'','','','odd');
$objeditAgreeTable->addCell($txtmonths->show());
$objeditAgreeTable->endRow();

$txtNOW = new textinput('now', $setNOW);

// Create a link to fill number of workers from old agreement if it exists
if ($oldId = $this->objAgree->getPreviousAgreementId($agree['id'], $agree['implementation'])) {
	$linkText = $this->objLanguage->languageText('mod_award_populateworkers', 'award');
	$oldAgree = $this->objAgree->getRow('id', $oldId);
	//$oldAgree['workers'] = "1000";
	$script = "<script type='text/javascript'>
				function populateWorkers() {
					jQuery('#input_now').val('{$oldAgree['workers']}');
				}
			   </script>";
	$this->appendArrayVar('headerParams', $script);
	$workerLink = "<a href='javascript:populateWorkers();'>$linkText ({$oldAgree['workers']})</a>";
} else {
	$workerLink = '';
}

$objeditAgreeTable->startRow();
$objeditAgreeTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_NOW', 'award'),'','','','odd');
$objeditAgreeTable->addCell($txtNOW->show()." $workerLink");
$objeditAgreeTable->endRow();

$txtagreeNotes = new textarea('agreenotes', $setAgreeNotes);

$objeditAgreeTable->startRow();
$objeditAgreeTable->addCell($lblagreeNotes->show(),'','top','','odd');
$objeditAgreeTable->addCell($txtagreeNotes->show());
$objeditAgreeTable->endRow();

$btnSubmit = new button('submitagreement');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'agreementoverview', 'unitId'=>$unitId, 'id'=>$agreeId, 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$txthiddenUnit = new textinput('unitId', $unit['id'], 'hidden');
$txthiddenAgree = new textinput('agreeId', $agreeId, 'hidden');

$objeditAgreeTable->startRow();
$objeditAgreeTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objeditAgreeTable->addCell($txthiddenUnit->show().' '.$txthiddenAgree->show());
$objeditAgreeTable->endRow();

$objeditAgreementForm->addToForm($objeditAgreeTable->show());

$monthsIntRule = $objLanguage->languageText("mod_lrs_months_int_rule", 'award');
$nowIntRule = $this->objLanguage->languageText('mod_lrs_now_int_rule', 'award');
$howIntRule = $this->objLanguage->languageText('mod_award_hoursrequired', 'award');

$objeditAgreementForm->addRule('how',$howIntRule,'numeric');
$objeditAgreementForm->addRule('months',$monthsIntRule,'numeric');
$objeditAgreementForm->addRule('now',$nowIntRule,'numeric');

echo $objeditAgreementForm->show();
?>