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
	$title = $this->objLanguage->languageText('mod_ahis_exchangeratedetailstitle', 'openaris', 'Exchange rate details');
	
	// Header
	$header = new htmlheading();
	$header->type = 2;
	$header->str = $title;
	//echo $header->show();
	
	
	
	$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;
$formTable->cssClass = 'min50';
	
$formTable = $this->newObject('htmltable', 'htmlelements');

// exchange rate 
$label_exchange_rate = new label ('First currency:', 'firstcurrency');
$first_currency = new dropdown('firstcurrency');
$first_currency->addFromDB($this->objExchangerate->getAll("ORDER BY defaultcurrencyid"), 'defaultcurrencyid', 'defaultcurrencyid');
$formTable->startRow();
$formTable->addCell($label_exchange_rate->show(),NULL,NULL,'right');
$formTable->addCell($first_currency->show());
$formTable->endRow();

//second currency
$label_exchange_rate = new label ('Second currency:', 'secondcurrency');
$second_currency = new dropdown('secondcurrency');
$second_currency->addFromDB($this->objExchangerate->getAll("ORDER BY exchangecurrencyid"), 'exchangecurrencyid', 'exchangecurrencyid');
$formTable->startRow();
$formTable->addCell($label_exchange_rate->show(),NULL,NULL,'right');
$formTable->addCell($second_currency->show());
$formTable->endRow();

// conversion factor currency
$label_default_currency = new label ('Conversion factor:', 'conversionfactor');
$conversion_factor = new textinput('conversionfactor');

$formTable->startRow();
$formTable->addCell($label_default_currency->show(),NULL,NULL,'right');
$formTable->addCell($conversion_factor->show());
$formTable->endRow();



//start date
$label_start_date = new label('Start date: ','startdate');
$dateStartPicker = $this->newObject('datepicker', 'htmlelements');
$dateStartPicker->name = 'startdate';

$formTable->startRow();
$formTable->addCell($label_start_date->show(),NULL,NULL,'right');
$formTable->addCell($dateStartPicker->show(),NULL,NULL,'left');
$formTable->endRow();

//end date
$dateEndPicker = $this->newObject('datepicker', 'htmlelements');
$dateEndPicker->name = 'enddate';

$label_end_date = new label('End date: ','enddate');
$formTable->startRow();
$formTable->addCell($label_end_date->show(),NULL,NULL,'right');
$formTable->addCell($dateEndPicker->show(),NULL,NULL,'left');
$formTable->endRow();



	$formAction = 'exchangeratedetails_save';  
    $buttonText = 'Save';
	
	// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

//form validations
$form->addRule('conversionfactor', $this->objLanguage->languageText('mod_ahis_conversionfactorerror','openaris'),'numeric');
$form->addRule('startdate', $this->objLanguage->languageText('mod_ahis_startdateerror','openaris'),'datenotfuture');
if($dateStartPicker > $dateEndPicker)
{
	$form->addRule('enddate', $this->objLanguage->languageText('mod_ahis_enddateerror','openaris'),'datenotpast');
}


 //container-table
$topTable = $this->newObject('htmltable', 'htmlelements');
$topTable->startRow();
$topTable->addCell($formTable->show());
$topTable->endRow();
$form->addToForm($topTable->show());

 //buttons
$button = new button ('exchangeratedetails_save', 'Save');
$button->setCSS('saveButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'exchangeratedetails_admin'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());

$objLayer = new layer();
$objLayer->addToStr($header->show()."<hr />".$form->show());
$objLayer->align = 'center';

echo $objLayer->show(); 

?>
