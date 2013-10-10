<?php
header ( "Content-Type: text/html;charset=utf-8" );
$cssLayout = $this->newObject ( 'csslayout', 'htmlelements' );
$cssLayout->setNumColumns ( 2 );

$this->objOps = $this->getObject('eventsops');
$this->objTeeny = $this->getObject ( 'tiny', 'tinyurl');
$eventdata    = json_decode($eventdata);
//var_dump($eventdata);
$eventurl = $this->objTeeny->create($this->uri(array('action' => 'viewsingle', 'eventid' => $eventdata->event->id)));
$objShare = $this->getObject('share', 'toolbar');
$objShare->setup($eventurl, $eventdata->event->name, 'Check out this event! ');

$middleColumn = NULL;
$leftColumn = NULL;
$rightColumn = NULL;

$this->loadClass('htmlheading', 'htmlelements');
$headerev = new htmlheading();
$headerev->type = 1;
$headerev->str = $this->objLanguage->languageText("mod_events_eventdetails", "events"); // ." (#".$eventdata->hashtag->mediatag.")";

$middleColumn .= $headerev->show();
$middleColumn .= $objShare->show()."<br />";

$middleColumn .= $this->objOps->viewsingleContainer($eventdata);

$leftColumn .= $this->objOps->browseEventsBox();
$leftColumn .= $this->objOps->showAttendeesBox($eventdata);
$leftColumn .= $this->objOps->showTicketBox($eventdata);
//$rightColumn .= $this->objOps->showWelcomeBox();

$cssLayout->setMiddleColumnContent ( $middleColumn );
$cssLayout->setLeftColumnContent ( $leftColumn );
//$cssLayout->setRightColumnContent ( $rightColumn );

echo $cssLayout->show ();
