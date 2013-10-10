<?php
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$sideMenu =& $this->getObject('sidemenu','toolbar');

$cssLayout->setLeftColumnContent($sideMenu->menuUser());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>
