<?php
$middleColumn = $this->getVar('middleContent');

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);
$cssLayout->setMiddleColumnContent($middleColumn);

echo $cssLayout->show();
?>
