<?php
$cssLayout =  $this->newObject('csslayout', 'htmlelements');

$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>
