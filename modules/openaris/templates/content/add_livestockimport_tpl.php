<?php
	$this->loadClass('htmlheading', 'htmlelements');
	$this->loadClass('form', 'htmlelements');
	$this->loadClass('textinput', 'htmlelements');
	$this->loadClass('button', 'htmlelements');
	$this->loadClass('label', 'htmlelements');
	$this->loadClass('dropdown', 'htmlelements');
	$this->loadClass('layer', 'htmlelements');
	
	$title = 'Livestock Import';
	
	// Header
	$header = new htmlheading();
	$header->type = 2;
	$header->str = $title;
	
	
$formTable = $this->newObject('htmltable', 'htmlelements');
$formTable->cellspacing = 2;
$formTable->width = NULL;

$label_district = new label ('District Name:', 'district');
$input_district = new textinput('district', $dist);
$input_district->extra = 'readonly';

$formTable->startRow();
$formTable->addCell($label_district->show());
$formTable->addCell($input_district->show());
$formTable->endRow();

$label_pointentry = new label ('Point of Entry:', 'entrypoint');
$entrypoint= new textinput('entrypoint');

$formTable->startRow();
$formTable->addCell($label_pointentry->show());
$formTable->addCell($entrypoint->show());
$formTable->endRow();


$label_countspecies = new label('Number of Live Animals: ','countspecies');
$countspecies = new textinput('countspecies');
$formTable->startRow();
$formTable->addCell($label_countspecies->show());
$formTable->addCell($countspecies->show());
$formTable->endRow();
// animal origin	
$label = new label ('Animal origin: ', 'origin');
$origin = new dropdown('origin');
$origin->addFromDB($geo2, 'name', 'name');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($origin->show());
$formTable->endRow();

// animal destination	
$label = new label ('Animal Destination: ', 'destination');
$destination = new dropdown('destination');
$destination->addFromDB($geo2, 'name', 'name');

$formTable->startRow();
$formTable->addCell($label->show());
$formTable->addCell($destination->show());
$formTable->endRow();

// animal classification	
$label = new label ('Animal Classification: ', 'classification');
$classification = new dropdown('classification');
$classification->addFromDB($species, 'name', 'name');

$formTable->startRow();
$formTable->addCell($label->show()."&nbsp;");
$formTable->addCell($classification->show());
$formTable->endRow();

//products	
$label_products = new label ('Products: ', 'products');
$label_units = new label (' :Units', 'units');
$formTable->startRow();
$formTable->addCell($label_products->show());
$formTable->addCell($label_units->show());
$formTable->endRow();

$label_eggs = new label('Eggs: ','eggs');
$eggs = new textinput('eggs');
$formTable->startRow();
$formTable->addCell($label_eggs->show());
$formTable->addCell($eggs->show());
$formTable->endRow();

$label_milk = new label('Milk: ','milk');
$milk = new textinput('milk');
$formTable->startRow();
$formTable->addCell($label_milk->show());
$formTable->addCell($milk->show());
$formTable->endRow();

$label_cheese = new label('Cheese: ','cheese');
$cheese = new textinput('cheese');
$formTable->startRow();
$formTable->addCell($label_cheese->show());
$formTable->addCell($cheese->show());
$formTable->endRow();

$label_poultry = new label('Poultry: ','poutry');
$poultry = new textinput('poultry');
$formTable->startRow();
$formTable->addCell($label_poultry->show());
$formTable->addCell($poultry->show());
$formTable->endRow();

$label_beef = new label('Beef: ','beef');
$beef = new textinput('beef');
$formTable->startRow();
$formTable->addCell($label_beef->show());
$formTable->addCell($beef->show());
$formTable->endRow();

$formAction = 'livestockimport_save';  
$buttonText = 'Save';
	
// Create Form
$form = new form ('add', $this->uri(array('action'=>$formAction)));

//form validations
$form->addRule('district', $this->objLanguage->languageText('mod_ahis_districterror','openaris'),'required');
$form->addRule('entrypoint', $this->objLanguage->languageText('mod_ahis_entrypointerror','openaris'),'required');
$form->addRule('entrypoint', $this->objLanguage->languageText('mod_ahis_entrypointerror','openaris'),'nonnumeric');
$form->addRule('origin', $this->objLanguage->languageText('mod_ahis_originerror','openaris'),'required');
$form->addRule('destination', $this->objLanguage->languageText('mod_ahis_destinationerror','openaris'),'required');
$form->addRule('classification', $this->objLanguage->languageText('mod_ahis_classificationerror','openaris'),'required');
$form->addRule('eggs', $this->objLanguage->languageText('mod_ahis_eggserror','openaris'),'required');
$form->addRule('eggs', $this->objLanguage->languageText('mod_ahis_eggsnumbererror','openaris'),'numeric');
$form->addRule('milk', $this->objLanguage->languageText('mod_ahis_milkerror','openaris'),'required');
$form->addRule('milk', $this->objLanguage->languageText('mod_ahis_milknumbererror','openaris'),'numeric');
$form->addRule('cheese', $this->objLanguage->languageText('mod_ahis_cheeseerror','openaris'),'required');
$form->addRule('cheese', $this->objLanguage->languageText('mod_ahis_cheesenumbererror','openaris'),'numeric');
$form->addRule('poultry', $this->objLanguage->languageText('mod_ahis_poultryerror','openaris'),'required');
$form->addRule('poultry', $this->objLanguage->languageText('mod_ahis_poultrynumbererror','openaris'),'numeric');
$form->addRule('beef', $this->objLanguage->languageText('mod_ahis_beeferror','openaris'),'required');
$form->addRule('beef', $this->objLanguage->languageText('mod_ahis_beefnumbererror','openaris'),'numeric');
$form->addRule('countspecies', $this->objLanguage->languageText('mod_ahis_countspecieserror','openaris'),'required');
$form->addRule('countspecies', $this->objLanguage->languageText('mod_ahis_countspeciesnumbererror','openaris'),'numeric');


$form->addToForm($formTable->show());
 
$save = new button('livestockimport_save', 'Save');
$save->setToSubmit();
$save->setCSS('saveButton');
$backUri = $this->uri(array('action'=>'select_officer'));
$cancel = new button('back', $this->objLanguage->languageText('word_cancel'), "javascript: document.location='$backUri'");
$cancel->setCSS('cancelButton');

$form->addToForm($save->show()." ",NULL,NULL,'right');
$form->addToForm($cancel->show());

$objLayer = new layer();
$objLayer->addToStr($header->show()."<hr />".$form->show());

echo $objLayer->show(); 
?>