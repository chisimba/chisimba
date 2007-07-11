<?php
  
//create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$this->userMenuBar=& $this->getObject('contextmenu','toolbar');
//set columns to 2
$cssLayout->setNumColumns(2);

//add left column
$cssLayout->setLeftColumnContent($this->userMenuBar->show());

//set middle content
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show(); 
?>