<?php
$userMenu  = &$this->newObject('usermenu','toolbar');
// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

// Add Post login menu to left column
$leftSideColumn ='';
$leftSideColumn = $userMenu->show();
$middleColumn = NULL;

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

$this->href = $this->getObject('href', 'htmlelements');

$devmsg = urldecode($devmsg);
$usrmsg = urldecode($usrmsg);

$midcol = $devmsg;
$midcol .= $usrmsg;
$cssLayout->setMiddleColumnContent($midcol);

echo $cssLayout->show();
?>