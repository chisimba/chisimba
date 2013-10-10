<?php

$this->loadClass('htmlheading', 'htmlelements');
//Add Group Link
$iconConfig = $this->getObject('geticon', 'htmlelements');
$iconConfig->setIcon('configure');
$iconConfig->title = $this->objLanguage->languageText("mod_podcaster_configureevents", 'podcaster', 'Configure events');
$iconConfig->alt = $this->objLanguage->languageText("mod_podcaster_configureevents", 'podcaster', 'Configure events');

$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
            'module' => 'podcaster',
            'action' => 'configure_events'
        )));

$objLink->link = $iconConfig->show();
$mylinkConfig = $objLink->show();

$header = new htmlheading();
$header->type = 2;

$header->str = $this->objLanguage->languageText('mod_podcaster_eventlist', 'podcaster', 'Event list') . " " . $mylinkConfig;

echo $header->show();

$content = "The system needs to be upgraded with the new group manager for this to work";
if (class_exists('groupops', false)) {
    $content = $this->objEventUtils->getUserEvents();
}
echo $content;
$objLink->link = $this->objLanguage->languageText("mod_podcaster_configureevents", 'podcaster', 'Configure events');
$mylinkAddTxt = $objLink->show();

echo $mylinkAddTxt . " " . $mylinkConfig;
?>