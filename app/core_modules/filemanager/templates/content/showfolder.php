<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$this->appendArrayVar('headerParams', $this->getJavascriptFile('selectall.js', 'htmlelements'));

if ($folder['folderlevel'] == 2) {
    $icon = '';
    $folderpath = 'My Files';
    $breadcrumbs = 'My Files';
} else {
    $icon = $objIcon->getDeleteIconWithConfirm($folderId, array('action'=>'deletefolder', 'id'=>$folderId), 'filemanager', 'Are you sure wou want to remove this folder');
}

echo '<p>'.$breadcrumbs.'</p>';

switch ($this->getParam('message'))
{
    default:
        break;
    case 'foldercreated':
        echo '<span class="confirm">Folder has been created </span>'; break;
    case 'filesdeleted':
        echo '<span class="confirm">File(s) have been deleted </span>'; break;
    case 'folderdeleted':
        echo '<span class="confirm"><strong>'.$this->getParam('ref').'</strong> folder has been deleted </span>'; break;
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

echo '<h1>'.$folderpath.' '.$icon.'</h1>';

if (count($files) > 0 || count($subfolders) > 0) {
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

echo '<h3>Upload Files</h3>';
echo $this->objUpload->show($folderId);

echo $this->objFolders->showCreateFolderForm($folderId);

 ?>


