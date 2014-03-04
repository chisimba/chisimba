<?php
/**
* Download content template for essay.
* @package essay
*/

$objFiles = $this->getObject('dbfile','filemanager');
$file = $objFiles->getFileInfo($this->getParam('fileid'));
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