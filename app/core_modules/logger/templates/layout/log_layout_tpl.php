<?php
/**
* @package toolbar
*/

/**
* Layout template for the test module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');

$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($this->logDisplay->leftMenu());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>