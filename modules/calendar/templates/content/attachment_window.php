<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');

$this->setVar('bodyParams', 'class="iframewindow"');

$objFileIcons =& $this->getObject('fileicons', 'files');
$objIcon = $this->getObject('geticon', 'htmlelements');

if (count($files) == 0) {
	echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('phrase_noattachments_at_present', 'calendar', 'No Attachments at Present').'</div>';
} else {
	$table = $this->getObject('htmltable', 'htmlelements');
	$table->cellpadding = 4;
	//print_r($files);
	foreach ($files AS $file)
	{
		$table->startRow();
		$table->addCell($objFileIcons->getFileIcon($file['filename']), 20);
		$table->addCell($file['filename'], '90%');
		
		$array = array('action'=>'deleteattachment', 'filename'=>$file['filename'], 'mode'=>$mode, 'id'=>$id);
		$deleteIcon = $objIcon->getDeleteIconWithConfirm(NULL, $array, 'calendar', $this->objLanguage->languageText('phrase_confirm_delete_attachment', 'Are you sure you want to delete this attachment?'));

		$table->addCell($deleteIcon, 20);
		$table->endRow();
	}
	echo $table->show();
}


$form = new form('upload', $this->uri(array('action' => 'uploadattachment')));
    
$form->extra = 'enctype="multipart/form-data"';




$fileInput = new textinput('upload');
$fileInput->fldType = 'file';
$fileInput->size = 50;

$form->addToForm($fileInput->show());


$hiddenId = new textinput('id');
$hiddenId->fldType = 'hidden';
$hiddenId->value = $id;

$hiddenMode = new textinput('mode');
$hiddenMode->fldType = 'hidden';
$hiddenMode->value = $mode;

//$table->addCell();

$submitButton = new button('submit', $this->objLanguage->languageText('mod_calendar_fileupload', 'calendar'));
$submitButton->setToSubmit();
$form->addToForm(' '.$submitButton->show().$hiddenId->show().$hiddenMode->show());



echo $form->show();

?>
