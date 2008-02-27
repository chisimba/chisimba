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
    theModule = 'postlogin';

// ]]>
</script>
<?php
echo $this->getJavaScriptFile('contextblocks.js', 'context');
} // End Addition of JavaScript

$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$objCssLayout = $this->getObject('csslayout', 'htmlelements');
$objCssLayout->setNumColumns(3);

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
    
    $smallBlocksDropDown->cssId = 'ddleftblocks';
    $smallBlocksDropDown->name = 'leftblocks';
    
    $leftBlocks = $smallBlocksDropDown->show();
    
    $wideBlocksDropDown = new dropdown ('middleblocks');
    $wideBlocksDropDown->cssId = 'ddmiddleblocks';
    $wideBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One').'...');
    
    foreach ($wideDynamicBlocks as $wideBlock)
    {
        $wideBlocksDropDown->addOption('dynamicblock|'.$wideBlock['id'].'|'.$wideBlock['module'], htmlentities($wideBlock['title']));
    }
    
    foreach ($wideBlocks as $wideBlock)
    {
        $block = $this->newObject('block_'.$wideBlock['blockname'], $wideBlock['moduleid']);
        $title = $block->title;
        
        $wideBlocksDropDown->addOption('block|'.$wideBlock['blockname'].'|'.$wideBlock['moduleid'], htmlentities($title));
    }
    
    
    $button = new button ('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
    $button->cssId = 'rightbutton';
    
    $rightButton = $button->show();
    
    $button = new button ('addleftblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
    $button->cssId = 'leftbutton';
    
    $leftButton = $button->show();
    
    
    $editOnButton = new button ('editonbutton', $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'));
    $editOnButton->cssId = 'editmodeswitchbutton';
    $editOnButton->setOnClick("switchEditMode();");

}

$header = new htmlheading();
$header->type = 3;
$header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');

$userMenu  = $this->newObject('postloginmenu','toolbar');
$objCssLayout->setLeftColumnContent($userMenu->show());

$objCssLayout->leftColumnContent .= '<div id="leftblocks">'.$leftBlocksStr.'</div>';

if ($objUser->isAdmin()) {
    $objCssLayout->leftColumnContent .= '<div id="leftaddblock">'.$header->show().$leftBlocks;
    $objCssLayout->leftColumnContent .= '<div id="lefttpreview"><div id="leftpreviewcontent"></div> '.$leftButton.' </div>';
    $objCssLayout->leftColumnContent .= '</div>';
}

$objCssLayout->rightColumnContent = '';

if ($objUser->isAdmin()) {
    $objCssLayout->rightColumnContent .= '<div id="editmode">'.$editOnButton->show().'</div>';
}
$objCssLayout->rightColumnContent .= '<div id="rightblocks">'.$rightBlocksStr.'</div>';

if ($objUser->isAdmin()) {
    $objCssLayout->rightColumnContent .= '<div id="rightaddblock">'.$header->show().$rightBlocks;
    $objCssLayout->rightColumnContent .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> '.$rightButton.' </div>';
    $objCssLayout->rightColumnContent .= '</div>';
}

$button = new button ('addmiddleblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
$button->cssId = 'middlebutton';

$objCssLayout->middleColumnContent = '<div id="middleblocks">'.$middleBlocksStr.'</div>';

if ($objUser->isAdmin()) {
    $objCssLayout->middleColumnContent .= '<div id="middleaddblock">'.$header->show().$wideBlocksDropDown->show();
    $objCssLayout->middleColumnContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> '.$button->show().' </div>';
    $objCssLayout->middleColumnContent .= '</div>';
}

echo $objCssLayout->show();