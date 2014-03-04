<?php
/**
* Template layout for worksheet module
* @package worksheet
*/

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$leftMenu=& $this->getObject('contextsidebar', 'context');
$objHead=& $this->newObject('htmlheading','htmlelements');

$objDBContext = & $this->getObject('dbcontext','context');
if($objDBContext->isInContext())
{
    $objContextUtils = & $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('worksheet','show');
} else {
    $cm = '';
}

if(!isset($heading))
    $heading=$objLanguage->languageText('mod_worksheet_name').' '.$objLanguage->languageText('mod_worksheet_in')
.' '.$contextTitle;

$objHead->str=$heading;
$objHead->type=1;
$main = $objHead->show();

$main .= $this->getContent();

$cssLayout->setLeftColumnContent($leftMenu->show());
$cssLayout->setMiddleColumnContent($main);

echo $cssLayout->show();
?>
