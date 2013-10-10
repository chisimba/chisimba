<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 3 );

// get the sidebar object
$this->leftMenu     = $this->newObject ( 'usermenu', 'toolbar' );
$this->objDbEvents  = $this->getObject('dbevents');
$this->objOps       = $this->getObject('eventsops');
$this->objDia       = $this->getObject('jqdialogue', 'jquery');

$middleColumn = NULL;

if(isset($message) && !empty($message) && $message != '' && is_object($message)) {
    $middleColumn .= $message->show();
}
$catname = $catname[0];
$this->loadClass('htmlheading', 'htmlelements');
$headercat = new htmlheading();
$headercat->type = 1;
$headercat->str = $this->objLanguage->languageText("mod_events_eventsforcat", "events").": ".$catname['cat_name']." (".$catname['cat_desc'].")";

$middleColumn .= $headercat->show();;
$middleColumn .= $eventdata;

$leftColumn = NULL;
$rightColumn = NULL;

$leftColumn .= $this->objOps->browseEventsBox();
$rightColumn .= $this->objOps->showWelcomeBox();
$objBlocks = $this->getObject('blocks', 'blocks');
$rightColumn .= $objBlocks->showBlock('lastten', 'activitystreamer');
//$rightColumn .= $this->objOps->showLocWeatherBox();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();
