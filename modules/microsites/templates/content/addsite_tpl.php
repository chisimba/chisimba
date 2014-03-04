<?php

//Add a site template


$objForm = $this->newObject('form', 'htmlelements');
$objTextBox = $this->newObject('textinput', 'htmlelements');
$objTextBoxUrl = $this->newObject('textinput', 'htmlelements');
$objButton = $this->newObject('button', 'htmlelements');

$objForm->action = $this->uri(array('action' => 'addsite'));
$objForm->displayType = 2;

$objTextBox->name = 'sitename';
$objTextBox->value = "";
$objTextBox->label = $this->objLanguage->languageText("mod_microsites_sitename", "microsites");

$objTextBoxUrl->name = 'url';
$objTextBoxUrl->value = "";
$objTextBoxUrl->label = $this->objLanguage->languageText("mod_microsites_url", "microsites");

$objButton->setToSubmit();
$objButton->value = $this->objLanguage->languageText("mod_microsites_addsite", "microsites");

$objForm->addToForm($objTextBox);
$objForm->addToForm($objTextBoxUrl);
$objForm->addToForm($objButton);

echo $objForm->show()
?>


