<?php
$this->setVar('canvastype', 'context');
/*
  $objExtJS = $this->getObject('extjs', 'ext');
  $objExtJS->show();
 */
//Language items
$default = 'You are using an unsupported browser. Please switch to Mozilla FireFox available at ( http://getfirefox.com ). Currently the system functionality is limited. Thanks!';
$browserError = $objLanguage->languageText('mod_poll_browserError', 'poll', $default);
$objConfig = $this->getObject('altconfig', 'config');
$objIcon = $this->newObject('geticon', 'htmlelements');
// Add JavaScript if User can update blocks
if ($this->isValid('addblock')) {


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
/*
  $whoIsOnlineJs='
  <SCRIPT language="JavaScript1.2">
  var base="'.$objConfig->getSiteRoot().'?module=livechat'.'";

  function showWhoIsOnlineWin()
  {
  var win=window.open ("?module=livechat","Live chat","location=0,status=0,scrollbars=0,width=10,height=10,top=-1, left=-1");
  win.blur();
  win.opener.focus();
  }


  </SCRIPT>
  ';
  $this->appendArrayVar('headerParams',$whoIsOnlineJs);
  $objModule = $this->getObject('modules', 'modulecatalogue');

  //See if tcontextinstructor is registered, if so, then show
  $isRegistered = $objModule->checkIfRegistered('livechat');
  if ($isRegistered) {
  $params = 'onload="javascript: showWhoIsOnlineWin()"';
  $this->setVar("bodyParams", $params);

  } */
$objModule = $this->getObject('modules', 'modulecatalogue');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$objCssLayout = $this->getObject('csslayout', 'htmlelements');
$objCssLayout->setNumColumns(3);

if ($this->isValid('addblock')) {

    $rightBlocksDropDown = new dropdown('rightblocks');
    $rightBlocksDropDown->cssId = 'ddrightblocks';
    $rightBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'phrase', 'Select One') . '...');

    $leftBlocksDropDown = new dropdown('leftblocks');
    $leftBlocksDropDown->cssId = 'ddleftblocks';
    $leftBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'phrase', 'Select One') . '...');

    // Create array for sorting
    $smallBlockOptions = array();

    // Add Small Dynamic Blocks
    foreach ($smallDynamicBlocks as $smallBlock) {
        $smallBlockOptions['dynamicblock|' . $smallBlock['id'] . '|' . $smallBlock['module']] = htmlentities($smallBlock['title']) . " (" . $smallBlock['module'] . ")";
    }

    // Add Small Blocks
    foreach ($smallBlocks as $smallBlock) {
        if ($smallBlock['moduleid'] != "contentblocks") {
            $block = $this->newObject('block_' . $smallBlock['blockname'], $smallBlock['moduleid']);

            $title = $block->title;
            //parse some abstractions
            $title = $this->objLanguage->abstractText($title);
            if ($title == '') {
                $title = $smallBlock['blockname'] . ' | ';
            }
            $title .= " (" . $smallBlock['moduleid'] . ")";
            $smallBlockOptions['block|' . $smallBlock['blockname'] . '|' . $smallBlock['moduleid']] = htmlentities($title);
            
        } else {
            
            // fetch contentblock data
            $block = $this->objBlocks->showBlock($smallBlock["id"], "contentblocks");
            $bData = $this->objBlocksContent->getBlockById($smallBlock["id"]);
            if (!empty($bData)) {
                $bData = $bData[0];
                $title = $bData["title"];
                //parse some abstractions
                $title = $this->objLanguage->abstractText($title);
                if ($title == '') {
                    $title = $smallBlock['blockname'] . ' | ';
                }
                $title .= " (" . $smallBlock['moduleid'] . ")";
                $smallBlockOptions['block|' . $bData["id"] . '|' . "contentblocks"] = htmlentities($bData["title"]. ' (contentblocks)');
            }
        }
    }
    // Sort Alphabetically
    asort($smallBlockOptions);

    // Add Small Blocks for right
    foreach ($smallBlockOptions as $block => $title) {
        $rightBlocksDropDown->addOption($block, $title);
    }

    //then left too
    foreach ($smallBlockOptions as $block => $title) {
        $leftBlocksDropDown->addOption($block, $title);
    }


    // Create array for sorting
    $wideBlockOptions = array();

    $wideBlocksDropDown = new dropdown('middleblocks');
    $wideBlocksDropDown->cssId = 'ddmiddleblocks';
    $wideBlocksDropDown->addOption('', $objLanguage->languageText('phrase_selectone', 'phrase', 'Select One') . '...');

    foreach ($wideDynamicBlocks as $wideBlock) {
        $smallBlockOptions['dynamicblock|' . $wideBlock['id'] . '|' . $wideBlock['module']] = htmlentities($wideBlock['title']);
    }

    foreach ($wideBlocks as $wideBlock) {
        if($wideBlock["moduleid"]!="contentblocks"){
        $block = $this->newObject('block_' . $wideBlock['blockname'], $wideBlock['moduleid']);
        $title = $block->title;

        if ($title == '') {
            $title = $wideBlock['blockname'] . ' | ';
        }
        $title .= " (" . $wideBlock['moduleid'] . ")";
        $wideBlockOptions['block|' . $wideBlock['blockname'] . '|' . $wideBlock['moduleid']] = htmlentities($title);
        } else {
            // fetch contentblock data
            $block = $this->objBlocks->showBlock($wideBlock["id"], "contentblocks");
            $bData = $this->objBlocksContent->getBlockById($wideBlock["id"]);
            if (!empty($bData)) {
                $bData = $bData[0];
                $title = $bData["title"];
                //parse some abstractions
                $title = $this->objLanguage->abstractText($title);
                if ($title == '') {
                    $title = $wideBlock['blockname'] . ' | ';
                }
                $title .= " (" . $wideBlock['moduleid'] . ")";
                $wideBlockOptions['block|' . $bData["id"] . '|' . "contentblocks"] = htmlentities($bData["title"]. ' (contentblocks)');
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
    $button = new button('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
    $button->cssId = 'rightbutton';

    $value = $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On');
    $objEdBut = $this->getObject('buildcanvas', 'canvas');
    $editBut = $objEdBut->getSwitchButton($value);
}

$header = new htmlheading();
$header->type = 3;
$header->str = $objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');

$toolbar = $this->getObject('contextsidebar');
$instructorProfile = "";

//See if tcontextinstructor is registered, if so, then show
$isRegistered = $objModule->checkIfRegistered('contextinstructor');
if ($isRegistered) {
    $objContextInstructor = $this->getObject('manager', 'contextinstructor');
    $instructorProfile = $objContextInstructor->show();
}

$contextContentIsRegistered = $objModule->checkIfRegistered('contextcontent');
$utillink = "";
$this->dbSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$showAdminShortcutBlock = $this->dbSysConfig->getValue('SHOW_SHORTCUTS_BLOCK', 'context');
if ($showAdminShortcutBlock == "TRUE" || $showAdminShortcutBlock == "true" || $showAdminShortcutBlock == "True") {
    if ($this->isValid('addblock')) {
        $trackerlink = "";

        $siteRoot = $objConfig->getsiteRoot();
        $moduleUri = $objConfig->getModuleURI();
        $imgPath = "";

        $objIcon->setIcon('plus');
        $plusIcon = $objIcon->show();
        $useractivitylink = new link($this->uri(array('action' => 'selectuseractivitybymoduledates')));
        $useractivitylink->link = $plusIcon . '&nbsp;' . ucfirst($this->objLanguage->code2Txt('mod_context_studentactivity', 'context', NULL, '[-readonly-] activity'));

        $toolsactivitylink = new link($this->uri(array('action' => 'selecttoolsactivitydates')));
        $toolsactivitylink->link = $plusIcon . '&nbsp;' . $this->objLanguage->code2Txt('mod_context_toolsactivity', 'context', NULL, 'Tools activity') . '<br/>';
        $contentactivitylinkStr = "";
        if ($contextContentIsRegistered) {
            $objIcon->setIcon('warning');
            $warnIcon = $objIcon->show();
            $contentactivitylink = new link($this->uri(array('action' => 'viewcontextcontentusage'), 'contextcontent'));
            $contentactivitylink->link = $warnIcon . '&nbsp;' . ucfirst($this->objLanguage->code2Txt('mod_context_coursecontentacitivity', 'context', NULL, '[-context-] content activity'));
            $contentactivitylinkStr = $contentactivitylink->show() . '<br/>';
        }
        $allactivitylink = new link($this->uri(array('action' => 'selectcontextsactivitydates')));
        $allactivitylink->link = $plusIcon . '&nbsp;' . $this->objLanguage->code2Txt('mod_context_allcoursesacitivity', 'context', NULL, 'All [-contexts-] activity');

        $allcontextactivity = "";
        if ($this->objUser->isAdmin()) {
            $allcontextactivity = $allactivitylink->show() . '<br/>';
        }
        $transferlink = new link($this->uri(array('action' => 'transfercontextusers')));
        $transferlink->link = $plusIcon . '&nbsp;' . ucwords($this->objLanguage->code2Txt('mod_context_batchcopy', 'context', NULL, 'Batch Copy [-readonly-]s'));


        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $content = $useractivitylink->show() . '<br/>' . $toolsactivitylink->show() . $allcontextactivity . $contentactivitylinkStr . $transferlink->show();

        $block = "shortcuts";
        $hidden = 'default';
        $showToggle = false;
        $showTitle = true;
        $cssClass = "featurebox";
        $utillink = $objFeatureBox->show(
                ucwords($this->objLanguage->code2Txt('mod_contextcontent_activityshortcuts', NULL, 'contextcontent', '[-context-] Activities')), $content, $block, $hidden, $showToggle, $showTitle, $cssClass, '');
    }
}
$objCssLayout->leftColumnContent = '<ul id="nav-secondary">' . $instructorProfile . '</ul>' . $toolbar->show(); //setLeftColumnContent($toolbar->show());

$objCssLayout->rightColumnContent = '';

if ($this->isValid('addblock')) {
    $objCssLayout->rightColumnContent .= '<div id="editmode">'
            . $editBut . '</div>';
}

$liveChatIsRegistered = $objModule->checkIfRegistered('livechat');
if ($liveChatIsRegistered) {
    $liveChatBlock = $this->getObject("block_livechat", "livechat");
    $utillink.=$liveChatBlock->show();
}
$objCssLayout->rightColumnContent .= $utillink;
$objCssLayout->rightColumnContent .= '<div id="rightblocks">' . $rightBlocksStr . '</div>';

if ($this->isValid('addblock')) {
    $objCssLayout->rightColumnContent .= '<div id="rightaddblock">' . $header->show() . $rightBlocksDropDown->show();
    $objCssLayout->rightColumnContent .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> ' . $button->show() . ' </div>';
    $objCssLayout->rightColumnContent .= '</div>';
}

$button = new button('addmiddleblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
$button->cssId = 'middlebutton';

$objCssLayout->middleColumnContent = '<div id="middleblocks">' . $middleBlocksStr . '</div>';

if ($this->isValid('addblock')) {
    $objCssLayout->middleColumnContent .= '<div id="middleaddblock">' . $header->show() . $wideBlocksDropDown->show();
    $objCssLayout->middleColumnContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> ' . $button->show() . ' </div>';
    $objCssLayout->middleColumnContent .= '</div>';
}

$button = new button('addleftblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
$button->cssId = 'leftbutton';

$objCssLayout->leftColumnContent .= '<br/><div id="leftblocks">' . $leftBlocksStr . '</div>';

if ($this->isValid('addblock')) {
    $objCssLayout->leftColumnContent .= '<div id="leftaddblock">' . $header->show() . $leftBlocksDropDown->show();
    $objCssLayout->leftColumnContent .= '<div id="leftpreview"><div id="leftpreviewcontent"></div> ' . $button->show() . ' </div>';
    $objCssLayout->leftColumnContent .= '</div>';
}

echo $objCssLayout->show();

if ($this->getParam('message') == 'contextsetup') {
    $alertBox = $this->getObject('alertbox', 'htmlelements');
    $alertBox->putJs();

    echo "<script type='text/javascript'>
 var browser=navigator.appName;
 var b_version=parseFloat(b_version);
 if(browser=='Microsoft Internet Explorer'){
    alert('" . $browserError . "');
 }else{
     jQuery.facebox(function() {
      jQuery.get('" . str_replace('&amp;', '&', $this->uri(array('action' => 'contextcreatedmessage'))) . "', function(data) {
        jQuery.facebox(data);
      })
     })
 }
</script>";
} else {
    //Check if poll module is installed
    $pollInstalled = $this->objModuleCatalogue->checkIfRegistered('poll');
    //get the version number
    $pollModuleVer = $this->objModuleCatalogue->getVersion('poll');
    //Display polls if poll is installed & version is higher than 0.121
    if ($pollModuleVer >= '0.121' && $pollInstalled == True) {
        $alertBox = $this->getObject('alertbox', 'htmlelements');
        $alertBox->putJs();
        echo "<script type='text/javascript'>
                 var browser=navigator.appName;
                 var b_version=parseFloat(b_version);
                 if(browser=='Microsoft Internet Explorer'){
                    alert('" . $browserError . "');
                 }else{
                     jQuery.facebox(function() {
                      jQuery.get('" . str_replace('&amp;', '&', $this->uri(array('action' => 'happyeval'), 'poll')) . "', function(data) {
                        jQuery.facebox(data);
                      })
                    })
                }
            </script>";
    }
}
?>
