<?php
/**
* @package messaging
*/

/**
* Chat room layout for the messaging module
*/

$cssLayout = $this->newObject('csslayout','htmlelements');
$cssLayout->setNumColumns(3);

$leftColumn = $this->newObject('sidemenu','toolbar');
$objBlocks = $this->newObject('blocks', 'blocks');
$returnBlock = $objBlocks->showBlock('chatreturn', 'messaging', '', '', '', FALSE);
$usersBlock = $objBlocks->showBlock('onlineusers', 'messaging', '', '', FALSE, FALSE);
$smileyBlock = $objBlocks->showBlock('smileys', 'messaging', '', '', FALSE, FALSE);
$formatBlock = $objBlocks->showBlock('formatting', 'messaging', '', '', FALSE, FALSE);

$cssLayout->setLeftColumnContent($returnBlock.$usersBlock.'<br />');
$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setRightColumnContent($smileyBlock.$formatBlock.'<br />');

echo $cssLayout->show();
?>