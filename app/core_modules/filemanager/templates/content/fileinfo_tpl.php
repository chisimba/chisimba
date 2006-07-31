<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('formatfilesize', 'files');

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


$objIcon->setIcon('download');
$link = new link ($this->uri(array('action'=>'file', 'id'=>$file['id'], 'filename'=>$file['filename'])));
$link2 = new link ($this->uri(array('action'=>'file', 'id'=>$file['id'], 'filename'=>$file['filename'])));

$link->link = $objIcon->show();
$link2->link = $this->objLanguage->languageText('phrase_downloadfile', 'filemanager', 'Download File');

echo '<p><br />'.$link->show().' '.$link2->show().'</p>';

echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_filehistory', 'filemanager', 'File History').'</h3>';
echo $this->objFiles->getFileHistory($file['id']);

echo '<h3>'.$this->objLanguage->languageText('mod_filemanager_filepreview', 'filemanager', 'File Preview').'</h3>';
echo $objFilePreview->previewFile($file['id']);


?>