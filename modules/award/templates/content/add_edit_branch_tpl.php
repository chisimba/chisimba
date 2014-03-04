<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS org
*/

/**
* Add Edit branch list template for the LRS org
* Author Brent van Rensburg
*/

//Set layout template
//$this -> setLayoutTemplate('layout_tpl.php');

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

$scriptRule = '<script type="text/javascript" src="modules/lrsadmin/resources/addRule.js"></script>';
//add to header
$this->appendArrayVar('headerParams', $scriptRule);

if(isset($branchId))
{
	$objAddEditBranchForm = new form('lrsorg', $this->uri(array('action'=>'editbranch', 'branchId'=>$branchId, 'unionId'=>$unionId,'selected'=>'init_10')));
}
else
{
	$objAddEditBranchForm = new form('lrsorg', $this->uri(array('action'=>'addbranch', 'unionId'=>$unionId,'selected'=>'init_10')));
}

$name = $this->objLanguage->languageText('word_name');
$region = $this->objLanguage->languageText('word_region');
$telephone = $this->objLanguage->languageText('word_telephone');
$fax = $this->objLanguage->languageText('word_fax');
$website = $this->objLanguage->languageText('mod_lrsorg_website', 'award');
$email = $this->objLanguage->languageText('word_email');
$address1 = $this->objLanguage->languageText('mod_lrsorg_address1', 'award');
$address2 = $this->objLanguage->languageText('mod_lrsorg_address2', 'award');
$postalLine = $this->objLanguage->languageText('word_postline');
$postalTown = $this->objLanguage->languageText('word_city');
$postalCode =  $this->objLanguage->languageText('mod_lrsorg_postalCode', 'award');

$msgName = $this->objLanguage->languageText('mod_lrsorg_branchnamerequired', 'award');
$msgRegion = $this->objLanguage->languageText('mod_lrsorg_regionrequired', 'award');

//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
if (isset($branchId)){
	$header->str = $this->objLanguage->languageText('mod_lrsorg_edit_branch', 'award');
	$objAddEditBranchForm->addToForm($header->show());
}
else 
{
	$header->str = $this->objLanguage->languageText('mod_lrsorg_add_branch', 'award');
	$objAddEditBranchForm->addToForm($header->show());
}

$objaddeditTable = new htmlTable('lrsorg');
$objaddeditTable->cellspacing = 2;
$objaddeditTable->cellpadding = '2';
$objaddeditTable->width = '90%';

$objaddeditHeadTable = new htmlTable('lrsorg');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrsorg_lbl_head_branch', 'award')."</i>");
$objaddeditHeadTable->addCell('');
$objaddeditHeadTable->endRow();

if(isset($branchId))
{

	$branchRow = $this->objDbBranch->getRow('id', $branchId);
	$districtRow = $this->objDistrict->getRow('id', $branchRow['districtid']);
	$regionRow = $this->objRegion->getRow('id', $districtRow['regionid']);

	
	$setName = $branchRow['name'];
	$setTelephone = $branchRow['telephone'];
	$setFax = $branchRow['fax'];
	$setWebsite = $branchRow['url'];
	$setEmail = $branchRow['email'];
	$setAddress1 = $branchRow['addressline1'];
	$setAddress2 = $branchRow['addressline2'];
	$setTown = $branchRow['postaltown'];
	$setCode = $branchRow['postalcode'];
}
else
{
	$setName = '';
	$setTelephone = '';
	$setFax = '';
	$setWebsite = '';
	$setEmail = '';
	$setAddress1 = '';
	$setAddress2 = '';
	$setTown = '';
	$setCode = '';
}

$txtName = new textinput('name', $setName);

$objaddeditTable->startRow();
$objaddeditTable->addCell($name. ':', NULL, NULL, NULL, 'odd');
$objaddeditTable->addCell($txtName->show());
$objaddeditTable->endRow();

//create dropdown
$objRegion = new dropdown('drpRegion');
$allRegions = $this->objRegion->getAll("ORDER BY name");

//default selected option for drodown
if(isset($branchId))
{
	$objRegion->addOption($regionRow['id'], $regionRow['name']);
}
else
{
	$objRegion->addOption('-1',$this->objLanguage->languageText('mod_lrs_index_select_one', 'award'));
}

foreach ($allRegions as $regions)
{
	$objRegion->addOption($regions['id'], $regions['name']);
}

$objaddeditTable->startRow();
$objaddeditTable->addCell($region. ':','','top','','odd');
$objaddeditTable->addCell($objRegion->show());
$objaddeditTable->endRow();

$txtTelephone = new textinput('telephone', $setTelephone);

$objaddeditTable->startRow();
$objaddeditTable->addCell($telephone. ':','','','','odd');
$objaddeditTable->addCell($txtTelephone->show());
$objaddeditTable->endRow();

$txtFax = new textinput('fax', $setFax);

$objaddeditTable->startRow();
$objaddeditTable->addCell($fax. ':','','','','odd');
$objaddeditTable->addCell($txtFax->show());
$objaddeditTable->endRow();

$txtWebsite = new textinput('website', $setWebsite);

$objaddeditTable->startRow();
$objaddeditTable->addCell($website. ':','','','','odd');
$objaddeditTable->addCell($txtWebsite->show());
$objaddeditTable->endRow();

$txtEmail = new textinput('email', $setEmail);

$objaddeditTable->startRow();
$objaddeditTable->addCell($email. ':','','','','odd');
$objaddeditTable->addCell($txtEmail->show());
$objaddeditTable->endRow();

$txtAddress1 = new textarea('address1', $setAddress1);

$objaddeditTable->startRow();
$objaddeditTable->addCell($address1. ':','','top','','odd');
$objaddeditTable->addCell($txtAddress1->show());
$objaddeditTable->endRow();

$txtAddress2 = new textarea('address2', $setAddress2);

$objaddeditTable->startRow();
$objaddeditTable->addCell($address2. ':','','top','','odd');
$objaddeditTable->addCell($txtAddress2->show());
$objaddeditTable->endRow();

$txtTown = new textinput('town', $setTown);

$objaddeditTable->startRow();
$objaddeditTable->addCell($postalTown. ':','','','','odd');
$objaddeditTable->addCell($txtTown->show());
$objaddeditTable->endRow();

$txtCode = new textinput('code', $setCode);

$objaddeditTable->startRow();
$objaddeditTable->addCell($postalCode. ':','','','','odd');
$objaddeditTable->addCell($txtCode->show());
$objaddeditTable->endRow();

$objaddeditTable->startRow();
$objaddeditTable->addCell("<br />");
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$btnSubmit = new button('submitvalues');
$btnSubmit->setToSubmit();
$btnSubmit->setValue(' '.$this->objLanguage->languageText("word_submit").' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewbranch', 'unionId'=>$unionId, 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

$objaddeditTable->startRow();
$objaddeditTable->addCell($btnSubmit->show().'  '.$btnCancel->show());
$objaddeditTable->addCell("<br />");
$objaddeditTable->endRow();

$objAddEditBranchForm->addRule('name',$this->objLanguage->languageText('mod_lrs_decent_work_msgName','award'),'required');
$objAddEditBranchForm->addRule('drpRegion',$this->objLanguage->languageText('mod_lrs_region_msg','award'),'select');
$objAddEditBranchForm->addToForm($objaddeditHeadTable->show());
$objAddEditBranchForm->addToForm($objaddeditTable->show());

echo $objAddEditBranchForm->show();
?>