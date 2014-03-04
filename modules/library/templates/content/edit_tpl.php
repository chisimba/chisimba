<?php

/* 
Set up the form processor
Note that this is only a basic edit form. You will want to
edit this form and change it to be more along the lines of what
you want. Especially note inputs that should not be there, etc.
You can also change the layout of the form.*/
$objLayer =& $this->newObject('layer', 'htmlelements');

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
$objForm = new form('tbl_library');
//Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
//Set the displayType to 3 for freeform
$objForm->displayType=3;

//Retrieve the data
if (isset($ar)) {
    $id = $ar['id'];
    $title = $ar['title'];
    $description = $ar['description'];
    $url = $ar['url'];
    $creatorId = $ar['creatorid'];
    $dateCreated = $ar['datecreated'];
    $modifierId = $ar['modifierid'];
    $dateModified = $ar['datemodified'];
}

// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=3; //Heading <h3>
$this->objH->str=$objLanguage->languageText("mod_library_title",'library');
$objForm->addToForm($this->objH->show());

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


//Create an element for the input of title
$objElement = new textarea ("title");
$objElement->cols = 80;
$objElement->rows = 2;
//Set the value of the element to $title
if (isset($title)) {
    $objElement->setContent($title);
}
//Add the $title element to the form
$titleLabel = new label('<b>'.$objLanguage->languageText("word_title").'</b>', 'input_title');
$objForm->addToForm($titleLabel->show() . ":<br />".$objElement->show()."<br /><br />");

//Create an element for the input of description
$objElement = new textarea ("description");
$objElement->cols = 80;
$objElement->rows = 6;
//Set the value of the element to $description
if (isset($description)) {
    $objElement->setContent($description);
}
//Add the $description element to the form
$titleDesc = new label('<b>'.$objLanguage->languageText("word_description").'</b>', 'input_description');
$objForm->addToForm($titleDesc->show() . ":<br />".$objElement->show()."<br /><br />");

//Create an element for the input of url
$objElement = new textinput ($name = "url", $value = "http://");

$objElement->size = 83;
//Set the value of the element to $url
if (isset($url)) {
    $objElement->setValue($url);
}
//Add the $url element to the form
$titleUrl = new label('<b>'.$objLanguage->languageText("word_url").'</b>', 'input_url');
$objForm->addToForm($titleUrl->show() . ":<br />".$objElement->show()."<br /><br />");

// Create an instance of the button object
$this->loadClass('button', 'htmlelements');
// Create a submit button
$objElement = new button('submit');	
// Set the button type to submit
$objElement->setToSubmit();	
// Use the language object to add the word save
$objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
// Add the button to the form
$objForm->addToForm('<br />'.$objElement->show());

$objLayer->str = $objForm->show();
$objLayer->padding = '5px;';
echo $objLayer->show();
?>