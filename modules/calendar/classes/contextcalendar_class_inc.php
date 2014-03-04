<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
 * User Calendar Functionality Class
 *
 * This class is sort of an extension to dbcalendar in the calendarbase module.
 *
 * It uses a modified version of methods as to that in dbcalendar, but
 * because of the structure of KEWL.Nextgen, can't extended the class
 *
 * Its methods are coded for user events.
 */
class contextcalendar extends object {
    var $eventsList; // an array to store current events


    /**
     * Constructor method to define the table
     */
    function init() {
        $this->objCalendar =& $this->getObject('dbcalendar', 'calendarbase');
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->eventsList = NULL;

        // Set the calling module
        $this->objCalendar->module = 'calendar';

        $this->contextObject =& $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();

        // If not in context, set code to be 'root' called 'Lobby'
        $this->contextTitle = $this->contextObject->getTitle();
        if ($this->contextCode == '') {
            $this->contextCode = 'root';
            $this->contextTitle = 'Lobby';
        }
    }


    /**
     * Method to save an event.
     */
    function addEvent(
            $date,
            $date2,
            $eventtitle,
            $eventdetails,
            $eventsurl,
            $multidayevent,
            $timeFrom,
            $timeTo) {

        $eventsList = 'all';
        $objCalendar = $this->getObject('managecalendar');
        $objCalendar->insertSingleContextEvent($date, $eventtitle, $eventdetails, $eventurl, $this->contextCode, $this->userId, 0, NULL, $timeFrom, $timeTo);
       
    }

    /**
     * Method to show a small calendar with list of events. Used for the Context module.
     */
    function show() {
        $objUser =& $this->getObject('user', 'security');


        $events2 = $this->objCalendar->getEvents('context', $this->contextCode, (date('Y-m-d')), NULL, 5);

        $title = '<h1>'.$this->objLanguage->languageText('word_calendar').'</h1>';

        $eventsList = $this->objCalendar->generateSmallListing ($events2, 'calendar');

        $calendar = $this->objCalendar->generateSmallCalendar('context', $this->contextCode);

        $uri = $this->uri(NULL, 'calendar');

        $link = '<br /><p><a href="'.$uri.'">'.$this->objLanguage->languageText('word_calendar').'</a></p>';

        return $title.' '.$calendar.'<br /><br />'.$eventsList.$link ;
    }



} #end of class
?>