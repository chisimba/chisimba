<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$middleColumn = NULL;
$leftCol = NULL;
$rightSideColumn = NULL;
$cssLayout->setNumColumns(2);

//get the category manager
$middleColumn = $card;

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
echo $cssLayout->show();

?>
