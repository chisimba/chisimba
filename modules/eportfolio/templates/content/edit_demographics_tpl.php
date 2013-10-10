<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$type = 'Demographics';
$categorytypeList = $this->objDbCategorytypeList->listCategorytype($type);
$objPopupcal = $this->newObject('datepickajax', 'popupcalendar');
//	$objLabel =& $this->newObject('label', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_editdemographics", 'eportfolio');
echo $objHeading->show();
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'editdemographicsconfirm',
    'id' => $id
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
$dropdown = new dropdown('demographics_type');
if (!empty($categorytypeList)) {
    foreach($categorytypeList as $categories) {
        $dropdown->addOption($categories['id'], $categories['type']);
        $dropdown->setSelected($demographics_type);
    }
} else {
    $dropdown->addOption('None', "-There are no Types-");
}
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_demographicstype", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $dropdown->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
$mybirth = explode(" ", $birth);
//birth text box
//$textinput = new textinput("birth", $mybirth[0]);
//$textinput->size = 20;
$strtdate = &$this->getObject('datepicker', 'htmlelements');
$strtdate->setName('birth');
$strtdate->setDateFormat("YYYY-MM-DD");
$strtdate->setDefaultDate($mybirth[0]);
//$startField = $this->objPopupcal->show('birth', 'no', 'no', $birth);
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_birth", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 200, 'top', 'right');
$form->addRule('birth', 'Please enter your birth date', 'required');
$row = array(
    $strtdate->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//nationality text field
$textinput = new textinput("nationality", $nationality);
$textinput->size = 30;
$form->addRule('nationality', 'Please enter your nationality', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_nationality", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $textinput->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'view_contact'
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
