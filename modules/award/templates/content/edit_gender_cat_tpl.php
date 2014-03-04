<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package LRS Admin
*/

/**
* Edit gender template for the LRS Admin
* Author Brent van Rensburg
*/

//Load the form class
$this->loadClass('form', 'htmlelements');
//Load the textinput class
$this->loadClass('textinput', 'htmlelements');
//Load the button class
$this->loadClass('button', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');

//Set form action
$formAction = $this->uri(array('action'=>'savegendercat','selected'=>'init_10'));
//Create new form object
$objEditGenderForm = new form('editaddgrade', $formAction);
$objEditGenderForm->displayType = '3';

$header = $this->getObject('htmlheading','htmlelements');
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_lrs_gender_edit_category', 'award');
$objEditGenderForm->addToForm($header->show());


$txtCategory = new textinput("category", $genderCat);
$txtOldCat = new textinput("genderCat", $genderCat, 'hidden');

// Create a submit button
$objSubmit = new button('submit'); 
// Set the button type to submit
$objSubmit->setToSubmit(); 
// Use the language object to add the word
$objSubmit->setValue(' ' . $this->objLanguage->languageText("word_submit") . ' ');

$btnCancel = new button('cancel');
$location = $this->uri(array('action'=>'viewgender', 'selected'=>'init_10'), 'award');
$btnCancel->setOnClick("javascript:window.location='$location'");
$btnCancel->setValue(' '.$this->objLanguage->languageText("word_back").' ');

//Create table for edit form
$objFormTable =& $this->newObject('htmltable', 'htmlelements');
$objFormTable->cellspacing = '2';
$objFormTable->cellpadding = '2';
$objFormTable->width = '90%';

$objaddeditHeadTable = new htmlTable('lrsadmin');
$objaddeditHeadTable->cellspacing = 2;
$objaddeditHeadTable->cellpadding = '2';
$objaddeditHeadTable->width = '90%';

$objaddeditHeadTable->startRow();
$objaddeditHeadTable->addCell("<i>".$this->objLanguage->languageText('mod_lrs_gender_tbl_head_gender', 'award')."</i>");
$objaddeditHeadTable->addCell('');
$objaddeditHeadTable->endRow();

$objFormTable->startRow();
$objFormTable->addCell($this->objLanguage->languageText('word_category'). ':', '25%','','','odd');
$objFormTable->addCell($txtCategory->show());
$objFormTable->endRow();

$objFormTable->startRow();
$objFormTable->addCell($txtOldCat->show());
$objFormTable->addCell("<br />");
$objFormTable->endRow();

$objFormTable->startRow();
$objFormTable->addCell($objSubmit->show().'  '.$btnCancel->show());
$objFormTable->addCell("<br />");
$objFormTable->endRow();

//Add validation here
$objEditGenderForm->addRule('category', $this->objLanguage->languageText('mod_lrs_gender_catrequired', 'award'), 'required');
//Add table to form
$objEditGenderForm->addToForm($objaddeditHeadTable->show());
$objEditGenderForm->addToForm($objFormTable->show());

echo $objEditGenderForm->show();
?>