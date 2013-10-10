<?php

// Get translation language items.
$headingText = $this->objLanguage->languageText('mod_educommons_importheading', 'educommons');
$fileText = $this->objLanguage->languageText('mod_educommons_imspackage', 'educommons');
$contextText = $this->objLanguage->languageText('mod_educommons_contextlabel', 'educommons');
$buttonText = $this->objLanguage->languageText('mod_educommons_uploadbutton', 'educommons');

// Create and output page heading.
$heading = $this->newObject('htmlheading', 'htmlelements');
$heading->htmlheading($headingText, 1);
echo $heading->show();

// Create the label for the file upload field.
$objFileLabel = $this->newObject('label', 'htmlelements');
$objFileLabel->label($fileText, 'input_fileupload');

// Create the file upload field.
$objFileUpload = $this->newObject('uploadinput', 'filemanager');

// Generate the file upload markup to go into the form.
$fileMarkup = '<p>' . $objFileLabel->show() . ': ' . $objFileUpload->show() . '</p>';

// Create the label for the context dropdown box.
$objContextLabel = $this->newObject('label', 'htmlelements');
$objContextLabel->label($contextText, 'input_context');

// Create and populate the context dropdown box.
$objContextDropdown = $this->newObject('dropdown', 'htmlelements');
$objContextDropdown->dropdown('context');

foreach ($contexts as $context) {
    $objContextDropdown->addOption($context['contextcode']);
}

// Generate the context dropdown markup to go into the form.
$contextMarkup = '<p>' . $objContextLabel->show() . ': ' . $objContextDropdown->show() . '</p>';

// Create the upload submit button.
$objUploadButton = new button('upload', $buttonText);
$objUploadButton->setToSubmit();

// Generate the upload submit button markup to go into the form.
$uploadMarkup = '<p>' . $objUploadButton->show() . '</p>';

// Generate the form action URI.
$uri = $this->uri(array('action'=>'upload'));

// Create, populate and output the form.
$objForm = new form('upload', $uri);
$objForm->extra = ' ENCTYPE=\'multipart/form-data\'';

$objForm->addToForm($fileMarkup);
$objForm->addToForm($contextMarkup);
$objForm->addToForm($uploadMarkup);

echo $objForm->show();
