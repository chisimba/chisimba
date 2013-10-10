<?php
/**
* Main template file for the skins upload module
* Displays data and forms
*/

print "<div align='center'>";

/**
* Upload template for skinsuploader module
* Allows upload of skins
* Written by James Scoble using code written by Wesley Nitsckie 
* and modified by Jarrett L. Jordaan
*/

//Creating the form
$form = $this->newObject('form','htmlelements');
$form->extra=' enctype="multipart/form-data" ';
$form->name='importusers';
$paramArray = array('action' => 'uploadSkin');
$form->setAction($this->uri($paramArray,'skinsuploader'));

//the file input
$fileInput = $this->newObject('textinput','htmlelements');
$fileInput->fldType='file';
$fileInput->label=$this->objLanguage->languageText("word_upload");
$fileInput->name='upload';
$fileInput->size=60;

//the submit button
$objElement = new button('CSV');
$objElement->setToSubmit();
$objElement->setValue($this->objLanguage->languageText("word_upload"));

//add the objects to the form
$form->setDisplayType(1);
$form->addToForm($fileInput);
$form->addToForm($objElement);

//Heading
$objHeading = $this->newObject('htmlheading','htmlelements');
$objHeading->str=$this->objLanguage->languageText("word_upload_heading");
$objHeading->type=3;
$strCenter=$objHeading->show();
$strCenter.=$form->show();

print $strCenter."\n";

print"</div>\n";

?>
