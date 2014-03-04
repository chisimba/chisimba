<?php
/**
* @package wiki version 2
*/

/**
* Default layout for the wiki version 2 module
*/

$cssLayout=&$this->newObject('csslayout','htmlelements');
$cssLayout->setNumColumns(2);

$leftColumn=$this->newObject('wikidisplay','wiki');

$left = $leftColumn->showWikiToolbar();
$left = "<div class='wiki_left'>$left</div>";
$cssLayout->setLeftColumnContent($left);
$middle = $this->getContent();
$middle = "<div class='wiki_main'>$middle</div>";
$cssLayout->setMiddleColumnContent($middle);
echo $cssLayout->show();
?>