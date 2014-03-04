<?php



$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');


$objIcon = $this->newObject('geticon', 'htmlelements');

$header = new htmlheading();
$header->type = 1;

if ($mode == 'add') {
    $header->str = $this->objLanguage->languageText('mod_worksheet_createnewworksheet', 'worksheet', 'Create New Worksheet');
    $formAction = 'saveworksheet';
	$id = "";
	$activity_status = "";
} else {

    $header->str = 'Edit Worksheet';
    $formAction = 'updateworksheet';
	$formAction = 'saveworksheetedit';
	$activity_status = $worksheet['activity_status'];
	$id = $worksheet['id'];
}

$objStepMenu = $this->newObject('stepmenu', 'navigation');
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_worksheetinfo', 'worksheet', 'Worksheet Information'), $this->objLanguage->languageText('mod_worksheet_worksheetinfo_desc', 'worksheet', 'Add Information about the Worksheet'));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_addquestions', 'worksheet', 'Add Questions'), $this->objLanguage->languageText('mod_worksheet_addquestions_desc', 'worksheet', 'Add Questions and Mark Allocation to the worksheet'));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_activateworksheet', 'worksheet', 'Activate Worksheet'), $this->objLanguage->code2Txt('mod_worksheet_activateworksheet_desc', 'worksheet', NULL, 'Allow [-readonlys-] to start answering worksheet'));

echo $objStepMenu->show();

echo '<br />'.$header->show();


$form = new form ('updateworksheet', $this->uri(array('action'=>$formAction, 'id' => $id, 'activity_status' => $activity_status)));

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_worksheet_worksheetname', 'worksheet', 'Worksheet Name'), 200);

$textinput = new textinput('title');
$textinput->size = 60;
($mode == 'edit') ? $textinput->value = $worksheet['name'] : "";

$table->addCell($textinput->show(), NULL, NULL, NULL, NULL, 'colspan="2"');
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_worksheet_worksheetdesc', 'worksheet', 'Worksheet Description'));
$htmlArea = $this->newObject('htmlarea', 'htmlelements');
$htmlArea->name = 'description';
($mode == 'edit') ? $htmlArea->value = $worksheet['description'] : "";
$table->addCell($htmlArea->show(), NULL, NULL, NULL, NULL, 'colspan="2"');
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_worksheet_closingdatetime', 'worksheet', 'Closing Date and Time'));

$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objTimePicker = $this->newObject('timepicker', 'htmlelements');

if($mode == 'edit')
{
	$dt = split(" ", $worksheet['closing_date']);
	$date = split(":", $dt[0]);
	$datestr = $date[0]."-".$date[1]."-".$date[2];
	$time = split(":", $dt[1]);

	$objDatePicker->setDefaultDate($dt[0]);
	$objTimePicker->setSelected($dt[1]);
}
$table->addCell($objDatePicker->show(), 250);
$table->addCell($objTimePicker->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_worksheet_percfinalmark', 'worksheet', 'Percentage Final Mark'));

$textinput = new textinput('percentage');
($mode == 'edit') ? $textinput->value = $worksheet['percentage'] : "";
$table->addCell($textinput->show(), NULL, NULL, NULL, NULL, 'colspan="2"');
$table->endRow();


$table->startRow();
$table->addCell('&nbsp;');

$submitButton = new button('saveworksheet', $this->objLanguage->languageText('mod_worksheet_save', 'worksheet'));
$submitButton->setToSubmit();

$cancelButton = new button('cancel', $this->objLanguage->languageText('mod_worksheet_cancel', 'worksheet'));
$returnUrl = $this->uri(array('action' => 'home'));
$cancelButton->setOnClick("javascript: window.location='{$returnUrl}';");

$table->addCell($submitButton->show().'&nbsp;'.$cancelButton->show(), NULL, NULL, NULL, NULL, 'colspan="2"');
$table->endRow();

$form->addToForm($table->show());

$form->addRule('title', $this->objLanguage->languageText('mod_worksheet_validation_name', 'worksheet', 'Please enter the name of the worksheet'), 'required');
$form->addRule('percentage', $this->objLanguage->languageText('mod_worksheet_finalmark_num', 'worksheet', 'Percentage Final Mark should be a number'), 'numeric');
$form->addRule('percentage', $this->objLanguage->languageText('mod_worksheet_validation_finalmark', 'worksheet', 'Please enter Percentage Final Mark'), 'required');

echo $form->show();

?>