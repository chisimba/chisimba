<?php
/**
* Template layout for worksheet module
* @package worksheet
*/
$this->setVar('pageSuppressToolbar', TRUE);

$objLayer =& $this->newObject('layer', 'htmlelements');
$leftMenu=& $this->getObject('contextsidebar', 'context');
$objHead=& $this->newObject('htmlheading','htmlelements');

if(!isset($heading))
    $heading=$objLanguage->languageText('mod_worksheet_name').' '.$objLanguage->languageText('mod_worksheet_in')
.' '.$contextTitle;

$objHead->str=$heading;
$objHead->type=1;
$main = $objHead->show();

$main .= $this->getContent();

$objLayer->str = $main;
$objLayer->padding = '10px';
echo $objLayer->show();
?>
