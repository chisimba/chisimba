<?php
// Create an Instance of the CSS Layout

$cssLayout =& $this->newObject('csslayout', 'htmlelements');

  $cssLayout->setNumColumns(2);


if ($this->_objUser->isLoggedIn())
{
    $cssLayout->setLeftColumnContent($this->_objUtils->getNav());	
} else {
    $objBlocks = $this->getObject('blocks', 'blocks');
    $cssLayout->setLeftColumnContent($objBlocks->showBlock('login', 'security')); 
}

// Set the Content of middle column


//$cssLayout->setRightColumnContent($this->getRightWidgets());
$cssLayout->setMiddleColumnContent($this->getContent().'<br style="clear:both">');



// Display the Layout

echo $cssLayout->show();	


?>
