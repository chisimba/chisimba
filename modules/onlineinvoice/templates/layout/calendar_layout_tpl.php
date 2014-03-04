<?php



// Create an Instance of the CSS Layout

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Create an Instance of the User Side Menu
$this->sideMenuBar=& $this->getObject('sidemenu','toolbar');
$sideMenuBar=& $this->getObject('sidemenu','toolbar');
//Set the Content of left side column
$cssLayout->setLeftColumnContent($this->sideMenuBar->userDetails());
//$cssLayout->setRightColunmContent($this->sideMenuBar->userDetails()); 
// Set the Content of left side column
//    $cssLayout->setLeftColumnContent($sideMenuBar->show('user'));

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent()); 
// Display the Layout
echo $cssLayout->show();



?>

