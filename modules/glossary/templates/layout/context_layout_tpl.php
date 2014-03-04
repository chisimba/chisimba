<?php
$this->_objDBContext =& $this->getObject('dbcontext','context');
if($this->_objDBContext->isInContext())
{
    $objContextUtils = & $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('glossary','none');
} else {
    $cm ='';
}
$toolbar = $this->getObject('contextsidebar', 'context');

// Create an Instance of the CSS Layout
$cssLayout =& $this->newObject('csslayout', 'htmlelements');

// Create an Instance of the User Side Menu
//$userMenuBar=& $this->getObject('contextmenu','toolbar');
$userMenuBar=& $this->getObject('sidemenu','toolbar');

// Set the Content of left side column
$cssLayout->setLeftColumnContent($toolbar->show());
//$cssLayout->setLeftColumnContent($cm.$userMenuBar->menuContext());

// Set the Content of middle column
$cssLayout->setMiddleColumnContent($this->getContent());

// Display the Layout
echo $cssLayout->show(); 

?>
