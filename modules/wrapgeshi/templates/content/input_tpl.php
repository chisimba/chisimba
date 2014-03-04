<?php

//Set up the form processor
$paramArray=array(
    'action'=>'parse');
$formAction=$this->uri($paramArray);

//Get the save button
$objButtons = & $this->getObject('navbuttons', 'navigation');
$saveButton = $objButtons->putSaveButton();

//Set the title
$formTitle=$this->objLanguage->code2Txt("mod_wrapgeshi_title", "wrapgeshi");


//Load the form elements that I need
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');


//Create the form class
$objForm = new form('code');
$objForm->setAction($formAction);
$objForm->displayType=3;  //Free form

//Add the form title to the form
//$objForm->addToForm('<div align="center">');
$objForm->addToForm('<h3 align="center">'.$formTitle.'</h3>');

//Create a table to layout the form elements
$objFmTable = $this->newObject("htmltable", "htmlelements");
$objFmTable->width = "70%";

//Add a row for the code language
$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("word_language").":&nbsp;", NULL, "top", "right");
$objTextInput = new textinput('language');
$objFmTable->addCell($objTextInput->show(), NULL, "top", "left");
$objFmTable->endRow();

//Add a row for the line number
$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("mod_wrapgeshi_stlineno", "wrapgeshi").":&nbsp;", NULL, "top", "right");
$objLineInput = new textinput('line', 1);
$objFmTable->addCell($objLineInput->show(), NULL, "top", "left");
$objFmTable->endRow();


//Add a row for the code
$objFmTable->startRow();
$objFmTable->addCell($objLanguage->languageText("word_code").":&nbsp;", NULL, "top", "right");
$objTextArea = new textarea('code');
$objTextArea->cols=77;
$objTextArea->rows=30;
$objFmTable->addCell($objTextArea->show(), NULL, "top", "left");
$objFmTable->endRow();

//Add the current table to the form
$objForm->addToForm($objFmTable->show());

//Add a save button
$objButton = $this->newObject('button', 'htmlelements');
$objButton->button('save', $this->objLanguage->languageText('word_highlight'));
$objButton->setToSubmit();
$objForm->addToForm($objButton->show()."<br /><br />");



// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);
//Set the leftsidecolumn
$leftSideColumn = $objLanguage->languageText("mod_wrapgeshi_info", "wrapgeshi");
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
$cssLayout->setMiddleColumnContent($objForm->show()); 
//Render output
echo $cssLayout->show(); 

?>