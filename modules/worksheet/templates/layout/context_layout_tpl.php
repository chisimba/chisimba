<?php
/**
* Template layout for worksheet module
* @package worksheet
*/

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$leftMenu=& $this->getObject('contextsidebar', 'context');


$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>