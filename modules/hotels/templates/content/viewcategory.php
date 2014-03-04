<?php

echo $content;

$editOptions = array();

if (isset($pageId)) {
    if ($this->isValid('editstory')) {
        $editStoryLink = new link ($this->uri(array('action'=>'editstory', 'id'=>$pageId)));
        $editStoryLink->link = $this->objLanguage->languageText('mod_hotels_editstory', 'hotels', 'Edit Hotel');
        $editOptions[] = $editStoryLink->show();
    }
    
    if ($this->isValid('deletestory')) {
        $deleteStoryLink = new link ($this->uri(array('action'=>'deletestory', 'id'=>$pageId)));
        $deleteStoryLink->link = $this->objLanguage->languageText('mod_hotels_deletestory', 'hotels', 'Delete Hotel');
        $editOptions[] = $deleteStoryLink->show();
    }
}

if ($this->isValid('addstory')) {
    $addStoryLink = new link ($this->uri(array('action'=>'addstory', 'id'=>$category['id'])));
    $addStoryLink->link = $this->objLanguage->languageText('mod_hotels_addstoryincategory', 'hotels', 'Add Hotel in this Category');
    $editOptions[] = $addStoryLink->show();
}

if ($this->isValid('liststories')) {
    $listStoriesLink = new link ($this->uri(array('action'=>'liststories', 'id'=>$category['id'])));
    $listStoriesLink->link = $this->objLanguage->languageText('mod_hotels_liststoriesincategory', 'hotels', 'List Hotels in this Category');
    $editOptions[] = $listStoriesLink->show();
}

if ($this->isValid('editmenuitem') && $menuId != FALSE) {
    $editCategoryLink = new link ($this->uri(array('action'=>'editmenuitem', 'id'=>$menuId)));
    $editCategoryLink->link = $this->objLanguage->languageText('mod_hotels_editcategory', 'hotels', 'Edit Category');
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