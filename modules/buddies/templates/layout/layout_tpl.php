<?php
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$sideMenu =& $this->getObject('sidemenu','toolbar');

//$this->loadClass('csslayout','htmlelements');

$cssLayout->setLeftColumnContent($sideMenu->show('user'));
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>
