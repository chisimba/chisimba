<?php
$objIcon = $this->newObject('geticon', 'htmlelements');
// Add JavaScript if User can update blocks
if ($objUser->isAdmin()) {


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
$this->dbSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$showAdminShortcutBlock = $this->dbSysConfig->getValue('SHOW_SHORTCUTS_BLOCK', 'context');

$utillink = "";
if (strtoupper($showAdminShortcutBlock) == "TRUE") {
    $objIcon->setIcon('plus');
    $plusIcon = $objIcon->show();
    $allactivitylink = new link($this->uri(array('action' => 'selectcontextsactivitydates'), 'context'));
    $allactivitylink->link = $plusIcon . '&nbsp;' . $this->objLanguage->code2Txt('mod_context_allcoursesacitivity', 'context', NULL, 'All [-contexts-] activity');
    $objFeatureBox = $this->newObject('featurebox', 'navigation');
    $content = $allactivitylink->show();

    $block = "shortcuts";
    $hidden = 'default';
    $showToggle = false;
    $showTitle = false;
    $cssClass = "xfeaturebox";
    $utillink = $objFeatureBox->show(
                    $this->objLanguage->languageText('mod_contextcontent_shortcuts', 'contextcontent', 'Shortcuts'),
                    $content,
                    $block,
                    $hidden,
                    $showToggle,
                    $showTitle,
                    $cssClass, '');
}

if ($objUser->isAdmin()) {

    $rightBlocksDropDown = new dropdown('rightblocks');
    $rightBlocksDropDown->cssId = 'ddrightblocks';
    $rightBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');

    $leftBlocksDropDown = new dropdown('leftblocks');
    $leftBlocksDropDown->cssId = 'ddleftblocks';
    $leftBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');

    foreach ($smallDynamicBlocks as $smallBlock) {
        $title = htmlentities(trim($smallBlock['title']));
        if ($title !== "" && $title !== NULL) {
            $title .= "(" . $smallBlock['module'] . ")";
        }
        $rightBlocksDropDown->addOption('dynamicblock|'
                . $smallBlock['id'] . '|' . $smallBlock['module'], $title);
        $leftBlocksDropDown->addOption('dynamicblock|' . $smallBlock['id']
                . '|' . $smallBlock['module'], $title);
    }

    foreach ($smallBlocks as $smallBlock) {
        $block = $this->newObject('block_' . $smallBlock['blockname'],
                        $smallBlock['moduleid']);
        $moduleId = $smallBlock['moduleid'];
        $title = trim($block->title);
        if ($title !== "" && $title !== NULL) {
            $title .= "(" . $smallBlock['moduleid'] . ")";
        }
        $rightBlocksDropDown->addOption('block|' . $smallBlock['blockname']
                . '|' . $smallBlock['moduleid'], htmlentities($title));
        $leftBlocksDropDown->addOption('block|' . $smallBlock['blockname']
                . '|' . $smallBlock['moduleid'], htmlentities($title));
    }

    $wideBlocksDropDown = new dropdown('middleblocks');
    $wideBlocksDropDown->cssId = 'ddmiddleblocks';
    $wideBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');

    foreach ($wideDynamicBlocks as $wideBlock) {
        $title = htmlentities($wideBlock['title']);
        if ($title !== "" && $title !== NULL) {
            $title .= "(" . $wideBlock['module'] . ")";
        }
        $wideBlocksDropDown->addOption('dynamicblock|'
                . $wideBlock['id'] . '|' . $wideBlock['module'],
                $title);
    }

    foreach ($wideBlocks as $wideBlock) {
        $block = $this->newObject('block_' . $wideBlock['blockname'], $wideBlock['moduleid']);
        $title = htmlentities($block->title);
        if ($title !== "" && $title !== NULL) {
            $title .= "(" . $wideBlock['moduleid'] . ")";
        }
        $wideBlocksDropDown->addOption('block|' . $wideBlock['blockname'] . '|' . $wideBlock['moduleid'], $title);
    }
    //Content Wide Blocks
    /* $contentWdBlks = "";
      foreach ($contentWideBlocks as $contentWideBlock) {
      $contentWdBlks .= $this->objBlocks->showBlock($contentWideBlock["id"],"contentblocks");
      } */
    //Add content wideblocks to options
    if (!empty($contentWideBlocks)) {
        foreach ($contentWideBlocks as $contentWideBlock) {
            $block = $this->objBlocks->showBlock($contentWideBlock["id"], "contentblocks");
            $wideBlocksDropDown->addOption('block|' . $contentWideBlock["id"] . '|' . "contentblocks", htmlentities($contentWideBlock["title"]) . '(contentblocks)');
        }
    }
    //Add content sideblocks to options
    if (!empty($contentSmallBlocks)) {
        foreach ($contentSmallBlocks as $contentSmallBlock) {
            $block = $this->objBlocks->showBlock($contentSmallBlock["id"], "contentblocks");
            $rightBlocksDropDown->addOption('block|' . $contentSmallBlock["id"] . '|' . "contentblocks", htmlentities($contentSmallBlock["title"]) . '(contentblocks)');
            $leftBlocksDropDown->addOption('block|' . $contentSmallBlock["id"] . '|' . "contentblocks", htmlentities($contentSmallBlock["title"]) . '(contentblocks)');
        }
    }
    $rightBlocks = $rightBlocksDropDown->show();
    $leftBlocks = $leftBlocksDropDown->show();

    $button = new button('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
    $button->cssId = 'rightbutton';

    $rightButton = $button->show();

    $button = new button('addleftblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
    $button->cssId = 'leftbutton';

    $leftButton = $button->show();


    $value = $objLanguage->languageText('mod_context_turneditingon',
                    'context', 'Turn Editing On');
    $objEdBut = $this->getObject('buildcanvas', 'canvas');
    $editBut = $objEdBut->getSwitchButton($value);
}

$header = new htmlheading();
$header->type = 3;
$header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');


$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$postLoginSideMenu = $objSysConfig->getValue('SIDEMENU', 'postlogin');
switch (strtolower($postLoginSideMenu)) {
    case 'elearnpostlogin':
        $elearnPostLoginMenu = $this->newObject('postloginmenu_elearn', 'toolbar');
        $objCssLayout->setLeftColumnContent($elearnPostLoginMenu->show());
        break;
    default:
        $postLoginMenu = $this->newObject('postloginmenu', 'toolbar');
        $objCssLayout->setLeftColumnContent($postLoginMenu->show());
        break;
}



$objCssLayout->leftColumnContent .= '<div id="leftblocks">' . $leftBlocksStr . '</div>';

if ($objUser->isAdmin()) {
    $objCssLayout->leftColumnContent .= '<div id="leftaddblock">' . $header->show() . $leftBlocks;
    $objCssLayout->leftColumnContent .= '<div id="leftpreview"><div id="leftpreviewcontent"></div> ' . $leftButton . ' </div>';
    $objCssLayout->leftColumnContent .= '</div>';
}

$objCssLayout->rightColumnContent = '';

if ($objUser->isAdmin()) {
    $objCssLayout->rightColumnContent .= '<div id="editmode">' . $editBut . $utillink . '</div>';
}
$objCssLayout->rightColumnContent .= '<div id="rightblocks">' . $rightBlocksStr . '</div>';

if ($objUser->isAdmin()) {
    $objCssLayout->rightColumnContent .= '<div id="rightaddblock">' . $header->show() . $rightBlocks;
    $objCssLayout->rightColumnContent .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> ' . $rightButton . ' </div>';
    $objCssLayout->rightColumnContent .= '</div>';
}

$button = new button('addmiddleblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
$button->cssId = 'middlebutton';

$objCssLayout->middleColumnContent = '<div id="middleblocks">' . $middleBlocksStr . '</div>';

if ($objUser->isAdmin()) {
    $objCssLayout->middleColumnContent .= '<div id="middleaddblock">' . $header->show() . $wideBlocksDropDown->show();
    $objCssLayout->middleColumnContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> ' . $button->show() . ' </div>';
    $objCssLayout->middleColumnContent .= '</div>';
}

echo $objCssLayout->show();
