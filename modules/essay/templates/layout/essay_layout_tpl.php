<?php
/*
* Layout template for essay management.
* @package essayadmin
*/
/*
if (!$this->objContext->isInContext()) {
    $contextMenu ='';
} else {
    $objContextUtils = $this->getObject('utilities','context');
    $contextMenu = $objContextUtils->getHiddenContextMenu('essay','none');
}
*/
$leftMenu = $this->getObject('contextsidebar', 'context');

$content = '';

$objHeading = $this->newObject('htmlheading','htmlelements');
$objHeading->str = $heading;
$objHeading->type = 1;
$content .= $objHeading->show();

$objLayer = $this->objLayer;
$objLayer->str = $this->getContent();
$content .= $objLayer->show();
$content = "<div class='essay_main'>$content</div>";
$objLayout = $this->newObject('csslayout', 'htmlelements');
$objLayout->setLeftColumnContent($leftMenu->show());
$objLayout->setMiddleColumnContent($content);

echo $objLayout->show();
?>