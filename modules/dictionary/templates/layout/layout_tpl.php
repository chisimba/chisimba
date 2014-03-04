<?php



// Create an Instance of the CSS Layout

$cssLayout =& $this->newObject('csslayout', 'htmlelements');

  $cssLayout->setNumColumns(2);

// Set the Content of middle column
 $objDBContext = & $this->getObject('dbcontext','context');
//create the context menu if you are in a context
if($objDBContext->isInContext())
{
    $objContextUtils = & $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('dictionary','show');
} else {
    $cm = "";//$this->getMenu();
}

$leftMenu=& $this->getObject('contextsidebar', 'context');
$cssLayout->setLeftColumnContent($leftMenu->show());


$cssLayout->setMiddleColumnContent($this->getContent());



// Display the Layout

echo $cssLayout->show(); 



?>