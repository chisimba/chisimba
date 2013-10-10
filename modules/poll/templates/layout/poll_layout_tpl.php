<?
/**
* @package etd
*
* Layout template for the etd module
*/

$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$cssLayout->setLeftColumnContent($this->pollTools->leftMenu());
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();
?>