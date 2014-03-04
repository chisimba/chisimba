<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
//Load Classes
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
        
$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_podcaster_uploadstepone', 'podcaster', 'Upload podcast step 1');

$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');

if ($selected == '') {

    $folders = $this->getDefaultFolder($this->baseDir);
    $selected = $folders[0];
}

if ($selected != "unknown0") {
    $cfile = substr($selected, strlen($this->baseDir));
    //$header->str = $cfile;
}

echo $header->show();

$createFolder = "";
$label = $this->objLanguage->languageText('mod_podcaster_uploadpodcast', 'podcaster', 'Upload podcast');
$note = "<b> * ".$this->objLanguage->languageText('mod_podcaster_upsteponecreate', 'podcaster', 'Type in the name of folder if you want to create a new folder within the selected folder')."</b>";
$buttonLabel = $this->objLanguage->languageText('word_next', 'system', 'System')." ".$this->objLanguage->languageText('mod_podcaster_wordstep', 'podcaster', 'Step');

$buttonNote = $this->objLanguage->languageText('mod_podcaster_clicknext', 'podcaster', 'Click on the "Next step" button to save and proceed to uploading the podcast file');

$createFolder = $this->objUtils->showCreateFolderForm("/", 'createfolder2',$label, $buttonLabel,$note, $buttonNote);

echo '<p><strong id="confirm">'.$successmsg."</strong></p>";

echo $createFolder;

?>