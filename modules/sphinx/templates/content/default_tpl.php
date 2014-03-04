<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$middleColumn = NULL;
$leftColumn = NULL;

$middleColumn = $box;

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftColumn);

echo $cssLayout->show();    
?>
