<?php
//Sending display to 1 column layout
ob_start();

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('multitabbedbox', 'htmlelements');

$header = new htmlheading();
$header->type=1;
$header->str='Add an Attachment';
echo $header->show();


$attachmentForm = new form('attachmentForm', $this->uri( array('action'=>'addattachment', 'type'=>$forumtype)));
$attachmentForm->displayType = 3;

$content1 = 'asffsafs';

$filesTable = $this->getObject('htmltable', 'htmlelements');
$filesTable->width='99%';
$filesTable->cellpadding = 4;

foreach ($files as $file)
{
    $filesTable->startRow();
    
    $fileCheckbox = new checkbox('attachments[]');
    if ($file['used'] != '') {
        $fileCheckbox->setChecked(true);
    }
    $fileCheckbox->extra = ' value="'.$file['attachment_id'].'"';
	$filesTable->addCell($fileCheckbox->show(), 10);
    
    $filesTable->addCell($file['filename']);
    $filesTable->addCell($file['description']);
    
    $filesTable->endRow();
}

$attachmentForm->addToForm($filesTable->show());

// Element_id
$hiddenId = new textinput('id');
$hiddenId->fldType = 'hidden';
$hiddenId->value = $id;
$attachmentForm->addToForm($hiddenId->show());

// Forum
$hiddenId = new textinput('forum');
$hiddenId->fldType = 'hidden';
$hiddenId->value = $forum;
$attachmentForm->addToForm($hiddenId->show());

$submitButton = new button('submitform', $this->objLanguage->languageText('mod_forum_attachselected'));
$submitButton->cssClass = 'upload';
$submitButton->setToSubmit();
$attachmentForm->addToForm($submitButton->show());

// start - second tab contents

$form = new form('uploadimage', $this->uri(array('action' => 'uploadattachment')));
    
$form->extra = 'enctype="multipart/form-data"';


$table = $this->getObject('htmltable', 'htmlelements');
$table->width='99%';
$table->cellpadding = 4;

$table->startRow();
$fileLabel = new label($this->objLanguage->languageText('mod_forum_filetoupload'), 'input_userFile');
$table->addCell($fileLabel->show());

$fileInput = new textinput('userFile');
$fileInput->fldType = 'file';
$fileInput->size = 50;

$table->addCell($fileInput->show());
$table->endRow();

$table->startRow();
$descriptionLabel = new label($this->objLanguage->languageText('word_description'), 'input_description');
$table->addCell($descriptionLabel->show());

$descriptionInput = new textinput('description');
$descriptionInput->size = 40;
$table->addCell($descriptionInput->show());
$table->endRow();

$table->startRow();

$hiddenId = new textinput('id');
$hiddenId->fldType = 'hidden';
$hiddenId->value = $id;
$table->addCell($hiddenId->show());

$submitButton = new button('submitform2', $this->objLanguage->languageText('mod_forum_uploadfile'));
$submitButton->cssClass = 'upload';
$submitButton->setToSubmit();
$table->addCell($submitButton->show());

$table->endRow();

$form->addToForm($table->show());    

$hiddenId = new textinput('forum');
$hiddenId->fldType = 'hidden';
$hiddenId->value = $forum;
$form->addToForm($hiddenId->show());

// ENd second tab content

$objElement =new multitabbedbox('75%','95%');

if (count($files) > 0) {
    $objElement->addTab(array('name'=>$this->objLanguage->languageText('mod_forum_selectattachment'),'content' => $attachmentForm->show(),'default' => true));
    
    $objElement->addTab(array('name'=>$this->objLanguage->languageText('mod_forum_uploadfile'),'content' => $form->show()));
    
    echo $objElement->show();
    
} else {

    echo $form->show();
}

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);

//print_r($files);

?>