<?php
// Load classes.
$this->loadClass("form", "htmlelements");
$this->loadClass("textinput", "htmlelements");
$this->loadClass("button", "htmlelements");
$this->loadClass("htmltable", 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
//	$objLabel =& $this->newObject('label', 'htmlelements');
$objWindow = &$this->newObject('windowpop', 'htmlelements');
$objHeading = &$this->getObject('htmlheading', 'htmlelements');
$type = 'Place';
$categorytypeList = $this->objDbCategorytypeList->listCategorytype($type);
$objHeading->type = 1;
$objHeading->str = $objLanguage->languageText("mod_eportfolio_addAddress", 'eportfolio');
echo $objHeading->show();
//instantiate the form object
$form = new form("add", $this->uri(array(
    'module' => 'eportfolio',
    'action' => 'addaddressconfirm'
)));
//set the display type
//set the action parameters
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
//Type text box
$dropdown = new dropdown('address_type');
if (!empty($categorytypeList)) {
    foreach($categorytypeList as $categories) {
        $dropdown->addOption($categories['id'], $categories['type']);
        $dropdown->setSelected($address_type);
    }
} else {
    $dropdown->addOption('None', "-There are no categories-");
}
$row = array(
    $dropdown->show()
);
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_type", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $dropdown->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Street No text box
$street_no = new textinput("street_no", "");
$street_no->size = 25;
$street_no->label = 'Street No(must be filled out)';
/*
$form->addRule('street_no', 'Please enter the Street No', 'required');
*/
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_streetno", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $street_no->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Street name text field
$street_name = new textinput("street_name", "");
$street_name->size = 25;
$street_name->label = 'Street Name(must be filled out)';
$form->addRule('street_name', 'Please enter the Street Name', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_streetname", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $street_name->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Locality text field
$locality = new textinput("locality", "");
$locality->size = 25;
$locality->label = 'Locality(must be filled out)';
$form->addRule('locality', 'Please enter the Locality', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_locality", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $locality->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//City text field
$city = new textinput("city", "");
$city->size = 25;
$city->label = 'City(must be filled out)';
$form->addRule('city', 'Please enter the City', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_city", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $city->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Post Code select box
$postcode = new textinput("postcode", "");
$postcode->size = 25;
$postcode->label = 'Post Code(must be filled out)';
$form->addRule('postcode', 'Please enter the Post Code', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_postcode", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $postcode->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Postal Address text box
$postal_address = new textinput("postal_address", "");
$postal_address->size = 25;
$postal_address->label = 'Post Address(must be filled out)';
//$form->addRule('postal_address', 'Please enter the Post Address', 'required');
$row = array(
    "<b>" . $label = $objLanguage->languageText("mod_eportfolio_postaddress", 'eportfolio') . ":</b>"
);
$objTable->startRow();
$objTable->addCell($row[0], 140, 'top', 'right');
$row = array(
    $postal_address->show()
);
$objTable->addCell($row[0], Null, 'top', 'left');
$objTable->endRow();
//Save button
$button = new button("submit", $objLanguage->languageText("word_save")); //word_save
$button->setToSubmit();
// Show the cancel link
$buttonCancel = new button("cancel", $objLanguage->languageText("word_cancel"));
$objCancel = &$this->getObject("link", "htmlelements");
$objCancel->link($this->uri(array(
    'module' => 'eportfolio',
    'action' => 'main'
)));
//$objLanguage->languageText("mod_filemanager_returnto")." ".$objLanguage->languageText("mod_eportfolio_eportfoliohome");
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
