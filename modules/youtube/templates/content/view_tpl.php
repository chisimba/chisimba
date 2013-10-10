<?php
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Set columns to 2
$cssLayout->setNumColumns(2);


//Add Left column
$vw = $this->getObject('youtubetpl','youtube');
$tagForm = $vw->getTagSearchBox();
$userForm = $vw->getUserSearchBox();
$plForm = $vw->getPlSearchBox();
$ytMethod = $vw->showMethod();

$leftSideColumn=$tagForm . "<br />" 
  . $userForm . $plForm . $ytMethod;
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($str);

//Output the content to the page
echo $cssLayout->show();
?>