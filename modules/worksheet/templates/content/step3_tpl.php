<?php



$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('radio', 'htmlelements');


/*
$js = $this->getJavascriptFile('jquery/jquery.form.js', 'htmlelements');
$this->appendArrayVar('headerParams', $js);
$this->appendArrayVar('headerParams', $this->getJavaScriptFile('worksheet.js'));

$script = "jQuery('#form_addquestion').ajaxForm(options);";
$this->appendArrayVar('bodyOnLoad', $script);
*/

$objIcon = $this->newObject('geticon', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $worksheet['name'].' : '.$this->objLanguage->languageText('mod_worksheet_activatedeactivate', 'worksheet');

$objStepMenu = $this->newObject('stepmenu', 'navigation');

$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_worksheetinfo', 'worksheet', 'Worksheet Information'), $this->objLanguage->languageText('mod_worksheet_worksheetinfo_desc', 'worksheet', 'Add Information about the Worksheet'), $this->uri(array('action'=>'worksheetinfo', 'id'=>$id)));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_addquestions', 'worksheet', 'Add Questions'), $this->objLanguage->languageText('mod_worksheet_addquestions_desc', 'worksheet', 'Add Questions and Mark Allocation to the worksheet'), $this->uri(array('action'=>'managequestions', 'id'=>$id)));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_activateworksheet', 'worksheet', 'Activate Worksheet'), $this->objLanguage->code2Txt('mod_worksheet_activateworksheet_desc', 'worksheet', NULL, 'Allow [-readonlys-] to start answering worksheet'));

$objStepMenu->setCurrent(3);

echo $objStepMenu->show();

echo '<br />'.$header->show();

echo $this->objWashout->parseText($worksheet['description']);

$objDateTime = $this->getObject('dateandtime', 'utilities');

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date').'</strong>: '.$objDateTime->formatDate($worksheet['closing_date']), '55%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions').'</strong>: '.count($questions), '15%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_percentage', 'worksheet', 'Percentage').'</strong>: '.$worksheet['percentage'].'%', '15%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark').'</strong>: '.$worksheet['total_mark'], '15%');
$table->endRow();

echo $table->show();

echo '<hr />';

/*
$header = new htmlheading();
$header->type = 1;
$header->str = ;
echo $header->show();
*/

/*
$this->loadClass('fieldset', 'htmlelements');
*/

$form = new form ('activate', $this->uri(array('action'=>'updatestatus')));

$objElement = new radio('activity_status');
$objElement->addOption('inactive', $this->objWorksheet->getStatusText('inactive'));
$objElement->addOption('open', $this->objWorksheet->getStatusText('open'));
$objElement->addOption('closed', $this->objWorksheet->getStatusText('closed'));
$objElement->addOption('marked', $this->objWorksheet->getStatusText('marked'));
$objElement->setSelected($worksheet['activity_status']);
$objElement->setBreakSpace('<br />');

$table = $this->newObject('htmltable', 'htmlelements');
$table->width = '99%';
$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_worksheet_status', 'worksheet'));
$table->addCell($objElement->show());
$table->endRow();

//$form->addToForm($table->show());
//$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_worksheet_closingdatetime', 'worksheet'));
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->setDefaultDate(substr($worksheet['closing_date'], 0, 10));
$objTimePicker = $this->newObject('timepicker', 'htmlelements');
$objTimePicker->setSelected(substr($worksheet['closing_date'], 11));

$innerTable = $this->newObject('htmltable', 'htmlelements');
$innerTable->width = '99%';
$innerTable->startRow();
$innerTable->addCell($objDatePicker->show());
$innerTable->addCell($objTimePicker->show());
$innerTable->endRow();
$table->addCell($innerTable->show());

$table->endRow();

$table->startRow();
$button = new button('updateworksheet', $this->objLanguage->languageText('mod_worksheet_update', 'worksheet'));
$button->setToSubmit();
$table->addCell('&nbsp;');
$table->addCell($button->show());
$table->endRow();

$form->addToForm($table->show());

$hiddenInput = new hiddeninput('id', $id);
$form->addToForm($hiddenInput->show());

/*
$fieldset = new fieldset();
$fieldset->setLegend($this->objLanguage->languageText('mod_worksheet_statusclosingdateandtime', 'worksheet'));
$fieldset->addContent($form->show());
echo $fieldset->show();
*/

echo $form->show();

echo '<hr />';

/*
$editLink = new link ($this->uri(array('action'=>'editworksheet', 'id'=>$id)));
$editLink->link = $this->objLanguage->languageText('mod_worksheet_editworksheet', 'worksheet', 'Edit Worksheet');

$deleteLink = new link ($this->uri(array('action'=>'deleteworksheet', 'id'=>$id)));
$deleteLink->link = $this->objLanguage->languageText('mod_worksheet_deleteworksheet', 'worksheet', 'Delete Worksheet');
*/

$infoLink = new link ($this->uri(array('action'=>'worksheetinfo', 'id'=>$id)));
$infoLink->link = $this->objLanguage->languageText('mod_worksheet_worksheetinfo', 'worksheet', 'Worksheet Information');

$activateDeactivate = $this->objLanguage->languageText('mod_worksheet_activatedeactivateworksheet', 'worksheet', 'Activate / Deactivate Worksheet');

$questionLink = new link ($this->uri(array('action'=>'managequestions', 'id'=>$id)));
$questionLink->link = $this->objLanguage->languageText('mod_worksheet_addremovequestions', 'worksheet', 'Add / Remove Questions');

echo '<p>'.$infoLink->show().' | './*$editLink->show().' | '.$deleteLink->show().' | '.*/$questionLink->show().' | '.$activateDeactivate.'</p>';

$link = new link ($this->uri(NULL));
$link->link = $this->objLanguage->languageText('mod_worksheet_backtoworksheets', 'worksheet', 'Back to Worksheets');

echo '<p>'.$link->show().'</p>';

?>