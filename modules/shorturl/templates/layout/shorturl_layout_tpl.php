<?php

$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objBlocks =  $this->newObject('blocks', 'blocks');
$objLucene =  $this->newObject('searchresults', 'search');
$objModule =  $this->newObject('modules', 'modulecatalogue');
$objLink =  $this->newObject('link', 'htmlelements');
$objUser = $this->newObject('user', 'security');
$objLanguage = $this->newObject('language', 'language');

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);

//$leftColumn = $this->getVar('leftContent');

//$cssLayout->setLeftColumnContent($leftColumn.'<br />');

$middleColumn = $this->getVar('middleContent');

$cssLayout->setMiddleColumnContent($middleColumn);

echo $cssLayout->show();

$this->setVar('footerStr', $this->footerStr);
