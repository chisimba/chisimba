<?php

$objModule = $this->getObject('modules', 'modulecatalogue');


if ($objModule->checkIfRegistered('filemanager')) {

    
    
    
    $objSelectFile = $this->getObject('selectimage', 'filemanager');
    $objSelectFile->name = 'imageselect';
    //$objSelectFile->restrictFileList = array('jpg', 'gif', 'png', 'jpeg', 'bmp');
   
}

echo $objSelectFile->show();
//print_r($files);

?>