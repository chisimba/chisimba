<?php
$cssLayout =& $this->newObject('csslayout', 'htmlelements');

$userMenuBar=& $this->getObject('sidemenu','toolbar');
$toolbar = $this->getObject('contextsidebar', 'context');
$cssLayout->setLeftColumnContent($toolbar->show());

$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>
