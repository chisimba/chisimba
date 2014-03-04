<?php
/**
* Layout template for the wikiwriter module
* 
* @author Ryan Whitney, ryan@greenlikeme.org 
* @package wikiwriter
*/

$cssLayout = &$this->newObject('cssLayout', 'htmlelements');
$sidemenu = &$this->newObject('renderToolbar', 'wikiwriter');

// Add to layout and display
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($sidemenu->show());
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();

?>
