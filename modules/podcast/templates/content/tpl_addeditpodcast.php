<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_podcast_addpodcast', 'podcast');
$header->type = 1;

echo $header->show();

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_filemanager_uploadnewfile', 'filemanager', 'Upload new file');
$header->type = 4;

echo $header->show();

$form = new form('addpodcastbyupload', $this->uri(array('action'=>'uploadpodcast')));
$form->extra = 'enctype="multipart/form-data"';

$objUpload = $this->newObject('uploadinput', 'filemanager');
$objUpload->restrictFileList = array('mp3');

$button = new button('submitform', $this->objLanguage->languageText('mod_podcast_uploadpodcast', 'podcast', 'Upload Podcast'));
$button->setToSubmit();;

$form->addToForm($objUpload->show().'<br />'.$button->show());


echo $form->show();

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_filemanager_chooseexisting', 'filemanager', 'Choose existing file from file manager');
$header->type = 4;

echo $header->show();

$form = new form('addpodcast', $this->uri(array('action'=>'savenewpodcast')));


$objSelectFile = $this->newObject('selectfile', 'filemanager');

$objSelectFile->name = 'podcast';
$objSelectFile->restrictFileList = array('mp3');

$button = new button('submitform', $this->objLanguage->languageText('mod_podcast_addpodcast', 'podcast'));
$button->setToSubmit();;

$form->addToForm($objSelectFile->show().'<br />'.$button->show());


echo $form->show();

$link = new link ($this->uri(NULL));
$link->link = $this->objLanguage->languageText('mod_podcast_returntopodcasthome', 'podcast');

echo '<p>'.$link->show().'</p>';
?>