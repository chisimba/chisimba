<?

$objBlocks = $this->getObject('blocks','blocks');
$b = $objBlocks->showBlock('login', 'security');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$cssLayout->setMiddleColumnContent($this->getContent());
$cssLayout->setLeftColumnContent($b);

echo $cssLayout->show();

?>