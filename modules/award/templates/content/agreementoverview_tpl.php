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
* Agreement overview template for the LRS Wages
* Gives an overview of the agreement selected or created and lists the wages associated with that agreement
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
$objconfirmForm = new form('lrsadmin');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_wage_overview_header', 'award');
$objconfirmForm->addToForm($header->show());

//Create a table for the header
$objconfirmTable = new htmlTable('lrsadmin');
$objconfirmTable->cellspacing = 2;

$lblagreeName = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_name', 'award'), NULL);

$lblagreeType = new label($this->objLanguage->languageText('mod_lrs_confirm_agree_type', 'award'), NULL);
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
$lbljobCodes = new label($this->objLanguage->languageText('mod_lrs_confirm_job_Codes', 'award'), NULL);
$lblgrades = new label($this->objLanguage->languageText('mod_lrs_confirm_grades', 'award'), NULL);
$lbldelete = new label($this->objLanguage->languageText('mod_lrs_agree_delete', 'award'), NULL);

$agree = $this->objAgree->getRow('id', $id);
$agreementType = $this->objDbAgreeType->getRow('id', $agree['typeid']);
$benefit = $this->objBenefit->getRow('agreeid', $id);

$wage = $this->objDbWages->getRow('agreeid', $agree['id']);
$bargainType = $this->objDbUnit->getRow('id', $agree['unitid']);
$wageName = $this->objwageSocName->getRow('id', $wage['id']);
$socName = $this->objDbSocNames->getRow('id', $wageName['socnameid']);
//$jobCodes = $this->objjobCodes->getRow('id', $wageName['job_codeId']);
//$grades = $this->objgrades->getRow('id', $wageName['gradeId']);
$majorGrp = $this->objDbSocMajorGroup->getRow('id', $socName['id']);
$subMajorGrp = $this->objDbSubMajorGroups->getRow('id', $socName['major_groupid']);
$minorGrp = $this->objDbMinorGroups->getRow('id', $socName['minor_groupid']);
$unitGrp = $this->objDbUnitGroups->getRow('id', $socName['unit_groupid']);

$objconfirmTable->startHeaderRow();
$objconfirmTable->addHeaderCell($lblagreeName->show());
$objconfirmTable->addHeaderCell($agree['name']);
$objconfirmTable->endHeaderRow();

$objconfirmTable->startRow('odd');
$objconfirmTable->addCell($lblagreeType->show());
$objconfirmTable->addCell($agreementType['name']);
$objconfirmTable->endRow();

$objconfirmTable->startRow('even');
$objconfirmTable->addCell($lblagreeDate->show());
$objconfirmTable->addCell($agree['implementation']);
$objconfirmTable->endRow();

$objconfirmTable->startRow('odd');
$objconfirmTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_HOW', 'award'));
$objconfirmTable->addCell($benefit['value']);
$objconfirmTable->endRow();

$objconfirmTable->startRow('even');
$objconfirmTable->addCell($lblagreeMonths->show());
$objconfirmTable->addCell($agree['length']);
$objconfirmTable->endRow();

$objconfirmTable->startRow('odd');
$objconfirmTable->addCell($this->objLanguage->languageText('mod_lrs_wagerate_NOW', 'award'));
$objconfirmTable->addCell($agree['workers']);
$objconfirmTable->endRow();

$objconfirmTable->startRow('even');
$objconfirmTable->addCell($lblagreeNotes->show(), '', 'top');
$objconfirmTable->addCell($agree['notes']);
$objconfirmTable->endRow();

//link to edit an agreement
$link = new link($this->uri(array('action'=>'edit','agreeid'=>$agree['id'], 'selected'=>'init_10'),'award'));
$link->link = $this->objLanguage->languageText('mod_lrs_edit_agreement', 'award');

//link to edit conditions
$linkconditions = new link($this->uri(array('action'=>'conditions_admin','agreeid'=>$agree['id'], 'selected'=>'init_10'),'award'));
$linkconditions->link = $this->objLanguage->languageText('mod_lrs_edit_conditions', 'award');

//link back to previous step in path
$linkback = new link($this->uri(array('action'=>'bargainingunitoverview', 'id'=>$agree['unitid'], 'selected'=>'init_10'),'award'));
$linkback->link = $this->objLanguage->languageText('mod_lrs_link_back', 'award');

$objconfirmTable->startRow();
$objconfirmTable->addCell("<br />");
$objconfirmTable->addCell("<br />");
$objconfirmTable->endRow();

$objconfirmTable->startRow();
$objconfirmTable->addCell($link->show().' / '.$linkconditions->show());
$objconfirmTable->addCell("<br />");
$objconfirmTable->endRow();

$objconfirmTable->startRow();
$objconfirmTable->addCell("<br />");
$objconfirmTable->addCell("<br />");
$objconfirmTable->endRow();

$objwagesTable = new htmlTable('wages');
$objwagesTable->cellspacing = 2;

//Select fields from wage, wage soc name and soc name tables to be displayed as the list of wages 
$sql = "SELECT wage.id AS id, socname.name AS name, wage.notes AS notes, wage.weeklyrate AS rate
		FROM tbl_award_wage AS wage, tbl_award_wage_socname AS wagesocname, tbl_award_socname AS socname
		WHERE wagesocname.id = wage.id AND wagesocname.socnameid = socname.id AND wage.agreeid = '{$agree['id']}'
		ORDER BY name";
$wageList = $this->objDbSocNames->getArray($sql);

$objwagesTable->startHeaderRow();
$objwagesTable->addHeaderCell($lblsocName->show());
$objwagesTable->addHeaderCell($lblwageRate->show());
$objwagesTable->addHeaderCell($lblwageNotes->show());
$objwagesTable->addHeaderCell($lbldelete->show());
$objwagesTable->endHeaderRow();


$link = new link();
$objIcon = $this->newObject('geticon','htmlelements');
$class = '';

$benefitRow = $this->objBenefit->getRow('agreeid', $id);

//loop to add all the wages to the table
foreach ($wageList as $list) 
{
	$class = ($class=='odd')? 'even' : 'odd';
	$link->link = $list['name'];
	$link->link($this->uri(array('action'=>'wage', 'wageId'=>$list['id'], 'agreeId'=>$id, 'benefitId'=>$benefitRow['nameid'], 'selected'=>'init_10'),'award'));
	$objwagesTable->startRow($class);
	$objwagesTable->addCell($link->show());
	$objwagesTable->addCell(number_format($list['rate'],2));
	$objwagesTable->addCell($list['notes']);
	$objwagesTable->addCell($objIcon->getDeleteIconWithConfirm($list['id'],array('action'=>'deletewage','wageId'=>$list['id'], 'agreeId'=>$agree['id'], 'unitId'=>$agree['unitid'], 'selected'=>'init_10' ),'award'));
	$objwagesTable->endRow();	
}

$objwagesTable->startRow();
$objwagesTable->addCell("");
$objwagesTable->addCell("<br />");
$objwagesTable->addCell("<br />");
$objwagesTable->endRow();

//Add a new wage link
$linkaddWage = new link($this->uri(array('action'=>'addwage','agreeId'=>$id, 'unitId'=>$agree['unitid'], 'selected'=>'init_10'),'award'));
$linkaddWage->link = $this->objLanguage->languageText('mod_lrs_add_wage_link', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$objwagesTable->startRow();
$objwagesTable->addCell($linkaddWage->show().' / '.$linkback->show());
$objwagesTable->addCell("<br />");
$objwagesTable->addCell("<br />");
$objwagesTable->addCell($linkExit->show(), NULL, 'top', 'right');
$objwagesTable->endRow();

$objconfirmForm->addToForm($objconfirmTable->show());

//create heading
$headerWage = $this->getObject('htmlheading','htmlelements');
$headerWage->type = 3;
$headerWage->str = $this->objLanguage->languageText('mod_lrs_wages_heading', 'award');
$objconfirmForm->addToForm($headerWage->show());

$objconfirmForm->addToForm($objwagesTable->show());

echo $objconfirmForm->show();

?>