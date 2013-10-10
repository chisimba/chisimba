<?php
/**
* @package etd
*
* Layout template for the etd module
*/
$objMenu = $this->getObject('sidemenu', 'toolbar');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$cssLayout->setLeftColumnContent($objMenu->menuUser());
$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setRightColumnContent($right);

echo $cssLayout->show();
?>