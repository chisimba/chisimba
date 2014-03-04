<?php

$this->loadClass('htmlheading', 'htmlelements');
//Add Group Link
$iconAdd = $this->getObject('geticon', 'htmlelements');
$iconAdd->setIcon('add');
$iconAdd->title = $this->objLanguage->languageText("mod_podcaster_addevent", 'podcaster', 'Add event');
$iconAdd->alt = $this->objLanguage->languageText("mod_podcaster_addevent", 'podcaster', 'Add event');

$objLink = &$this->getObject('link', 'htmlelements');
$objLink->link($this->uri(array(
            'module' => 'podcaster',
            'action' => 'add_event'
        )));
$objLink->link = $iconAdd->show();
$mylinkAdd = $objLink->show();

$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_podcaster_myevents', 'podcaster', 'My events') . " " . $mylinkAdd;
echo $header->show();

$content = "The system needs to be upgraded with the new group manager for this to work";
if (class_exists('groupops')) {
    $content = $this->objEventUtils->getUserGroups();
}
echo $content;

$homeLink = new link($this->uri(array('module' => 'podcaster', 'action' => 'myevents')));
$homeLink->link = $this->objLanguage->languageText("mod_podcaster_backtoevents", "podcaster", 'Back to events');

echo '<p>' . $homeLink->show() . '</p>';
?>