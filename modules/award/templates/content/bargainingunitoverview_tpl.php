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
* Bargaining unit overview template for the LRS Wages
* Gives an overview of the selected or created bargaining unit and a list of the agreements associated with it, from here the user can choose to view an agreement from the list or add an agreement
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
$this->loadClass('getIcon', 'htmlelements');
$this->loadClass('checkBox', 'htmlelements');

//Create form
$objoverviewForm = new form('lrsadmin', $this->uri(array('action'=>'agreementdetails', 'selected'=>'init_10'), 'award'));

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_overview_header', 'award');
$objoverviewForm->addToForm($header->show());

//Create a table for the header
$objoverviewTable = new htmlTable('lrsadmin');
$objoverviewTable->cellspacing = 2;

$lblbargainingUnit = new label($this->objLanguage->languageText('mod_lrs_confirm_unit_Type' , 'award'), NULL);
$lbltradeUnion = new label($this->objLanguage->languageText('mod_lrs_trade_union', 'award'), NULL);
$industry = new label($this->objLanguage->languageText('mod_lrs_industry', 'award'), NULL);
$lblname = new label($this->objLanguage->languageText('mod_lrs_agree_list_name', 'award'), NULL);
$lblmonths = new label($this->objLanguage->languageText('mod_lrs_agree_list_months', 'award'), NULL);
$lbldate = new label($this->objLanguage->languageText('mod_lrs_agree_list_date', 'award'), NULL);
$lblnotes = new label($this->objLanguage->languageText('mod_lrs_agree_list_notes', 'award'), NULL);
$lbldelete = new label($this->objLanguage->languageText('mod_lrs_agree_delete', 'award'), NULL);
$lblnumWorkers = $this->objLanguage->languageText('mod_lrs_no_Workers', 'award');
$lblregion = $this->objLanguage->languageText('mod_lrs_region_Type', 'award');
$lblunitNotes = new label($this->objLanguage->languageText('mod_lrs_unit_Notes', 'award'), NULL);

$objoverviewTable->startHeaderRow();
$objoverviewTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_table_header', 'award'), '27%');
$objoverviewTable->addHeaderCell("<br />");
$objoverviewTable->endHeaderRow();

$orgUnitInfo = $this->objDbUnit->getRow('id', $id);

$objoverviewTable->startRow();
$objoverviewTable->addCell($lblbargainingUnit->show(), NULL, NULL, NULL, 'odd');
$objoverviewTable->addCell($orgUnitInfo['name'], NULL, NULL, NULL, 'even');
$objoverviewTable->endRow();

//Get the branch, party and tradeunion ids
$orgUnitInfo = $this->objDbUnit->getRow('id', $id);
$objUnitBranch = $this->getObject('dbunitbranch','awardapi');
$branchId = $objUnitBranch->getRow('unitid', $id);
$partyId = $this->objDbBranch->getRow('id', $branchId['branchid']);
$regionId = $this->objArea->getRow('unitid',$id);
$objRegion = $this->getObject('dbregion','awardapi');
$region = $objRegion->getRow('id',$regionId['regionid']);

//$regionId = $this->objRegion->getRow('id', $region['regionid']);
$tradeUnion = $this->objDbParty->getRow('id', $partyId['partyid']);
$agreeList = $this->objAgree->getAll("WHERE unitid = '$id' ORDER BY implementation DESC");
$agreementInfo = current($agreeList);

$noAgree = $this->objLanguage->languageText('phrase_noagreements');
$recentDate = ($agreementInfo['implementation'])? date("Y", strtotime($agreementInfo['implementation'])) : "<i>$noAgree</i>";

$objoverviewTable->startRow();
$objoverviewTable->addCell($lbltradeUnion->show(), NULL, NULL, NULL, 'odd');
$objoverviewTable->addCell($tradeUnion['name'], NULL, NULL, NULL, 'even');
$objoverviewTable->endRow();

$objUnitSic = $this->getObject('dbunitsic','awardapi');
$objSicDiv = $this->getObject('dbsicdiv','awardapi');
$objSicMajDiv = $this->getObject('dbsicmajordiv','awardapi');
$objSicMajGrp = $this->getObject('dbsicmajorgroup','awardapi');
$objSicSubGrp = $this->getObject('dbsicsubgroup','awardapi');
$objSicGrp = $this->getObject('dbsicgroup','awardapi');

$bargUnitSicRow = $objUnitSic->getRow('unitid', $id);
$sicMajDiv = $objSicMajDiv->getRow('id', $bargUnitSicRow['major_divid']);
$sicDiv = $objSicDiv->getRow('id', $bargUnitSicRow['divid']);
$sicMajGroup = $objSicMajGrp->getRow('id', $bargUnitSicRow['major_groupid']);
$sicGroup = $objSicGrp->getRow('id', $bargUnitSicRow['groupid']);
$sicSubGroup = $objSicSubGrp->getRow('id', $bargUnitSicRow['sub_groupid']);

//Use both the description of the major_divId and divId  

$sicData = '';
$code ='';
if (isset($sicMajDiv)) {
	$code = $sicMajDiv['code'].'0000';
	$sicData .= "{$code} - {$sicMajDiv['description']}<br />";
}
if (isset($sicDiv)) {
	$code = $sicMajDiv['code'].$sicDiv['code'].'000';
	$sicData .= "{$code} - {$sicDiv['description']}<br />";
}
if (isset($sicMajGroup) && ($sicMajGroup['id'] != 'init_0')) {
	$code = $sicMajDiv['code'].$sicDiv['code'].$sicMajGroup['code'].'00';
	$sicData .= "{$code} - {$sicMajGroup['description']}<br />";
}
if (isset($sicGroup) && ($sicGroup['id'] != 'init_0')) {
	$code = $sicMajDiv['code'].$sicDiv['code'].$sicMajGroup['code'].$sicGroup['code'].'0';
	$sicData .= "{$code} - {$sicGroup['description']}<br />";
}
if (isset($sicSubGroup)&& ($sicSubGroup['id'] != 'init_0')) {
	$code = $sicMajDiv['code'].$sicDiv['code'].$sicMajGroup['code'].$sicGroup['code'].$sicSubGroup['code'];
	$sicData .= "{$code} - {$sicSubGroup['description']}<br />";
}

$objoverviewTable->startRow();
$objoverviewTable->addCell($industry->show(), null, 'top', NULL, 'odd');
$objoverviewTable->addCell($sicData);
//$objoverviewTable->addCell($sicSubDiv['description']. ' - ' .$sicMajDiv['description'], NULL, NULL, NULL, 'even');
$objoverviewTable->endRow();

$objoverviewTable->startRow();
$objoverviewTable->addCell($lblregion, NULL, NULL, NULL, 'odd');
$objoverviewTable->addCell($region['name']);
$objoverviewTable->endRow();

$objoverviewTable->startRow();
$objoverviewTable->addCell($lblmonths->show(), NULL, NULL, NULL, 'odd');
$objoverviewTable->addCell($agreementInfo['length'].' ('.$recentDate.')');
$objoverviewTable->endRow();

$objoverviewTable->startRow();
$objoverviewTable->addCell($lblnumWorkers, NULL, NULL, NULL, 'odd');
$objoverviewTable->addCell($agreementInfo['workers'].' ('.$recentDate.')');
$objoverviewTable->endRow();

$setDisplay = ($orgUnitInfo['active'] == 1)?
    $this->objLanguage->languageText('mod_lrs_active', 'award'):
    $this->objLanguage->languageText('mod_lrs_expired', 'award');

$objoverviewTable->startRow();
$objoverviewTable->addCell($this->objLanguage->languageText('mod_lrs_edit_status', 'award'), null,null,null,'odd');
$objoverviewTable->addCell($setDisplay);
$objoverviewTable->endRow();

$objoverviewTable->startRow();
$objoverviewTable->addCell($lblunitNotes->show(), NULL, NULL, NULL, 'odd');
$objoverviewTable->addCell($orgUnitInfo['notes']);
$objoverviewTable->endRow();

$linkEditBU = new link($this->uri(array('action'=>'editbargainingunit', 'unitid'=>$id, 'selected'=>'init_10'),'award'));
$linkEditBU->link = $this->objLanguage->languageText('mod_lrs_link_edit_bu', 'award');

$objoverviewTable->startRow();
$objoverviewTable->addCell($linkEditBU->show());
$objoverviewTable->addCell("<br />");
$objoverviewTable->endRow();

$objagreementsTable = $this->newObject('htmltable','htmlelements');
$objagreementsTable->cellspacing = 2;

$objagreementsTable->startHeaderRow();
$objagreementsTable->addHeaderCell($lblname->show(), '30%');
$objagreementsTable->addHeaderCell($lblmonths->show(), '10%');
$objagreementsTable->addHeaderCell($lbldate->show(), '15%');
$objagreementsTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_no_Workers', 'award'), '10');
//$objagreementsTable->addHeaderCell($this->objLanguage->languageText('mod_lrs_status'), '10%');
$objagreementsTable->addHeaderCell($lblnotes->show(), '20%');
$objagreementsTable->addHeaderCell($lbldelete->show(), '10%');
$objagreementsTable->endHeaderRow();

$link = new link();
$objIcon = $this->newObject('geticon','htmlelements');
$class = '';

//Create the agreement list with links to there overviews on each name
foreach ($agreeList as $list) 
{	
	//$expired = new checkBox("expired", null, $setDisplay);
	
	$class = ($class=='odd')? 'even' : 'odd';
	$link->link = $list['name'];
	$link->link($this->uri(array('action'=>'agreementoverview', 'id'=>$list['id'], 'unitId'=>$id, 'selected'=>'init_10'),'award'));
	$objagreementsTable->startRow($class);
	$objagreementsTable->addCell($link->show());
	$objagreementsTable->addCell($list['length']);
	$objagreementsTable->addCell($list['implementation']);
	$objagreementsTable->addCell($list['workers']);
	//$objagreementsTable->addCell($setDisplay);
	$objagreementsTable->addCell($list['notes']);
	$objagreementsTable->addCell($objIcon->getDeleteIconWithConfirm($list['id'],array('action'=>'deleteagreement','unitid'=>$id,'agreeid'=>$list['id'], 'selected'=>'init_10'),'award'));
	$objagreementsTable->endRow();	
}

//Hide the bargaining unit name to pass to the next action
$txtnamehidden = new textinput('unitName', $orgUnitInfo['name'], 'hidden');

//Link to add a new agreement
$linkcreate = new link($this->uri(array('action'=>'add', 'unitid'=>$id, 'selected' => 'init_10'),'award'));
$linkcreate->link = $this->objLanguage->languageText('mod_lrs_add_agreement', 'award');

//Link to go back one step in the chosen path
$linkback = new link($this->uri(array('action'=>'start', 'selected' => 'init_10'),'award'));
$linkback->link = $this->objLanguage->languageText('mod_lrs_link_back', 'award');

$linkExit = new link($this->uri(array('action'=>'admin', 'selected' => 'init_10'), 'award'));
$linkExit->link = $this->objLanguage->languageText("word_exit");

$objagreementsTable->startRow();
$objagreementsTable->addCell($txtnamehidden->show().$linkcreate->show().' / '.$linkback->show());
$objagreementsTable->addCell("<br />");
$objagreementsTable->addCell("<br />");
$objagreementsTable->addCell("<br />");
$objagreementsTable->addCell("<br />");
$objagreementsTable->addCell($linkExit->show(), NULL, 'top', 'right');
$objagreementsTable->endRow();

$objoverviewForm->addToForm($objoverviewTable->show());

$objoverviewForm->addToForm($objagreementsTable->show());

echo $objoverviewForm->show();
?>