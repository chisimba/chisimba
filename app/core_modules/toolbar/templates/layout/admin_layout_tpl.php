<?php
/**
* @package toolbar
*/

/**
* Layout template for the test module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$leftMenu = $this->newObject('usermenu','toolbar');
$rightMenu = $this->getObject('userLoginHistory','security');
$tabpane = $this->newObject('tabpane', 'htmlelements');
$objTable = $this->newObject('htmltable', 'htmlelements');
$tab = $this->newObject('tabbedbox', 'htmlelements');
$cssLayout->setNumColumns(2);
$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($this->getContent());


echo $cssLayout->show();
?>