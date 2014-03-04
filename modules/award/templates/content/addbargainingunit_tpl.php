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
* add bargaining unit template for the LRS Wages
* This template is used to add a new bargainging unit. All dropdowns must be selected to continue
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

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
if(isset($unitId))
{
	$header->str = $this->objLanguage->languageText('mod_lrs_Edit_Unit_header', 'award');
	$objAddUnitForm = new form('lrsadmin', $this->uri(array('action'=>'updatebargainingunit', 'unitId'=>$unitId, 'selected'=>'init_10')));
} else {
	$header->str = $this->objLanguage->languageText('mod_lrs_Add_Unit_header', 'award');
	$objAddUnitForm = new form('lrsadmin', $this->uri(array('action'=>'updatebargainingunit', 'selected'=>'init_10')));
}


$objAddUnitForm->addToForm($header->show());

//Create a table for the header
$objAddUnitTable = new htmlTable('lrsadmin');
$objAddUnitTable->cellspacing = 2;

$lblbargainingUnit = new label($this->objLanguage->languageText('mod_lrs_confirm_unit_Type', 'award'), NULL);
$lbltradeUnionType = new label($this->objLanguage->languageText('mod_lrs_trade_union_Type', 'award'), NULL);
$lblpartyBranch = new label($this->objLanguage->languageText('mod_lrs_branch_Type', 'award'), NULL);
$lblregion = new label($this->objLanguage->languageText('mod_lrs_region_Type', 'award'), NULL);
$lbldistrict = new label($this->objLanguage->languageText('mod_lrs_district_Type', 'award'), NULL);
$lblunitNotes = new label($this->objLanguage->languageText('mod_lrs_unit_Notes', 'award'), NULL);
$lblsicMajorDiv = $this->objLanguage->languageText('mod_lrs_sicMajDiv', 'award');
$lblsicDiv = $this->objLanguage->languageText('mod_lrs_sicDiv', 'award');
$lblsicMajGrp = $this->objLanguage->languageText('mod_lrs_sicMajGrp', 'award');
$lblsicGrp = $this->objLanguage->languageText('mod_lrs_sicGroup', 'award');
$lblindustryCatagory = new label($this->objLanguage->languageText('mod_lrs_industry_cat_Type', 'award'), NULL);


//Message to be displayed when a rule is broken
$msgtradeUnionType = $this->objLanguage->languageText('mod_lrs_trade_union_msg', 'award');
$msgpartyBranch = $this->objLanguage->languageText('mod_lrs_branch_msg', 'award');
$msgindustryCatagory = $this->objLanguage->languageText('mod_lrs_industry_cat_msg', 'award');
$msgregion = $this->objLanguage->languageText('mod_lrs_region_msg', 'award');
$msgdistrict = $this->objLanguage->languageText('mod_lrs_district_msg', 'award');
$msgMajorDiv = $this->objLanguage->languageText('mod_lrs_majorDiv_msg', 'award');
$msgDiv = $this->objLanguage->languageText('mod_lrs_div_msg', 'award');
$defaultDrop = $this->objLanguage->languageText('mod_lrs_default_drop', 'award');
$setNotes = '';

if(isset($unitId))
{
	$unitRow = $this->objDbUnit->getRow('id', $unitId);
	$unit = $unitRow['name'];
	$unitBranchRow = $this->objUnitBranch->getRow('unitid', $unitId);
	$branchRow = $this->objDbBranch->getRow('id', $unitBranchRow['branchid']);
	$bargUnitSicRow = $this->objUnitSic->getRow('unitid', $unitId);

	$setParty = $branchRow['partyid'];
	$setBranch = $unitBranchRow['branchid'];
	$setMajDiv = $bargUnitSicRow['major_divid'];
	$setDiv = $bargUnitSicRow['divid'];
	$setMajorGroup = $bargUnitSicRow['major_groupid'];
	$setGroup = $bargUnitSicRow['groupid'];
	$setSubGroup = $bargUnitSicRow['sub_groupid'];
	$setNotes = $unitRow['notes'];

	$sql = "SELECT id, regionid FROM tbl_award_unit_region WHERE unitid = '$unitId'";
	$regionInfo = $this->objRegion->getArray($sql);
	$setRegion = current($regionInfo);

}

//get parties
$parties = $this->objDbParty->getAll("ORDER BY abbreviation ASC");

$unitBox = new textinput('unit', $unit);
$objAddUnitTable->startHeaderRow();
$objAddUnitTable->addHeaderCell($lblbargainingUnit->show());
$objAddUnitTable->addHeaderCell($unitBox->show());
$objAddUnitTable->endHeaderRow();

//use javascript and xajax to filter the next dropdown according to the selection
$dropUnionType = new dropdown('tradeUnionType');
$dropUnionType->extra = " onchange = \"javascript:populatePB(this.value)\"";
$dropUnionType->addOption('-1',$defaultDrop);
$dropUnionType->addFromDB($parties, 'abbreviation', 'id');
$dropUnionType->setSelected('-1');
if(isset($unitId)) {
	$dropUnionType->setSelected($setParty);
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lbltradeUnionType->show(), NULL, "", NULL, 'odd');
$objAddUnitTable->addCell($dropUnionType->show());
$objAddUnitTable->endRow();

$dropBranchName = new dropdown('branch');
$dropBranchName->addOption('-1',$defaultDrop);
if(isset($unitId)) {
	$content = $this->objDbBranch->getAll("WHERE partyid = '$setParty' ORDER BY name ASC");
	$dropBranchName->addFromDB($content, 'name', 'id');
	$dropBranchName->setSelected($setBranch);
} else {
	$dropBranchName->extra = "disabled";
	$dropBranchName->setSelected(-1);
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblpartyBranch->show(), NULL, "", NULL, 'odd');
$objAddUnitTable->addCell("<div id='myDiv_input_branch'>".$dropBranchName->show()."</div>");
$objAddUnitTable->endRow();

$objSicMajDiv = $this->getObject('dbsicmajordiv','awardapi');
$sicMajDivs = $objSicMajDiv->getAll("ORDER BY description ASC");
$dropSicMajDiv = new dropdown('majorDiv');
$dropSicMajDiv->extra = " onchange = \"javascript:populateSicDiv(this.value)\"";
//default selected option for drodown
$dropSicMajDiv->addOption('-1',$defaultDrop);
$dropSicMajDiv->setSelected('-1');
$dropSicMajDiv->addFromDB($sicMajDivs, 'description', 'id');
if(isset($unitId)) {
	$dropSicMajDiv->setSelected($setMajDiv);
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblsicMajorDiv, NULL, "", NULL, 'odd');
$objAddUnitTable->addCell($dropSicMajDiv->show());
$objAddUnitTable->endRow();

$objSicDiv = $this->getObject('dbsicdiv','awardapi');
$dropSicDiv = new dropdown('div');
$dropSicDiv->addOption('-1',$defaultDrop);

if(isset($unitId)) {
	$content = $objSicDiv->getAll("WHERE major_divid = '$setMajDiv' ORDER BY description ASC");
	$dropSicDiv->extra = "onchange = \"javascript:populateSicMajGrp(this.value)\"";
	foreach ($content as $c) {
		//The description in the dropdown cannot exceed 55 characters
		if (strlen($c['description']) > 55)	{
			$c['description'] = substr($c['description'],0,52).'...';
		}
		$dropSicDiv->addOption($c['id'],$c['description']);
	}
	$dropSicDiv->setSelected($setDiv);
} else {
	$dropSicDiv->extra = "disabled";
	$dropSicDiv->setSelected('-1');
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblsicDiv, NULL, "", NULL, 'odd');
$objAddUnitTable->addCell("<div id='myDiv_input_div'>".$dropSicDiv->show()."</div>");
$objAddUnitTable->endRow();

$objSicMajGrp = $this->getObject('dbsicmajorgroup','awardapi');
$dropSicMajGrp = new dropdown('majGrp');
$dropSicMajGrp->addOption('-1',$defaultDrop);
	
if(isset($unitId)) {
	$content = $objSicMajGrp->getAll("WHERE divid = '$setDiv' ORDER BY description ASC");
	$dropSicMajGrp->extra = "onchange = \"javascript:populateSicGrp(this.value)\"";
	foreach ($content as $c) {
		//The description in the dropdown cannot exceed 55 characters
		if (strlen($c['description']) > 55) {
			$c['description'] = substr($c['description'],0,52).'...';
		}
		$dropSicMajGrp->addOption($c['id'],$c['description']);
	}
	$dropSicMajGrp->setSelected($setMajorGroup);
} else {
	$dropSicMajGrp->extra = "disabled";
	$dropSicMajGrp->setSelected(-1);
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblsicMajGrp, NULL, "", NULL, 'odd');
$objAddUnitTable->addCell("<div id='myDiv_input_majGrp'>".$dropSicMajGrp->show()."</div>");
$objAddUnitTable->endRow();

$objSicGrp = $this->getObject('dbsicgroup','awardapi');
$dropSicGrp = new dropdown('grp');
$dropSicGrp->addOption('-1',$defaultDrop);
if(isset($unitId)) {
	$content = $objSicGrp->getAll("WHERE major_groupid = '$setMajorGroup' ORDER BY description ASC");
	$dropSicGrp->extra = "onchange = \"javascript:populateSicSubGrp(this.value)\"";
	foreach ($content as $c) {
		//The description in the dropdown cannot exceed 55 characters
		if (strlen($c['description']) > 55) {
			$c['description'] = substr($c['description'],0,52).'...';
		}
		$dropSicGrp->addOption($c['id'],$c['description']);
	}
	$dropSicGrp->setSelected($setGroup);
} else {
	$dropSicGrp->extra = "disabled";
	$dropSicGrp->setSelected('-1');
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblsicGrp, NULL, "", NULL, 'odd');
$objAddUnitTable->addCell("<div id='myDiv_input_grp'>".$dropSicGrp->show()."</div>");
$objAddUnitTable->endRow();

$objSicSubGrp = $this->getObject('dbsicsubgroup','awardapi');

$dropSicSub = new dropdown('subGrp');
$dropSicSub->addOption('-1',$defaultDrop);
if(isset($unitId)) {
	$content = $objSicSubGrp->getAll("WHERE groupid = '$setGroup' ORDER BY description ASC");
	foreach ($content as $c) {
		//The description in the dropdown cannot exceed 55 characters
		if (strlen($c['description']) > 55) {
			$c['description'] = substr($c['description'],0,52).'...';
		}
		$dropSicSub->addOption($c['id'],$c['description']);
	}
	$dropSicSub->setSelected($setSubGroup);
} else {
	$dropSicSub->extra = "disabled";
	$dropSicSub->setSelected('-1');
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblindustryCatagory->show(), NULL, "", NULL, 'odd');
$objAddUnitTable->addCell("<div id='myDiv_input_subGrp'>".$dropSicSub->show()."</div>");
$objAddUnitTable->endRow();

$region = $this->objDbRegion->getAll("ORDER BY name ASC");

//use javascript and xajax to filter the next dropdown according to the selection
$dropRegion = new dropdown('region');
$dropRegion->addOption('-1',$defaultDrop);
$dropRegion->addFromDB($region, 'name', 'id');
$dropRegion->setSelected('-1');
if(isset($unitId)) {
	$dropRegion->setSelected($setRegion['regionid']);
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblregion->show(), NULL, "", NULL, 'odd');
$objAddUnitTable->addCell($dropRegion->show());
$objAddUnitTable->endRow();

/*if(isset($unitId)) {
	if($unitRow['active'] == 1){
		$setDisplay = false;
	} else {
		$setDisplay = true;
	}
	$expired = new checkBox("expired", null, $setDisplay);
	$objAddUnitTable->startRow();
	$objAddUnitTable->addCell($this->objLanguage->languageText('mod_lrs_edit_expired', 'award'),'','','','odd');
	$objAddUnitTable->addCell($expired->show());
	$objAddUnitTable->endRow();
}*/

$txtunitNotes = new textarea('unitNotes',$setNotes);

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($lblunitNotes->show(), NULL, NULL, NULL, 'odd');
$objAddUnitTable->addCell($txtunitNotes->show());
$objAddUnitTable->endRow();

//once the submit button is selected a javascript function is called to validate that all the dropdowns have been selected
$btnSubmit = new button('submitUnit');
$btnSubmit->setToSubmit();
$btnSubmit->setValue($this->objLanguage->languageText("word_submit"));

if(isset($unitId)) {
	$btnCancel = new button('cancel');
	$location = $this->uri(array('action'=>'bargainingunitoverview', 'id'=>$unitId, 'selected'=>'init_10'));
	$btnCancel->setOnClick("javascript:window.location='$location'");
	$btnCancel->setValue($this->objLanguage->languageText("word_back"));
} else {
	$btnCancel = new button('cancel');
	$location = $this->uri(array('action'=>'start', 'selected'=>'init_10'));
	$btnCancel->setOnClick("javascript:window.location='$location'");
	$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');
}

if(isset($unitId)) {
	$txthiddenRegion = new textinput('regionId', $setRegion['id'], 'hidden');
	$objAddUnitTable->startRow();
	$objAddUnitTable->addCell($txthiddenRegion->show());
	$objAddUnitTable->addCell("<br />");
	$objAddUnitTable->endRow();
}

$objAddUnitTable->startRow();
$objAddUnitTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objAddUnitTable->addCell("<br />");
$objAddUnitTable->endRow();

$objAddUnitForm->addToForm($objAddUnitTable->show());

$script = $this->getResourceUri('wageadmin.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$script'></script>");
echo $objAddUnitForm->show();
?>