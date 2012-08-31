<?php
/**
* @package toolbar
*/

/**
* Layout template for the test module
*/
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$leftMenu = $this->newObject('adminmenu','toolbar');
$rightMenu = $this->getObject('userLoginHistory','security');
$tabpane = $this->newObject('tabpane', 'htmlelements');
$objTable = $this->newObject('htmltable', 'htmlelements');
//$tab = $this->newObject('tabbedbox', 'htmlelements');
$cssLayout->setNumColumns(2);

$left = '<div class="toolbar_left">' . $leftMenu->show() . '</div>';
$cssLayout->setLeftColumnContent($left);

$ret = '<div class="toolbar_main">' . $this->getContent() . '</div>';
$cssLayout->setMiddleColumnContent($ret);

echo $cssLayout->show();
?>