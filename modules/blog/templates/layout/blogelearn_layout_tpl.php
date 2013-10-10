<?php
// Create Side Bar Navigation
$objSideBar = $this->newObject('sidebar', 'navigation');

$sidebar = '';
$menuItems = array();

$blogHome = array('text'=>$this->objLanguage->languageText('mod_blog_bloghome', 'blog', 'Blog Home'), 'uri'=>$this->uri(NULL));
$menuItems[] = $blogHome;

$myBlog = array('text'=>$this->objLanguage->languageText('mod_blog_myblog', 'blog', 'My Blog'), 'uri'=>$this->uri(array('action'=>'viewblog')));
$menuItems[] = $myBlog;

if ($objUser->isLoggedIn()) {
    $allBlogs = array('text'=>$this->objLanguage->languageText('mod_blog_viewallblogs', 'blog', 'List of all bloggers'), 'uri'=>$this->uri(array('action'=>'allblogs')));
    $menuItems[] = $allBlogs;
}

$addPost = array('text'=>$this->objLanguage->languageText('mod_blog_writepost', 'blog', 'Write new post'), 'uri'=>$this->uri(array('action'=>'blogadmin', 'mode'=>'writepost')));

$menuItems[] = $addPost;

// End Side Bar Navigation



if ($this->getParam('action') != 'allblogs') {
    $sidebar .= $this->objblogProfiles->showFullProfile($this->getParam('userid', $objUser->userId()));
}

//$sidebar = '<br /><p align="center">'.$this->objUser->getUserImage().'</p>';
$sidebar .= $objSideBar->show($menuItems);


$bloggers = $this->objDbBlog->getUBlogs('userid', 'tbl_blog_posts');


//$sidebar .= $this->objblogSearching->searchBox();

if (count($bloggers) > 0) {
    $this->loadClass('form', 'htmlelements');
    $this->loadClass('button', 'htmlelements');
    $this->loadClass('dropdown', 'htmlelements');
    $this->loadClass('hiddeninput', 'htmlelements');
    $this->loadClass('label', 'htmlelements');

    $form = new form ('viewbyuser', $this->uri(array('action' => 'byuser')));
    $form->method = 'get';
    $module = new hiddeninput('module', 'blog');
    $form->addToForm($module->show());

    $action = new hiddeninput('action', 'randblog');
    $form->addToForm($action->show());

    $dropdown = new dropdown('userid');

    foreach ($bloggers as $blogger)
    {
        $dropdown->addOption($blogger['userid'], $objUser->fullName($blogger['userid']));
    }

    if ($this->getParam('action') != 'allblogs') {
        $dropdown->setSelected($this->getParam('userid', $objUser->userId()));
    }

    $label = new label ($this->objLanguage->languageText('mod_blog_viewbyblogger', 'blog', 'View by Blogger'), 'input_id');

    $button = new button(NULL, $this->objLanguage->languageText('help_blog_title_viewblog', 'blog', 'View Blog'));
    $button->setToSubmit();

    $objFeatureBox = $this->newObject('featurebox', 'navigation');

    $form->addToForm($dropdown->show().'<br /><br />'.$button->show());


    $sidebar .= '<br />'.$objFeatureBox->show($label->show(), $form->show());

}

if ($objUser->isLoggedIn()) {
    $sidebar .= $this->objblogOps->showAdminSection(TRUE, FALSE, 'block');
}

$cssLayout = $this->newObject('csslayout', 'htmlelements');

$cssLayout->setLeftColumnContent($sidebar);

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();

?>
