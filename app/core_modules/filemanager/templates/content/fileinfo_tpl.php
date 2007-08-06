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

$objFilePreview = $this->getObject('filepreview');

$objFileIcons = $this->getObject('fileicons', 'files');
$objFileIcons->size = 'large';

$objIcon->setIcon('edit');

$editLink = new link ($this->uri(array('action'=>'editfiledetails', 'id'=>$file['id'])));
$editLink->link = $objIcon->show();

echo '<h1>'.$objFileIcons->getFileIcon($file['filename']).' '.str_replace('_', ' ', htmlentities($file['filename'])).$editLink->show().'</h1>';

echo '<br /><p><strong>'.$this->objLanguage->languageText('word_description', 'system', 'Description').':</strong> <em>'.$file['filedescription'].'</em></p>';

echo '<p><strong>'.$this->objLanguage->languageText('word_tags', 'system', 'Tags').':</strong> ';

if (count($tags) == 0) {
    echo '<em>'.$this->objLanguage->languageText('phrase_notags', 'system', 'no tags').'</em>';
} else {
    $comma = '';
    foreach ($tags as $tag)
    {
        $tagLink = new link ($this->uri(array('action'=>'viewbytag', 'tag'=>$tag)));
        $tagLink->link = $tag;
        
        echo $comma.$tagLink->show();
        $comma = ', ';
    }
}

echo '</p>';

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
    $form->addToForm($this->objLanguage->languageText('mod_filemanager_extractarchiveto', 'filemanager', 'Extract Archive to').': '.$this->objFolders->getTreedropdown($folderId));
    
    $button = new button ('submitform', $this->objLanguage->languageText('mod_filemanager_extractfiles', 'filemanager', 'Extract Files'));
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