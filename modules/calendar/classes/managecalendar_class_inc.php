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
class managecalendar extends object
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
    }

    /**
    * Method to set the permission to edit/delete context events
    * @param boolean $permission TRUE if user has permission to edit context events - else FALSE
    */
    function setContextPermissions($permission)
    {
        $this->objCalendar->editDeletePermission[1] = $permission;
    }

    function setEventsTag($eventsTag)
    {
        $this->objCalendar->eventsTag = $eventsTag;
    }

    /**
    * Method to get the details of a single event by providing the record Id
    *
    * @param string $id Record Id of the Event
    * @return array record
    */
    function getSingle($id)
    {
        return $this->objCalendar->getSingle($id);
    }

    /**
    * Method to insert a single day event into the database.
    *
    * @param string $date - Date of the Event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $user - The user to whom the event belongs to
    * @param string $multidayevent - a flag to indicate whether this is a multiday event or not.
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @return string $lastInsert - Record Id of the event that has just been added
    */
    function insertSingleUserEvent($date, $eventtitle, $eventdetails, $eventurl, $user, $multidayevent = 0, $multidaystart = NULL, $timeFrom = NULL, $timeTo = NULL)
    {
        $lastInsert = $this->objCalendar->insertSingle(
                $date, // Date of Event
                $multidayevent, // Is this a multiday event
                $multidaystart, // starting record id
                $eventtitle, // Title of Event
                $eventdetails, // Details of the event
                $eventurl, // Url
                0, // userorcontext
                NULL, // Context
                NULL, // workgroup
                0, // show users
                $user, // Use First Entry
                NULL, // User Last Modified
                strftime('%Y-%m-%d %H:%M:%S', mktime()), // date first entry
                strftime('%Y-%m-%d %H:%M:%S', mktime()), // date of last entry
                $timeFrom,
                $timeTo
            );

        return $lastInsert;
    }

	/**
    * Method to insert a single day event for a context into the database.
    *
    * @param string $date - Date of the Event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $contextCode - ContextCode of the Context
    * @param string $user - The user to whom the event belongs to
    * @param string $multidayevent - a flag to indicate whether this is a multiday event or not.
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @return string $lastInsert - Record Id of the event that has just been added
    */
    function insertSingleContextEvent($date, $eventtitle, $eventdetails, $eventurl, $contextCode, $userFirstEntry, $multidayevent = 0, $multidaystart = NULL, $timeFrom = NULL, $timeTo = NULL)
    {
        $lastInsert = $this->objCalendar->insertSingle(
                $date, // Date of Event
                $multidayevent, // Is this a multiday event
                $multidaystart, // starting record id
                $eventtitle, // Title of Event
                $eventdetails, // Details of the event
                $eventurl, // Url
                1, // userorcontext
                $contextCode, // Context
                NULL, // workgroup
                0, // show users
                $userFirstEntry, // Use First Entry
                NULL, // User Last Modified
                strftime('%Y-%m-%d %H:%M:%S', mktime()), // date first entry
                strftime('%Y-%m-%d %H:%M:%S', mktime()), // date of last entry
                $timeFrom,
                $timeTo
            );
        return $lastInsert;
    }
    
    	/**
    * Method to insert a single day event for a context into the database.
    *
    * @param string $date - Date of the Event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $contextCode - ContextCode of the Context
    * @param string $user - The user to whom the event belongs to
    * @param string $multidayevent - a flag to indicate whether this is a multiday event or not.
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @return string $lastInsert - Record Id of the event that has just been added
    */
    function insertSingleGroupEvent($date, $eventtitle, $eventdetails, $eventurl, $contextCode, $userFirstEntry, $multidayevent = 0, $multidaystart = NULL, $timeFrom = NULL, $timeTo = NULL,$groupid)
    {
        $lastInsert = $this->objCalendar->insertSingle(
                $date, // Date of Event
                $multidayevent, // Is this a multiday event
                $multidaystart, // starting record id
                $eventtitle, // Title of Event
                $eventdetails, // Details of the event
                $eventurl, // Url
                1, // userorcontext
                $contextCode, // Context
                $groupid, // workgroup
                0, // show users
                $userFirstEntry, // Use First Entry
                NULL, // User Last Modified
                strftime('%Y-%m-%d %H:%M:%S', mktime()), // date first entry
                strftime('%Y-%m-%d %H:%M:%S', mktime()), // date of last entry
                $timeFrom,
                $timeTo
            );
        return $lastInsert;
    }

    /**
    * Method to insert a multi day event into the database.
    *
    * @param string $date - Date of the Event
    * @param string $date2 - Date when the event ends
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $user - The user to whom the event belongs to
    * @param string $userLastModified - User Id of the person who last updated the entry
    * @param string $dateFirstEntry - Date the first entry was made
    * @param string $dateLastModified - Date the entry was last updated.
    * @param string $eventStartId - Record ID of the Start (First Day) of the multiday event. If none is provided, the function will generate one.
    */
    function insertMultiDayUserEvent ($date, $date2, $eventtitle, $eventdetails, $eventurl, $user, $userLastModified = NULL, $dateFirstEntry = NULL, $dateLastModified = NULL, $eventStartId=NULL, $timeFrom = NULL, $timeTo = NULL)
    {
        if ($dateFirstEntry == NULL) {
            $dateFirstEntry = strftime('%Y-%m-%d %H:%M:%S', mktime());
        }

        if ($dateLastModified == NULL) {
            $dateLastModified = strftime('%Y-%m-%d %H:%M:%S', mktime());
        }

        $lastInsert = $this->objCalendar->insertMultiDayEvent(
                $date, // Date of Event
                $date2, // End Date
                $eventtitle, // Title of Event
                $eventdetails, // Details of the event
                $eventurl, // Url
                0, // userorcontext
                NULL, // Context
                NULL, // workgroup
                0, // show users
                $user, // Use First Entry
                $userLastModified, // User Last Modified
                $dateFirstEntry, // date first entry
                $dateLastModified, // date of last entry
                $eventStartId,
                $timeFrom,
                $timeTo
            );

        return $lastInsert;
    }/**
    * Method to insert a multi day event into the database.
    *
    * @param string $date - Date of the Event
    * @param string $date2 - Date when the event ends
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $contextCode - ContextCode of the Context
    * @param string $user - The user to whom the event belongs to
    * @param string $userLastModified - User Id of the person who last updated the entry
    * @param string $dateFirstEntry - Date the first entry was made
    * @param string $dateLastModified - Date the entry was last updated.
    * @param string $eventStartId - Record ID of the Start (First Day) of the multiday event. If none is provided, the function will generate one.
    */
    function insertMultiDayContextEvent ($date, $date2, $eventtitle, $eventdetails, $eventurl, $contextCode, $userFirstEntry, $userLastModified = NULL, $dateFirstEntry = NULL, $dateLastModified = NULL, $eventStartId=NULL, $timeFrom = NULL, $timeTo = NULL)
    {

        if ($dateFirstEntry == NULL) {
            $dateFirstEntry = strftime('%Y-%m-%d %H:%M:%S', mktime());
        }

        if ($dateLastModified == NULL) {
            $dateLastModified = strftime('%Y-%m-%d %H:%M:%S', mktime());
        }

        $lastInsert = $this->objCalendar->insertMultiDayEvent(
                $date, // Date of Event
                $date2, // End Date
                $eventtitle, // Title of Event
                $eventdetails, // Details of the event
                $eventurl, // Url
                1, // userorcontext
                $contextCode, // Context
                NULL, // workgroup
                0, // show users
                $userFirstEntry, // Use First Entry
                $userLastModified, // User Last Modified
                $dateFirstEntry, // date first entry
                $dateLastModified, // date of last entry
                $eventStartId,
                $timeFrom,
                $timeTo
            );

        return $lastInsert;
    }

    
    function insertMultiDayGroupEvent ($date, $date2, $eventtitle, $eventdetails, $eventurl, $contextCode, $userFirstEntry, $userLastModified = NULL, $dateFirstEntry = NULL, $dateLastModified = NULL, $eventStartId=NULL,$groupid, $timeFrom = NULL, $timeTo = NULL)
    {

        if ($dateFirstEntry == NULL) {
            $dateFirstEntry = strftime('%Y-%m-%d %H:%M:%S', mktime());
        }

        if ($dateLastModified == NULL) {
            $dateLastModified = strftime('%Y-%m-%d %H:%M:%S', mktime());
        }

        $lastInsert = $this->objCalendar->insertMultiDayEvent(
                $date, // Date of Event
                $date2, // End Date
                $eventtitle, // Title of Event
                $eventdetails, // Details of the event
                $eventurl, // Url
                1, // userorcontext
                $contextCode, // Context
               $groupid, // workgroup
                0, // show users
                $userFirstEntry, // Use First Entry
                $userLastModified, // User Last Modified
                $dateFirstEntry, // date first entry
                $dateLastModified, // date of last entry
                $eventStartId,
                $timeFrom,
                $timeTo
            );

        return $lastInsert;
    }
    /**
    * Method to update an event
    *
    * @param string $id - Record If of the event
    * @param string $multidayevent - A flag to indicate whether this is a multiday event or not
    * @param string $date - Date of the Event
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $user - The user to whom the event belongs to
    * @param string $multiday_startid - Record ID of the Start (First Day) of the multiday event. If none is provided, the function will generate one.
    */
    function updateUserEvent ($id, $multidayevent, $date, $eventtitle, $eventdetails, $eventurl, $user, $multiday_startid = NULL)
    {
        if ($multidayevent == 0) {
            $multiday_startid = NULL;
        }

        $this->objCalendar->updateSingle(
                $id,
                $multidayevent, // update
                $date,
                $multiday_startid, // update
                $eventtitle,
                $eventdetails,
                $eventurl,
                0, // userorcontext
                NULL, // Context
                NULL, // workgroup
                0, // show users
                $user,
                strftime('%Y-%m-%d %H:%M:%S', mktime()) // date LAST entry
            );

        return;
    }

	/**
    * Method to update an event
    *
    * @param string $id - Record If of the event
    * @param string $multidayevent - A flag to indicate whether this is a multiday event or not
    * @param string $date - Date of the Event
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $contextCode - ContextCode of the Context
    * @param string $user - The user to whom the event belongs to
    * @param string $multiday_startid - Record ID of the Start (First Day) of the multiday event. If none is provided, the function will generate one.
    */
    function updateContextEvent ($id, $multidayevent, $date, $eventtitle, $eventdetails, $eventurl, $contextCode, $user, $multiday_startid = NULL)
    {
        if ($multidayevent == 0) {
            $multiday_startid = NULL;
        }

        $this->objCalendar->updateSingle(
                $id,
                $multidayevent, // update
                $date,
                $multiday_startid, // update
                $eventtitle,
                $eventdetails,
                $eventurl,
                1, // userorcontext
                $contextCode, // Context
                NULL, // workgroup
                0, // show users
                $user,
                strftime('%Y-%m-%d %H:%M:%S', mktime()) // date LAST entry
            );

        return;
    }

    /**
    * Method to delete a single event
    *
    * @param string $id Record Id of the Event
    * @param string $userId User Id of the person to whom the event belongs
    */
    function deleteSingle($id, $userId)
    {
        $event = $this->objCalendar->getSingle($id);

        if ($event['userFirstEntry'] != $userId || $event['userorcontext'] != 0) {
            return;
        } else {
            return $this->objCalendar->deleteSingle($id);
        }
    }

    /**
    * Method to delete a multi day event event
    *
    * @param string $multiEventId Record Id of the start of the multiday event
    */
    function deleteBatch($multiEventId)
    {
        $this->objCalendar->deleteBatch($multiEventId);
        return;
    }

    /**
    * Method to delete a multi day event (children)
    *
    * @param string $multiEventId Record Id of the start of the multiday event
    */
    function deleteMultiEventsChild($multiEventId)
    {
        $this->objCalendar->deleteMultiEventsChild($multiEventId);
        return;
    }


    /**
    * Method to get the start of a multi-day event
    *
    * @param string $start_id Record Id of the start date
    */
    function getStartMultiDayEvent($start_id)
    {
        return $this->objCalendar->getStartMultiDayEvent($start_id);
    }

    /**
    * Method to get the end of a multi-day event
    *
    * @param string $start_id Record Id of the start date
    */
    function getLastMultiDayEvent($start_id)
    {
        return $this->objCalendar->getLastMultiDayEvent($start_id);
    }

	/**
	* Method to generate a calendar
	* @param string $userId Record Id of the User if it needs to include User Events
	* @param string $context Record Id of the Context if it needs to include Context Events
	* @param int $month Calendar Month
	* @param int $year Calendar Year
	* @return string Generated Calendar
	*/
	function getEventsCalendar($user=NULL, $context=NULL, $month, $year)
	{
		$sql = $this->objCalendar->getCalendarSQL($user, $context, $month, $year);
		return $this->objCalendar->generateCalendar('user', $user, $month, $year, $sql);
	}

    /**
    * Method to show a small calendar with list of events. Used for the Personal Space module.
    */
    function show()
    {
        $objUser =& $this->getObject('user', 'security');


        $events2 = $this->objCalendar->getEvents('user', $objUser->userId(), (date('Y-m-').'01'), NULL, 10);

        $title = '<h1>'.$this->objLanguage->languageText('word_calendar').'</h1>';

        $eventsList = $this->objCalendar->generateSmallListing ($events2, 'calendar');

        $calendar = $this->objCalendar->generateSmallCalendar('user', $objUser->userId());

        $uri = $this->uri(NULL, 'calendar');

        $link = '<br /><p><a href="'.$uri.'">'.$this->objLanguage->languageText('word_calendar').'</a></p>';

        return $title.' '.$calendar.'<br /><br />'.$eventsList.$link ;
    }



} #end of class
?>
