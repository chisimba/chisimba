<?php
//initiate objects
$this->loadClass('label', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$this->loadClass('htmlarea', 'htmlelements');
$userid = $this->objUser->userId();
$profile = $this->objDbBlog->checkProfile($userid);
$pane = $this->newObject('tabpane', 'htmlelements');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('sidebar', 'navigation');
// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = $this->newObject('usermenu', 'toolbar');
$rightSideColumn = NULL;
$leftCol = NULL;
$middleColumn = NULL;

$heading = new htmlheading();
$heading->type = 1;
$heading->str = $this->objLanguage->languageText('mod_blog_blogadmin', 'blog');
$middleColumn = $heading->show();

//left menu section
$leftCol = $leftMenu->show();
//$leftCol .= $this->objblogOps->rssBox('http://slashdot.org/slashdot.rdf', 'Slashdot');
$tabpane = $this->newObject('tabcontent', 'htmlelements');
$tabpane->width = '97%';
//write new post
$nplabel = $this->objLanguage->languageText("mod_blog_writepost", "blog");
$npcontent = $this->objblogPosts->postEditor($userid, NULL);
$nplink = NULL;
$tabpane->addTab($nplabel, $npcontent, $nplink, TRUE);
//Profile editor
$prflabel = $this->objLanguage->languageText("mod_blog_setprofile", "blog");
$prfcontent = $this->objblogProfiles->profileEditor($userid, $profile);
$prflink = NULL;
$tabpane->addTab($prflabel, $prfcontent, $prflink, FALSE);
//blog importer
//$implabel = $this->objLanguage->languageText("mod_blog_blogimport", "blog");
//$impcontent = $this->objblogOps->showImportForm(FALSE);
//$implink = NULL;
//$tabpane->addTab($implabel, $impcontent, $implink, FALSE);
//edit posts
$eplabel = $this->objLanguage->languageText("mod_blog_word_editposts", "blog");
$epcontent = $this->objblogPosts->managePosts($userid);
$eplink = NULL;
$tabpane->addTab($eplabel, $epcontent, $eplink, FALSE);
//edit/create cats
//$eclabel = $this->objLanguage->languageText("mod_blog_word_categories", "blog");
//$eccontent = $this->objblogOps->categoryEditor($userid);
//$eclink = NULL;
//$tabpane->addTab($eclabel, $eccontent, $eclink, FALSE);
//add delete rss feeds
$rslabel = $this->objLanguage->languageText("mod_blog_rssaddedit", "blog");
$rscontent = $this->objblogRss->rssEditor(FALSE);
$rslink = NULL;
$tabpane->addTab($rslabel, $rscontent, $rslink, FALSE);
//mail setup
//$maillabel = $this->objLanguage->languageText("mod_blog_setupmail", "blog");
//$mailcontent = $mailsetup;
//$maillink = NULL;
//$tabpane->addTab($maillabel, $mailcontent, $maillink, FALSE);
$middleColumn.= $tabpane->show();
//$middleColumn .= $this->objblogOps->showAdminSection(TRUE, TRUE);
$rightSideColumn.= $this->objblogCategories->quickCats(TRUE);
$rightSideColumn.= $this->objblogOps->showAdminSection(TRUE);
$rightSideColumn.= $this->objblogPosts->quickPost($userid, TRUE);


$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    $cssLayout->setMiddleColumnContent($middleColumn);
    $cssLayout->setLeftColumnContent($leftCol);
    $cssLayout->setRightColumnContent($rightSideColumn);
    echo $cssLayout->show();
}
?>