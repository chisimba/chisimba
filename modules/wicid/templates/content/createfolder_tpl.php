<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
//Load Classes
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$header = new htmlheading();
$header->type = 2;
$this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'wicid');

if ($selected == '') {
    $folders = $this->getDefaultFolder($this->baseDir);
    $selected = $folders[0];
}
if ($selected != "unknown0") {
    $cfile = substr($selected, strlen($this->baseDir));
    $header->str = $cfile;

    //echo $header->show();
}

$createFolder = "";
if ($this->objUser->isAdmin()) {
    $createFolder = $this->objUtils->showCreateFolderForm("/", $selected);
}
echo '<p><strong id="confirm">'.$successmsg."</strong></p>";
echo $createFolder;


?>