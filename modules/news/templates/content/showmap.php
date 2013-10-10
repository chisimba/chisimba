<?php
$this->loadClass('htmlheading', 'htmlelements');

echo $this->objNewsMenu->toolbar('map');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_news_newsinmapformat', 'news', 'News in Map Format');

echo $header->show();

$this->setVar('pageSuppressXML', TRUE);

$objMap = $this->getObject('simplebuildmap', 'simplemap');
$objMap->smap = str_replace('&amp;', '&', $this->uri(array('action'=>'generatekml', 'ext'=>'.smap')));
$objMap->width = '100%';
$objMap->height = '500';
$objMap->magnify = 2;
$objMap->gLat = 2;
$objMap->mapControlType = 'large';

//echo $objMap->getMapContents();

echo $objMap->show();

//echo'<script src="'.$this->uri(array('action'=>'generatekml', 'ext'=>'.kml')).'" type="text/javascript"></script>';


?>

