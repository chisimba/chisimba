<?php

// Create an Instance of the CSS Layout

$cssLayout =& $this->newObject('csslayout', 'htmlelements');

/**$objBlocks = $this->getObject('blocks', 'blocks');



$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent($objBlocks->showBlock('login', 'security'));
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
**/
 	

  $cssLayout->setNumColumns(3);

// Set the Content of middle column


$cssLayout->setLeftColumnContent(" ");


$cssLayout->setRightColumnContent(" ");
$cssLayout->setMiddleColumnContent($this->getContent());



// Display the Layout

echo $cssLayout->show(); 
?>
