<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = 'De-Register Training';

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');


$textinput = new textinput('referencenum');
$textinput->size = 30;

$table->startRow();
$table->addCell("<b>Please enter reference number(received via email when you registered):</b>");
$table->endRow();

$table->startRow();
$table->addCell($textinput->show());
$table->endRow();

$form = new form('deregistertraining', $this->uri(array('action' => 'deregistertraining')));
$form->addToForm($table->show());

$button = new button('deregistertraining', 'De-Register');
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show());


echo $form->show();
?>