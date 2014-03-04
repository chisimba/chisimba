<?php
/**
* Template layout for assignment management module
* @package assignment
*/

/**
* Template layout for assignment management module
*/

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$toolbar = $this->getObject('contextsidebar', 'context');

$cssLayout->setLeftColumnContent($toolbar->show());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>