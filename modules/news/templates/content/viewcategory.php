<?php

echo $content;

$editOptions = array();

if ( $this->objUser->inAdminGroup($this->objUser->userId()) ) {
    if ( isset($pageId) ) {
        if ( $this->isValid('editstory') ) {
            $editStoryLink = new link ($this->uri(array('action'=>'editstory', 'id'=>$pageId)));
            $editStoryLink->link = $this->objLanguage->languageText('mod_news_editstory', 'news', 'Edit Story');
            $editOptions[] = $editStoryLink->show();
        }

        if ( $this->isValid('deletestory') ) {
            $deleteStoryLink = new link ($this->uri(array('action'=>'deletestory', 'id'=>$pageId)));
            $deleteStoryLink->link = $this->objLanguage->languageText('mod_news_deletestory', 'news', 'Delete Story');
            $editOptions[] = $deleteStoryLink->show();
        }
    }

    if ( $this->isValid('addstory') ) {
        $addStoryLink = new link ($this->uri(array('action'=>'addstory', 'id'=>$category['id'])));
        $addStoryLink->link = $this->objLanguage->languageText('mod_news_addstoryincategory', 'news', 'Add Story in this Category');
        $editOptions[] = $addStoryLink->show();
    }
}

if ( $this->isValid('liststories') ) {
    $listStoriesLink = new link ($this->uri(array('action'=>'liststories', 'id'=>$category['id'])));
    $listStoriesLink->link = $this->objLanguage->languageText('mod_news_liststoriesincategory', 'news', 'List Stories in this Category');
    $editOptions[] = $listStoriesLink->show();
}

if ( $this->isValid('editmenuitem') && $menuId != FALSE && $this->objUser->inAdminGroup($this->objUser->userId()) ) {
    $editCategoryLink = new link ($this->uri(array('action'=>'editmenuitem', 'id'=>$menuId)));
    $editCategoryLink->link = $this->objLanguage->languageText('mod_news_editcategory', 'news', 'Edit Category');
    $editOptions[] = $editCategoryLink->show();
}

if (count($editOptions) > 0) {
    $divider = '';
    echo '<p>';
    foreach ($editOptions as $editOption)
    {
        echo $divider.$editOption;
        $divider = ' : ';
    }
    echo '</p>';
}


?>
