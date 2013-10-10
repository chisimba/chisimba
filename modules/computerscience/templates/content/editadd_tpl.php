<?php
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$this->loadClass('htmlheading', 'htmlelements');

// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;
$rightSideColumn = NULL;

$rightSideColumn .= $this->objDict->uMenu();

$leftCol = NULL;
$middleColumn = NULL;
$leftCol .= $objSideBar->show();

$middleColumn .= $message;
$middleColumn .= $str;

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);

echo $cssLayout->show();
?>