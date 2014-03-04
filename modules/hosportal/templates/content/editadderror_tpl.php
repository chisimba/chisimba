<?php

$this->setErrorMessage("Please Supply Data in All Fields");

//Get the CSS layout to make two column layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Add some text to the left column
$cssLayout->setLeftColumnContent("Place holder text");
//get the editform object and instantiate it
$objEditForm = $this->getObject('editmessage', 'hosportal');
//Add the form to the middle (right in two column layout) area
$cssLayout->setMiddleColumnContent($objEditForm->show());
//$cssLayout->setFooterContent(    $this->putMessages());
echo $cssLayout->show();

?>
