<?php
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('formatfilesize', 'files');

$this->loadClass('form', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = 'Upload Files';//$this->objLanguage->languageText('mod_filemanager_listof'.$category, 'filemanager');

echo $header->show();

echo '<p class="warning">Warning:</p>';
echo '<ul>';
echo '<li>Please remove all apostraphes from file names as they may corrupt the file upload name</li>';
echo '</ul>';

$this->objUpload->numInputs = 5;
echo $this->objUpload->show();

?>