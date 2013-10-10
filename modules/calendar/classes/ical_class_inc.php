<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Model class for the table tbl_eventscalendar
*
* @author Wesley Nitsckie
* @copyright (c) 2005 University of the Western Cape
* @package eventscalendar
* @version 1
*
*
*/



require_once('icalcreator_class_inc.php');
class ical extends dbTable
{


    /**
     *Constructor
     **/
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $setup = array('UNIQUE'       => 'ical.net'  // site unique id
                        /* Some (MS) calendar definitions */
             ,'CALNAME'      => 'testFile'
             ,'CALDESC'      => 'Calendar test file'
             ,'TIMEZONE'     => 'Harare/Pretoria'
             );
        $this->config = $setup;
        //$this->objDBCalendar =
        $this->objCalendar =& $this->getObject('dbcalendar', 'calendarbase');
        /** initialize calendar */
        $this->calendar  = new vcalendar();
        $this->calendar->setConfig(   'unique_id',     $this->objConfig->getsiteRoot() );
        $this->calendar->setProperty( 'METHOD',        'PUBLISH' );
        $this->calendar->setProperty( 'X-WR-CALNAME',  $this->objConfig->getSiteName() );
        $this->calendar->setProperty( 'X-WR-CALDESC',  $this->objConfig->getSiteName().' Calendar' );
        $this->calendar->setProperty( 'X-WR-TIMEZONE', $this->config['TIMEZONE'] );
    }

    /**
     *Method to create a iCal formatted file that can be used
     *where ever one can use an iCal calendar like Google Caledar
     *@access public
     *@return string
     *@author Wesley Nitsckie
     */
    public function export()
    {
        $objUser =& $this->getObject('user', 'security');
        $userEvents = $this->objCalendar->getEvents('user', $objUser->userId(), (date('Y-m-').'01'), NULL, 10);
        //var_dump($events2);
        $this->addEvents($userEvents);
        $googleCal = "http://www.google.com/calendar/ical/a8irhvtgo7tnc7qfrk5pu3gf14@group.calendar.google.com/public/basic.ics";
        //$this->calendar->setConfig( 'url', $googleCal);
        $this->calendar->returnCalendar();
        //print '<pre>';
        //print $this->calendar->createCalendar();
    }


    /**
     *Method to formulate the calendar event
     *so that that it is ready for the iCal creator to render it
     *@param array $events The events
     *@return boolean
     *@access public
     */
    public function addEvents($events)
    {

        foreach ($events as $event)
        {
            $noTime = "00:00:00";

            $vevent = new vevent(); // create an event calendar component
            if ($event['timefrom'] == $noTime && $event['timeto'] == $noTime)
            {
                $date = str_replace('-', '', $event['eventdate']);
                $vevent->setProperty( 'dtstart', $date, array('VALUE' => 'DATE'));// alt. date format, now for an all-day event
                $vevent->setProperty( 'dtend', $date, array('VALUE' => 'DATE'));
            } else {
                $dateArr = split("-", $event['eventdate']);
                $timefromArr = split(":", $event['timefrom']);
                $timetoArr = split(":", $event['timeto']);
                $vevent->setProperty( 'dtstart', array( 'year'=>$dateArr[0], 'month'=>$dateArr[1], 'day'=>$dateArr[2], 'hour'=>$timefromArr[0], 'min'=>$timefromArr[1],  'sec'=>$timefromArr[2] ));
                $vevent->setProperty( 'dtend',  array( 'year'=>$dateArr[0], 'month'=>$dateArr[1], 'day'=>$dateArr[2], 'hour'=>$timetoArr[0], 'min'=>$timetoArr[1], 'sec'=>$timetoArr[2] ));

            }



            //if ($events['location']){
            //    $vevent->setProperty( 'LOCATION', 'Central Placa' ); // property name - case independent
            //}
            $vevent->setProperty( 'summary', htmlentities($event['eventtitle']) );
            $vevent->setProperty( 'description', htmlentities($event['eventdetails']) );
            //$vevent->setProperty( 'comment', 'This is a comment' );
            //$vevent->setProperty( 'attendee', 'attendee1@icaldomain.net' );
            $this->calendar->setComponent ( $vevent ); // add event to calendar
        }


    }

}
?>