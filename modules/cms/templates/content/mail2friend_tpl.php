<?php
//mail2friend template

$this->loadClass('href', 'htmlelements');
$icon_mail = $this->newObject('geticon', 'htmlelements');
$objIconCancel = $this->newObject('geticon', 'htmlelements');
$tt = $this->newObject('domtt', 'htmlelements');
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$objSideBar = $this->newObject('usermenu', 'toolbar');
// Set columns to 3
//$cssLayout->setNumColumns(3);
$icon_mail->setIcon('email', 'png', 'icons/cms/');
// Cancel	 		
$url = 'javascript:history.back();';
$linkText = ucwords($this->objLanguage->languageText('word_cancel'));
$iconList = $objIconCancel->getCleanTextIcon('', $url, 'back', $linkText, 'png', 'icons/cms/');
			 		
$leftMenu = NULL;

$rightSideColumn = NULL;

$leftCol = $icon_mail->show().'<br/>';
$leftCol .= '<p style="align:right;">'.$iconList.'</p>';
$middleColumn = NULL;

//load up a featurebox and display it nicely
$objFeatureBox = $this->getObject('featurebox', 'navigation');

//gooi the form with a message and the name thing with email address(es) to send to
$middleColumn .= $this->objLayout->sendMail2FriendForm($m2fdata);

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
$cssLayout->setRightColumnContent($rightSideColumn);
echo $cssLayout->show();
?>
