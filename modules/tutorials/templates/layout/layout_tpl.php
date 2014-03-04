<?php
/**
* @package tutorials
*/

/**
* Default layout for the tutorials module
*/

$cssLayout = $this->newObject('csslayout','htmlelements');
$leftMenu = $this->getObject('contextsidebar', 'context');

$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>