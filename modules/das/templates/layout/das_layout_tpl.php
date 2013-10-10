<?php

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setLeftColumnContent($this->objViewRender->getLeftBlocks());
$cssLayout->setRightColumnContent($this->objViewRender->getRightBlocks());
echo $cssLayout->show();
?>
