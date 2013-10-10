<?php
/**
* Handles attachments to events.
*/
class block_smallcalendar extends object
{
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        
        $this->title = $this->objLanguage->languageText('mod_calendar_sidecal', 'system', 'Side calendar');
        $this->objCalendarInterface = $this->newObject('calendarinterface');
    }
    
    public function show()
    {
        $month = $this->getParam('month', date('m'));
        $year = $this->getParam('year', date('Y'));
        $this->objCalendarInterface->calendarSize = 'small';
        $this->objCalendarInterface->setupCalendar($month, $year);
        $str = $this->objCalendarInterface->getCalendar('small')
          . $this->objCalendarInterface->getSmallEventsList();
        $this->loadClass('link', 'htmlelements');
        $calendarLink = new link ($this->uri(NULL, 'calendar'));
        $calendarLink->link = $this->title ;
        $addEvent = new link ($this->uri(array('action'=>'add'), 'calendar'));
        $addEvent->link = $this->objLanguage->languageText('mod_calendarbase_addevent', 'calendarbase', 'Add an Event');
        $str .= '<p>' . $calendarLink->show() . ' / ' 
           .$addEvent->show() . '</p>';
        return $str;
    }
}

?>