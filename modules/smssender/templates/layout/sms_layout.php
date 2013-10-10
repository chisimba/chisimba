<?php



// Create Side Bar Navigation
$objSideBar = $this->newObject('sidebar', 'navigation');

$menuItems = array();

$smsHome = array('text'=>'Send SMS', 'uri'=>$this->uri(NULL));
$menuItems[] = $smsHome;

$sentSMS = array('text'=>'View Sent SMS', 'uri'=>$this->uri(array('action'=>'viewsent')));
$menuItems[] = $sentSMS;



// End Side Bar Navigation

//$sidebar = '<br /><p align="center">'.$this->objUser->getUserImage().'</p>';
$sidebar = $objSideBar->show($menuItems);



$cssLayout = $this->newObject('csslayout', 'htmlelements');

$cssLayout->setLeftColumnContent($sidebar);

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();

?>