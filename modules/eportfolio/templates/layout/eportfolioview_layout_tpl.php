<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
//$objMenu =& $this->newObject('sidemenu','toolbar');
//$cssLayout->setLeftColumnContent($objMenu->menuUser());
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>
