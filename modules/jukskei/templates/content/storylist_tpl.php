<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

$sessionmanager= $this->getObject("contentmanager");

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);
//Add the table to the centered layer
//$rightSideColumn .=  $sessionmanager->showTopicList();

$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent( $postLoginMenu->show());

// Add Right Column
$cssLayout->setMiddleColumnContent( $rightSideColumn);

//Output the content to the page
echo $cssLayout->show();
echo $sessionmanager->showTopicList();
?>
