<?php
//tburl template
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$middleColumn = NULL;
//load up a featurebox and display it nicely
$objFeatureBox = $this->getObject('featurebox', 'navigation');
$theurl = $objFeatureBox->show($this->objLanguage->languageText("mod_blog_tbheader", "blog") , $theurl);
$middleColumn.= $theurl;
//$userid = $this->objUser->userId();


// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
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