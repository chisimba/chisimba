<?php

$objBlocks = $this->getObject('blocks', 'blocks');



$cssLayout =& $this->getObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent($objBlocks->showBlock('login', 'security'));
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>
