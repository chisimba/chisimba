<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$type = 'Demographics';
$categorytypeList = $this->objDbCategorytypeList->listCategorytype($type);
//	$objLabel =& $this->newObject('label', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_adddemographics", 'eportfolio');
echo $objHeading->show();
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'adddemographicsconfirm'
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
echo "<br />";
//type drop down list
$dropdown = new dropdown('demographics_type');
if (!empty($categorytypeList)) {
    foreach($categorytypeList as $categories) {
        $dropdown->addOption($categories['id'], $categories['type']);
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
//birth text box
$strtdate = &$this->getObject('datepicker', 'htmlelements');
$strtdate->setName('birth');
$strtdate->setDateFormat("YYYY-MM-DD");
//$strtdate->setDefaultDate($activityFinish[0]);
//$textinput = new textinput("birth", "");
//$textinput->size = 20;
//$startField = $this->objPopupcal->show('birth', 'no', 'no', Null);
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
$textinput = new textinput("nationality", "");
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
