<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$middleColumn = NULL;
$objUi = $this->getObject('blogui');
// left hand blocks
$leftCol = $objUi->leftBlocks($userid);
// right side blocks
$rightSideColumn = $objUi->rightBlocks($userid, NULL);
if ($leftCol == NULL || $rightSideColumn == NULL) {
    $cssLayout->setNumColumns(2);
} else {
    $cssLayout->setNumColumns(3);
}
$washer = $this->getObject('washout', 'utilities');
$middleColumn = $this->objblogExtras->parseTimeline("WEEK", $startdate, $tlurl); //$washer->parseText('[TIMELINE]<a href="'.$tlurl.'">blogtimeline</a>[/TIMELINE]');


// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
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
}
?>