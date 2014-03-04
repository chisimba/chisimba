<?php
$this->loadClass('href', 'htmlelements');
$tt = $this->newObject('domtt', 'htmlelements');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
// Set columns to 3
$cssLayout->setNumColumns(3);
$leftMenu = NULL;
$rightSideColumn = NULL;
$leftCol = NULL;
$middleColumn = NULL;
$rightSideColumn.= $objFeatureBox->show($this->objLanguage->languageText("mod_blog_bloglist", "blog") , $this->objLanguage->languageText("mod_blog_intro", "blog"));
$rightSideColumn.= $this->objblogOps->showBlogsLink(TRUE);
if (empty($ret)) {
    $middleColumn.= $this->objLanguage->languageText("mod_blog_noblogs", "blog");
    $uinfo[] = array();
} else {
    foreach($ret as $users) {
        //grab the user info from the user object
        $id = $users['userid'];
        //echo $this->showfullname;
        if ($this->showfullname == 'FALSE') {
            $name = $this->objUser->userName($id);
            $nmlink = new href($this->uri(array(
            	'action' => 'randblog',
            	'userid' => $id
        	)) , $name);
        	$name = $nmlink->show();
        } else {
            $name = $this->objUser->fullname($id);
            $nmlink = new href($this->uri(array(
            	'action' => 'randblog',
            	'userid' => $id
        	)) , $name);
        	$name = $nmlink->show();
        }
        //$name = $this->objUser->fullName($id);
        $laston = $this->objUser->getLastLoginDate($id);
        $img = $this->objUser->getUserImage($id, FALSE);
        $uinfo[] = array(
            'id' => $id,
            'name' => $name,
            'laston' => $laston,
            'img' => $img
        );
    }
    //print_r($uinfo);
    foreach($uinfo as $blogger) {
        $middleColumn.= $this->objblogOps->buildBloggertable($blogger) . "<br />";
    }
}
//left menu section
$leftCol = NULL;
if ($this->objUser->isLoggedIn()) {
    $leftCol.= $objSideBar->show();
    $rightSideColumn.= $this->objblogOps->showAdminSection(TRUE);
    $leftCol.= $this->objblogExtras->showDiaporama();
    //$rightSideColumn .=$this->objblogOps->quickPost($this->objUser->userId(), TRUE);
    
} else {
    $leftCol = $this->objblogOps->loginBox(TRUE);
    $leftCol.= $this->objblogExtras->showDiaporama();
    //$rightSideColumn .= $this->objblogOps->showBlogsLink(TRUE);
    
}

$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    //show the feeds section
    //$leftCol .= $this->objblogOps->showFeeds(&$userid, TRUE);
    $cssLayout->setMiddleColumnContent($middleColumn);
    $cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
    $cssLayout->setRightColumnContent($rightSideColumn);
    echo $cssLayout->show();
}
?>