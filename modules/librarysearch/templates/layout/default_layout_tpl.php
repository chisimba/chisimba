<?php

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);

//$leftColumn = $this->getVar('leftContent');
//$cssLayout->setLeftColumnContent($leftColumn.'<br />');

$middleColumn = $this->getVar('middleContent');
$cssLayout->setMiddleColumnContent($middleColumn);

echo $cssLayout->show();