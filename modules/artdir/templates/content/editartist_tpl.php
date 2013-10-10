<?php

// Display dialogue if necessary.
echo $this->objTermsDialogue->show();

//edit cats
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$leftCol = NULL;
$middleColumn = NULL;
$rightSideColumn = NULL;

$objUi = $this->getObject('artdirui');

// right side blocks
$rightSideColumn .= $objUi->rightBlocks();

if ($leftCol == NULL || $rightSideColumn == NULL) {
    $cssLayout->setNumColumns(2);
} else {
    $cssLayout->setNumColumns(3);
}

//get the category manager
$middleColumn = $objUi->artistEditor($artistid, TRUE);
$middleColumn .= $objUi->imageUpload($artistid);

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($rightSideColumn);
echo $cssLayout->show();

?>
