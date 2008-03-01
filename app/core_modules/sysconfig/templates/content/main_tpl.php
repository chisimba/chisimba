<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$cssLayout->setMiddleColumnContent($str);
$cssLayout->setLeftColumnContent("Working here");
echo $cssLayout->show();
?>