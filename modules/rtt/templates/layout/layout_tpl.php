<?php
/**
* Template layout for realtime module
* @package realtime
*/


$cssLayout = $this->getObject('csslayout', 'htmlelements');
$leftMenu = $this->getObject('sidemenu','toolbar');
$objHead = $this->newObject('htmlheading','htmlelements');

//create the context menu if you are in a context
if($this->objContext->isInContext())
{
    $objContextUtils = $this->getObject('utilities','context');
    $cm = $objContextUtils->getHiddenContextMenu('eventscalendar','show');
} else {
    $cm ='';
}

$heading=$this->objLanguage->languageText('mod_realtime_title', 'realtime');
    
$objHead->str=$heading;
$objHead->type=1;
$head = $objHead->show();

$cssLayout->setLeftColumnContent($leftMenu->menuContext().$cm);
$cssLayout->setMiddleColumnContent($head.$this->getContent());

echo $cssLayout->show();
?>