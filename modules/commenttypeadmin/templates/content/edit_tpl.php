<?php
// Load the form class
$this->loadClass('form', 'htmlelements');
// Load the textinput class
$this->loadClass('textinput', 'htmlelements');
// Load the textarea class
$this->loadClass('textarea', 'htmlelements');
// Load the label class
$this->loadClass('label', 'htmlelements');

//------------
//Added 2006/07/25 Serge Meunier - To make sure you cannot delete a type with comments attached to it
//$objModule = &$this->getObject('modulesadmin','modulelist');
$objModule = &$this->getObject('modules','modulecatalogue');
if ($objModule->checkIfRegistered('comment', 'comment')){
    $comReg=TRUE;
} else {
    $comReg=FALSE;
}

//-------------

//Set up the form processor
$paramArray = array(
    'action' => 'save',
    'mode' => $mode);
$formAction = $this->uri($paramArray);
// Create and instance of the form class
$objForm = new form('commenttypeadmin');
// Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
// Set the displayType to 3 for freeform
$objForm->displayType = 3;
// Retrieve the data
if (isset($ar)) {
    $id = $ar['id'];
    $comment = $ar['type'];
    $title = $ar['title'];
    $dateCreated = $ar['datecreated'];
    $creatorId = $ar['creatorid'];
    $dateModified = $ar['datemodified'];
    $modifierId = $ar['modifierid'];
    $modified = $ar['modified'];
} 

//------------
//Added 2006/07/25 Serge Meunier - To make sure you cannot delete a type with comments attached to it
if ($comReg){
    $objDbComment = &$this->getObject('dbcomment', 'comment');
    $where = "WHERE type = '" . $comment . "'";
    $commentCount = $objDbComment->getRecordCount($where);
}else{
    $commentCount = 0;
}
//-------------

// Set up the header.
if ($mode == "edit") {
    $rep = array('category' => "<i>" . $title . "</i>");
    $header = $this->objLanguage->code2txt('mod_commenttypeadmin_editlabel','commenttypeadmin', $rep);
} else {
    $header = $this->objLanguage->languageText("mod_commenttypeadmin_addlabel",'commenttypeadmin');
} 
// Create the heading useng htmlheading class
$this->objH = &$this->getObject('htmlheading', 'htmlelements');
$this->objH->type = 1; //Heading <h1>
$this->objH->str = $header ;
// Create an element for the hidden text input
$objElement = new textinput("id");
// Set the value to the primary keyid
if (isset($id)) {
    $objElement->setValue($id);
} 
// Set the field type to hidden for the primary key
$objElement->fldType = "hidden";
// Add the hidden PK field to the form
$objForm->addToForm($objElement->show());
// Create an element for the input of comment
//------------
//Added 2006/07/25 Serge Meunier - To make sure you cannot delete a type with comments attached to it
if ($commentCount > 0){
    $objCat = new textinput ("type", $comment, "hidden");
}else{
    $objCat = new textinput ("type");
}
//------------



//Create label for input
$typelabel = new label ($this->objLanguage->languageText("mod_commenttypeadmin_type","commenttypeadmin"), "input_type");
// Set the value of the element to $comment
if (isset($comment)) {
    $objCat->setValue($comment);
} 



// Create an element for the input of title
$objTit = new textinput ("title");
//Create label for input
$titlelabel = new label ($this->objLanguage->languageText("mod_commenttypeadmin_title","commenttypeadmin"), "input_title");
// Set the width
$objTit->size = 70;
// Set the value of the element to $title
if (isset($title)) {
    $objTit->setValue($title);
} 
// Create an instance of the fieldset object
$objFieldset = &$this->getObject('fieldset', 'htmlelements');

//-----------------
//Modified 2006/07/25 Serge Meunier
if ($commentCount > 0){
    $objFieldset->contents = "<table><tr><td align=\"right\">"
     . $typelabel->show()
     . ": </td><td>" . $comment .  $objCat->show() . "</td></tr>"
     . "<tr><td align=\"right\">" . $titlelabel->show()
     . ": </td><td>" . $objTit->show() . "</td></tr></table>";
}else{
    $objFieldset->contents = "<table><tr><td align=\"right\">"
     . $typelabel->show()
     . ": </td><td>" . $objCat->show() . "</td></tr>"
     . "<tr><td align=\"right\">" . $titlelabel->show()
     . ": </td><td>" . $objTit->show() . "</td></tr></table>";
}

// Add the fieldset to the form
$objForm->addToForm($objFieldset->show());
// Create an instance of the button object
$this->loadClass('button', 'htmlelements');
// Create a submit button
$objElement = new button('submit'); 
// Set the button type to submit
$objElement->setToSubmit(); 
// Use the language object to add the word save
$objElement->setValue(' ' . $this->objLanguage->languageText("word_save") . ' ');
// Create cancel button
$objCancel = new button('cancel');
$objCancel->setOnClick("window.location='".$this->uri(NULL)."';");
$objCancel->setValue(' ' . $this->objLanguage->languageText("mod_commenttypeadmin_cancel",'commenttypeadmin') . ' ');
// Add the button to the form
$objForm->addToForm('<br/>' . $objElement->show()."&nbsp;".$objCancel->show());
// Set the content of the left side column
$leftSideColumn = $this->objLanguage->languageText("mod_commenttypeadmin_lefteditist",'commenttypeadmin');
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
