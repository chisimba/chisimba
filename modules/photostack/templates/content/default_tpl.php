<?php
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$leftCol = NULL;
$middleColumn = NULL;

$objUi = $this->getObject('stackui');

$cssLayout->setNumColumns(2);

$middleColumn = $objUi->getGallery($userid);
$leftCol .= $objUi->getSocial();

$cssLayout->setMiddleColumnContent($middleColumn);
$cssLayout->setLeftColumnContent($leftCol);
echo $cssLayout->show();
?>
