<?php
$this->loadClass('htmlheading', 'htmlelements');

$objCheckOverwrite = $this->getObject('checkoverwrite');

if  ($objCheckOverwrite->checkUserOverwrite() == 0) {
    $header = new htmlheading();
    $header->type = 1;
    $header->str = $this->objLanguage->languageText('mod_filemanager_nofilesneedoverwrite', 'filemanager', 'No Files need to be Overwritten');

    echo $header->show();

    //echo '<p>&nbsp;</p>';
    echo '<p><a href="javascript:window.close();">'.$this->objLanguage->languageText('phrase_closewindow', 'filemanager', 'Close this Window').'</a></p>';
} else {

    $header = new htmlheading();
    $header->type = 1;
    $header->str = $this->objLanguage->languageText('phrase_overwritefiles', 'filemanager', 'Overwrite Files?');

    echo $header->show();

    echo $this->objLanguage->languageText('mod_filemanager_explainoverwrite', 'filemanager', 'Recently you tried to upload some files that already exist on the server. Instead of automatically overwriting them, the uploaded file has been stored in a temporary folder pending your action. Please indicate how what you would like them to do with them.');

    echo $successMessage;
    echo $errorMessage;

    echo $objCheckOverwrite->showUserOverwiteInterface();

}
?>