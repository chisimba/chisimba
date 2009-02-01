<?php
$middleColumn = $this->getVar('middleColumn');

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);
$cssLayout->setMiddleColumnContent($middleColumn);

echo $cssLayout->show();


?>

