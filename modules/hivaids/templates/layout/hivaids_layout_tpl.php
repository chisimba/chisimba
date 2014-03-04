<?php
/**
* @package hivaids
*
* Layout template for the hivaids module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$cssLayout->setLeftColumnContent($this->hivTools->showLeftColumn().'<br />');
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>