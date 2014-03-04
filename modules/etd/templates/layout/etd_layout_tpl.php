<?php
/**
* @package etd
*
* Layout template for the etd module
*/

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$cssLayout->setLeftColumnContent($this->etdTools->getLeftSide());
$cssLayout->setMiddleColumnContent($this->getContent());
//$cssLayout->setRightColumnContent();

echo $cssLayout->show();
?>