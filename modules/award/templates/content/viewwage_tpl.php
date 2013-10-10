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
* viewwage template for the LRS Wages
* This template gives an overview of the wage that has been selected or created
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

//Create form
$objviewwageForm = new form('lrsadmin');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_viewwage_header', 'award');
$objviewwageForm->addToForm($header->show());

//Create a table for the header
$objviewwageTable = new htmlTable('lrsadmin');
$objviewwageTable->cellspacing = 2;

$lblwageNotes = new label($this->objLanguage->languageText('mod_lrs_confirm_wage_Notes', 'award'), NULL);
$lblwageRate = new label($this->objLanguage->languageText('mod_lrs_confirm_wage_Rate', 'award'), NULL);
$lblsocMajGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Maj_Grp', 'award'), NULL);
$lblsocSubMajGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Sub_Min_Grp', 'award'), NULL);
$lblsocMinGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Min_Grp', 'award'), NULL);
$lblsocUnitGrp = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Unit_Grp', 'award'), NULL);
$lblsocName = new label($this->objLanguage->languageText('mod_lrs_confirm_soc_Name', 'award'), NULL);
$lbljobCodes = new label($this->objLanguage->languageText('mod_lrs_confirm_job_Codes', 'award'), NULL);
$lblgrades = new label($this->objLanguage->languageText('mod_lrs_confirm_grades', 'award'), NULL);
$lbldelete = new label($this->objLanguage->languageText('mod_lrs_agree_delete', 'award'), NULL);

$wage = $this->objDbWages->getRow('id', $wageId);
$wageName = $this->objwageSocName->getRow('id', $wageId);
//$jobCode = $this->objjobCodes->getRow('id', $wageName['job_codeId']);
//$grade = $this->objgrades->getRow('id', $wageName['gradeId']);
$socName = $this->objDbSocNames->getRow('id', $wageName['socnameid']);
$majorGrp = $this->objDbSocMajorGroup->getRow('id', $socName['major_groupid']);
$subMajorGrp = $this->objDbSubMajorGroups->getRow('id', $socName['submajor_groupid']);
$minorGrp = $this->objDbMinorGroups->getRow('id', $socName['minor_groupid']);
$unitGrp = $this->objDbUnitGroups->getRow('id', $socName['unit_groupid']);
$agreeId = $wage['agreeid'];
$agree = $this->objAgree->getRow('id', $agreeId);
$payPeriod = $this->objDbPayPeriodType->getRow('id', $wage['payperiodtypeid']);
$benefit = $this->objBenefit->getAll("WHERE agreeid='$agreeId' AND nameid='init_7'");
$objviewwageTable->startHeaderRow();
$objviewwageTable->addHeaderCell($lblsocName->show());
$objviewwageTable->addHeaderCell($socName['name']);
$objviewwageTable->endHeaderRow();
/*
$objviewwageTable->startRow('odd');
$objviewwageTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_HOW'));
$objviewwageTable->addCell($benefit['benefitValue']);
$objviewwageTable->endRow();
*/
$wageRate = ($payPeriod['factor'] == 0)? $wage['weeklyrate']/$benefit[0]['value'] :
                                         $wage['weeklyrate'] * $payPeriod['factor'];
$objviewwageTable->startRow('even');
$objviewwageTable->addCell($lblwageRate->show());
$objviewwageTable->addCell(number_format($wageRate,2)." - {$payPeriod['name']}");
$objviewwageTable->endRow();

$objviewwageTable->startRow('odd');
$objviewwageTable->addCell($lblwageNotes->show(), '', 'top');
$objviewwageTable->addCell($wage['notes']);
$objviewwageTable->endRow();

$objviewwageTable->startRow('even');
$objviewwageTable->addCell($lblsocMajGrp->show());
$objviewwageTable->addCell($majorGrp['description']);
$objviewwageTable->endRow();

$objviewwageTable->startRow('odd');
$objviewwageTable->addCell($lblsocSubMajGrp->show());
$objviewwageTable->addCell($subMajorGrp['description']);
$objviewwageTable->endRow();

$objviewwageTable->startRow('even');
$objviewwageTable->addCell($lblsocMinGrp->show());
$objviewwageTable->addCell($minorGrp['description']);
$objviewwageTable->endRow();

$objviewwageTable->startRow('odd');
$objviewwageTable->addCell($lblsocUnitGrp->show());
$objviewwageTable->addCell($unitGrp['description']);
$objviewwageTable->endRow();

/*$objviewwageTable->startRow('even');
$objviewwageTable->addCell($lbljobCodes->show());
$objviewwageTable->addCell($jobCode['description']);
$objviewwageTable->endRow();

$objviewwageTable->startRow('odd');
$objviewwageTable->addCell($lblgrades->show());
$objviewwageTable->addCell($grade['name']);
$objviewwageTable->endRow();*/

//The link to the edit wage template
$link = new link($this->uri(array('action'=>'editwage','agreeId'=>$agreeId, 'wageId'=>$wageId, 'selected'=>'init_10'),'award'));
$link->link = $this->objLanguage->languageText('mod_lrs_edit_wage_header','award');

$linkback = new link($this->uri(array('action'=>'agreementoverview', 'unitId'=>$agree['unitid'], 'id'=>$agreeId, 'selected'=>'init_10'),'award'));
$linkback->link = $this->objLanguage->languageText('mod_lrs_link_back','award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$objviewwageTable->startRow();
$objviewwageTable->addCell("<br />");
$objviewwageTable->addCell("<br />");
$objviewwageTable->endRow();

$objviewwageTable->startRow();
$objviewwageTable->addCell($link->show().' / '.$linkback->show());
$objviewwageTable->addCell($linkExit->show(), NULL, 'top', 'right');
$objviewwageTable->endRow();

$objviewwageTable->startRow();
$objviewwageTable->addCell("<br />");
$objviewwageTable->addCell("<br />");
$objviewwageTable->endRow();

$objviewwageForm->addToForm($objviewwageTable->show());

echo $objviewwageForm->show();

?>