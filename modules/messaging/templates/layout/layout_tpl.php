<?php
/**
* @package messaging
*/

/**
* Default layout for the messaging module
*/

$cssLayout=&$this->newObject('csslayout','htmlelements');
$cssLayout->setNumColumns(2);

$leftColumn=&$this->newObject('sidemenu','toolbar');

$cssLayout->setLeftColumnContent($leftColumn->menuUser());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>