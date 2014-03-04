<?php


	$this->loadClass('htmltable', 'htmlelements');
	$this->loadClass('link', 'htmlelements');
	$this->loadClass('htmlheading', 'htmlelements');
	$this->loadClass('form', 'htmlelements');
	$this->loadClass('textinput', 'htmlelements');
	$this->loadClass('hiddeninput', 'htmlelements');
	$this->loadClass('textarea', 'htmlelements');
	$this->loadClass('button', 'htmlelements');
	$this->loadClass('label', 'htmlelements');
	$this->loadClass('radio', 'htmlelements');
	$this->loadClass('dropdown', 'htmlelements');
	$this->loadClass('csslayout', 'htmlelements');
	$this->loadClass('layer', 'htmlelements');
	
	

	// Create an instance of the css layout class
	$cssLayout = &$this->newObject('csslayout', 'htmlelements');
	// Set columns to 2
	$cssLayout->setNumColumns(3);
	// get the sidebar object
	$this->leftMenu = $this->newObject('usermenu', 'toolbar');
	// Initialize left column
	$leftSideColumn = $this->leftMenu->show();
	$rightSideColumn = NULL;
	$middleColumn = NULL;

	$objIcon = $this->newObject('geticon', 'htmlelements');
	$objIcon->setIcon('loader');

	$link = new link($this->uri(array('action' => 'default')));

	$loadingIcon = $objIcon->show();

	//title
	$title = $this->objLanguage->languageText('mod_ahis_exchangeratetitle', 'openaris', 'Exchange rates');
	
	// Header
	$header = new htmlheading();
	$header->type = 2;
	$header->str = $title;
	
	$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;
$formTable->cssClass = 'min50';
	
$formTable = $this->newObject('htmltable', 'htmlelements');

// default currency
$default_currency = new dropdown('defaultcurrencyid');
//$default_currency .= '<option value ="Select one..."';
$default_currency->addFromDB($this->objCurrency->getAll("ORDER BY currency"), 'currency', 'currency');
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_defaultcurrency'),NULL,NULL,'right');
$formTable->addCell($default_currency->show());
$formTable->endRow();

//exchange currency
$exchange_currency = new dropdown('exchangecurrencyid');
$exchange_currency->addFromDB($this->objCurrency->getAll("ORDER BY currency"), 'currency', 'currency');
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_exchangecurrency'),NULL,NULL,'right');
$formTable->addCell($exchange_currency->show());
$formTable->endRow();

//start date
$dateStartPicker = $this->newObject('datepicker', 'htmlelements');
$dateStartPicker->name = 'startdate';
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_startdate'),NULL,NULL,'right');
$formTable->addCell($dateStartPicker->show(),NULL,NULL,'left');
$formTable->endRow();

//end date
$dateEndPicker = $this->newObject('datepicker', 'htmlelements');
$dateEndPicker->name = 'enddate';
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_enddate'),NULL,NULL,'right');
$formTable->addCell($dateEndPicker->show(),NULL,NULL,'left');
$formTable->endRow();


if (isset($output)) {
    $objMsg = $this->getObject('timeoutmessage','htmlelements');
    $objMsg->setHideTypeToNone();
    switch($output)
	{
		case 'yes':
		$objMsg->setMessage("Invalid Dates! Check your start and end dates.<br />");
		break;
	}
         
    $msg = $objMsg->show();

} else {
    $msg = '';
}

	$formAction = 'exchangerate_save';  
    $buttonText = 'Save';
	
	// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

//form validations
$form->addRule('defaultcurrencyid', $this->objLanguage->languageText('mod_ahis_defaultcurrencyiderror','openaris'),'required');
$form->addRule('exchangecurrencyid', $this->objLanguage->languageText('mod_ahis_exchangecurrencyiderror','openaris'),'required');
$form->addRule('startdate', $this->objLanguage->languageText('mod_ahis_startdateerror','openaris'),'datenotfuture');



if($dateEndPicker < $dateStartPicker)
{
	$form->addRule('enddate', $this->objLanguage->languageText('mod_ahis_enddateerror','openaris'),'datenotpast');
}

 //container-table
$topTable = $this->newObject('htmltable', 'htmlelements');
$topTable->startRow();
$topTable->addCell($formTable->show());
$topTable->endRow();
$form->addToForm($topTable->show());

 $button = new button('exchangerate_save', 'Save');
 $button->setCSS('saveButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'exchangerates_admin'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());


$objLayer = new layer();
$heading = $msg;
$objLayer->addToStr($heading);
$objLayer->addToStr($header->show()."<hr class='openaris' />".$form->show());
$objLayer->align = 'center';


echo $objLayer->show(); 

?>
