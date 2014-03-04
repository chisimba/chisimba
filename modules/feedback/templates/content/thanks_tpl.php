<?php
$objmsg = &$this->getObject('timeoutmessage', 'htmlelements');
$this->loadClass('href', 'htmlelements');
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
$objFeatureBox = $this->newObject('featurebox', 'navigation');

// Set columns to 3
$cssLayout->setNumColumns(3);

$leftMenu = NULL;
$rightSideColumn = NULL;
$leftCol = NULL;
$middleColumn = NULL;

//check for messages...
if ($msg == 'save') {
    $objmsg->message = $this->objLanguage->languageText('mod_feedback_recsaved', 'feedback');
    echo $objmsg->show();
} elseif($msg = 'nodata') {
	$objmsg->message = $this->objLanguage->languageText('mod_feedback_elaborate', 'feedback');
    echo $objmsg->show();
    $msg = NULL;
}
else {
	$msg = NULL;
}

if($this->objUser->isLoggedIn())
{
	$leftCol .= $objSideBar->show();
	
}
else {
	$leftCol = null;
	
}
$middleColumn = $this->objFb->thanks();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);

echo $cssLayout->show();
?>