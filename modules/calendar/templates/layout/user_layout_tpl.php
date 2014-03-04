<?php

// Create an Instance of the CSS Layout
$cssLayout =& $this->newObject('csslayout', 'htmlelements');

// Create an Instance of the User Side Menu
//$sideMenuBar=& $this->getObject('sidemenu','toolbar');
$sideMenuBar = $this->getObject('usermenu', 'toolbar');

$objContext =& $this->getObject('dbcontext', 'context');

// Set the Content of left side column
if ($objContext->getContextCode() == '') {
    //$cssLayout->setLeftColumnContent($sideMenuBar->show('user'));
    $cssLayout->setLeftColumnContent($sideMenuBar->show());
} else {
    //$cssLayout->setLeftColumnContent($sideMenuBar->show('context'));
    $cssLayout->setLeftColumnContent($sideMenuBar->show());
}

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();

?>