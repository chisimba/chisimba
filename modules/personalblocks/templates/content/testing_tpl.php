<?php
echo "<h1>Testing</h1>";
$paramArray=array(
'action' => 'testing');
$formAction=$this->uri($paramArray);
//Load the form class
$this->loadClass('form','htmlelements');
//Load the textinput class
$this->loadClass('textinput','htmlelements');
//Load the label class
$this->loadClass('label','htmlelements');
//Create and instance of the form class
$objForm = new form('personalblock');
//Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
//Set the displayType to 3 for freeform
$objForm->displayType=3;
 //Create an element for the input of blockname
$objElement = new textinput ('blockname');
$lbl = $this->objLanguage->languageText("mod_personalblocks_blname", "personalblocks");
$objElemLabel = new label($lbl, "blockname");
$requiredName = $this->objLanguage->languageText("mod_personalblocks_namerequired", "personalblocks");
//Add a validation rule
$objForm->addRule('blockname',$requiredName,'required');
$objForm->addToForm($objElemLabel->show());
$objForm->addToForm($objElement->show());


// Create an instance of the button object
$this->loadClass('button', 'htmlelements');
// Create a submit button
$objButton = new button('submit');
// Set the button type to submit
$objButton->setToSubmit();
// Use the language object to add the word save
$objButton->setValue(' '.$this->objLanguage->languageText("word_save").' ');
// Add the buttons to the form
$objForm->addToForm($objButton->show());

echo $objForm->show();

?>