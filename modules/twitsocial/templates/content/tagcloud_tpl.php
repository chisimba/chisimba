<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');

// Set columns to 3
$cssLayout->setNumColumns(2);
$leftMenu = NULL;
$leftCol = NULL;
$middleColumn = NULL;
$rightSideColumn = NULL;

$middleColumn .= $tagcloud;
$leftCol .= $objSideBar->show();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>