<?php

$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 2;

$content = "The system needs to be upgraded with the new group manager for this to work";
if (class_exists('groupops')) {
    //If not logged in, resolve to public
    if ($userId == Null || empty($natureofevent)) {
        $natureofevent = 'public';
    }
    if ($natureofevent == 'public') {
        $header->str = $this->objLanguage->languageText('mod_podcaster_publicevents', 'podcaster', 'Public events');
        $content = $this->objEventUtils->getOtherEvents($natureofevent);
    } else if ($natureofevent == 'open') {
        $header->str = $this->objLanguage->languageText('mod_podcaster_openevents', 'podcaster', 'Open events');
        $content = $this->objEventUtils->getOtherEvents($natureofevent);
    }
}
echo $header->show();

echo $content;
?>