<?php
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass('textarea', 'htmlelements');
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$objCheck = $this->loadClass('checkbox', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_wordAdd", 'eportfolio') . ' ' . $objLanguage->languageText("mod_eportfolio_wordGroup", 'eportfolio');
echo $objHeading->show();
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'addgroupconfirm'
)));
$objTable = new htmltable();
$objTable->width = '30';
$objTable->attributes = " align='center' border='0'";
$objTable->cellspacing = '12';
$row = array(
    "<b>" . $objLanguage->languageText("word_name") . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $objUser->fullName()
);
$objTable->addRow($row, NULL);
//category text box
$category = new textinput("group", "");
$category->size = 60;
$form->addRule('group', 'Please enter the Group name', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_wordGroup", 'eportfolio') . ' ' . $label = $objLanguage->languageText("word_name") . ":</b>"
);
$objTable->addRow($row, NULL);
$row = array(
    $category->show()
);
$objTable->addRow($row, NULL);
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("submit", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
$objCancel->link = $buttonCancel->show();
$linkCancel = $objCancel->show();
$row = array(
    $button->show() . ' / ' . $linkCancel
);
$objTable->addRow($row, NULL);
$form->addToForm($objTable->show());
echo $form->show();
?>
