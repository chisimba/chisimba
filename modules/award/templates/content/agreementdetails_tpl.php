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
* Agreement details template for the LRS Wages
* This template is used to add an agreement to a bargaining unit, all the fields are entered and inserted into there respective table
* 
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


$setDate = date('Y-m-d');
$objaddAgreementForm = new form('agreeadd', $this->uri(array('action'=>'addagreement', 'selected'=>'init_10')));
$unit = $this->objDbUnit->getRow('id', $unitId);
$branchUnitId = $this->objUnitBranch->getRow('unitid', $unitId);
$branchInfo = $this->objDbBranch->getRow('id', $branchUnitId['id']);

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;


$header->str = $this->objLanguage->languageText('mod_lrs_add_Agreement_header', 'award');

$objaddAgreementForm->addToForm($header->show());

//Create a table for the header
$objaddAgreeTable = new htmlTable('lrsadmin');
$objaddAgreeTable->cellspacing = '2';
$objaddAgreeTable->cellpadding = '2';
$objaddAgreeTable->width = '90%';

$script2 = '<script type="text/javascript" src="modules/lrsadmin/resources/addRule.js"></script>';
//add to header
$this->appendArrayVar('headerParams', $script2);

$agreeTypeRule = $objLanguage->languageText("mod_lrs_agreeType_rule", 'award');
$monthsRule = $objLanguage->languageText("mod_lrs_months_rule", 'award');
$monthsIntRule = $objLanguage->languageText("mod_lrs_months_int_rule", 'award');
$wageRateRule = $objLanguage->languageText("mod_lrs_wage_rule", 'award');
$wageIntRateRule = $objLanguage->languageText("mod_lrs_wage_int_rule", 'award');
$occNameRule = $objLanguage->languageText("mod_lrs_nameGrp_rule", 'award');
//$jobCodesRule = $this->objLanguage->languageText("mod_lrs_jobCodes_rule");
//$gradesRule = $this->objLanguage->languageText("mod_lrs_grades_rule");
$gradejobcodeRule = $this->objLanguage->languageText('mod_grade_jobcode_rule', 'award');
$paymentRule = $this->objLanguage->languageText('mod_lrs_payperiod_rule', 'award');
$howIntRule = $this->objLanguage->languageText('mod_lrs_how_int_rule', 'award');
$howRule = $this->objLanguage->languageText('mod_lrs_how_rule', 'award');
$nowIntRule = $this->objLanguage->languageText('mod_lrs_now_int_rule', 'award');
$nowRule = $this->objLanguage->languageText('mod_lrs_now_rule', 'award');

$lblagreeName = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_name', 'award'), NULL);

$lblagreeType = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_type', 'award'), NULL);
$lblperiodType = new label($this->objLanguage->languageText('mod_lrs_confirm_period_Type', 'award'), NULL);
$lblunitType = new label($this->objLanguage->languageText('mod_lrs_confirm_unit_Type', 'award'), NULL);
$lblagreeDate = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_Date', 'award'), NULL);
$lblagreeMonths = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_Month', 'award'), NULL);
$lblagreeNotes = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_Notes', 'award'), NULL);
$lblwageNotes = new label($this->objLanguage->languageText('mod_lrs_confirm_wage_Notes', 'award'), NULL);
$lblwageRate = new label($this->objLanguage->languageText('mod_lrs_confirm_wage_Rate', 'award'), NULL);
$lblsocMajGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Maj_Grp', 'award'), NULL);
$lblsocSubMajGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Sub_Min_Grp', 'award'), NULL);
$lblsocMinGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Min_Grp', 'award'), NULL);
$lblsocUnitGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Unit_Grp', 'award'), NULL);
$lblsocName = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Name', 'award'), NULL);
$lblGradeCode = new label($this->objLanguage->languageText('mod_lrs_confirm_gradeCode', 'award'), NULL);
$lbljobCodes = new label($this->objLanguage->languageText('mod_lrs_confirm_job_Codes', 'award'), NULL);
$lblgrades = new label($this->objLanguage->languageText('mod_lrs_confirm_grades', 'award'), NULL);
$gradejobcodeRule = $this->objLanguage->languageText('mod_grade_jobcode_rule', 'award');

$objagreeType = new dropdown('agreeType');
$objagreeType->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));

if($branchInfo['id'] == 'init_0'){
	$tblSectAgreeType = $this->objAgreeTypes->getRow('id', 5);
	$tblUniAgreeType = $this->objAgreeTypes->getRow('id', 6);
	
	$objagreeType->addOption($tblSectAgreeType['id'],$tblSectAgreeType['name']);
	$objagreeType->addOption($tblUniAgreeType['id'],$tblUniAgreeType['name']);
}else{
	$tblAgree = $this->objDbAgreeType->getAll("ORDER BY name ASC");
	foreach ($tblAgree as $typeId) 
	{
		$objagreeType->addOption($typeId['id'],$typeId['name']);
	}
}


$objaddAgreeTable->startHeaderRow();
$objaddAgreeTable->addHeaderCell($lblunitType->show());
$objaddAgreeTable->addHeaderCell($unitName);
$objaddAgreeTable->endHeaderRow();

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($lblagreeType->show(),'','','','odd');
$objaddAgreeTable->addCell($objagreeType->show());
$objaddAgreeTable->endRow();

//Display the date of the completion of the most recent agreement
$sqlLastDate = "SELECT ADDDATE(Max(implementation), INTERVAL length MONTH) AS date FROM tbl_award_agree WHERE unitid = '$unitId'";
$arrayLastDate = $this->objAgree->getArray($sqlLastDate);
$elementLastDate = current($arrayLastDate);

$objdatePick = $this->getObject('datepicker','htmlelements');

if(isset($elementLastDate['date']))
{
	$objdatePick->setDefaultDate($elementLastDate['date']);
}

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($lblagreeDate->show(),'','','','odd');
$objaddAgreeTable->addCell($objdatePick->show());
$objaddAgreeTable->endRow();

$txtmonths = new textinput('months');

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($lblagreeMonths->show(),'','','','odd');
$objaddAgreeTable->addCell($txtmonths->show());
$objaddAgreeTable->endRow();

$txtHOW = new textinput('how');

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_HOW', 'award'),'','','','odd');
$objaddAgreeTable->addCell($txtHOW->show());
$objaddAgreeTable->endRow();

$txtNOW = new textinput('now');

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_NOW', 'award'),'','','','odd');
$objaddAgreeTable->addCell($txtNOW->show());
$objaddAgreeTable->endRow();

$txtagreeNotes = new textarea('agreenotes');

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($lblagreeNotes->show(),'','top','','odd');
$objaddAgreeTable->addCell($txtagreeNotes->show());
$objaddAgreeTable->endRow();

$txtwageRate = new textinput('wageRate');
$objperiodType = new dropdown('payPeriod');

$payPeriodArray = $this->objDbPayPeriodType->getAll();

$objperiodType->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
foreach ($payPeriodArray as $period) 
{
	$objperiodType->addOption($period['id'],$period['name']);
}

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_payperiod', 'award'),'','','','odd');
$objaddAgreeTable->addCell($txtwageRate->show()."  ".$objperiodType->show());
$objaddAgreeTable->endRow();

$txtwageNotes = new textarea('wagenotes');

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($lblwageNotes->show(),'','top','','odd');
$objaddAgreeTable->addCell($txtwageNotes->show());
$objaddAgreeTable->endRow();

$script = $this->getResourceURI('wageadmin.js');
//add to header
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$script'></script>");

//create dropdown
$objsocName = new dropdown('drpsocName');

//on pressing character key in text input the javascript function is called
$txtfilterSocName = new textinput('txtsocName');
$txtfilterSocName->extra = " onkeyup = \"javascript:updateOccupationList(this.value)\"";

//default selected option for drodown
$objsocName->addOption('-1',$this->objLanguage->languageText('mod_lrs_select', 'award'));
$objsocName->setSelected('-1');

$lblsocName = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Name', 'award'), NULL);

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($lblsocName->show(),'','top','','odd');
$objaddAgreeTable->addCell($txtfilterSocName->show() . "<div id='myDiv'>".$objsocName->show()."</div>");
$objaddAgreeTable->endRow();

//$tbljobCodes = $this->objjobCodes->getAll("ORDER BY name ASC");
//$tblgrades = $this->objgrades->getAll();

//Create 2 radio buttons so that a grade or job code can be selected. javascript functions in place to hide and show the dropdowns of each selection and to set the index to -1 when not selected
//$objGradeCode = new radio('gradeCode_radio');
//$objGradeCode->addOption('code', $lbljobCodes->show(), " onchange = \"javascript:showDisplay('input_jobcodes'); javascript:hideDisplay('input_grades'); javascript:setIndex('input_grades')\"");
//$objGradeCode->addOption('grade',$lblgrades->show(), " onchange = \"javascript:showDisplay('input_grades'); javascript:hideDisplay('input_jobcodes'); javascript:setIndex('input_jobcodes')\"");
//$objGradeCode->setSelected('code');

//$objjobCodes = new dropdown('jobcodes');

//$objjobCodes->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
// foreach ($tbljobCodes as $jobCodes) 
// {
// 	$objjobCodes->addOption($jobCodes['id'],$jobCodes['description']." [".$jobCodes['name']."]");
// }

//$objgrades = new dropdown('grades');
//$objgrades->extra = "style = 'display:none' ";

//Sort the grade in ascending order with the id and name
//$gradeArray = array();
// foreach ($tblgrades as $grades) 
// {
// 	$gradeArray[$grades['id']] = $grades['name'];
// }
//natsort($gradeArray);
//$objgrades->addOption('-1', $this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
//foreach ($gradeArray as $id => $grade) 
//{
//	$objgrades->addOption($id,$grade);
//}

//$objaddAgreeTable->startRow();
//$objaddAgreeTable->addCell($lblGradeCode->show(),'','top','','odd');
//$objaddAgreeTable->addCell($objGradeCode->show()."<br />".$objjobCodes->show().$objgrades->show());
//$objaddAgreeTable->endRow();

$btnSubmit = new button('submitagreement');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$txthidden = new textinput('unitId', $unit['id'], 'hidden');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'bargainingunitoverview', 'id'=>$unitId, 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell("<br />");
$objaddAgreeTable->addCell("<br />");
$objaddAgreeTable->endRow();

$objaddAgreeTable->startRow();
$objaddAgreeTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddAgreeTable->addCell($txthidden->show());
$objaddAgreeTable->addCell("<br />");
$objaddAgreeTable->endRow();

$objaddAgreementForm->addToForm($objaddAgreeTable->show());
$objaddAgreementForm->addRule('agreeType', $this->objLanguage->languageText('mod_lrs_agreeType_rule', 'award'), 'select');
$objaddAgreementForm->addRule('months', $this->objLanguage->languageText('mod_lrs_agreelength_rule', 'award'), 'numeric');
$objaddAgreementForm->addRule('how', $this->objLanguage->languageText('mod_lrs_how_int_rule', 'award'), 'numeric');
$objaddAgreementForm->addRule('now', $this->objLanguage->languageText('mod_lrs_now_int_rule', 'award'), 'numeric');
$objaddAgreementForm->addRule('wageRate', $this->objLanguage->languageText('mod_lrs_wage_int_rule', 'award'), 'numeric');
$objaddAgreementForm->addRule('payPeriod', $this->objLanguage->languageText('mod_lrs_payperiod_rule', 'award'), 'select');
$objaddAgreementForm->addRule('drpsocName', $this->objLanguage->languageText('mod_lrs_selectocc_rule', 'award'), 'select');


echo $objaddAgreementForm->show();

?>