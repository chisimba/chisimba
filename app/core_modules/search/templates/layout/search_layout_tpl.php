<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);
$middleColumn = $this->getVar('middleContent');
$cssLayout->setMiddleColumnContent($middleColumn);
echo $cssLayout->show();
