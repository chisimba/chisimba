<?php
/*
* Template for uploading themes.
* 
*/

// set up html elements
$this->loadClass('htmlheading', 'htmlelements');
$this->loadclass('htmltable','htmlelements');
$this->loadclass('textinput','htmlelements');
$objConfirm = $this->newObject('timeoutmessage','htmlelements');
$objSelectFile = $this->newObject('selectfile','filemanager');
// set up language items

$btnupload=$this->objLanguage->languageText('mod_contextadmin_upload' ,'contextadmin','Upload Theme');
$uploadhead=$btnupload;
$head=$uploadhead;
$btnexit=$this->objLanguage->languageText('word_exit');
$wordstudent=ucwords($this->objLanguage->languageText('mod_context_readonly'));

/************************* set up table ******************************/
$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_filemanager_uploadnewfile', 'filemanager', 'Upload new file');
$header->type = 4;

echo $header->show();

$form = new form('addpodcastbyupload', $this->uri(array('action'=>'saveuploadedtheme','id'=>$id)));
$form->extra = 'enctype="multipart/form-data"';

$objUpload = $this->newObject('uploadinput', 'filemanager');

$button = new button('submitform', $btnupload);
$button->setToSubmit();;
$form->addToForm($objUpload->show().'<br />'.$button->show());
echo $form->show();

?>