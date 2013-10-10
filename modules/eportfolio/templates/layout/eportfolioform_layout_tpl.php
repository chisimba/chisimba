<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objMenu = &$this->newObject('sidemenu', 'toolbar');
$cssLayout->setLeftColumnContent($objMenu->menuUser());
//$cssLayout->setRightColumnContent($objMenu->menuUser());
$cssLayout->setMiddleColumnContent($this->getContent());
//$cssLayout->setMiddleColumnContent($this->getContent());
//echo $cssLayout->show();
$content = &$this->getContent();
echo $content;
?>
