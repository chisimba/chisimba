<?php
// Generate Icons used bu JavaScript
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('up');
$upIcon = $objIcon->show();


$objIcon->setIcon('down');
$downIcon = $objIcon->show();

$objIcon->setIcon('delete');
$deleteIcon = $objIcon->show();

$objLanguage = $this->getObject('language', 'language');


// Set JavaScript Variables
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
    theModule = 'personalspace';

    // ]]>
</script>
<?php
echo $this->getJavaScriptFile('contextblocks.js', 'context');
// End Addition of JavaScript

$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$objCssLayout = $this->getObject('csslayout', 'htmlelements');
$objCssLayout->setNumColumns(3);



$smallBlocksDropDown = new dropdown('rightblocks');
$smallBlocksDropDown->cssId = 'ddrightblocks';
$smallBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');

// Create array for sorting
$smallBlockOptions = array();

// Add Small Dynamic Blocks
foreach ($smallDynamicBlocks as $smallBlock) {
    $smallBlockOptions['dynamicblock|' . $smallBlock['id'] . '|' . $smallBlock['module']] = htmlentities($smallBlock['title']);
}

// Add Small Blocks
foreach ($smallBlocks as $smallBlock) {
    if ($smallBlock['moduleid'] != "contentblocks") {
        $block = $this->newObject('block_' . $smallBlock['blockname'], $smallBlock['moduleid']);
        $title = $block->title;

        if ($title == '') {
            $title = $smallBlock['blockname'] . '|' . $smallBlock['moduleid'];
        } else {
            $title = $title . "(" . $smallBlock['moduleid']. ")";
        }

        $smallBlockOptions['block|' . $smallBlock['blockname'] . '|' . $smallBlock['moduleid']] = htmlentities($title);
    } else {

        // fetch contentblock data
        $block = $this->objBlocks->showBlock($smallBlock["id"], "contentblocks");
        $bData = $this->objBlocksContent->getBlockById($smallBlock["id"]);
        if (!empty($bData)) {
            $bData = $bData[0];
            $title = $bData["title"];
            //parse some abstractions
            $title = $objLanguage->abstractText($title);
            if ($title == '') {
                $title = $smallBlock['blockname'] . ' | ';
            }
            $title .= " (" . $smallBlock['moduleid'] . ")";
            $smallBlockOptions['block|' . $bData["id"] . '|' . "contentblocks"] = htmlentities($bData["title"] . ' (contentblocks)');
        }
    }
}

// Sort Alphabetically
asort($smallBlockOptions);

// Add Small Blocks
foreach ($smallBlockOptions as $block => $title) {
    $smallBlocksDropDown->addOption($block, $title);
}

//Add content sideblocks to options
if (!empty($contentSmallBlocks)) {
    foreach ($contentSmallBlocks as $contentSmallBlock) {
        $smallBlocksDropDown->addOption('block|' . $contentSmallBlock["id"] . '|' . "contentblocks", htmlentities($contentSmallBlock["title"]) . '(contentblocks)');
    }
}
$rightBlocks = $smallBlocksDropDown->show();

$smallBlocksDropDown->cssId = 'ddleftblocks';
$smallBlocksDropDown->name = 'leftblocks';

$leftBlocks = $smallBlocksDropDown->show();


// ------------------------------
// Create array for sorting
$wideBlockOptions = array();

$wideBlocksDropDown = new dropdown('middleblocks');
$wideBlocksDropDown->cssId = 'ddmiddleblocks';
$wideBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'context', 'Select One') . '...');

foreach ($wideDynamicBlocks as $wideBlock) {
    $smallBlockOptions['dynamicblock|' . $wideBlock['id'] . '|' . $wideBlock['module']] = htmlentities($wideBlock['title']);
}

foreach ($wideBlocks as $wideBlock) {
    if ($wideBlock["moduleid"] != "contentblocks") {
        $block = $this->newObject('block_' . $wideBlock['blockname'], $wideBlock['moduleid']);
        $title = $block->title;

        if ($title == '') {
            $title = $wideBlock['blockname'] . '|' . $wideBlock['moduleid'];
        } else {
            $title = $title . " (" . $wideBlock['moduleid'] . ")";
        }

        $wideBlockOptions['block|' . $wideBlock['blockname'] . '|' . $wideBlock['moduleid']] = htmlentities($title);
    } else {
        // fetch contentblock data
        $block = $this->objBlocks->showBlock($wideBlock["id"], "contentblocks");
        $bData = $this->objBlocksContent->getBlockById($wideBlock["id"]);
        if (!empty($bData)) {
            $bData = $bData[0];
            $title = $bData["title"];
            //parse some abstractions
            $title = $objLanguage->abstractText($title);
            if ($title == '') {
                $title = $wideBlock['blockname'] . ' | ';
            }
            $title .= " (" . $wideBlock['moduleid'] . ")";
            $wideBlockOptions['block|' . $bData["id"] . '|' . "contentblocks"] = htmlentities($bData["title"] . ' (contentblocks)');
        }
    }
}

// Sort Alphabetically
asort($wideBlockOptions);

// Add Small Blocks
foreach ($wideBlockOptions as $block => $title) {
    $wideBlocksDropDown->addOption($block, $title);
}

//Add content wideblocks to options
if (!empty($contentWideBlocks)) {
    foreach ($contentWideBlocks as $contentWideBlock) {
        $wideBlocksDropDown->addOption('block|' . $contentWideBlock["id"] . '|' . "contentblocks", htmlentities($contentWideBlock["title"]) . '(contentblocks)');
    }
}

$button = new button('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
$button->cssId = 'rightbutton';

$rightButton = $button->show();

$button = new button('addleftblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
$button->cssId = 'leftbutton';

$leftButton = $button->show();

$header = new htmlheading();
$header->type = 3;
$header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');



$userMenu = $this->newObject('usermenu', 'toolbar');
$objCssLayout->setLeftColumnContent($userMenu->show());




$objCssLayout->leftColumnContent .= '<div id="leftblocks">' . $leftBlocksStr . '</div>';


$objCssLayout->leftColumnContent .= '<div id="leftaddblock">' . $header->show() . $leftBlocks;
$objCssLayout->leftColumnContent .= '<div id="lefttpreview"><div id="leftpreviewcontent"></div> ' . $leftButton . ' </div>';
$objCssLayout->leftColumnContent .= '</div>';


$objCssLayout->rightColumnContent = '';


$value = $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On');
$objEdBut = $this->getObject('buildcanvas', 'canvas');
$editBut = $objEdBut->getSwitchButton($value);
$objCssLayout->rightColumnContent .= '<div id="editmode">' . $editBut . '</div>';

$objCssLayout->rightColumnContent .= '<div id="rightblocks">' . $rightBlocksStr . '</div>';


$objCssLayout->rightColumnContent .= '<div id="rightaddblock">' . $header->show() . $rightBlocks;
$objCssLayout->rightColumnContent .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> ' . $rightButton . ' </div>';
$objCssLayout->rightColumnContent .= '</div>';


$button = new button('addmiddleblock', $objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
$button->cssId = 'middlebutton';

$objCssLayout->middleColumnContent = '<div id="middleblocks">' . $middleBlocksStr . '</div>';


$objCssLayout->middleColumnContent .= '<div id="middleaddblock">' . $header->show() . $wideBlocksDropDown->show();
$objCssLayout->middleColumnContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> ' . $button->show() . ' </div>';
$objCssLayout->middleColumnContent .= '</div>';


echo $objCssLayout->show();