<?php
//profile add/edit template
$cssLayout = $this->newObject('csslayout', 'htmlelements');
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
if ($this->objUser->isLoggedIn()) {
    if (isset($profile)) {
        $middleColumn.= $this->objblogProfiles->profileEditor($userid, $profile);
    } else {
        $middleColumn.= $this->objblogProfiles->profileEditor($userid);
    }
}


// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    //dump the cssLayout to screen
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