<?php
$this->loadClass('htmlheading', 'htmlelements');

echo $this->objNewsMenu->toolbar('themecloud');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_hotels_newsintagcloudformat', 'hotels', 'Hotels in Tag Cloud Format');

echo $header->show();

echo $this->objKeywords->getKeywordCloud();


?>