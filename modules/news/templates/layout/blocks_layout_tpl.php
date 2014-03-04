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
    $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery.livequery.js', 'jquery'));
    echo $this->getJavaScriptFile('newsblocks.js');
} // End Addition of JavaScript

$objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
$wideBlocks = $objBlocks->getBlocks('wide', 'site|user|postlogin');

// Load Blocks
$myrightBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('story', 'story', 'right');
$myleftBlocks = $this->objNewsBlocks->getBlocksAndSendToTemplate('story', 'story', 'left');

$leftContent = $this->objNewsMenu->generateMenu();
$leftContent .= $this->objNewsStories->getFeedLinks();

$leftContent .= '<br/><div id="leftblocks">'.$myleftBlocks.'</div>';
$right = '';


$adminOptions = array();

if ($objUser->isAdmin()) {
    $newsCategoriesLink = new link($this->uri(array('action' => 'managecategories')));
    $newsCategoriesLink->link = 'Manage News Categories';
    $adminOptions[] = '<li>' . $newsCategoriesLink->show() . '</li>';
}

if ($objUser->isAdmin()) {
    $addNewsStoryLink = new link($this->uri(array('action' => 'addstory')));
    $addNewsStoryLink->link = 'Add News Story';
    $adminOptions[] = '<li>' . $addNewsStoryLink->show() . '</li>';
}

if ($objUser->isAdmin()) {
    $addNewsStoryLink = new link($this->uri(array('action' => 'viewarchives')));
    $addNewsStoryLink->link = 'Views News Archives';
    $adminOptions[] = '<li>' . $addNewsStoryLink->show() . '</li>';
}

if (count($adminOptions) > 0) {

    $leftContent .= '<h3>News Options</h3>';

    $leftContent .= '<ul>';

    foreach ($adminOptions as $option) {
        $leftContent .= $option;
    }

    $leftContent .= '</ul>';
}

if ($objUser->isAdmin()) {

    $rightSmallBlocksDropDwon = new dropdown('rightblocks');
    $rightSmallBlocksDropDwon->cssId = 'ddrightblocks';
    $rightSmallBlocksDropDwon->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');

    $leftSmallBlocksDropDwon = new dropdown('leftblocks');
    $leftSmallBlocksDropDwon->cssId = 'ddleftblocks';
    $leftSmallBlocksDropDwon->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');


    foreach ($smallDynamicBlocks as $smallBlock) {
        $rightSmallBlocksDropDwon->addOption('dynamicblock|' . $smallBlock['id'] . '|' . $smallBlock['module'], htmlentities($smallBlock['title']));
        $leftSmallBlocksDropDwon->addOption('dynamicblock|' . $smallBlock['id'] . '|' . $smallBlock['module'], htmlentities($smallBlock['title']));
    }

    foreach ($smallBlocks as $smallBlock) {
        $block = $this->newObject('block_' . $smallBlock['blockname'], $smallBlock['moduleid']);
        $title = $block->title;

        $rightSmallBlocksDropDwon->addOption('block|' . $smallBlock['blockname'] . '|' . $smallBlock['moduleid'], htmlentities($title));
        $leftSmallBlocksDropDwon->addOption('block|' . $smallBlock['blockname'] . '|' . $smallBlock['moduleid'], htmlentities($title));
    }
    $wideBlocksDropDown = new dropdown('middleblocks');
    $wideBlocksDropDown->cssId = 'ddmiddleblocks';
    $wideBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');

    $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
    $wideDynamicBlocks = $objDynamicBlocks->getWideSiteBlocks();

    foreach ($wideDynamicBlocks as $wideBlock) {
        $wideBlocksDropDown->addOption('dynamicblock|' . $wideBlock['id'] . '|' . $wideBlock['module'], htmlentities($wideBlock['title']));
    }

    foreach ($wideBlocks as $wideBlock) {
        $block = $this->newObject('block_' . $wideBlock['blockname'], $wideBlock['moduleid']);
        $title = $block->title;

        $wideBlocksDropDown->addOption('block|' . $wideBlock['blockname'] . '|' . $wideBlock['moduleid'], htmlentities($title));
    }


    $rightBlocks = $rightSmallBlocksDropDwon->show();
    $leftBlocks = $leftSmallBlocksDropDwon->show();

    $button = new button('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
    $button->cssId = 'rightbutton';
    $rightButton = $button->show();

    $button = new button('addleftblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
    $button->cssId = 'leftbutton';
    $leftButton = $button->show();



    $editOnButton = new button('editonbutton', $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'));
    $editOnButton->cssId = 'editmodeswitchbutton';
    $editOnButton->setOnClick("switchEditMode();");
}




if (isset($rightContent)) {
    $right .= $rightContent;
}

if ($objUser->isAdmin()) {
    $right .= '<div id="editmode">' . $editOnButton->show() . '</div>';
}

$right .= '<div id="rightblocks">'.$myrightBlocks.'</div>';


$left = '';

if (isset($leftContent)) {
    $left .= $leftContent;
}



//$left .= '<div id="leftblocks">'.$leftBlocks.'</div>';

if ($objUser->isAdmin()) {

    $header = new htmlheading();
    $header->type = 3;
    $header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');

    $right .= '<div id="rightaddblock">' . $header->show() . $rightBlocks;
    $right .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> ' . $rightButton . ' </div>';
    $right .= '</div>';

    $left .= '<div id="leftaddblock">' . $header->show() . $leftBlocks;
    $left .= '<div id="leftpreview"><div id="leftpreviewcontent"></div> ' . $leftButton . ' </div>';
    $left .= '</div>';
}

$button = new button('addmiddleblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
$button->cssId = 'middlebutton';
$objContextBlocks = $this->getObject('dbcontextblocks', 'context');
if (!(isset($this->contextCode))) {
    $this->contextCode = 'lobby';
}
$middleBlocks = $objContextBlocks->getContextBlocks($this->contextCode, 'middle');

$cssLayout->middleColumnContent = '<div id="middleblocks">' . $middleBlocks . '</div>';

if ($objUser->isAdmin()) {
    $cssLayout->middleColumnContent .= '<div id="middleaddblock">' . $wideBlocksDropDown->show();
    $cssLayout->middleColumnContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> ' . $button->show() . ' </div>';
    $cssLayout->middleColumnContent .= '</div>';
}
$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent($left);
$cssLayout->setMiddleColumnContent($this->getContent());

if ($right == '<div id="rightblocks"></div>') {
    $cssLayout->setNumColumns(2);
} else {
    $cssLayout->setNumColumns(3);
    $cssLayout->setRightColumnContent($right);
}

echo $cssLayout->show();
?>
