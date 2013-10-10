<?php
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$menuBar=& $this->getObject('contextsidebar','context');

$cssLayout->setLeftColumnContent($menuBar->show());

$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>