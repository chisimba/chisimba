<?php
//print_r($this->tagCloud); die();
//create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');

//set columns to 2
$cssLayout->setNumColumns(2);

//add left column
$ret = $this->objSideMenu->show($activeCat).$this->objTagCloud;
$ret = "<div class='modcat_left'>$ret</div>";
$cssLayout->setLeftColumnContent($ret);
unset($ret);
//set middle content
$ret = $this->getContent();
$ret = "<div class='modcat_main'>$ret</div>";
$cssLayout->setMiddleColumnContent($ret);

// Render module catalogue.
echo $cssLayout->show();
?>