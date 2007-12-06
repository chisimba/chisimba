<?php
//print_r($this->tagCloud); die();
//create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');

//set columns to 2
$cssLayout->setNumColumns(2);

//add left column
$cssLayout->setLeftColumnContent($this->objSideMenu->show($activeCat).$this->objTagCloud);

//set middle content
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show(); 
?>