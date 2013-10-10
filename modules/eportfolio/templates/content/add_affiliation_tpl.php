<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$type = 'Affiliation';
$categorytypeList = $this->objDbCategorytypeList->listCategorytype($type);
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_addAffiliation", 'eportfolio');
echo $objHeading->show();
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'addaffiliationconfirm'
)));
$objTable = new htmltable();
$objTable->width = '100%';
$objTable->attributes = " align='left' border='0'";
$objTable->cellspacing = '5';
$row = array(
    "<b>" . $objLanguage->languageText("word_name") . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $objUser->fullName()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//type drop down list
$mydropdown = new dropdown('affiliation_type');
if (!empty($categorytypeList)) {
    foreach($categorytypeList as $categories) {
        $mydropdown->addOption($categories['id'], $categories['type']);
    }
} else {
    $mydropdown->addOption('None', "-There are no Types-");
}
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_affiliationtype", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $mydropdown->show()
);
$objTable->addCell($row[0], Null, 'bottom', 'left');
$objTable->endRow();
//classification text box
$textinput = new textinput("classification", "");
$textinput->size = 40;
$form->addRule('classification', 'Please enter the classification', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_classification", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $textinput->show()
);
$objTable->addCell($row[0], Null, 'bottom', 'left');
$objTable->endRow();
//role text field
$textinput = new textinput("role", "");
$textinput->size = 40;
$form->addRule('role', 'Please enter the role', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_role", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $textinput->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//organisation text box
$textinput = new textinput("organisation", "");
$textinput->size = 40;
$form->addRule('organisation', 'Please enter the organisation', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_organisation", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $textinput->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//start text field
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_activitystart", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'bottom', 'right');
//$startField = $this->objPopupcal->show('start', 'yes', 'no', "");
//$form->addRule('start', 'Please enter the start date', 'required');
$strtdate = &$this->getObject('datepicker', 'htmlelements');
$strtdate->setName('start');
$strtdate->setDateFormat("YYYY-MM-DD");
$row = array(
    $strtdate->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//finish text field
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_activityfinish", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'bottom', 'right');
//$startField = $this->objPopupcal->show('finish', 'yes', 'no', "");
//$form->addRule('finish', 'Please enter the finish date', 'required');
$findate = &$this->getObject('datepicker', 'htmlelements');
$findate->setName('finish');
$findate->setDateFormat("YYYY-MM-DD");
$row = array(
    $findate->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//short description text box
$objTable->startRow();
$shortdescription = new textarea("shortdescription", "");
$form->addRule('shortdescription', 'Please enter a short description', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_shortdescription", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $shortdescription->show()
);
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//long description text field
$objTable->startRow();
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_longdescription", 'eportfolio') . ":</b>"
);
$objTable->addCell($row[0], 140, 'top', 'right');
//Add the WYSWYG editor
$editor = $this->newObject('htmlarea', 'htmlelements');
$editor->setName('longdescription');
$editor->height = '300px';
$editor->width = '550px';
$longdescription = '';
//To set the basic toolbar
//$editor->setBasicToolBar();
$editor->setContent($longdescription);
//$objTable->addCell($editor->showFCKEditor(), NULL, "top", "center", NULL, "colspan=\"2\"");
$row = array(
    $editor->show()
);
//$form->addRule('longdescription', 'Please enter the long description','required');
$objTable->addCell($row[0], NULL, 'top', 'left');
$objTable->endRow();
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_affiliation'
)));
//$objCancel->link = $buttonCancel->show();
$objCancel->link = $objLanguage->languageText("mod_filemanager_returnto", "filemanager") . " " . $objLanguage->languageText("mod_eportfolio_eportfoliohome", "eportfolio");
$linkCancel = $objCancel->show();
$row = array(
    $button->show()
);
$objTable->startRow();
$objTable->addCell('&nbsp;', 140, 'top', 'right');
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('&nbsp;', 140, 'top', 'right');
$objTable->addCell($linkCancel, Null, 'top', 'left');
$objTable->endRow();
$form->addToForm($objTable->show());
echo $form->show();
?>
