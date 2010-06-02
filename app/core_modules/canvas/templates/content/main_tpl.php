<?php
// Create an instance of the css layout class & set columns to 2
$cssLayout =  $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$objBlock = $this->getObject('blocks', 'blocks');
$leftColumn = $objBlock->showBlock('selecttype', 'canvas', NULL, 20, TRUE, FALSE);
$wideColumn = $objBlock->showBlock('canvasviewer', 'canvas', NULL, 20, TRUE, FALSE);

// Add Left column
$cssLayout->setLeftColumnContent($leftColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($wideColumn);

//Output the content to the page
echo $cssLayout->show();
?>