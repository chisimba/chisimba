<?php

$this->loadclass('link', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');


$table=$this->getObject('htmltable','htmlelements');

$textinput = new textinput('donor');
$textinput->size = 60;
$table->startRow();
$table->addCell("<b>Donor Name or Part of name</b>");
$table->addCell($textinput->show());
$table->endRow();

$form = new form('filterform', $this->uri(array('action' => 'filterbydonor')));

$button = new button('addgift', "View");
$button->setToSubmit();
$form->addToForm($table->show());
$form->addToForm('<br/>'.$button->show());

echo $form->show();
?>
