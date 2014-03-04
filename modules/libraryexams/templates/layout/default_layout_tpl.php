<?php
// Create an instance of the CSS Layout
$cssLayout = $this->getObject('csslayout', 'htmlelements');

//Set to automatically render htmllist into tree menu
$cssLayout->setNumColumns(2);
// Set the Content of middle column
$cssLayout->setLeftColumnContent("");
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show();

?>
