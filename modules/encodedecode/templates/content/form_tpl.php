<?php

$encodeString = $this->getParam('encode', '');
$encodedString = $this->getParam('encoded', '');

if ($encodeString !=='') {
    $encodedString = rawurlencode($encodeString);
}

// Load the form element classes we require.
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button', 'htmlelements');

// Create the form.
$objForm = new form('encoderdecoder',$this->uri(array('action'=>'default')));
// Create a text input for the URL
$urlInput = new textinput('encode', NULL, NULL, 80);
// Add the url input text box to the form.
$objForm->addToForm( "&nbsp;&nbsp;" . $urlInput ->show() . "&nbsp;&nbsp;");
//Create a button for submitting the form
$objButton = new button('encodeit');
// Set the button type to submit
$objButton->setToSubmit();
// Add the word Encode to the button
$objButton->setValue(' '. $this->objLanguage->languageText("mod_encodedecode_encode", "encodedecode") .' ');
// Add the button to the form.
$objForm->addToForm($objButton->show() . "<br />");

// Render the form

echo "<h1>" . $this->objLanguage->languageText("mod_encodedecode_title", "encodedecode") . "</h1>";
echo $objForm->show();
echo "<div class=\"colorbox whitebox\">" . $encodedString . "</div>";
?>