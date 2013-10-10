<?php
// Make it a three panel page using the css layout class.
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
// Instantiate the rendering class
$objPbrender = $this->getObject("pbrender", "personalblocks");

// Get the left blocks
$leftSide = $objPbrender->renderLeft();

// Get the right blocks
$rightSide = $objPbrender->renderRight();

// Get the list and set up middle area
$middle = "";
// We are going to use the tabbed box for cutting between view and edit
$objTab = $this->newObject('tabber', 'htmlelements');
$objTab->tabId = TRUE;
$objTab->addTab(array('name'=> $this->objLanguage->languageText("mod_personalblocks_view", "personalblocks"),'content' => $objPbrender->renderMiddle()));
$objTab->addTab(array('name'=> $this->objLanguage->languageText("mod_personalblocks_edit", "personalblocks"),'content' => $objPbrender->showAll()));
$middle = $objPbrender->showTitle();
$middle .= $objTab->show();

// ---------------The final render -----------------
// Add Left column
$cssLayout->setLeftColumnContent($leftSide);
// Add middle Column
$cssLayout->setMiddleColumnContent($middle);
// Add right column
$cssLayout->setRightColumnContent($rightSide);
// Output the content to the page.
echo $cssLayout->show();
?>