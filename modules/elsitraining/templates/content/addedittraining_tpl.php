<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = 'Create Training';

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$editor = $this->newObject('htmlarea', 'htmlelements');
$editor->name = 'description';
$editor->height = '100px';
$editor->width = '550px';
$editor->setMCQToolBar();

$table->startRow();
$table->addCell("<b>Description</b>");
$table->addCell($editor->show());
$table->endRow();

$table->startRow();
$table->addCell('<b>Time Start</b>');
$objDateTime = $this->getObject('dateandtime', 'utilities');
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'starttime';

$table->addCell($objDatePicker->show());
$table->endRow();

$table->startRow();
$table->addCell('<b>Time End</b>');
$objDateTime = $this->getObject('dateandtime', 'utilities');
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'endtime';

$table->addCell($objDatePicker->show());
$table->endRow();

$textinput = new textinput('venue');
$textinput->size = 60;

$table->startRow();
$table->addCell("<b>Venue</b>");
$table->addCell($textinput->show());
$table->endRow();

$rlimit = new dropdown('maxlimit');
for ( $counter = 5; $counter <= 15; $counter += 1) {
    $rlimit->addOption($counter);
}
//$rlimit->addOption("Select ...");
//$rlimit->addOption("Sponsorship");
//$rlimit->addOption("Individual");
//$rlimit->addOption("Group");

$table->startRow();
$table->addCell("<b>Type</b>");
$table->addCell($rlimit->show());
$table->endRow();

$textinput = new textinput('contactperson');
$textinput->size = 12;

$table->startRow();
$table->addCell("<b>Contact Person (Staff number)</b>");
$table->addCell($textinput->show());
$table->endRow();


$form = new form('addedittraining', $this->uri(array('action' => 'createtraining')));
$form->addToForm($table->show());

$button = new button('create', 'Create');
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show());


echo $form->show();
?>
