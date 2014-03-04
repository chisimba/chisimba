<?php
//Sending display to 1 column layout
ob_start();

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$form = new form('saveattachment', $this->uri(array('action'=>'saveattachment')));

// Todo Implement WorkGroup
// Todo Implement Context
$objSelectFile = $this->newObject('selectfile', 'filemanager');
$objSelectFile->name = 'attachment';
$form->addToForm($objSelectFile->show());

$button = new button('save', 'Attach File');
$button->cssClass = 'save';
$button->setToSubmit();
$form->addToForm(' &nbsp; &nbsp; '.$button->show());

$hiddeninput = new hiddeninput('id', $id);
$form->addToForm($hiddeninput->show());

echo $form->show();

if (count($files) > 0) {

    echo '<ul>';
    
    foreach ($files AS $file)
    {
        $icon = $objIcon->getDeleteIconWithConfirm($file['id'], array('action'=>'deleteattachment', 'id'=>$file['id'], 'attachmentwindow'=>$id), 'forum', 'Are you sure wou want to remove this attachment');
        echo ('<li>'.$file['filename'].' '.$icon.'</li>');
    
    }
    
    echo '</ul>';
} 

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);

?>