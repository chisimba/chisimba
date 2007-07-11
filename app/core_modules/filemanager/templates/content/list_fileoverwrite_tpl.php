<?php
$this->loadClass('htmlheading', 'htmlelements');

$result = $this->getParam('result');

if ($result != '') {

    $string = '';
    
    $results = explode('--------', $result);
    foreach ($results as $item)
    {
        $split = explode('----', $item);
        if (count($split) >= 2) {
            
            switch ($split[1])
            {
                case 'deletetemp': $action = $this->objLanguage->languageText('mod_filemanager_tempfiledeleted', 'filemanager', 'Temporary File Deleted'); break;
                case 'overwrite': $action = $this->objLanguage->languageText('mod_filemanager_oldfileoverwritten', 'filemanager', 'Old file overwritten'); break;
                case 'renamed': $action = $this->objLanguage->languageText('mod_filemanager_filerenamedto', 'filemanager', 'File renamed to').' '; break;
                default: $action = $this->objLanguage->languageText('mod_filemanager_noactiononfile', 'filemanager', 'No action taken on file');
            }
            $string .= '<li>'.$split[0].' - '.$action.'</li>';
        }
    }
    
    if ($string != '') {
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('mod_filemanager_fileoverwriteresults', 'filemanager', 'File Overwrite Results');

        echo $header->show();
        echo '<ul>'.$string.'</ul>';
    }
}

$objCheckOverwrite = $this->getObject('checkoverwrite');

if  ($objCheckOverwrite->checkUserOverwrite() == 0 && $results == '') {

    $header = new htmlheading();
    $header->type = 1;
    $header->str = $this->objLanguage->languageText('mod_filemanager_nofilesneedoverwrite', 'filemanager', 'No Files need to be Overwritten');

    echo $header->show();
    
    
    echo '<p><a href="'.$this->uri(NULL).'">'.$this->objLanguage->languageText('mod_filemanager_returntofilemanager', 'filemanager', 'Return to File Manager').'</a></p>';
    
    
} else if  ($objCheckOverwrite->checkUserOverwrite() != 0) {

    $header = new htmlheading();
    $header->type = 1;
    $header->str = $this->objLanguage->languageText('phrase_overwritefiles', 'filemanager', 'Overwrite Files?');

    echo $header->show();

    echo $this->objLanguage->languageText('mod_filemanager_explainoverwrite', 'filemanager', 'Recently you tried to upload some files that already exist on the server. Instead of automatically overwriting them, the uploaded file has been stored in a temporary folder pending your action. Please indicate how what you would like them to do with them.');

    echo $objCheckOverwrite->showUserOverwiteInterface();
}
if  ($objCheckOverwrite->checkUserOverwrite() == 0) {
    echo $this->objUpload->show();
}
?>