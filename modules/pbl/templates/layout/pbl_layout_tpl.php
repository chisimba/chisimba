<?php
/**
* @package pbl
*/

/**
* Layout template for the pbl module
*/

$leftMenu = $this->getObject('contextsidebar', 'context');
$objHead = $this->newObject('htmlheading','htmlelements');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

$heading=$objLanguage->languageText('mod_pbl_pbl', 'pbl');

$objDBContext = $this->getObject('dbcontext','context');
if($objDBContext->isInContext())
{
    $objContextUtils = $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('pbl','show');
} else {
    $cm = '';
}

$objHead->str=$heading;
$objHead->type=1;
$head = $objHead->show();

$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($head.$this->getContent());
$cssLayout->setRightColumnContent($rightContent);

echo $cssLayout->show();
?>