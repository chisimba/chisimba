<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

echo '<p>'.$breadcrumbs.'</p>';

switch ($this->getParam('message'))
{
    default:
        break;
    case 'foldercreated':
        echo '<span class="confirm">Folder has been created </span>'; break;
    case 'filesdeleted':
        echo '<span class="confirm">File(s) have been deleted </span>'; break;
}

switch ($this->getParam('error'))
{
    default:
        break;
    case 'nofoldernameprovided':
        echo '<span class="error">Folder was not created. No name provided</span>'; break;
    case 'illegalcharacters':
        echo '<span class="error">Folder was not created. Folders cannot contain any of the following characters: \ / : * ? &quot; &lt; &gt; |</span>'; break;
}

echo '<h1>'.$folderpath.'</h1>';

if (count($files) > 0) {
    $form = new form('deletefiles', $this->uri(array('action'=>'multidelete')));
    $form->addToForm($table);

    $button = new button ('submitform', 'Delete Selected Items');
    $button->setToSubmit();

    $selectallbutton = new button ('selectall', 'Select All');
    $selectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', true);");

    $deselectallbutton = new button ('deselectall', 'Deselect All');
    $deselectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', false);");

    $form->addToForm($button->show().' &nbsp; &nbsp; '.$selectallbutton->show().' '.$deselectallbutton->show());
    
    $folderInput = new hiddeninput('folder', $folderId);
    $form->addToForm($folderInput->show());
    
    echo $form->show();
} else {
    echo $table;
}

echo $this->objUpload->show($folderId);

echo $this->objFolders->showCreateFolderForm($folderId);

 ?>


