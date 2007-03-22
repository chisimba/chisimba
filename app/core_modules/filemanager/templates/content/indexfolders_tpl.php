<?php

$this->objIndexFiles = $this->getObject('indexfiles');

$results = $this->objIndexFiles->scanDirectory($this->objConfig->getcontentBasePath().'users/');


foreach ($results[1] as $folder)
{
    $this->objFolders->indexFolder($folder).'<br />';
    


}

$this->objFiles->updateFilePath();
?>(.)*?usrfiles