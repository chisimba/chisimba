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
$header->str = $this->objLanguage->languageText('phrase_uploadfiles', 'system', 'Upload Files');

echo $header->show();

echo '<p class="warning">'.$this->objLanguage->languageText('word_warning', 'system', 'Warning').':</p>';
echo '<ul>';
echo '<li>'.$this->objLanguage->languageText('mod_filemanager_removeallapostraphesfromfilenames', 'filemanager', 'Please remove all apostraphes from file names as they may corrupt the file upload name').'</li>';
echo '</ul>';

$this->objUpload->numInputs = 5;
echo $this->objUpload->show();

?>