<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');

$action = 'addLang';

if ($mode == 'edit') {
    $action = "update";
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

$textinput = new textinput('langid');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $lang['langid'];
}
if ($mode == "fixup") {
    $textinput->value = $langid;
}
$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_langadmin_langid', 'langadmin'));
$table->addCell($textinput->show());
$table->endRow();


$textinput = new textinput('name');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $lang['name'];
}
if ($mode == "fixup") {
    $textinput->value = $langid;
}
$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_langadmin_langname', 'langadmin'));
$table->addCell($textinput->show());
$table->endRow();


$textinput = new textinput('meta');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $lang['meta'];
}
if ($mode == "fixup") {
    $textinput->value = $meta;
}


$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_langadmin_langmeta', 'langadmin'));
$table->addCell($textinput->show());
$table->endRow();




$textinput = new textinput('errortext');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $lang['errortext'];
}
if ($mode == "fixup") {
    $textinput->value = $errortext;
}

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_langadmin_errortext', 'langadmin'));
$table->addCell($textinput->show());
$table->endRow();


$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());

// Form
$form = new form('addeditlang', $this->uri(array('action' => $action)));

//$form
if ($mode == 'edit') {
    $hiddenId = new hiddeninput('langid', $lang['langid']);
    $form->addToForm($hiddenId->show());
}

$efs = new fieldset();
$efs->setLegend($this->objLanguage->languageText('mod_langadmin_fieldsrequired', 'langadmin'));
if (count($errormessages) > 0) {

    // $errorstr = '<ul>';

    foreach ($errormessages as $errormessage) {
        $errorstr.='<div class="error">' . $errormessage . '</div>';
    }
    // $errorstr.='</li>';
    $efs->addContent($errorstr);
    $form->addToForm($efs);
}
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
