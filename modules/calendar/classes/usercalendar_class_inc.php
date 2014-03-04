<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
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
class usercalendar extends object
{
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
        $this->getListing = TRUE;
        
    }
    
    /**
    * Method to show a small calendar with list of events. Used for the Personal Space module.
    */
    function show($userSimple = TRUE)
    {
        $objUser =& $this->getObject('user', 'security');
        
        
        $events2 = $this->objCalendar->getEvents('user', $objUser->userId(), (date('Y-m-').'01'), NULL, 10);
       
        $title ='';// '<h1>'.$this->objLanguage->languageText('word_calendar').'</h1>';
        
        $eventsList = $this->objCalendar->generateSmallListing ($events2, 'calendar');
        
        if(!$userSimple)
        {
            $calendar = $this->objCalendar->generateSmallCalendar('user', $objUser->userId());
        } else {
            $calendar = $this->objCalendar->generateCalendar('user', $objUser->userId());
        }
            
        
        $uri = $this->uri(NULL, 'calendar');
        
        $link ='';// '<br /><p><a href="'.$uri.'">'.$this->objLanguage->languageText('word_calendar').'</a></p>';
        
        if($this->getListing){
            return $title.' '.$calendar.'<br />'.$eventsList.$link ;    
        } else {
            return $title.' '.$calendar ;
        }
        
    }

    

} #end of class
?>