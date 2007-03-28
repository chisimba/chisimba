<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('formatfilesize', 'files');

echo $this->objFolders->generateBreadcrumbsFromUserPath($this->objUser->userId(), $file['path']);

// Get Folder Id of Item
$folderId = $this->objFolders->getFolderId(dirname($file['path']));

// Set for Layout Template
$this->setVar('folderId', $folderId);

$objIcon = $this->newObject('geticon', 'htmlelements');

$objFilePreview =& $this->getObject('filepreview');

$objFileIcons =& $this->getObject('fileicons', 'files');
$objFileIcons->size = 'large';

echo '<h1>'.$objFileIcons->getFileIcon($file['filename']).' '.str_replace('_', ' ', htmlentities($file['filename'])).'</h1>';


echo '<p><em>'.$file['description'].'</em></p>';

echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_fileinfo', 'filemanager', 'File Information').'</h3>';

echo $this->objFiles->getFileInfoTable($file['id']);


if (array_key_exists('width', $file)) {
    echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_mediainfo', 'filemanager', 'Media Information').'</h3>';
    echo $this->objFiles->getFileMediaInfoTable($file['id']);
}

$fileDownloadPath = $this->objConfig->getcontentPath().$file['path'];
$this->objCleanUrl->cleanUpUrl($fileDownloadPath);
            
$objIcon->setIcon('download');
$link = new link ($fileDownloadPath);
$link2 = new link ($fileDownloadPath);

$link->link = $objIcon->show();
$link2->link = $this->objLanguage->languageText('phrase_downloadfile', 'filemanager', 'Download File');

echo '<p><br />'.$link->show().' '.$link2->show().'</p>';


if ($file['category'] == 'archives' && $file['datatype'] == 'zip') {
    $form = new form ('extractarchive', $this->uri(array('action'=>'extractarchive')));
    $form->addToForm('Extract Archive to: '.$this->objFolders->getTreedropdown($folderId));
    
    $button = new button ('submitform', 'Extract Files');
    $button->setToSubmit();
    
    $form->addToForm($button->show());
    
    $hiddeninput = new hiddeninput ('file', $file['id']);
    $form->addToForm($hiddeninput->show());
    echo $form->show();
}

// echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_filehistory', 'filemanager', 'File History').'</h3>';
// echo $this->objFiles->getFileHistory($file['id']);

echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview').'</h3>';
echo $objFilePreview->previewFile($file['id']);


?>