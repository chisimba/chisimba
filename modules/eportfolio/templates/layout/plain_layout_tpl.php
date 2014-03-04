<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$cssLayout->setColumnContent($this->getContent());
//$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>
