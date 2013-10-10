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
	$title = $this->objLanguage->languageText('mod_ahis_speciescategoriestitle', 'openaris', 'Species categories');
	
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


//species name
$species_name = new dropdown('speciesnameid');
$species_name->setSelected('id');
$species_name->addFromDB($speciesnames,'speciesname','id');
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_speciesname'),NULL,NULL,'right');
$formTable->addCell($species_name->show());
$formTable->endRow();

//category
$category = new textinput('category');
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_category'),NULL,NULL,'right');
$formTable->addCell($category->show());
$formTable->endRow();

//abbreviation
$abbreviation = new textinput('abbreviation');
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_abbreviation'),NULL,NULL,'right');
$formTable->addCell($abbreviation->show());
$formTable->endRow();

//description
$description = new textarea('description');
$formTable->startRow();
$formTable->addCell($this->objLanguage->languageText('phrase_description'),NULL,NULL,'right');
$formTable->addCell($description->show());
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

	$formAction = 'speciescategories_save';  
    $buttonText = 'Save';
	
	// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

//form validations
$form->addRule('speciesnameid', $this->objLanguage->languageText('mod_ahis_speciesnameerror','openaris'),'required');
$form->addRule('category', $this->objLanguage->languageText('mod_ahis_categoryerror','openaris'),'required');
$form->addRule('abbreviation', $this->objLanguage->languageText('mod_ahis_abbreviationerror','openaris'),'required');
$form->addRule('description', $this->objLanguage->languageText('mod_ahis_descriptionerror','openaris'),'required');
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
$button = new button ('speciescategories_save', 'Save');
$button->setCSS('saveButton');
$button->setToSubmit();
$backUri = $this->uri(array('action'=>'speciescategory_admin'));
$btcancel = new button('cancel', 'Cancel', "javascript: document.location='$backUri'");
$btcancel->setCSS('cancelButton');

$form->addToForm($button->show()." ");
$form->addToForm($btcancel->show());



$objLayer = new layer();
$objLayer->addToStr($header->show()."<hr />".$form->show());
$objLayer->align = 'center';
echo $objLayer->show(); 

?>
