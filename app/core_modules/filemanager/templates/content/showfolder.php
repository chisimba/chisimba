<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$this->appendArrayVar('headerParams', $this->getJavascriptFile('selectall.js', 'htmlelements'));

if ($folder['folderlevel'] == 2) {
    $icon = '';
    $folderpath = $this->objLanguage->languageText('mod_filemanager_myfiles', 'filemanager', 'My Files');
    $breadcrumbs = $this->objLanguage->languageText('mod_filemanager_myfiles', 'filemanager', 'My Files');
} else {
    $icon = $objIcon->getDeleteIconWithConfirm($folderId, array('action'=>'deletefolder', 'id'=>$folderId), 'filemanager', $this->objLanguage->languageText('mod_filemanager_confirmdeletefolder', 'filemanager', 'Are you sure wou want to remove this folder?'));
}

echo '<p>'.$breadcrumbs.'</p>';

switch ($this->getParam('message'))
{
    default:
        break;
    case 'foldercreated':
        echo '<span class="confirm">'.$this->objLanguage->languageText('mod_filemanager_folderhasbeencreated', 'filemanager', 'Folder has been created').' </span>'; break;
    case 'filesdeleted':
        echo '<span class="confirm">'.$this->getParam('numfiles').' '.$this->objLanguage->languageText('mod_filemanager_fileshavebeendeleted', 'filemanager', 'File(s) have been deleted').' </span>'; break;
    case 'folderdeleted':
        echo '<span class="confirm"><strong>'.$this->getParam('ref').'</strong> '.$this->objLanguage->languageText('mod_filemanager_folderhasbeendeleted', 'filemanager', 'folder has been deleted').' </span>'; break;
}

switch ($this->getParam('error'))
{
    default:
        break;
    case 'nofoldernameprovided':
        echo '<span class="error">'.$this->objLanguage->languageText('mod_filemanager_folderwasnotcreatednoname', 'filemanager', 'Folder was not created. No name provided').'</span>'; break;
    case 'illegalcharacters':
        echo '<span class="error">'.$this->objLanguage->languageText('mod_filemanager_folderwasnotcreatedillegalcharacters', 'filemanager', 'Folder was not created. Folders cannot contain any of the following characters').': \ / : * ? &quot; &lt; &gt; |</span>'; break;
}

echo '<h1>'.$folderpath.' '.$icon.'</h1>';

if (count($files) > 0 || count($subfolders) > 0) {
    $form = new form('deletefiles', $this->uri(array('action'=>'multidelete')));
    $form->addToForm($table);

    $button = new button ('submitform', $this->objLanguage->languageText('mod_filemanager_deleteselecteditems', 'filemanager', 'Delete Selected Items'));
    $button->setToSubmit();

    $selectallbutton = new button ('selectall', $this->objLanguage->languageText('phrase_selectall', 'system', 'Select All'));
    $selectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', true);");

    $deselectallbutton = new button ('deselectall', $this->objLanguage->languageText('phrase_deselectall', 'system', 'Deselect all'));
    $deselectallbutton->setOnClick("javascript:SetAllCheckBoxes('deletefiles', 'files[]', false);");

    $form->addToForm($button->show().' &nbsp; &nbsp; '.$selectallbutton->show().' '.$deselectallbutton->show());
    
    $folderInput = new hiddeninput('folder', $folderId);
    $form->addToForm($folderInput->show());
    
    echo $form->show();
} else {
    echo $table;
}

echo '<h3>'.$this->objLanguage->languageText('phrase_uploadfiles', 'system', 'Upload Files').'</h3>';
echo $this->objUpload->show($folderId);

echo $this->objFolders->showCreateFolderForm($folderId);

//$objSWFUpload = $this->newObject('swfupload');
//$objSWFUpload->additionalParams['folder'] = $folderId;
//echo $objSWFUpload->show();

?>