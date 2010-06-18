<?php

/**
 *
 * Edit template for KEWL.NextGen userparamsadmin
 * @author Derek Keats using TableAdmin generated code
 * @version $Id: edit_tpl.php 17302 2010-03-28 13:09:34Z dkeats $
 * @copyright 2005 GNU GPL
 * 
 **/


$paramArray=array(
'action'=>'save',
'mode'=>$mode);
$formAction=$this->uri($paramArray);

//Load the form class 
$this->loadClass('form','htmlelements');
//Load the textinput class 
$this->loadClass('textinput','htmlelements');
//Load the textarea class 
$this->loadClass('textarea','htmlelements');
//Load the label class
$this->loadClass('label','htmlelements');
//Create and instance of the form class
$objForm = new form('userparamsadmin');
//Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
//Set the displayType to 3 for freeform
$objForm->displayType=3;


$objHeading = $this->newObject('htmlheading', 'htmlelements');
$objHeading->type=1;
if ($this->getParam('action', NULL) == 'add' ) {
    $objHeading->str = $this->objLanguage->languageText("mod_userparamsadmin_titadd",'userparamsadmin');

} else {
    $objHeading->str = $this->objLanguage->languageText("mod_userparamsadmin_titedit",'userparamsadmin');
}
echo $objHeading->show();


//Retrieve the data
$id = (isset($keyEdit)) ? $keyEdit : '';
$pname = (isset($keyEdit)) ? $keyEdit : '';
$ptag = (isset($valueEdit)) ? $valueEdit : '';

// Make it easy to build links to add parameters
if ($mode=="add") {
    if ($pname=='') {
        // Get it from the querystring
        $pname=$this->getParam('key','');
    }
}        

//Create an element for the hidden text input
$objElement = new textinput("id");
//Set the value to the primary keyid
if (isset($id)) {
    $objElement->setValue($id);
}
//Set the field type to hidden for the primary key
$objElement->fldType="hidden";
//Add the hidden PK field to the form
$objForm->addToForm($objElement->show());

//Create a label for the input of pname
$pnameLabel = new label($this->objLanguage->languageText("mod_userparamsadmin_pname",'userparamsadmin'), "input_pname");
//Create an element for the input of pname
$objElement = new textinput ("pname");
//Hide it to avoid breaking the system if its an edit
if ($mode=="edit") {
    $objElement->fldType="hidden";
    //Set the value of the element to $pname
    if (isset($pname)) {
        $objElement->setValue($pname);
    }
    $txtToShow = $objElement->show() . $pname;
} else {
    // We are adding.
    if (isset($pname)) {
        $objElement->setValue($pname);
    }
    $txtToShow = $objElement->show();
}

//Add the $pname element to the form
$objForm->addToForm($pnameLabel->show().": ". $txtToShow ."<br />");

//Create label for the input of ptag
$ptagLabel = new label($this->objLanguage->languageText("mod_userparamsadmin_pvalue",'userparamsadmin'), "input_ptag");
//Create an element for the input of ptag
$objElement = new textinput ("ptag");
//Set the value of the element to $ptag
if (isset($ptag)) {
    $objElement->setValue($ptag);
}
//Add the $ptag element to the form
$objForm->addToForm($ptagLabel->show().": ".$objElement->show()."<br />");

$commaWarn = "<div class='warning'>"
  . $this->objLanguage->languageText("mod_userparams_nocommas",'userparamsadmin')
  . "</div>";
$objForm->addToForm($commaWarn);

// Create an instance of the button object
$this->loadClass('button', 'htmlelements');
// Create a submit button
$objElement = new button('submit');
$objElement->setIconClass("save");
// Set the button type to submit
$objElement->setToSubmit();	
// Use the language object to add the word save
$objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
//Create cancel button
$objCancel = new button('cancel');
$objCancel->setIconClass("cancel");
$objCancel->setOnClick("window.location='".$this->uri(array())."';");
$objCancel->setValue(' ' . $this->objLanguage->languageText("mod_userparamsadmin_cancel",'userparamsadmin') . ' ');
// Add the buttons to the form
$objForm->addToForm('<br/>&nbsp;'.$objElement->show()."&nbsp;".$objCancel->show());
echo "<div class='standard_form'>" . $objForm->show() . "</div>";

?>
