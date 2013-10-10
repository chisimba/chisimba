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
if ($mode == "add") {
    $action = 'save';
}
if ($mode == 'edit') {
    $action = "update";
}

$this->loadClass('dropdown', 'htmlelements');

$cfile = substr($selected, strlen($this->baseDir));

$xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'New Gift');
if ($mode == 'edit') {

    $xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Edit Gift');
}
$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle . "&nbsp;-&nbsp;" . $this->objDepartments->getDepartmentName($this->getSession("departmentid"));

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$textinput = new textinput('giftname');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $gift['giftname'];
}
if ($mode == "fixup") {
    $textinput->value = $name;
}
$table->startRow();
$table->addCell("<b>Gift Name</b>");
$table->addCell($textinput->show());
$table->endRow();


$editor = $this->newObject('htmlarea', 'htmlelements');
$editor->name = 'giftdescription';
$editor->height = '100px';
$editor->width = '550px';
$editor->setMCQToolBar();
if ($mode == 'edit') {
    $editor->setContent($gift['description']);
}

if ($mode == 'fixup') {
    $editor->setContent($description);
}



$table->startRow();
$table->addCell("<b>Description</b>");
$table->addCell($editor->show());
$table->endRow();



$table->startRow();
$table->addCell('<b>Date Received</b>');
$objDateTime = $this->getObject('dateandtime', 'utilities');
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'date_recieved';
if ($mode == 'edit') {
    $objDatePicker->setDefaultDate(substr($document['date_recieved'], 0, 10));
}


$table->addCell($objDatePicker->show());
$table->endRow();
$textinput = new textinput('donor');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $gift['donor'];
}
if ($mode == "fixup") {
    $textinput->value = $donor;
}
$table->startRow();
$table->addCell("<b>Donor</b>");
$table->addCell($textinput->show());
$table->endRow();


$textinput = new textinput('giftvalue');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $gift['value'];
}
if ($mode == "fixup") {
    $textinput->value = $value;
}
$table->startRow();
$table->addCell("<b>Value (ZAR)</b>");
$table->addCell($textinput->show());
$table->endRow();

$gtype = new dropdown('type');
$gtype->addOption("Select ...");
$gtype->addOption("Sponsorship");
$gtype->addOption("Individual");
$gtype->addOption("Group");
if ($gmode == 'fixup') {
    $type->setSelected($type);
}
if ($mode == 'edit') {
    $gtype->setSelected($gift['gift_type']);
}
$table->startRow();
$table->addCell("<b>Type</b>");
$table->addCell($gtype->show());
$table->endRow();

if ($mode == 'edit') {

    $table->startRow();
    $table->addCell("<b>" . $this->divisionLabel . "</b>");

    $departmentname = $this->objDepartments->getDepartmentName($gift['division']);

    $table->addCell($this->objGift->getTree('htmldropdown', $departmentname));
    $table->endRow();
}

$editor = $this->newObject('htmlarea', 'htmlelements');
$editor->name = 'comments';
$editor->height = '100px';
$editor->width = '550px';
$editor->setMCQToolBar();

if ($mode == 'edit') {
    $editor->setContent($gift['comments']);
}

if ($mode == 'fixup') {
    $editor->setContent($comments);
}


$table->startRow();
$table->addCell("<b>Comments</b>");
$table->addCell($editor->show());
$table->endRow();

//$attachStr= '<input type="file" name="filename"   id="fileupload"size="40" /><br/> ';
//$attachStr .= '<input type="file" name="fileupload2"   id="fileupload2"size="40" /><br/> ';
//$attachStr.= '<input type="file" name="fileupload3"   id="fileupload3"size="40" /><br/> ';

$checkbox = new checkbox('includeattachments', 'includeattachment');
if ($mode == 'add' || $mode == 'fixup') {
    $table->startRow();
    $table->addCell("<b>Include Attachments</b>");
    $table->addCell($checkbox->show());
    $table->endRow();
}

if ($mode == 'edit') {
    $attchs = $this->objAttachments->getAttachments($gift['id']);
    $attachs = "";
    foreach ($attchs as $attach) {
        $link = new link($this->uri(array("action" => "downloadattachment", "giftid" => $gift['id'], "filename" => $attach['name'])));
        $link->link = $attach['name'];
        $objIcon->setIcon("delete");
        $deletelink = new link($this->uri(array("action" => "deleteattachment", "id" => $attach['id'],'giftid'=>$gift['id'])));
        $deletelink->link = $objIcon->show();

        $attachs.=$link->show().$deletelink->show() . '<br/>';
    }
    $table->startRow();
    $table->addCell("<b>Attachments</b>");
    $table->addCell($attachs);
    $table->endRow();
}


$legend = "New Gift";
if ($mode == 'edit') {
    $legend = "Edit gift";
}
$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());

// Form
$form = new form('addeditgift', $this->uri(array('action' => $action)));

//$form
if ($mode == 'edit') {
    $hiddenId = new hiddeninput('id', $gift['id']);
    $form->addToForm($hiddenId->show());
}

$hiddenId = new hiddeninput('departmentid', $departmentid);
$form->addToForm($hiddenId->show());
$efs = new fieldset();
$efs->setLegend('Please, note that the following fields are mandatory');
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

$button = new button('save', $this->objLanguage->languageText('mod_gift_save', 'gift', 'Save'));
$button->setToSubmit();
$form->addToForm('<br/>' . $button->show());

if ($mode == 'edit') {
    $button = new button('cancel', "Attach");
    $uri = $this->uri(array("action" => "attach", "id" => $gift['id']));
    $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
    $form->addToForm($button->show());
}

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array());
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>
