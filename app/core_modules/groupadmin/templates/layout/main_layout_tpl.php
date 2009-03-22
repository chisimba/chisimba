<?php

//create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');

//set columns to 2
$cssLayout->setNumColumns(2);

//add left column
//$cssLayout->setLeftColumnContent($this->objOps->getLeftMenu());

//set middle content
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show(); 
    
?>