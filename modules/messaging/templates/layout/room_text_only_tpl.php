<?php
/**
* @package messaging
*/

/**
* Chat room layout for the messaging module
*/

$cssLayout = $this->newObject('csslayout','htmlelements');
$cssLayout->setNumColumns(2);

$leftColumn = $this->newObject('sidemenu','toolbar');
$objBlocks = $this->newObject('blocks', 'blocks');
$returnBlock = $objBlocks->showBlock('chatreturn', 'messaging', '', '', '', FALSE);
$usersBlock = $objBlocks->showBlock('onlineusers', 'messaging', '', '', FALSE, FALSE);

$cssLayout->setLeftColumnContent($returnBlock.$usersBlock.'<br />');
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>