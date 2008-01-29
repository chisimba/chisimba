<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_useradmin_searchresultsfor', 'system', 'Search Results for').': <em>'.$searchText.'</em>';

echo $header->show();

echo $searchResults;

?>