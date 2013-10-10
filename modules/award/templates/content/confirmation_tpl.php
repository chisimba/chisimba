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
* confirmation template for the LRS Wages
* This template is used as an inbetween for when a bargaining unit is created with a similar name in the database. The user can choose to either create the desired bargaining unit or select the unit which has a similar name which already exists 
* Author Brent van Rensburg
*/

//Load classes 
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');

//Create form
$objconfirmForm = new form('lrsadmin');

$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_confimation_header', 'award');

//Array holds the database name and the name of the unit created by the user
$rep = array('DBUNIT' => "<strong>$dbunit</strong>", 'UNIT' => "<strong>$unit</strong>");

$message = "<span class = 'error'>".$this->objLanguage->code2Txt("mod_lrs_confirmation", 'award', $rep)."</span>";
$questionYes = $this->objLanguage->code2Txt("mod_lrs_confirm_yes", 'award', $rep);
$questionNo = $this->objLanguage->code2Txt("mod_lrs_confirm_no", 'award', $rep);


//The yes form is created so that when the yes button is pushed it can be sent to the action 'yes'
$objYesForm = new form('lrsadmin', $this->uri(array('action'=>'yes','selected'=>'init_10'), 'award'));

$btnYes = new button('yes');
$btnYes->setToSubmit();
$btnYes->setValue(' '.$this->objLanguage->languageText("word_yes").' ');

//The bargaining unit is hidden to pass to the next action
$txthiddendbUnit = new textinput('orgUnitId', $orgUnitId, 'hidden');

$objYesForm->addToForm($btnYes->show());
$objYesForm->addToForm($txthiddendbUnit->show());

//The no form is created so that when the no button is pushed it can be sent to the action 'no'
$objNoForm = new form('lrsadmin', $this->uri(array('action'=>'no', 'selected'=>'init_10'), 'award'));

$btnNo = new button('no');
$btnNo->setToSubmit();
$btnNo->setValue($this->objLanguage->languageText("word_no"));

//The bargaining unit name is hidden to pass to the next action
$txthiddenUnit = new textinput('unitName', $unit, 'hidden');

$objNoForm->addToForm($btnNo->show());
$objNoForm->addToForm($txthiddenUnit->show());

//Table to hold both forms with buttons
$objYesNoTable = new htmltable('yesno');

$objYesNoTable->startRow();
$objYesNoTable->addCell("<i>".$questionYes."<i>");
$objYesNoTable->endRow();

$objYesNoTable->startRow();
$objYesNoTable->addCell($objYesForm->show());
$objYesNoTable->endRow();


$objYesNoTable->startRow();
$objYesNoTable->addCell("<i>".$questionNo."</i>");
$objYesNoTable->endRow();

$objYesNoTable->startRow();
$objYesNoTable->addCell($objNoForm->show());
$objYesNoTable->endRow();


echo $header->show()."$message<br /><br />";
echo $objYesNoTable->show();
?>