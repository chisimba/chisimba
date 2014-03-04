<?php


// Load the form class
$this->loadClass('form', 'htmlelements');
// Load the textinput class
$this->loadClass('textinput', 'htmlelements');
// Load the textarea class
$this->loadClass('textarea', 'htmlelements');
// Load the label class
$this->loadClass('label', 'htmlelements');
//Set up the form processor
$paramArray = array(
    'action' => 'doupload');
$formAction = $this->uri($paramArray);
// Create and instance of the form class
$objForm = new form('uploadForm');
// Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
// Set the displayType to 3 for freeform
$objForm->displayType = 3;
//Set the enctype for multipart file upload
$objForm->extra = " enctype=\"multipart/form-data\"";

// Retrieve the data
if (isset($ar)) {
    $id = $ar['id'];
    $comment = $ar['type'];
    $title = $ar['title'];
    $dateCreated = $ar['dateCreated'];
    $creatorId = $ar['creatorid'];
    $dateModified = $ar['datemodified'];
    $modifierId = $ar['modifierid'];
    $modified = $ar['modified'];
} 
// Set up the header.
    $header = $this->objLanguage->languageText("mod_commenttypeadmin_upload",'commenttypeadmin');
// Create the heading useng htmlheading class
$this->objH = &$this->getObject('htmlheading', 'htmlelements');
$this->objH->type = 1; //Heading <h1>
$this->objH->str = $header ;
// Create an element for the hidden text input
$objType = new textinput("type");
// Set the value to the primary keyid

//if (isset($type)) {
    $objType->setValue($commentType);
//} 
// Set the field type to hidden for the primary key
$objType->fldType = "hidden";
// Add the hidden type field to the form
$objForm->addToForm($objType->show());
// Create an element for the input of comment
$objFile = new textinput ("fileupload");
$objFile->fldType = "file";
$objFile->size = 70;
//Create label for input
$filelabel = new label ($this->objLanguage->languageText("mod_commenttypeadmin_word_file","commenttypeadmin"), "input_file");

// Create an instance of the fieldset object
$objFieldset = &$this->getObject('fieldset', 'htmlelements');
$objFieldset->contents = "<table><tr><td align=\"right\">"
 . $filelabel->show()
 . ": </td><td>" . $objFile->show() . "</td></tr></table>";

// Add the fieldset to the form
$objForm->addToForm($objFieldset->show());
// Create an instance of the button object
$this->loadClass('button', 'htmlelements');
// Create a submit button
$objElement = new button('submit'); 
// Set the button type to submit
$objElement->setToSubmit(); 
// Use the language object to add the word save
$objElement->setValue(' ' . $this->objLanguage->languageText("mod_commenttypeadmin_upload", "commenttypeadmin" ) . ' ');
// Create cancel button
$objCancel = new button('cancel');
$objCancel->setOnClick("window.location='".$this->uri(NULL)."';");
$objCancel->setValue(' ' . $this->objLanguage->languageText("mod_commenttypeadmin_cancel",'commenttypeadmin') . ' ');
// Add the button to the form
$objForm->addToForm('<br/>' . $objElement->show()."&nbsp;".$objCancel->show());
// Set the content of the left side column
$leftSideColumn = $this->objLanguage->languageText("mod_commenttypeadmin_leftupload",'commenttypeadmin');
// Set the content of the centered layer
$rightSideColumn = $this->objH->show();
$rightSideColumn .= $objForm->show();
// Create an instance of the css layout class
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);
// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
// Output the content to the page
echo $cssLayout->show();

?>
