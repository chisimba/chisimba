<?php
/*
* Layout template for group administration
* @package groupadmin
*/

/**
* @param object $rightInfo The right information panel
*/

$objLayer= &$this->newObject( 'layer', 'htmlelements');
$objHead= &$this->newObject('htmlheading','htmlelements');

$objHead->str=isset($heading)?$heading:NULL;
$objHead->type=1;

$main = $objHead->show();

$objLayer->str = $this->getContent();

$main.=$objLayer->show();

echo $main;
?>
