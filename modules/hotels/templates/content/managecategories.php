<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');

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
$objIcon->alt = $this->objLanguage->languageText('mod_hotels_editcategory', 'hotels', 'Edit Category');
$objIcon->title = $this->objLanguage->languageText('mod_hotels_editcategory', 'hotels', 'Edit Category');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$objIcon->alt = $this->objLanguage->languageText('phrase_deletecategory', 'hotels', 'Delete Category');
$objIcon->title = $this->objLanguage->languageText('phrase_deletecategory', 'hotels', 'Delete Category');
$deleteIcon = $objIcon->show();

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_hotels_newsmenu', 'hotels', 'Hotels Menu');
echo $header->show();

// Array for In Place Editors
$inPlaceEditors = array();

if (count($menuItems) == 0) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_hotels_nomenuitemssetup', 'hotels', 'No Menu Items have been setup yet.').'</div>';
} else {
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->id = 'menuitemstable';
    $table->alternate_row_colors = TRUE;
    $table->active_rows = TRUE;
    
    $table->startHeaderRow();
        $table->addHeaderCell($this->objLanguage->languageText('word_number_shorthand', 'word', 'No.'), 25);
        $table->addHeaderCell($this->objLanguage->languageText('mod_hotels_menuitemname', 'hotels', 'Menu Item Name'));
        $table->addHeaderCell($this->objLanguage->languageText('word_type', 'word', 'Type'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_hotels_examplelink', 'hotels', 'Example Link'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_word_stories', 'word', 'Stories'), 100, NULL, 'center');
        $table->addHeaderCell($this->objLanguage->languageText('mod_context_move', 'context', 'Move'), 50);
        $table->addHeaderCell($this->objLanguage->languageText('word_options', 'system', 'Options'), 70);
    $table->endHeaderRow();
    
    $counter = 0;
    
    $oddOrEven = 'even';
    
    foreach ($menuItems as $item)
    {
        $counter++;
        
        $oddOrEven = ($oddOrEven == 'even') ? 'odd' : 'even';
        
        $table->startRow($oddOrEven);
        $table->addCell($counter.'.');
        $table->addCell('<div id="'.$item['id'].'">'.$item['itemname'].'</div>');
        $table->addCell($item['itemtype']);
        $table->addCell(str_replace('<br />', '', $this->objNewsMenu->prepareItem($item)));
        
        
        if ($counter == 1) {
            $moveItemUp = '&nbsp;&nbsp;';
        } else {
            $link = new link ($this->uri(array('action'=>'movecategoryup', 'id'=>$item['id'])));
            $link->link = $upIcon;
            $moveItemUp = $link->show();
        }
        
        if ($counter == count($menuItems)) {
            $moveItemDown = '&nbsp;';
        } else {
            $link = new link ($this->uri(array('action'=>'movecategorydown', 'id'=>$item['id'])));
            $link->link = $downIcon;
            $moveItemDown = $link->show();
        }
        
        switch ($item['itemtype'])
        {
            case 'divider':
                $edit = '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;';
                $delete = $objIcon->getDeleteIconWithConfirm($item['id'], array('action'=>'deletedivider','id'=>$item['id']), 'news', $this->objLanguage->languageText('mod_hotels_confirmdeletedivider', 'hotels', 'Are you sure want to delete this divider?'));
                $numItems = '&nbsp;';
                break;
            case 'text':
                $editOption = new link ($this->uri(array('action'=>'editmenuitem', 'id'=>$item['id'])));
                $editOption->link = $editIcon;
                $edit = $editOption->show();
                $delete = $objIcon->getDeleteIconWithConfirm($item['id'], array('action'=>'deletetext','id'=>$item['id']), 'hotels', $this->objLanguage->languageText('mod_hotels_confirmdeletetext', 'hotels', 'Are you sure want to delete this text?'));
                $numItems = '&nbsp;';
                break;
            case 'module':
                $edit = '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;';
                $delete = $objIcon->getDeleteIconWithConfirm($item['id'], array('action'=>'deletemodule','id'=>$item['id']), 'hotels', $this->objLanguage->languageText('mod_hotels_confirmdeletemodule', 'hotels', 'Are you sure want to remove this module?'));
                $numItems = '&nbsp;';
                break;
            case 'block':
                $edit = '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;';
                $delete = $objIcon->getDeleteIconWithConfirm($item['id'], array('action'=>'deleteblock','id'=>$item['id']), 'hotels', $this->objLanguage->languageText('mod_hotels_confirmdeleteblock', 'hotels', 'Are you sure want to remove this block?'));
                $numItems = '&nbsp;';
                break;
            case 'url':
                $edit = '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;';
                $delete = $objIcon->getDeleteIconWithConfirm($item['id'], array('action'=>'deleteurl','id'=>$item['id']), 'news', $this->objLanguage->languageText('mod_hotels_confirmdeleteurl', 'hotels', 'Are you sure want to delete this URL?'));
                $numItems = '&nbsp;';
                break;
            default:
                $editOption = new link ($this->uri(array('action'=>'editmenuitem', 'id'=>$item['id'], 'returnaction'=>'managecategories')));
                $editOption->link = $editIcon;
                $edit = $editOption->show();
                $numItems = $this->objNewsStories->getNumCategoryStories($item['itemvalue']);
                
                if ($numItems == 0) {
                    $link = new link ($this->uri(array('action'=>'deletecategory', 'id'=>$item['id'])));
                    $link->link = $deleteIcon;
                    
                    $delete = $link->show();
                } else {
                    $link = new link ('javascript:alert(\''.addslashes($this->objLanguage->languageText('mod_hotels_cannotdeletecategorywithstories', 'hotels', 'Cannot delete category because it contains hotels')).'\');');
                    $link->link = $deleteIcon;
                    
                    $delete = $link->show();
                }
                
                break;
        }
        
        $table->addCell($numItems, NULL, NULL, 'center');
        $table->addCell($moveItemUp.' &nbsp; '.$moveItemDown);
        $table->addCell($edit.' &nbsp; '.$delete);
        //$table->addCell('<span class="handler">'.$upIcon.'&nbsp;'.$downIcon.'</span>');
        $table->endRow();
        
        if ($item['itemtype'] != 'divider') {
            $inPlaceEditors[] = $item['id'];
        }
    }
    
    
    echo $table->show();
}

$link = new link ($this->uri(array('action'=>'addmenuitem')));
$link->link = $this->objLanguage->languageText('mod_hotels_addnewmenuitem', 'hotels', 'Add New Menu Item');


$homeLink = new link ($this->uri(NULL));
$homeLink->link = $this->objLanguage->languageText('mod_news_returntonewshome', 'hotels', 'Return to Hotels Home');
echo '<p>'.$link->show().' / '.$homeLink->show().'</p>';

?>