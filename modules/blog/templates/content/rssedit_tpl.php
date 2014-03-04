<?php
//rss add/edit template
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
    if (!empty($rdata)) {
        $middleColumn.= $this->objblogRss->rssEditor(FALSE, $rdata);
    } else {
        $middleColumn.= $this->objblogRss->rssEditor(FALSE);
    }
}
if (!empty($rss)) {
    foreach($rss as $feeds) {
        $timenow = time();
        if (($timenow-$feeds['rsstime']) > 43200) {
            $url = htmlentities($feeds['url']);
        } else {
            $url = htmlentities($feeds['rsscache']);
        }
        $leftCol.= $this->objblogRss->rssBox($url, $feeds['name']);
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