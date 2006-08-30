<?php
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$userMenu  = &$this->newObject('usermenu','toolbar');
$objTextArea = $this->loadclass('textarea', 'htmlelements');
$objHiddenInput = $this->loadclass('hiddeninput', 'htmlelements');

$objForm = new form('errormail',$this->uri(array('action'=>'errormail')));
$objTextArea = new textarea('comments','');

// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_errors_heading', 'errors');


// Add Post login menu to left column
$leftSideColumn ='';
$leftSideColumn = $userMenu->show();

$midcol = $header->show();

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

$this->href = $this->getObject('href', 'htmlelements');

$objHiddenInput = new hiddeninput('error', $mess);

$blurb = $this->objLanguage->languagetext("mod_errors_blurb", "errors");
//$midcol .= $blurb;
$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_usrtitle", "errors"), $mess);//'<div class="featurebox">' . nl2br($usrmsg) . '</div>';

//create the form
$objForm->displayType = 4;
$objForm->addToFormEx($objLanguage->languageText('mod_errors_submiterrs', 'errors'));
$objForm->addToFormEx($objTextArea->show());
$objForm->addToFormEx($objHiddenInput->show());

$this->objButton=&new button($objLanguage->languageText('word_submit', 'system'));
$this->objButton->setValue($objLanguage->languageText('word_submit', 'system'));
$this->objButton->setToSubmit();
$objForm->addToFormEx($this->objButton->show());


$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_mailadmins", "errors"),$objForm->show());
$cssLayout->setMiddleColumnContent($midcol);

echo $cssLayout->show();

?>