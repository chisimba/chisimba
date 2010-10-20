<?php
$this->setVar('canvastype', 'context');
$objExtJS = $this->getObject('extjs', 'ext');
$objExtJS->show();
//Language items
$default = 'You are using an unsupported browser. Please switch to Mozilla FireFox available at ( http://getfirefox.com ). Currently the system functionality is limited. Thanks!';
$browserError = $objLanguage->languageText('mod_poll_browserError', 'poll', $default);
$objConfig = $this->getObject('altconfig', 'config');
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
   
}*/

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
        $smallBlockOptions['dynamicblock|' . $smallBlock['id'] . '|' . $smallBlock['module']] = htmlentities($smallBlock['title']);
    }


    // Add Small Blocks
    foreach ($smallBlocks as $smallBlock) {
        $block = $this->newObject('block_' . $smallBlock['blockname'], $smallBlock['moduleid']);

        $title = $block->title;
        //parse some abstractions
        $title = $this->objLanguage->abstractText($title);
        if ($title == '') {
            $title = $smallBlock['blockname'] . '|' . $smallBlock['moduleid'];
        }

        $smallBlockOptions['block|' . $smallBlock['blockname'] . '|' . $smallBlock['moduleid']] = htmlentities($title);
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
        $block = $this->newObject('block_' . $wideBlock['blockname'], $wideBlock['moduleid']);
        $title = $block->title;

        if ($title == '') {
            $title = $wideBlock['blockname'] . '|' . $wideBlock['moduleid'];
        }

        $wideBlockOptions['block|' . $wideBlock['blockname'] . '|' . $wideBlock['moduleid']] = htmlentities($title);
    }

    // Sort Alphabetically
    asort($wideBlockOptions);

    // Add Small Blocks
    foreach ($wideBlockOptions as $block => $title) {
        $wideBlocksDropDown->addOption($block, $title);
    }


    $button = new button('addrightblock', $objLanguage->languageText('mod_prelogin_addblock', 'prelogin', 'Add Block'));
    $button->cssId = 'rightbutton';


    $editOnButton = new button('editonbutton', $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On'));
    $editOnButton->cssId = 'editmodeswitchbutton';
    $editOnButton->setOnClick("switchEditMode();");
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

$isRegistered = $objModule->checkIfRegistered('contextcontent');
$utillink = "";
$this->dbSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$showAdminShortcutBlock=$this->dbSysConfig->getValue('SHOW_SHORTCUTS_BLOCK', 'context');
if ($showAdminShortcutBlock == "TRUE" || $showAdminShortcutBlock == "true" || $showAdminShortcutBlock == "True") {
    if ($isRegistered && $this->isValid('addblock')) {
        $trackerlink = "";

        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $siteRoot = $this->objAltConfig->getsiteRoot();
        $moduleUri = $this->objAltConfig->getModuleURI();
        $imgPath = "";

        $link = new link($this->uri(array('action' => 'viewlogs'), 'contextcontent'));
        $link->link = $this->objLanguage->languageText('mod_contextcontent_useractivitylogs', 'contextcontent', 'User activity');


        $trackerlink .= $link->show() . '';
        $transferlink = new link($this->uri(array('action' => 'transfercontextusers')));
        $transferlink->link =ucwords($this->objLanguage->code2Txt('mod_context_transferusers', 'context', NULL, 'Transfer users'));


        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $content = $transferlink->show();
        $block = "shortcuts";
        $hidden = 'default';
        $showToggle = false;
        $showTitle = true;
        $cssClass = "featurebox";
        $utillink = $objFeatureBox->show(
                        $this->objLanguage->languageText('mod_contextcontent_shortcuts', 'contextcontent', 'Shortcuts'),
                        $content,
                        $block,
                        $hidden,
                        $showToggle,
                        $showTitle,
                        $cssClass, '');
    }
}
$objCssLayout->leftColumnContent = '<ul id="nav-secondary">' . $instructorProfile . '</ul>' . $toolbar->show(); //setLeftColumnContent($toolbar->show());

$objCssLayout->rightColumnContent = '';

if ($this->isValid('addblock')) {
    $objCssLayout->rightColumnContent .= '<div id="editmode">' . $editOnButton->show() . '</div>';
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
