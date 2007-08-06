<?php

/*
Set up the form processor
*/

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
$objForm = new form('tbl_storycategory');
//Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
//Set the displayType to 3 for freeform
$objForm->displayType=3;
// Create an instance of the css layout class
$cssLayout =  $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

//Set the content of the left side column
$leftSideColumn = $this->objLanguage->code2Txt("mod_storycategoryadmin_lefteditist", "storycategoryadmin");

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
//Retrieve the data
if (isset($ar)) {
    $id = $ar['id'];
    $category = $ar['category'];
    $title = $ar['title'];
    $dateCreated = $ar['datecreated'];
    $creatorId = $ar['creatorid'];
    $dateModified = $ar['datemodified'];
    $modifierId = $ar['modifierid'];
    $modified = $ar['modified'];
}

//Set up the label for the fieldset
if ($mode=="edit") {
    $rep = array('category' => $category);
    $fieldsetLabel = $this->objLanguage->code2txt('mod_storycategory_editlabel', "storycategoryadmin", $rep);
} else {
    $fieldsetLabel = $this->objLanguage->code2Txt("mod_storycategory_addlabel", "storycategoryadmin");
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


//Create label for input of category
$catLabel = new label($this->objLanguage->languageText("word_category"), "input_category");
//Create an element for the input of category
$objCat = new textinput ("category");
//Set the value of the element to $category
if (isset($category)) {
    $objCat->setValue($category);
}

//Create label for the input of title
$titLabel = new label($this->objLanguage->languageText("word_title"), "input_title");
//Create an element for the input of title
$objTit = new textinput ("title");
//Set the width
$objTit->size=70;
//Set the value of the element to $title
if (isset($title)) {
    $objTit->setValue($title);
}


//Create an instance of the fieldset object
$objFieldset =  $this->getObject('fieldset', 'htmlelements');
$objFieldset->legend=$fieldsetLabel;
$objFieldset->contents="<table><tr><td align=\"right\">"
  . $catLabel->show()
  . ": </td><td>" .$objCat->show() . "</td></tr>"
  ."<tr><td align=\"right\">" .  $titLabel->show()
  . ": </td><td>".$objTit->show()."</td></tr></table>";
//Add the fieldset to the form
$objForm->addToForm($objFieldset->show());


// Create an instance of the button object
$this->loadClass('button', 'htmlelements');
// Create a submit button
$objElement = new button('submit');
// Set the button type to submit
$objElement->setToSubmit();
// Use the language object to add the word save
$objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
// Create cancel button
$objCancel = new button('cancel');
$objCancel->setOnClick("window.location='".$this->uri(NULL)."';");
$objCancel->setValue(' '.$this->objLanguage->languageText("mod_storycategoryadmin_cancel", "storycategoryadmin").' ');
// Add the button to the form
$objForm->addToForm('<br/>'.$objElement->show()."&nbsp;".$objCancel->show());
//Add the heading to the layer
$this->objH = $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h1>
$this->objH->str=$objLanguage->languageText("mod_storycategoryadmin_title", "storycategoryadmin");
$rightSideColumn = $this->objH->show();

$rightSideColumn .= $objForm->show();
// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();

?>