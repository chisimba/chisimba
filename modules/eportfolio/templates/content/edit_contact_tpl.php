<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$type = 'Place';
$categorytypeList = $this->objDbCategorytypeList->listCategorytype($type);
$modetype = 'Mode';
$modetypeList = $this->objDbCategorytypeList->listCategorytype($modetype);
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_editcontact", 'eportfolio');
echo $objHeading->show();
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'editcontactconfirm',
    'id' => $id
)));
$objTable = new htmltable();
$objTable->width = '100%';
$objTable->attributes = " align='center' border='0'";
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
$dropdown = new dropdown('contact_type');
if (!empty($categorytypeList)) {
    foreach($categorytypeList as $categories) {
        $dropdown->addOption($categories['id'], $categories['type']);
        $dropdown->setSelected($contact_type);
    }
} else {
    $dropdown->addOption('None', "-There are no types-");
}
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_contype", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $dropdown->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Contact type dropdown
$mydropdown = new dropdown('contactType');
if (!empty($modetypeList)) {
    foreach($modetypeList as $categories) {
        $mydropdown->addOption($categories['id'], $categories['type']);
        $mydropdown->setSelected($contactType);
    }
} else {
    $mydropdown->addOption('None', "-There are no modes-");
}
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_contacttype", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $mydropdown->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//country_code text box
$country_code = new textinput("country_code", $country_code);
$country_code->size = 15;
$form->addRule('country_code', 'Please enter the country code', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_countrycode", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $country_code->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//area_code text field
$area_code = new textinput("area_code", $area_code);
$area_code->size = 15;
$form->addRule('area_code', 'Please enter the area code', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_areacode", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $area_code->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//contact number text field
$id_number = new textinput("id_number", $id_number);
$id_number->size = 30;
$form->addRule('id_number', 'Please enter the Id number', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_contactnumber", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $id_number->show()
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
