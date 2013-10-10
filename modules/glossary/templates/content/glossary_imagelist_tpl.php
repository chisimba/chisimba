<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$objSelectFile = $this->newObject('selectimage', 'filemanager');

// Filemanage test of image input

//$objSelectFile->name = 'nameofforminput';
//$editTermForm->addToForm($objSelectFile->show());

$objIcon =& $this->getObject('geticon', 'htmlelements');
$objIcon->alt = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
$objIcon->title = $this->objLanguage->languageText('mod_glossary_delete', 'glossary');
$objIcon->setIcon('delete');


$objPop= $this->newObject('windowpop', 'htmlelements');

$listTable=$this->newObject('htmltable','htmlelements');
$listTable->width='80%';
$listTable->attributes=' border="0"';
$listTable->cellspacing='0';
$listTable->cellpadding='5';

if (count($images) == 0)
{
    echo ('<p>'.$this->objLanguage->languageText('mod_glossary_noimageslisted', 'glossary').'</p>');

}

foreach ($images AS $image)
{
    $listTable->startRow();

    $link = $this->uri(array('action' => 'previewimage', 'id' => $image['image'], 'fname' => $image['filename']));


    $objPop->set('location',$link);
    $objPop->set('window_name','previewImage');
    $objPop->set('linktext',$image['caption']);
    $objPop->set('width','10');
    $objPop->set('height','10');
    $objPop->set('left','100');
    $objPop->set('top','100');

    $objConfirm=&$this->newObject('confirm','utilities');

    $url = $this->uri(array('action'=>'deleteimage', 'id' => $image['imageid'], 'returnid' => $id	)); //$image['image']

    $objConfirm->setConfirm($objIcon->show(),$url,$this->objLanguage->languageText('mod_glossary_areyousuredeleteimage', 'glossary'));

    //echo ('<li>'.$image['filename'].'</li>');

    $listTable->addCell($objPop->show());
    $listTable->addCell($objConfirm->show());

    //echo ('<li>'.$objPop->show().' '.$objConfirm->show().'</li>');

    $listTable->endRow();
}

echo $listTable->show();

//echo ('</ul>');

$form = new form('uploadimage', $this->uri(array(
		'module' => 'glossary',
		'action' => 'uploadimage'
	)));

$form->extra = 'enctype="multipart/form-data"';


$table = $this->getObject('htmltable', 'htmlelements');
$table->width='99%';
$table->cellpadding = 4;

$table->startRow();
$captionLabel = new label($this->objLanguage->languageText('mod_glossary_imagecaption', 'glossary'), 'input_caption');

$table->addCell($captionLabel->show());

$caption = new textinput('caption');
$caption->size = 30;

$table->addCell($caption->show());
$table->endRow();

$table->startRow();
$fileLabel = new label($this->objLanguage->languageText('mod_glossary_file', 'glossary'), 'input_userFile');
$table->addCell($fileLabel->show());

$objSelectFile->name = 'userFile';
//$fileInput = new textinput('userFile');
//$fileInput->fldType = 'file';

$table->addCell($objSelectFile->show());
$table->endRow();

$file = $this->getParam('userFile');


$table->startRow();
$hiddenId = new textinput('id');
$hiddenId->fldType = 'hidden';
$hiddenId->value = $id;
$table->addCell($hiddenId->show());

$submitButton = new button('submit', $this->objLanguage->languageText('mod_glossary_addimage', 'glossary'));
$submitButton->setToSubmit();
$table->addCell($submitButton->show());

$table->endRow();

$form->addToForm($table->show());
$form->addRule('caption',$this->objLanguage->languageText('mod_glossary_caption_validation', 'glossary'),'required');

/*
$form->addRule(array('name'=>'caption','length'=>15), 'Your surname is too long', 'maxlength');
$form->addRule('caption','Please enter your name','required');
$form->addRule('userFile','Please enter your name','required');
*/
echo $form->show();
?>