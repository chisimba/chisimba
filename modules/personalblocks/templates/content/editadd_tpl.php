<?php
// Make it a three panel page using the css layout class.
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
$objPbrender = $this->getObject("pbrender", "personalblocks");


$leftSide = $objPbrender->renderLeft(TRUE);
// Get the right blocks
$rightSide = $objPbrender->renderRight(TRUE);

$middle = "";


$middle = $objPbrender->showTitle();
$middle .=  $objPbrender->renderEditAddForm();


// ---------------The final render -----------------
//Add Left column
$cssLayout->setLeftColumnContent($leftSide);
// Add middle Column
$cssLayout->setMiddleColumnContent($middle);
//Add right column
$cssLayout->setRightColumnContent($rightSide);
// Add Right Column
//Output the content to the page
echo $cssLayout->show();
?>