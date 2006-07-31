<?

$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('phrase_uploadresults', 'filemanager', 'Upload Results');

echo $header->show();

echo $successMessage;
echo $errorMessage;

$objCheckOverwrite = $this->getObject('checkoverwrite');

if  ($objCheckOverwrite->checkUserOverwrite() == 0) {
    echo '<p><a href="'.$this->uri(NULL).'">'.$this->objLanguage->languageText('mod_filemanager_returntofilemanager', 'filemanager', 'Return to File Manager').'</a></p>';
    
    echo $this->objUpload->show();
} else {

    $header->str = $this->objLanguage->languageText('phrase_overwritefiles', 'filemanager', 'Overwrite Files?');

    echo $header->show();

    echo $this->objLanguage->languageText('mod_filemanager_explainoverwrite', 'filemanager', 'Recently you tried to upload some files that already exist on the server. Instead of automatically overwriting them, the uploaded file has been stored in a temporary folder pending your action. Please indicate how what you would like them to do with them.');

    echo $objCheckOverwrite->showUserOverwiteInterface();

}
?>