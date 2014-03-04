<?php
/**
* @package etd
*
* Layout template for the etd module
*/

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$cssLayout->setLeftColumnContent($this->etdTools->getLeftSide());
$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setRightColumnContent($this->etdTools->getRightSide());

echo $cssLayout->show();
?>