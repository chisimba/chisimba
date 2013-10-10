<?php
$cssLayout = &$this->newObject('csslayout','htmlelements');
$cssLayout->setLeftColumnContent('');
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>