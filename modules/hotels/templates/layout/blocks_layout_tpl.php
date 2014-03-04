<?php



// Add JavaScript if User can update blocks
if ($objUser->isAdmin()) {
    
    $objIcon = $this->newObject('geticon', 'htmlelements');
    $objIcon->setIcon('up');
    $upIcon = $objIcon->show();
    
    
    $objIcon->setIcon('down');
    $downIcon = $objIcon->show();
    
    $objIcon->setIcon('delete');
    $deleteIcon = $objIcon->show();
?>
<script type="text/javascript">
// <![CDATA[
    upIcon = '<?php echo $upIcon; ?>';
    downIcon = '<?php echo $downIcon; ?>';
    deleteIcon = '<?php echo $deleteIcon; ?>';
    deleteConfirm = '<?php echo $objLanguage->languageText('mod_context_confirmremoveblock', 'context', 'Are you sure you want to remove the block'); ?>';
    unableMoveBlock = '<?php echo $objLanguage->languageText('mod_context_unablemoveblock', 'context', 'Error - Unable to move block'); ?>';
    unableDeleteBlock = '<?php echo $objLanguage->languageText('mod_context_unabledeleteblock', 'context', 'Error - Unable to delete block'); ?>';
    unableAddBlock = '<?php echo $objLanguage->languageText('mod_context_unableaddblock', 'context', 'Error - Unable to add block'); ?>';
    turnEditingOn = '<?php echo $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'); ?>';
    turnEditingOff = '<?php echo $objLanguage->languageText('mod_context_turneditingoff', 'context', 'Turn Editing Off'); ?>';
    theModule = 'news';
    pageType = '<?php echo $pageType; ?>';
    pageId = '<?php echo $pageTypeId; ?>';

// ]]>
</script>
<?php
    $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery/jquery.livequery.js', 'jquery'));
    echo $this->getJavaScriptFile('newsblocks.js');
} // End Addition of JavaScript



$leftContent = $this->objNewsMenu->generateMenu();
$leftContent .= $this->objNewsStories->getFeedLinks();

$adminOptions = array();

if ($this->isValid('managecategories')) {
    $newsCategoriesLink = new link ($this->uri(array('action'=>'managecategories')));
    $newsCategoriesLink->link = 'Manage Hotel Categories';
    $adminOptions[] = '<li>'.$newsCategoriesLink->show().'</li>';
}

if ($this->isValid('addstory')) {
    $addNewsStoryLink = new link ($this->uri(array('action'=>'addstory')));
    $addNewsStoryLink->link = 'Add Hotel';
    $adminOptions[] = '<li>'.$addNewsStoryLink->show().'</li>';
}

if (count($adminOptions) > 0) {

    $leftContent .= '<h3>Hotel Options</h3>';

    $leftContent .= '<ul>';

    foreach ($adminOptions as $option)
    {
        $leftContent .= $option;
    }

    $leftContent .= '</ul>';

}

if ($objUser->isAdmin()) {

    $smallBlocksDropDown = new dropdown ('rightblocks');
    $smallBlocksDropDown->cssId = 'ddrightblocks';
    $smallBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One').'...');
    
    
    foreach ($smallDynamicBlocks as $smallBlock)
    {
        $smallBlocksDropDown->addOption('dynamicblock|'.$smallBlock['id'].'|'.$smallBlock['module'], htmlentities($smallBlock['title']));
    }
    
    foreach ($smallBlocks as $smallBlock)
    {
        $block = $this->newObject('block_'.$smallBlock['blockname'], $smallBlock['moduleid']);
        $title = $block->title;
        
        $smallBlocksDropDown->addOption('block|'.$smallBlock['blockname'].'|'.$smallBlock['moduleid'], htmlentities($title));
    }
    
    $rightBlocks = $smallBlocksDropDown->show();
    
    $button = new button ('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
    $button->cssId = 'rightbutton';
    
    $rightButton = $button->show();
    
    
    $editOnButton = new button ('editonbutton', $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'));
    $editOnButton->cssId = 'editmodeswitchbutton';
    $editOnButton->setOnClick("switchEditMode();");
}


$right = '';

if (isset($rightContent)) {
    $right .= $rightContent;
}

if ($objUser->isAdmin()) {
    $right .= '<div id="editmode">'.$editOnButton->show().'</div>';
}


$right .= '<div id="rightblocks">'.$rightBlocksStr.'</div>';

if ($objUser->isAdmin()) {
    
    $header = new htmlheading();
    $header->type = 3;
    $header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');

    $right .= '<div id="rightaddblock">'.$header->show().$rightBlocks;
    $right .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> '.$rightButton.' </div>';
    $right .= '</div>';
}

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent($leftContent);
$cssLayout->setMiddleColumnContent($this->getContent());

if ($right == '<div id="rightblocks"></div>') {
    $cssLayout->setNumColumns(2);
} else {
    $cssLayout->setNumColumns(3);
    $cssLayout->setRightColumnContent($right);
}

echo $cssLayout->show();

?>