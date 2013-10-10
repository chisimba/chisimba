<?php
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
//$menuBar=& $this->getObject('workgroupmenu', 'workgroup');
$cssLayout->setLeftColumnContent(''/*$menuBar->show()*/);
$cssLayout->setMiddleColumnContent($this->getContent());
echo $cssLayout->show();
?>