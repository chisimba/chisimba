<?php
$this->setVar('pageSuppressXML', TRUE);

$this->loadClass('iframe', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_podcaster_uploadsteptwo', 'podcaster', 'Step 1: Select/Create upload folder');
$upPath = $this->objLanguage->languageText('mod_podcaster_uploadpath', 'podcaster', 'Upload path');

echo $header->show();
$path = $folderdata['folderpath'];
$folderid = $folderdata['id'];
echo "<p>".$upPath.": ".$path."</p>";

$objAjaxUpload = $this->newObject('ajaxuploader');

echo $objAjaxUpload->showForm($path, $folderid);


?>
