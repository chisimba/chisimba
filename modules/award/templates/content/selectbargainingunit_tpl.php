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
* Select bargaining unit template for the Award Wages
* 
* This template allows the user to type in the first 3 letters of an agreement to filter the dropdown with the associated bargaining unit, from the the user can select a unit and proceed.
* The user can also create a unique unit by clicking on the create button and typing in the name in the shown textbox.
* 
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');

//Create form
$objselectForm = new form('lrsSelect', $this->uri(array('action'=>'bargainingunitoverview', 'selected'=>'init_10'),'award'));
$objcreateForm = new form('lrsCreate', $this->uri(array('action'=>'createbu', 'selected'=>'init_10'), 'award'));
//create heading
$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_add_agree_header', 'award');

//create text input
//input_unitfilter
$txtfilterDrop = new textinput('unitfilter');

//Create a table for the header
$objaddTable = new htmlTable('lrsadmin');
$objaddTable->width = '70%';
$objsubHeadingTable = new htmlTable('lrsadmin');
$objnewUnitTable = new htmlTable('lrsadmin');

//If the user enters a new unit which already exists in the database an error message is displayed
$objunit = new dropdown('id');
$message = '';
$error = $this->getParam('error');
if(isset($error)) {
	$unitId = $this->getParam('unitid');
	$unitName = $this->getParam('name');
    $message = "<span class = 'error'>".str_replace('[-BU-]',$unitName,$this->getParam('message'))."</span>";
	
	//default selected option for drodown
	$objunit->addOption($unitId, $unitName);
	$objunit->setSelected('-1');
	$objsubHeadingTable->startRow();
	$objsubHeadingTable->addCell($message);
	$objsubHeadingTable->endRow();
} else {
	
	//default selected option for drodown
	$objunit->addOption('-1',$this->objLanguage->languageText('mod_lrs_select', 'award'));
	$objunit->setSelected('-1');
}

//on pressing character key in text input the javascript function is called
$txtfilterDrop->extra = " onkeyup = \"javascript:updateUnitList(this.value)\"";

$lblorgUnit = new label($this->objLanguage->languageText('mod_lrs_org_unit', 'award'), NULL);

$objsubHeadingTable->startRow();
$objsubHeadingTable->addCell("<i>".$lblorgUnit->show()."</i>");
$objsubHeadingTable->endRow();

$objsubHeadingTable->startRow();
$objsubHeadingTable->addCell("<br />");
$objsubHeadingTable->endRow();

$objaddTable->startRow();
$objaddTable->addCell($txtfilterDrop->show(), '30%');
$objaddTable->addCell("<div id='myDiv'>".$objunit->show()."</div>", '70%');
$objaddTable->endRow();

//label to create a new bargaining unit with a div so it can be hidden until the create a new unit button is pressed
$lblcreateUnit = "<div id='create'"."<i>".$this->objLanguage->languageText('mod_lrs_add_org_unit', 'award')."</i></div>";

//input_addUnit
$txtaddUnit = new textinput('addUnit', NULL, 'text');

//This create button disables the current textinput and dropdown and shows the new text input which receives the new unit
$btncreate = new button('create');
$btncreate->setOnClick("javascript: showCreate()");
$btncreate->setValue($this->objLanguage->languageText("word_create"));
$btncreate->setId('createunitbutton');

//the variables to hold the messages to be alerted when the required rule is alerted
$inputRequired = $this->objLanguage->languageText("mod_lrs_unit_rule", 'award');
$newinputeRequired = $this->objLanguage->languageText("mod_lrspostlogin_selectagree", 'award');

//This select button selects the filtered down unit and proceeds to the next template
$btnselect = new button('select');
$btnselect->setOnClick("javascript:if(validSelection('input_id', '$newinputeRequired')) { $('form_lrsSelect').submit()}");
$btnselect->setValue($this->objLanguage->languageText("word_select"));
$btnselect->setId('selectexistingbutton');

$btnBack = new button('back');
$location = $this->uri(array('action'=>'admin', 'selected'=>'init_10'), 'award');
$btnBack->setOnClick("javascript:window.location='$location'");
$btnBack->setValue($this->objLanguage->languageText("word_exit"));

//This create new button creates a new unit. Once the first create button is pushed the new unit is entered and the button is pushed to proceed
$btncreateNew = new button('createnew');
$btncreateNew->setToSubmit();
$btncreateNew->setValue($this->objLanguage->languageText("phrase_createnew"));

//The cancel button is used when the user decides not to create a new unit but rather select a unit
$btncancel = new button('cancel');
$btncancel->setOnClick("javascript: hideCreate()");
$btncancel->setValue($this->objLanguage->languageText("word_cancel"));
$btncancel->setId('cancelnew');

$objaddTable->startRow();
$objaddTable->addCell("<br />");
$objaddTable->addCell("<br />");
$objaddTable->endRow();

$objaddTable->startRow();
$objaddTable->addCell($btncreate->show());
$objaddTable->addCell($btnselect->show());
$objaddTable->addCell($btnBack->show(),'', '', 'right');
$objaddTable->endRow();

$objnewUnitTable->startRow();
$objnewUnitTable->addCell($lblcreateUnit);
$objnewUnitTable->addCell("<br />");
$objnewUnitTable->endRow();

$objnewUnitTable->startRow();
$objnewUnitTable->addCell("<br />");
$objnewUnitTable->addCell("<br />");
$objnewUnitTable->endRow();

$objnewUnitTable->startRow();
$objnewUnitTable->addCell($txtaddUnit->show());
$objnewUnitTable->addCell("<br />");
$objnewUnitTable->endRow();

$objnewUnitTable->startRow();
$objnewUnitTable->addCell("<br />");
$objnewUnitTable->addCell("<br />");
$objnewUnitTable->endRow();

$objnewUnitTable->startRow();
$objnewUnitTable->addCell($btncreateNew->show().' '.$btncancel->show());
$objnewUnitTable->addCell("<br />");
$objnewUnitTable->endRow();

$objselectForm->addToForm($objaddTable->show());
$objcreateForm->addToForm($objnewUnitTable->show());
$objcreateForm->extra = "style='visibility: hidden'";
$objcreateForm->addRule('addUnit',$this->objLanguage->languageText('mod_lrs_new_unit_rule','award'),'required');
$content = $header->show().$objsubHeadingTable->show().$objselectForm->show().$objcreateForm->show();

$resourceURI = $this->getResourceURI("wageadmin.js");
$this->appendArrayVar("headerParams","<script type='text/javascript' src='$resourceURI'></script>");
echo $content;
?>