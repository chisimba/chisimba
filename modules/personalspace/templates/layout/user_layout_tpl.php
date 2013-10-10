<?php

//$events = $this->objLanguage->LanguageText('mod_personalspace_events','personalspace$

//$this->objcalender = & $this->newObject



$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$objMenu =& $this->newObject('sidemenu','toolbar');
$cssLayout->setLeftColumnContent($objMenu->menuUser());
$cssLayout->setMiddleColumnContent($this->getContent());
$moduleCheck = $this->newObject('modules','modulecatalogue');
if ($moduleCheck->checkIfRegistered('eventscalendar')) {
    $cssLayout->setNumColumns(3);
    $calendar =& $this->newObject('block_calendar','eventscalendar');
    $cssLayout->setRightColumnContent($calendar->show());
    
}
echo $cssLayout->show();
?>
