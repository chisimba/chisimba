<?php
$this->loadClass('htmlheading', 'htmlelements');

echo $this->objNewsMenu->toolbar('themecloud');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_news_newsintagcloudformat', 'news', 'News in Tag Cloud Format');

echo $header->show();

echo $this->objKeywords->getKeywordCloud();


?>