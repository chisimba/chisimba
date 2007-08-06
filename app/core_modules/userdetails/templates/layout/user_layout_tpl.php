<?php

// Create an Instance of the CSS Layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$objUserMenu = $this->getObject('usermenu', 'toolbar');

$cssLayout->setLeftColumnContent($objUserMenu->show());

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();

?>