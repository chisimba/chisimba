<?php

// Add JavaScript if User can update blocks
if ($this->isValid('addblock')) {
    
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
    theModule = 'context';

// ]]>
</script>
<?php
echo $this->getJavaScriptFile('contextblocks.js');
} // End Addition of JavaScript

$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$objCssLayout = $this->getObject('csslayout', 'htmlelements');
$objCssLayout->setNumColumns(3);

if ($this->isValid('addblock')) {

    $smallBlocksDropDown = new dropdown ('rightblocks');
    $smallBlocksDropDown->cssId = 'ddrightblocks';
    $smallBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'phrase', 'Select One').'...');
    
    // Create array for sorting
    $smallBlockOptions = array();
    
    // Add Small Dynamic Blocks
    foreach ($smallDynamicBlocks as $smallBlock)
    {
        $smallBlockOptions['dynamicblock|'.$smallBlock['id'].'|'.$smallBlock['module']] = htmlentities($smallBlock['title']);
    }
    
    // Add Small Blocks
    foreach ($smallBlocks as $smallBlock)
    {
        $block = $this->newObject('block_'.$smallBlock['blockname'], $smallBlock['moduleid']);
        $title = $block->title;
        
        if ($title == '') {
            $title = $smallBlock['blockname'].'|'.$smallBlock['moduleid'];
        }
        
        $smallBlockOptions['block|'.$smallBlock['blockname'].'|'.$smallBlock['moduleid']] = htmlentities($title);
    }
    
    // Sort Alphabetically
    asort($smallBlockOptions);
    
    // Add Small Blocks
    foreach ($smallBlockOptions as $block=>$title)
    {
        $smallBlocksDropDown->addOption($block, $title);
    }
    
    // Create array for sorting
    $wideBlockOptions = array();
    
    $wideBlocksDropDown = new dropdown ('middleblocks');
    $wideBlocksDropDown->cssId = 'ddmiddleblocks';
    $wideBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'phrase', 'Select One').'...');
    
    foreach ($wideDynamicBlocks as $wideBlock)
    {
        $smallBlockOptions['dynamicblock|'.$wideBlock['id'].'|'.$wideBlock['module']] = htmlentities($wideBlock['title']);
    }
    
    foreach ($wideBlocks as $wideBlock)
    {
        $block = $this->newObject('block_'.$wideBlock['blockname'], $wideBlock['moduleid']);
        $title = $block->title;
        
        if ($title == '') {
            $title = $wideBlock['blockname'].'|'.$wideBlock['moduleid'];
        }
        
        $wideBlockOptions['block|'.$wideBlock['blockname'].'|'.$wideBlock['moduleid']] = htmlentities($title);
    }
    
    // Sort Alphabetically
    asort($wideBlockOptions);
    
    // Add Small Blocks
    foreach ($wideBlockOptions as $block=>$title)
    {
        $wideBlocksDropDown->addOption($block, $title);
    }
    
    
    $button = new button ('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
    $button->cssId = 'rightbutton';
    
    
    $editOnButton = new button ('editonbutton', $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'));
    $editOnButton->cssId = 'editmodeswitchbutton';
    $editOnButton->setOnClick("switchEditMode();");

}

$header = new htmlheading();
$header->type = 3;
$header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');

$toolbar = $this->getObject('contextsidebar');
$objCssLayout->setLeftColumnContent($toolbar->show());

$objCssLayout->rightColumnContent = '';

if ($this->isValid('addblock')) {
    $objCssLayout->rightColumnContent .= '<div id="editmode">'.$editOnButton->show().'</div>';
}
$objCssLayout->rightColumnContent .= '<div id="rightblocks">'.$rightBlocksStr.'</div>';

if ($this->isValid('addblock')) {
    $objCssLayout->rightColumnContent .= '<div id="rightaddblock">'.$header->show().$smallBlocksDropDown->show();
    $objCssLayout->rightColumnContent .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> '.$button->show().' </div>';
    $objCssLayout->rightColumnContent .= '</div>';
}

$button = new button ('addmiddleblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
$button->cssId = 'middlebutton';

$objCssLayout->middleColumnContent = '<div id="middleblocks">'.$middleBlocksStr.'</div>';

if ($this->isValid('addblock')) {
    $objCssLayout->middleColumnContent .= '<div id="middleaddblock">'.$header->show().$wideBlocksDropDown->show();
    $objCssLayout->middleColumnContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> '.$button->show().' </div>';
    $objCssLayout->middleColumnContent .= '</div>';
}

echo $objCssLayout->show();




if ($this->getParam('message') == 'contextsetup') {
    $alertBox = $this->getObject('alertbox', 'htmlelements');
    $alertBox->putJs();
    
    echo "<script>
 jQuery.facebox(function() {
  jQuery.get('".str_replace('&amp;', '&', $this->uri(array('action'=>'contextcreatedmessage')))."', function(data) {
    jQuery.facebox(data);
  })
})
</script>";
}


?>