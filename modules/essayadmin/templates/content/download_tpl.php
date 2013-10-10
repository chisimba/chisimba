<?php
/**
* Template for downloading essays.
* @package essayadmin
*/

$objFiles = $this->getObject('dbfile','filemanager');
$file = $objFiles->getFileInfo($fileId);
if ($file == FALSE) {
    throw customException('File does not exist');
}
$objConfig = $this->getObject('altconfig', 'config');
$filePath = $objConfig->getcontentPath().$file['path'];
$objCleanUrl = $this->getObject('cleanurl','filemanager');
$objCleanUrl->cleanUpUrl($filePath);
if (file_exists($filePath)) {
    header("Location: {$filePath}");
} else {
    throw customException('File does not exist');
}

?>