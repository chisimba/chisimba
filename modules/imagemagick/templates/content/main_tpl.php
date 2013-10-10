<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$htmlheading = new htmlheading();
$htmlheading->type = 1;
$htmlheading->str = $this->objLanguage->languageText('mod_imagemagick_selectpdffile', 'imagemagick', 'Select a PDF file');

echo $htmlheading->show();

$form = new form ('convert', $this->uri(array('action'=>'convert')));

$objSelectFile = $this->newObject('selectfile', 'filemanager');
$objSelectFile->restrictFileList = array('pdf');

$button = new button('convert', $objLanguage->languageText('mod_imagemagick_converttoimage', 'imagemagick', 'Convert to Image'));
$button->setToSubmit();

$form->addToForm($objSelectFile->show().'<br />'.$button->show());

echo $form->show();
?>
<p>Note. This requires an installation of imagemagick.</p>