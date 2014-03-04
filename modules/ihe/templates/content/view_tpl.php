<?php
//create a 2 column layout. Left for navigation bar, right for content.
$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

//get the left navigation menu
$navigation = $this->getObject('navigation', 'ihe');
$cssLayout->setLeftColumnContent($navigation->getNavigationMenu());

//set the middle content to the story in question
if (isset($str)) {
  $cssLayout->setMiddleColumnContent($str); 
}
else {
  $cssLayout->setMiddleColumnContent("Invalid Story"); 
}

echo $cssLayout->show();
?>