<?php

// Display dialogue if necessary.
echo $this->objTermsDialogue->show();

//edit cats
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$middleColumn = NULL;
$leftCol = NULL;
$rightSideColumn = NULL;
$objUi = $this->getObject('artdirui');
// left hand blocks
$leftCol = $objUi->leftBlocks(NULL);
// right side blocks
$rightSideColumn = $objUi->rightBlocks(NULL, NULL);
if ($leftCol == NULL || $rightSideColumn == NULL) {
    $cssLayout->setNumColumns(2);
} else {
    $cssLayout->setNumColumns(3);
}

//get the category manager
$middleColumn = $objUi->signinBox();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();

?>
