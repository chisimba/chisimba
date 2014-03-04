<?php
//Load the form class
$this->loadClass('form','htmlelements');
//Load the textinput class
$this->loadClass('textinput','htmlelements');
//Load the textarea class
$this->loadClass('textarea','htmlelements');
//Load the button object
$this->loadClass('button', 'htmlelements');
//Load the label class
$this->loadClass('label', 'htmlelements');
//load the table class
$this->loadClass('htmlTable', 'htmlelements');
//load the Heading class
$this->loadClass('htmlHeading', 'htmlelements');


//Create and instance of the form class
$objForm = new form('festival', $this->uri(array('action'=>'convert')));

//create an element for the name of file label
$objNameLabel = new label($this->objLanguage->languageText('mod_festival_file', 'festival'), NULL);
//create an element for the heading label
$objHeadingLabel = new htmlHeading();
$objHeadingLabel->str=$this->objLanguage->languageText('mod_festival_mainHeading', 'festival');
$objHeadingLabel->type=1;
//create an element for the subheading label
$objSubheadingLabel = new htmlHeading();
$objSubheadingLabel->str=$this->objLanguage->languageText('mod_festival_subHeading', 'festival');
$objSubheadingLabel->type=2;
// Create an element for the comment title
$objTitle = new textinput('title', NULL);
//create an element for the comment label
$objCommentLabel = new label($this->objLanguage->languageText('mod_festival_comment', 'festival'), NULL);
//Create an element for the input of comment
$objComment = new textarea ("comment");
// Create a create button
$objElement = new button('create');
// Set the button type to submit
$objElement->setToSubmit();
// with the word create
$objElement->setValue(' '.$this->objLanguage->languageText("word_create").' ');

//create a table
$objTable = new htmltable('festival');

//Add a heading row in the table
$objTable->startHeaderRow();
$objTable->addHeaderCell("<br />");
$objTable->addHeaderCell($objHeadingLabel->show());
$objTable->endHeaderRow();

//Add a subheading row in the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell($objSubheadingLabel->show());
$objTable->endRow();

//Add an empty row to the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->endRow();

//Add the add the label and text input to the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell($objNameLabel->show(),'70%');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell($objTitle->show(),'70%');
$objTable->endRow();

//Add an empty row to the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->endRow();

//Add the comment label and text area to the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell($objCommentLabel->show(),'70%');
$objTable->endRow();

//Add the comment text area to the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell($objComment->show(),'70%');
$objTable->endRow();

//Add an empty row to the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->endRow();

//Add the create button to the table
$objTable->startRow();
$objTable->addCell("<br />");
$objTable->addCell($objElement->show());
$objTable->endRow();

//Add the table to the form
$objForm->addToForm($objTable->show());

//Create rules so that the title and comment areas cannot be null
$objForm->addRule('title',$objLanguage->languageText("mod_festival_val_titnotnull"),'required');
$objForm->addRule('comment',$objLanguage->LanguageText("mod_festival_val_comnotnull"),'required');

// Display the form in the page
echo $objForm->show();
?>