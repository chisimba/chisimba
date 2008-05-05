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
$header->str = $this->objLanguage->languageText('phrase_searchresultsfor', 'system', 'Search Results for').' <em>'.$this->getParam('filequery').'</em>';

echo $header->show();

$objSearch = $this->getObject('searchresults', 'search');
echo $objSearch->displaySearchResults($this->getParam('filequery'), 'filemanager', NULL, array('basefolder'=>'users/'.$this->objUser->userId()));

?>