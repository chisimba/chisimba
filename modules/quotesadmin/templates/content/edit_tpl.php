<?php

/* 
Set up the form processor
Note that this is only a basic edit form. You will want to
edit this form and change it to be more along the lines of what
you want. Especially note inputs that should not be there, etc.
You can also change the layout of the form.*/

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
$objForm = new form('tbl_quotes');
//Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
//Set the displayType to 3 for freeform
$objForm->displayType=3;
// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

//Set the content of the left side column
$leftSideColumn = "Replace this with what you want in the leftside column";

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
//Retrieve the data
if (isset($ar)) {
    $id = $ar['id'];
    $quote = $ar['quote'];
    $whosaidit = $ar['whosaidit'];
    $dateCreated = $ar['datecreated'];
    $creatorId = $ar['creatorid'];
    $dateModified = $ar['datemodified'];
    $modifierId = $ar['modifierid'];
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


//Create an element for the input of quote
$objElement = new textarea ("quote");
//Set the value of the element to $quote
if (isset($quote)) {
    $objElement->setContent($quote);
}
//Create label for the input of quote
$quoteLabel = new label($this->objLanguage->languageText('mod_quotesadmin_quote','quotesadmin'), "input_quote");
//Add the $quote element to the form
$objForm->addToForm($quoteLabel->show()."<br />".$objElement->show()."<br /><br />");

//Create an element for the input of whosaidit
$objElement = new textinput ("whosaidit");
//Set the value of the element to $whosaidit
if (isset($whosaidit)) {
    $objElement->setValue($whosaidit);
}
// Create label for course list
$wsiLabel = new label($this->objLanguage->languageText('mod_quotesadmin_whosaidit','quotesadmin'), "input_whosaidit");
//Add the $whosaidit element to the form
$objForm->addToForm($wsiLabel->show()."<br />".$objElement->show()."<br /><br />");

// Create an instance of the button object
$this->loadClass('button', 'htmlelements');
// Create a submit button
$objElement = new button('submit');	
// Set the button type to submit
$objElement->setToSubmit();	
// Use the language object to add the word save
$objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
//Create cancel button
$objCancel = new button('cancel');
$objCancel->setOnClick("window.location='".$this->uri(NULL)."';");
$objCancel->setValue(' ' . $this->objLanguage->languageText("mod_quotesadmin_cancel",'quotesadmin') . ' ');
// Add the buttons to the form
$objForm->addToForm('<br/>'.$objElement->show()."&nbsp;".$objCancel->show());
//Add the heading to the layer
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h1>
if($mode=='edit'){
  $this->objH->str=$objLanguage->languageText("mod_quotesadmin_edittitle",'quotesadmin');
 } else {
       $this->objH->str=$objLanguage->languageText("mod_quotesadmin_addtitle",'quotesadmin');
      }
$rightSideColumn = $this->objH->show();

$rightSideColumn .= $objForm->show();
// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();

?>
