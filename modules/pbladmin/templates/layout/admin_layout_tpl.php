<?php
/**
* @package pbladmin
*/

/**
* Layout template for pbladmin
*/

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$menuBar =& $this->getObject('contextsidebar', 'context');
$objHead =& $this->newObject('htmlheading','htmlelements');

if(!isset($heading)){
    $heading=$this->objLanguage->languageText('mod_pbladmin_name');
}

$objDBContext = $this->getObject('dbcontext','context');
if($objDBContext->isInContext())
{
    $objContextUtils = $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('pbladmin','show');
} else {
    $cm = '';
}

$objHead->str = $heading;
$objHead->type = 1;
$head = $objHead->show();

$cssLayout->setLeftColumnContent($menuBar->show());
$cssLayout->setMiddleColumnContent($head.$this->getContent());

echo $cssLayout->show();
?>