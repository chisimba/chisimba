<?php
/**
* @package contextgroups
*/

/**
* Layout template for the contextgroups module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$leftMenu = $this->newObject('contextmenu','toolbar');
$objHead = $this->newObject('htmlheading','htmlelements');

if(!isset($heading))
    $heading= $title;//"HEADING";//$objLanguage->languageText('mod_test_name');

$objHead->str=$heading;
$objHead->type=1;
$head = $objHead->show();

$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($head.$this->getContent());

echo $cssLayout->show();
?>
