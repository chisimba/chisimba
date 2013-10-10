<?php

// Display dialogue if necessary.
echo $this->objTermsDialogue->show();
if(!isset($catarr)) {
    $catarr = NULL;
}
//edit cats
$cssLayout = $this->newObject('csslayout', 'htmlelements');

$middleColumn = NULL;
$leftCol = NULL;
$rightSideColumn = NULL;
$objUi = $this->getObject('artdirui');
// left hand blocks
$leftCol = $objUi->leftBlocks($userid);
// right side blocks
$rightSideColumn = $objUi->rightBlocks($userid, NULL);

if ($leftCol == NULL || $rightSideColumn == NULL) {
    $cssLayout->setNumColumns(2);
} else {
    $cssLayout->setNumColumns(3);
}
//get the category manager
$middleColumn = $this->objCats->categoryEditor($userid, $mode, $catarr);


if ($leftCol == NULL) {
    $leftCol = $rightSideColumn;
    $cssLayout->setMiddleColumnContent($middleColumn);
    $cssLayout->setLeftColumnContent($leftCol);
    //$cssLayout->setRightColumnContent($rightSideColumn);
    echo $cssLayout->show();
} elseif ($rightSideColumn == NULL) {
    $cssLayout->setMiddleColumnContent($middleColumn);
    $cssLayout->setLeftColumnContent($leftCol);
    echo $cssLayout->show();
} else {
    $cssLayout->setMiddleColumnContent($middleColumn);
    $cssLayout->setLeftColumnContent($leftCol);
    $cssLayout->setRightColumnContent($rightSideColumn);
    echo $cssLayout->show();
}

?>
