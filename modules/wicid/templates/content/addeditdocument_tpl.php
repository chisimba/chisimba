<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');

$this->setVar('pageSuppressXML', TRUE);

$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$action = 'registerdocument';
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');
if ($mode == 'edit') {
    $action = "updatedocument";
    $selected = $this->baseDir . $document['topic'];
}

if ($selected == '') {

    $folders = $this->__getdefaultfolder($this->baseDir);
    $selected = $folders[0];
}

$this->loadClass('dropdown', 'htmlelements');

$cfile = substr($selected, strlen($this->baseDir));

$xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Register New Document');
if ($mode == 'edit') {

    $xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Edit Document');
}
$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

// Opening date
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('<b>Entry Date</b>');
$objDateTime = $this->getObject('dateandtime', 'utilities');
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'entrydate';
if ($mode == 'edit') {
    $objDatePicker->setDefaultDate(substr($document['date_created'], 0, 10));
}


$table->addCell($objDatePicker->show());

$documentNumber = new dropdown('number');
$documentNumber->addOption("Select ...");
$documentNumber->addOption("S");
$documentNumber->addOption("C");
$documentNumber->addOption("A");

if ($mode == 'fixup') {
    $documentNumber->setSelected($number);
}
if ($mode == 'edit') {
    $documentNumber->setSelected(substr($document['refno'], 0, 1));
}
$table->startRow();
$table->addCell("<b>Number</b>");
if ($mode == 'edit') {
    $table->addCell($document['refno'] . '-' . $document['version']);
} else {
    $table->addCell($documentNumber->show());
}
$table->endRow();

$textinput = new textinput('department');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $document['department'];
}
if ($mode == "fixup") {
    $textinput->value = $department;
}
$table->startRow();
$table->addCell("<b>Originating Department</b>");
$table->addCell($textinput->show());
$table->endRow();

$textinput = new textinput('contact');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $document['contact_person'];
} else if ($mode == "fixup") {
    $textinput->value = $contact;
} else {
    $textinput->value = $this->objUser->fullname();
}
$table->startRow();
$table->addCell("<b>Contact person</b>");
$table->addCell($textinput->show());
$table->endRow();



$textinput = new textinput('telephone');
$textinput->size = 40;
if ($mode == 'edit') {
    $textinput->value = $document['telephone'];
} else {
    $textinput->value = $telephone;
}
$table->startRow();
$table->addCell("<b>Telephone number</b>");
$table->addCell($textinput->show());
$table->endRow();



$textinput = new textinput('title');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $document['docname'];
}if ($mode == 'fixup') {
    $textinput->value = $title;
}
$table->startRow();
$table->addCell("<b>Document Title</b>");
$table->addCell($textinput->show());
$table->endRow();


$group = new dropdown('group');
$group->addOption("Select ...");
$group->addOption("Public");
$group->addOption("Council");
$group->addOption("Administration");
if ($mode == 'fixup') {
    $group->setSelected($groupid);
}
if ($mode == 'edit') {
    $group->setSelected($document['groupid']);
}
$table->startRow();
$table->addCell("<b>Group</b>");
$table->addCell($group->show());
$table->endRow();

$table->startRow();
$table->addCell("<b>Topic</b>");
$table->addCell($this->objUtils->getTree('htmldropdown', $cfile, "none"));
$table->endRow();

$legend = "New Document";
if ($mode == 'edit') {
    $legend = "Edit document";
}
$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());

// Form
$form = new form('registerdocumentform', $this->uri(array('action' => $action, 'mode' => $oldmode, 'active' => $active, 'rcount' => $rcount, 'rowcount' => $rowcount, 'start' => $start)));

$hiddenSelected = new hiddeninput('selected', $cfile);
$form->addToForm($hiddenSelected->show());

//$form
if ($mode == 'edit') {
    $hiddenId = new hiddeninput('docid', $document['id']);
    $form->addToForm($hiddenId->show());
}


$efs = new fieldset();
$efs->setLegend('Errors');
if (count($errormessages) > 0) {

    $errorstr = '<ul>';

    foreach ($errormessages as $errormessage) {
        $errorstr.='<li class="error">' . $errormessage . '<li/>';
    }
    $errorstr.='</li>';
    $efs->addContent($errorstr);
    $form->addToForm($efs);
}
$form->addToForm($fs->show());

$button = new button('save', $this->objLanguage->languageText('mod_wicid_save', 'wicid', 'Save Document'));
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show());

if ($this->objUser->isAdmin()) {
    if ($mode == 'edit') {
        $atstatus = null;
        if ($astatus == $atstatus) {
            $button = new button('approve', "Approve");
            $uri = $this->uri(array('action' => 'approvedocument', 'id' => $document['id'], 'mode' => $oldmode, 'active' => $active, 'rcount' => $rcount, 'rowcount' => $rowcount, 'start' => $start));
            $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
            $form->addToForm($button->show());
        }

        $button = new button('reject', "Reject");
        $uri = $this->uri(array('action' => 'rejectdocument', 'id' => $document['id'], 'mode' => $oldmode, 'active' => $active, 'rcount' => $rcount, 'rowcount' => $rowcount, 'start' => $start));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $form->addToForm($button->show());
    }
}
$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array());
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>
