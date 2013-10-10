<?php
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$heading = new htmlheading();
$heading->str = 'Upload Presentation';
$heading->type = 1;

echo $heading->show();

if ($this->getParam('message') != '') {
    echo '<p class="error">Error: '.$this->getParam('message').'</p>';
}


$form = new form ('uploadfile', $this->uri(array('action'=>'doupload')));
$form->extra = 'enctype="multipart/form-data"';

$fileInput = new textinput('fileupload');
$fileInput->fldType = 'file';

$button = new button ('upload', 'Upload');
$button->setToSubmit();

$form->addToForm($fileInput->show().' '.$button->show());

echo $form->show();



?>

