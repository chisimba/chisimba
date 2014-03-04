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
* Edit wage template for the LRS Wages
* This template used to edit or add a wage, if the addWage variable is set(in the controller) then this template is used to add a wage else it is used to edit a wage
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
$this->loadClass('radio', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');

if(isset($addWage)) {
	$setWageRate = '';
	$setWageNotes = '';
	$setSocName = '';
	$benefitVal = '';
	$period = '';
	$weekWageRate = '';
} else {
	$wage = $this->objDbWages->getRow('id', $wageId);
	$wageName = $this->objwageSocName->getRow('id', $wageId);
	$socName = $this->objDbSocNames->getRow('id', $wageName['socnameid']);
	$benefit = $this->objBenefit->getRow('agreeid', $agreeId);
	$payPeriod = $this->objDbPayPeriodType->getRow('id', $wage['payperiodtypeid']);
	
	$setWageNotes = $wage['notes'];
	$setSocName = $socName['name'];
	$benefitVal = $benefit['value'];
	$paymentPeriod = $payPeriod['id'];
	$wageRate = $wage['weeklyrate'];
	
	switch ($payPeriod['factor']) {
		case 0:
			$weekWageRate = $wageRate / $benefitVal;
			break;
		default:
			$weekWageRate = $wageRate * $payPeriod['factor'];
			break;		
	}
}

//if $addWage is set then the add wage form is created else the edit wage form is created they are labelled the same to resrict redundant code
if(isset($addWage)) {
	$objeditWageForm = new form('lrsadmin', $this->uri(array('action'=>'insertwage', 'agreeId'=>$agreeId, 'selected'=>'init_10')));
} else {
	$objeditWageForm = new form('lrsadmin', $this->uri(array('action'=>'updatewage', 'agreeId'=>$agreeId, 'wageId'=>$wageId, 'selected'=>'init_10')));
}

if(isset($addWage)) {
	//create heading
	$header = $this->getObject('htmlheading','htmlelements');
	$header->type = 2;
	$header->str = $this->objLanguage->languageText('mod_lrs_add_wage_link', 'award');
	$objeditWageForm->addToForm($header->show());
} else {
	//create heading
	$header = $this->getObject('htmlheading','htmlelements');
	$header->type = 2;
	$header->str = $this->objLanguage->languageText('mod_lrs_edit_wage_header', 'award');
	$objeditWageForm->addToForm($header->show());
}

$unitId = $this->getParam('unitId');

//Create a table for the header
$objeditWageTable = new htmlTable('lrsadmin');
$objeditWageTable->cellspacing = '2';
$objeditWageTable->cellpadding = '2';
$objeditWageTable->width = '90%';

$lblagreeName = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_name', 'award'), NULL);
$lblwageNotes = new label($this->objLanguage->languageText('mod_lrs_confirm_wage_Notes', 'award'), NULL);
$lblwageRate = new label($this->objLanguage->languageText('mod_lrs_confirm_wage_Rate', 'award'), NULL);
$lblsocMajGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Maj_Grp', 'award'), NULL);
$lblsocSubMajGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Sub_Min_Grp', 'award'), NULL);
$lblsocMinGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Min_Grp', 'award'), NULL);
$lblsocUnitGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Unit_Grp', 'award'), NULL);
$lblsocName = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Name', 'award'), NULL);
$lbljobCodes = new label($this->objLanguage->languageText('mod_lrs_confirm_job_Codes', 'award'), NULL);
$lblgrades = new label($this->objLanguage->languageText('mod_lrs_confirm_grades', 'award'), NULL);
$lblGradeCode = new label($this->objLanguage->languageText('mod_lrs_confirm_gradeCode', 'award'), NULL);

$agree = $this->objAgree->getRow('id', $agreeId);

if(isset($addWage)) {
	$objeditWageTable->startHeaderRow();
	$objeditWageTable->addHeaderCell($lblagreeName->show());
	$objeditWageTable->addHeaderCell($agree['name']);
	$objeditWageTable->endHeaderRow();
} else {
	$objeditWageTable->startHeaderRow();
	$objeditWageTable->addHeaderCell($lblsocName->show());
	$objeditWageTable->addHeaderCell($setSocName);
	$objeditWageTable->endHeaderRow();
}

$objwageRate = new textinput('wageRate', $weekWageRate);
$objperiodType = new dropdown('payPeriod');

if(isset($addWage)) {
	$objperiodType->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
	$paymentPeriod = "-1";
}

$payPeriodArray = $this->objDbPayPeriodType->getAll();
$objperiodType->addFromDB($payPeriodArray, 'name', 'id');




$objperiodType->setSelected($paymentPeriod);

$objeditWageTable->startRow();
$objeditWageTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_payperiod', 'award'),'','','','odd');
$objeditWageTable->addCell($objwageRate->show()."  ".$objperiodType->show());
$objeditWageTable->endRow();

$objwageNotes = new textarea('wageNotes', $setWageNotes);

$objeditWageTable->startRow();
$objeditWageTable->addCell($lblwageNotes->show(),'','top','','odd');
$objeditWageTable->addCell($objwageNotes->show());
$objeditWageTable->endRow();

$script = $this->getResourceURI('wageadmin.js');
//add to header
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$script'></script>");

//create dropdown
$objsocName = new dropdown('drpsocName');

//on pressing character key in text input the javascript function is called
$objfilterSocName = new textinput('txtsocName');
$objfilterSocName->extra = " onkeyup = \"javascript:updateOccupationList(this.value)\"";

//default selected option for drodown
if(isset($addWage))
{
	$objsocName->addOption('-1',$this->objLanguage->languageText('mod_lrs_select', 'award'));
}
else
{
	$objsocName->addOption($socName['id'], $socName['name']);
}

$lblsocName = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Name', 'award'), NULL);

$objeditWageTable->startRow();
$objeditWageTable->addCell($lblsocName->show(),'','top','','odd');
$objeditWageTable->addCell($objfilterSocName->show() . "<div id='myDiv'>".$objsocName->show()."</div>");
$objeditWageTable->endRow();

$btnSubmit = new button('submitwage');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'agreementoverview', 'id'=>$agreeId, 'unitId'=>$unitId, 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

//$tbljobCodes = $this->objjobCodes->getAll("ORDER BY name ASC");
//$tblgrades = $this->objgrades->getAll();

//Create 2 radio buttons so that a grade or job code can be selected. javascript functions in place to hide and show the dropdowns of each selection and to set the index to -1 when not selected
//$objGradeCode = new radio('gradeCode_radio');
//$objGradeCode->addOption('code', $lbljobCodes->show(), " onchange = \"javascript:showDisplay('input_jobcodes'); javascript:hideDisplay('input_grades'); javascript:setIndex('input_grades')\"");
//$objGradeCode->addOption('grade',$lblgrades->show(), " onchange = \"javascript:showDisplay('input_grades'); javascript:hideDisplay('input_jobcodes'); javascript:setIndex('input_jobcodes')\"");

//$objjobCodes = new dropdown('jobcodes');
//$objgrades = new dropdown('grades');

//$objjobCodes->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));

//foreach ($tbljobCodes as $jobCodes) 
//{
//	$objjobCodes->addOption($jobCodes['id'],$jobCodes['description']." [".$jobCodes['name']."]");
//}

//$objgrades->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
//Sort the grade in ascending order with the id and name
//$gradeArray = array();
//foreach ($tblgrades as $grades) 
//{
//	$gradeArray[$grades['id']] = $grades['name'];
//}
//natsort($gradeArray);
//foreach ($gradeArray as $id => $gradeName) /
//{
//	$objgrades->addOption($id,$gradeName);
//}

//if the add wage is set the job code should be displayed and the grade hidden
//if(isset($addWage))
//{
//	$objGradeCode->setSelected('code');
//	$objgrades->extra = "style = 'display:none' ";
//}
//else
//{
	//if the job code is set the job code radio is selected and the dropdown associated is shown else the grade radio button is selected and the associated dropdown
//	if(isset($setJobCode))
//	{
//		$objGradeCode->setSelected('code');
//		$objjobCodes->setSelected($jobCode['id']);
//		$objgrades->extra = "style = 'display:none' ";
//	}
//	else
//	{
//		$objGradeCode->setSelected('grade');
//		$objgrades->setSelected($grade['id']);
//		$objjobCodes->extra = "style = 'display:none' ";
//	}
//}

//$objeditWageTable->startRow();
//$objeditWageTable->addCell($lblGradeCode->show(),'','top','','odd');
//$objeditWageTable->addCell($objGradeCode->show()."<br />".$objjobCodes->show().$objgrades->show());
//$objeditWageTable->endRow();

$objeditWageTable->startRow();
$objeditWageTable->addCell("<br />");
$objeditWageTable->addCell("<br />");
$objeditWageTable->endRow();

$txthiddenUnit = new textinput('unitId', $unitId, 'hidden');

$objeditWageTable->startRow();
$objeditWageTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objeditWageTable->addCell($txthiddenUnit->show());
$objeditWageTable->endRow();

$objeditWageForm->addToForm($objeditWageTable->show());
$objeditWageForm->addRule('wageRate', $this->objLanguage->languageText('mod_lrs_wage_int_rule', 'award'), 'numeric');
$objeditWageForm->addRule('payPeriod', $this->objLanguage->languageText('mod_lrs_payperiod_rule', 'award'), 'select');
$objeditWageForm->addRule('drpsocName', $this->objLanguage->languageText('mod_lrs_selectocc_rule', 'award'), 'select');


echo $objeditWageForm->show();

?>