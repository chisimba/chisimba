<?

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('phrase_uploadresults', 'filemanager', 'Upload Results');

echo $header->show();

echo $successMessage;
echo $errorMessage;

if  ($overwriteMessage == '') {
    echo '<p><a href="'.$this->uri(NULL).'">'.$this->objLanguage->languageText('mod_filemanager_returntofilemanager', 'filemanager', 'Return to File Manager').'</a>';
    
    if ($this->getParam('folder') != '') {
        $folder = $this->objFolders->getFolder($this->getParam('folder'));
        
        if ($folder != FALSE) {
            $folderLink = new link ($this->uri(array('action'=>'viewfolder', 'folder'=>$folder['id'])));
            
            if ($folder['folderlevel'] == 2) {
                $folderLink->link = 'Return to <strong>My Files</strong> Folder';
            } else {
                $folderLink->link = 'Return to <strong>'.basename($folder['folderpath']).'</strong> Folder';
            }
            
            echo ' / '.$folderLink->show();
            
            $this->setVar('folderId', $folder['id']);
        }
    }
    
    echo '</p>';
    
    //echo $this->objUpload->show();
} else {

    $header->str = $this->objLanguage->languageText('phrase_overwritefiles', 'filemanager', 'Overwrite Files?');
    
    $header->type = 2;

    echo $header->show();

    echo '<p>'.$this->objLanguage->languageText('mod_filemanager_explainoverwrite', 'filemanager', 'Recently you tried to upload some files that already exist on the server. Instead of automatically overwriting them, the uploaded file has been stored in a temporary folder pending your action. Please indicate how what you would like them to do with them.').'</p>';

    echo $overwriteMessage;

}
?>