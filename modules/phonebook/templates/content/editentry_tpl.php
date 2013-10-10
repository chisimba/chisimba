<?php
/**
 * Model extension of controller that displays the interface for editing entries
 * @authors:Godwin Du Plessis, Ewan Burns, Helio Rangeiro, Jacques Cilliers, Luyanda Mgwexa, George Amabeoku, Charl Daniels, and Qoane Seitlheko.
 * @copyright 2007 University of the Western Cape
 */
// Create an instance of the css layout class
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);
// get the sidebar object
$this->leftMenu = $this->newObject('usermenu', 'toolbar');
// Initialize left column
$leftSideColumn = $this->leftMenu->show();
$rightSideColumn = NULL;
$middleColumn = NULL;
// Create link icon and link to view template
$this->loadClass('link', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$link = new link($this->uri(array(
    'action' => 'default'
)));
$objIcon->setIcon('prev');
$link->link = $objIcon->show();
$update = $link->show();
// Create header with add icon and set the action
$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
$pgTitle->type = 1;
$pgTitle->str = $objLanguage->languageText('mod_phonebook_return', 'phonebook') . "&nbsp;" . $update;
$this->objUser = $this->getObject('user', 'security');
$cform = new form('phonebook', $this->uri(array(
    'action' => 'updateentry'
)));
//start a fieldset
$cfieldset = $this->getObject('fieldset', 'htmlelements');
$ct = $this->newObject('htmltable', 'htmlelements');
$ct->cellpadding = 5;
//start a row, hide the id from the user and check whether there is any input
$ct->startRow();
$this->loadClass('hiddeninput', 'htmlelements');
$ctv = new hiddeninput('id');
if (isset($oldrec['id'])) {
    $ctv->value = $oldrec['id'];
}
$ct->addCell($ctv->show() == false);
$ct->addCell($ctv->show());
// end of the row
$ct->endRow();
//value textfield
//start a row, and does the same check
$ct->startRow();
$ctvlabel = new label($this->objLanguage->languageText('mod_phonebook_firstname', 'phonebook') . ':', 'input_cvalue');
$ctv = new textinput('firstname');
if (isset($oldrec['firstname'])) {
    $ctv->value = $oldrec['firstname'];
}
$ct->addCell($ctvlabel->show());
$ct->addCell($ctv->show());
// end of the row
$ct->endRow();
// start row and add cell just to make form look better
$ct->startRow();
$ct->addCell('&nbsp;');
// end of the row
$ct->endRow();
//value textfield
//start a row, and does the same check
$ct->startRow();
$ctvlabel = new label($this->objLanguage->languageText('mod_phonebook_lastname', 'phonebook') . ':', 'input_cvalue');
$ctv = new textinput('lastname');
if (isset($oldrec['lastname'])) {
    $ctv->value = $oldrec['lastname'];
}
$ct->addCell($ctvlabel->show());
$ct->addCell($ctv->show());
// end of the row
$ct->endRow();
// start row and add cell just to make form look better
$ct->startRow();
$ct->addCell('&nbsp;');
// end of the row
$ct->endRow();
//value textfield
//start a row, and does the same check
$ct->startRow();
$ctvlabel = new label($this->objLanguage->languageText('mod_phonebook_emailaddress', 'phonebook') . ':', 'input_cvalue');
$ctv = new textinput('emailaddress');
if (isset($oldrec['emailaddress'])) {
    $ctv->value = $oldrec['emailaddress'];
}
$ct->addCell($ctvlabel->show());
$ct->addCell($ctv->show());
// end of the row
$ct->endRow();
// start row and add cell just to make form look better
$ct->startRow();
$ct->addCell('&nbsp;');
// end of the row
$ct->endRow();
//value textfield
//start a row, and does the same check
$ct->startRow();
$ctvlabel = new label($this->objLanguage->languageText('mod_phonebook_landlinenumber', 'phonebook') . ':', 'input_cvalue');
$ctv = new textinput('landlinenumber');
if (isset($oldrec['landlinenumber'])) {
    $ctv->value = $oldrec['landlinenumber'];
}
$ct->addCell($ctvlabel->show());
$ct->addCell($ctv->show());
// end of the row
$ct->endRow();
// start row and add cell just to make form look better
$ct->startRow();
$ct->addCell('&nbsp;');
// end of the row
$ct->endRow();
//value textfield
//start a row, and does the same check
$ct->startRow();
$ctvlabel = new label($this->objLanguage->languageText('mod_phonebook_cellnumber', 'phonebook') . ':', 'input_cvalue');
$ctv = new textinput('cellnumber');
if (isset($oldrec['cellnumber'])) {
    $ctv->value = $oldrec['cellnumber'];
}
$ct->addCell($ctvlabel->show());
$ct->addCell($ctv->show());
// end of the row
$ct->endRow();
// start row and add cell just to make form look better
$ct->startRow();
$ct->addCell('&nbsp;');
// end of the row
$ct->endRow();
//value textfield
//start a row, and does the same check
$ct->startRow();
$ctvlabel = new label($this->objLanguage->languageText('mod_phonebook_address', 'phonebook') . ':', 'input_cvalue');
$ctv = new textarea('address');
if (isset($oldrec['address'])) {
    $ctv->value = $oldrec['address'];
}
$ct->addCell($ctvlabel->show());
$ct->addCell($ctv->show());
// end of the row
$ct->endRow();
// start row and add cell just to make form look better
$ct->startRow();
$ct->addCell('&nbsp;');
// end of the row
$ct->endRow();
//end off the form and add the buttons
$this->objconvButton = new button($this->objLanguage->languageText('mod_phonebook_update', 'phonebook'));
$this->objconvButton->setValue($this->objLanguage->languageText('mod_phonebook_update', 'phonebook'));
$this->objconvButton->setToSubmit();
$cfieldset->addContent($ct->show());
$cform->addToForm($cfieldset->show());
$cform->addToForm($this->objconvButton->show());
$cform = $cform->show();
//create the feature box and display the form
$objFeatureBox = $this->getObject('featurebox', 'navigation');
$ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_phonebook_update", "phonebook") , $cform);
$middleColumn = $pgTitle->show() . $ret;
// Create link back to my view template
$objBackLink = &$this->getObject('link', 'htmlelements');
$objBackLink->link($this->uri(array(
    'module' => 'phonebook'
)));
$objBackLink->link = $objLanguage->languageText('mod_phonebook_return', 'phonebook');
//add left column
$cssLayout->setLeftColumnContent($leftSideColumn);
$cssLayout->setRightColumnContent($rightSideColumn);
//add middle column
$cssLayout->setMiddleColumnContent($middleColumn);
echo $cssLayout->show();
?>
