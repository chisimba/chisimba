<?php
/**
* Handles attachments to events.
*/
class block_widecalendar extends object
{
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('word_calendar', 'system', 'Calendar');
        $this->objCalendarInterface = $this->newObject('calendarinterface');
    }
    
    public function show()
    {
        $month = $this->getParam('month', date('m'));
        $year = $this->getParam('year', date('Y'));
        // Set up the calendar, including setting internal properties
        $this->objCalendarInterface->setupCalendar($month, $year);
        $this->objCalendarInterface->calendarSize = 'big';
        
        $eventsCalendar = $this->objCalendarInterface->getCalendar();
        
        $str = $this->objCalendarInterface->getNav()
          . $this->objCalendarInterface->getCalendar();
        
        $this->loadClass('link', 'htmlelements');
        
        $calendarLink = new link ($this->uri(NULL, 'calendar'));
        $calendarLink->link = $this->title ;
        
        $addEvent = new link ($this->uri(array('action'=>'add'), 'calendar'));
        $addEvent->link = $this->objLanguage->languageText('mod_calendarbase_addevent', 'calendarbase', 'Add an Event');

        $eventsList= $this->objCalendarInterface->getEventsList();
        
        $str .= '<div class="undercallinks_wrapper"><p class="undercallinks">'
          . $calendarLink->show().' / '. $addEvent->show() . '</p></div>'
          . $eventsList;
        
        return $str;
    }
}

?>