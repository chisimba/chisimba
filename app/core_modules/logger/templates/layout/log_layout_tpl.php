<?php
/**
* Layout template for the logger module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');

$cssLayout->setNumColumns(2);
$left = '<div class="logger_left">' .  $this->logDisplay->leftMenu() . '</div>';
$cssLayout->setLeftColumnContent($left);

$ret = '<div class="logger_main">' . $this->getContent() . '</div>';
$cssLayout->setMiddleColumnContent($ret);

echo $cssLayout->show();
?>