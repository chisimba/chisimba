<?php

$this->loadClass('link', 'htmlelements');

echo '<h1>'.$this->objLanguage->languageText('mod_news_listofstoriesin', 'news', 'List of Stories in').' '.$category['categoryname'].'</h1>';

$objIcon = $this->newObject('geticon', 'htmlelements');

$objIcon->setIcon('mvup');
$objIcon->alt = $this->objLanguage->languageText('phrase_moveup', 'system', 'Move up');
$objIcon->title = $this->objLanguage->languageText('phrase_moveup', 'system', 'Move up');
$upIcon = $objIcon->show();

$objIcon->setIcon('mvdown');
$objIcon->alt = $this->objLanguage->languageText('phrase_movedown', 'system', 'Move down');
$objIcon->title = $this->objLanguage->languageText('phrase_movedown', 'system', 'Move down');
$downIcon = $objIcon->show();

$objIcon->setIcon('edit');
$objIcon->alt = $this->objLanguage->languageText('mod_news_editstory', 'news', 'Edit Story');
$objIcon->title = $this->objLanguage->languageText('mod_news_editstory', 'news', 'Edit Story');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$objIcon->alt = $this->objLanguage->languageText('mod_news_deletestory', 'news', 'Delete Story');
$objIcon->title = $this->objLanguage->languageText('mod_news_deletestory', 'news', 'Delete Story');
$deleteIcon = $objIcon->show();

$objIcon->setIcon('clock');
$objIcon->alt = $this->objLanguage->languageText('mod_news_willappearlater', 'news', 'Will Appear Later');
$objIcon->title = $this->objLanguage->languageText('mod_news_willappearlater', 'news', 'Will Appear Later');
$clockIcon = $objIcon->show();

if (count($stories) == 0) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_news_categoryhasnostories', 'news', 'Category has no Stories').'</div>';
} else {
    $table = $this->newObject('htmltable', 'htmlelements');
    
    $table->startHeaderRow();
    $table->addHeaderCell($this->objLanguage->languageText('mod_news_storydate', 'news', 'Story Date'));
    $table->addHeaderCell('&nbsp;', 30);
    $table->addHeaderCell($this->objLanguage->languageText('mod_news_storytitle', 'news', 'Story Title'));
    $table->addHeaderCell($this->objLanguage->languageText('mod_prelogin_location', 'system', 'Location'));
    $table->addHeaderCell($this->objLanguage->languageText('word_options', 'system', 'Options'));
    
    if ($category['itemsorder'] == 'storyorder') {
        $table->addHeaderCell(str_replace(' ', '&nbsp;', $this->objLanguage->languageText('mod_news_movepage', 'news', 'Move Page')));
    }
    
    $table->endHeaderRow();
    
    $counter = 0;
    
    foreach ($stories as $story)
    {
        $counter++;
        
        if ($counter == 1) {
            $moveItemUp = '&nbsp;&nbsp;';
        } else {
            $link = new link ($this->uri(array('action'=>'movepageup', 'id'=>$story['id'])));
            $link->link = $upIcon;
            $moveItemUp = $link->show();
        }
        
        if ($counter == count($stories)) {
            $moveItemDown = '&nbsp;';
        } else {
            $link = new link ($this->uri(array('action'=>'movepagedown', 'id'=>$story['id'])));
            $link->link = $downIcon;
            $moveItemDown = $link->show();
        }
        
        $table->startRow();
        $table->addCell($story['storydate'], 100);
		
		if ($story['dateavailable'] <= strftime('%Y-%m-%d %H:%M:%S', mktime())) {
			$table->addCell('&nbsp;');
		} else {
			$table->addCell($clockIcon);
		}
        
        $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
        $link->link = $story['storytitle'];
        
        $table->addCell($link->show());
        $table->addCell($story['location'], 120);
        
        $editOption = new link ($this->uri(array('action'=>'editstory', 'id'=>$story['id'])));
        $editOption->link = $editIcon;
        $edit = $editOption->show();
        
        $deleteLink = new link ($this->uri(array('action'=>'deletestory', 'id'=>$story['id'])));
        $deleteLink->link = $deleteIcon;
        $delete = $deleteLink->show();
        
        $table->addCell($edit.' &nbsp; '.$delete, 100);
        
        if ($category['itemsorder'] == 'storyorder') {
            $table->addCell($moveItemUp.' &nbsp; '.$moveItemDown, 50);
        }
        
        $table->endRow();
    }
    
    echo $table->show();
}

$backToCategoryLink = new link ($this->uri(array('action'=>'viewcategory', 'id'=>$category['id'])));
$backToCategoryLink->link = $this->objLanguage->languageText('mod_news_backtocategory', 'news', 'Back to Category');
$editOptions[] = $backToCategoryLink->show();


if ($this->isValid('addstory')) {
    $addStoryLink = new link ($this->uri(array('action'=>'addstory', 'id'=>$category['id'])));
    $addStoryLink->link = $this->objLanguage->languageText('mod_news_addstoryincategory', 'news', 'Add Story in this Category');
    $editOptions[] = $addStoryLink->show();
}

if ($this->isValid('editmenuitem') && $menuId != FALSE) {
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