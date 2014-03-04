<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//$leftMenu = &$this->newObject('usermenu', 'toolbar');
//$rightSideColumn = NULL; //$this->objLanguage->languageText('mod_blog_instructions', 'blog');
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
//check for sticky posts
if (isset($stickyposts)) {
    $middlecolumn.= $this->objblogPosts->showPosts($stickyposts, TRUE);
}
//show all the posts
if (isset($catid) && empty($posts) && empty($latestpost)) {
    $middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blog_nopostsincat", "blog") . "</center></em></h1>";
    if ($this->objUser->userId() == $userid) {
        $linker = new href($this->uri(array(
            'module' => 'blog',
            'action' => 'blogadmin',
            'mode' => 'writepost'
        )) , $this->objLanguage->languageText("mod_blog_writepost", "blog") , NULL); //$this->objblogOps->showAdminSection(TRUE);
        $middleColumn.= "<center>" . $linker->show() . "</center>";
    }
} elseif (!isset($catid) && empty($posts) && empty($latestpost)) {
    $middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blog_noposts", "blog") . "</center></em></h1>";
} elseif (isset($catid) && !empty($posts)) {
    foreach($posts as $p) {
        $middleColumn.= ($this->objblogPosts->showPosts($p));
    }
} elseif (isset($catid) && empty($posts) && empty($latestpost)) {
    $middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blog_nopostsincat", "blog") . "</center></em></h1>";
    if ($this->objUser->userId() == $userid) {
        $linker = new href($this->uri(array(
            'module' => 'blog',
            'action' => 'blogadmin',
            'mode' => 'writepost'
        )) , $this->objLanguage->languageText("mod_blog_writepost", "blog") , NULL); //$this->objblogOps->showAdminSection(TRUE);
        $middleColumn.= "<center>" . $linker->show() . "</center>";
    }
} else {
    if (!empty($latestpost) && !empty($posts)) {
        foreach($posts as $p) {
            $middleColumn.= ($this->objblogPosts->showPosts($p));
        }
    } else {
        $middleColumn.= "<h1><em><center>" . $this->objLanguage->languageText("mod_blog_nopostsincat", "blog") . "</center></em></h1>";
        if ($this->objUser->userId() == $userid) {
            $linker = new href($this->uri(array(
                'module' => 'blog',
                'action' => 'blogadmin',
                'mode' => 'writepost'
            )) , $this->objLanguage->languageText("mod_blog_writepost", "blog") , NULL); //$this->objblogOps->showAdminSection(TRUE);
            $middleColumn.= "<center>" . $linker->show() . "</center>";
        }
    }
}
if (!empty($rss)) {
    foreach($rss as $feeds) {
        $timenow = time();
        if ($timenow-$feeds['rsstime'] > 43200) {
            $url = $feeds['url'];
            $id = $feeds['id'];
            $leftCol.= $this->objblogRss->rssBox($url, $feeds['name']); //Refresh($url, $feeds['name'], $id);
            
        } else {
            $url = $feeds['rsscache'];
            $leftCol.= $this->objblogRss->rssBox($url, $feeds['name']);
        }
    }
}


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