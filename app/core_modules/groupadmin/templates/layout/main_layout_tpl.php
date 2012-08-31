<?php
//create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
//set columns to 2
$cssLayout->setNumColumns(2);
//set middle content
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>