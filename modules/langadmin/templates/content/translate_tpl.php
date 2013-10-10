<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');

$action = 'addItem';

if ($mode == 'edit') {
    $action = "updateItem";
}

$this->loadClass('dropdown', 'htmlelements');



$xtitle = $this->objLanguage->languageText('mod_langadmin_addlanguage', 'langadmin');
if ($mode == 'edit') {

    $xtitle = $this->objLanguage->languageText('mod_langadmin_editlanguage', 'langadmin');
}
$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;
echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_langadmin_code', 'langadmin'));
$table->endRow();

$table->startRow();
$table->addCell($code);
$table->endRow();


$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_langadmin_description', 'langadmin'));
$table->endRow();

$table->startRow();
$table->addCell($description);
$table->endRow();


$textinput = new textarea('translation');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $tranlation;
}
if ($mode == "fixup") {
    $textinput->value = $tranlation;
}

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_langadmin_translation', 'langadmin'));
$table->endRow();

$table->startRow();
$table->addCell($textinput->show());
$table->endRow();




$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());

// Form
$form = new form('addeditlang', $this->uri(array('action' => $action)));


$hiddenId = new hiddeninput('code', $code);
$form->addToForm($hiddenId->show());


$form->addToForm($fs->show());

$button = new button('save', $this->objLanguage->languageText('mod_langadmin_save', 'langadmin'));
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show());


$button = new button('cancel', $this->objLanguage->languageText('mod_langadmin_cancel', 'langadmin'));
$uri = $this->uri(array());
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>
