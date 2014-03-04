<?php
/**
* @package sudoku
*/

/**
* Default layout for sudoku
*/

$cssLayout =& $this -> newObject('csslayout', 'htmlelements');
$cssLayout -> setNumColumns(3);

$leftColumn =& $this -> newObject('sidemenu','toolbar');
$objBlocks =& $this -> newObject('blocks', 'blocks');
$rghtColumn =& $objBlocks -> showBlock('help', 'sudoku');

$cssLayout -> setLeftColumnContent($leftColumn -> menuUser());
$cssLayout -> setMiddleColumnContent($this -> getContent());
$cssLayout -> setRightColumnContent($rghtColumn);

echo $cssLayout->show();
?>