<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');


$header = new htmlheading();
$header->type = 2;
$header->str = "Edit department";

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$textinput = new textinput('name');
$textinput->size = 60;
$textinput->value = $departmentname;

$table->startRow();
$table->addCell("<b>Department Name:</b>");
$table->addCell($textinput->show());
$table->endRow();


// Form
$form = new form('deptform', $this->uri(array('action' => "updatedepartment")));
$hiddenId = new hiddeninput('id', $departmentid);
$form->addToForm($hiddenId->show());


$efs = new fieldset();
$efs->setLegend('Edit Department');
$efs->addContent($table->show());
if (isset($errormessage)) {
    $form->addToForm('<div class="error"><strong>' . $errormessage . '</strong></div>');
}

$form->addToForm($efs->show());

$button = new button('save', $this->objLanguage->languageText('mod_gift_save', 'gift', 'Save'));
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show());

echo $form->show();
?>
