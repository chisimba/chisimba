<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Model class for the table tbl_calendar
*
* @author Tohir Solomons
* @copyright (c) 2005 University of the Western Cape
* @package calendarbase
* @version 1
*
* This class is a base for the user, context and workgroup calendar.
* They have their own classses to reference this functionality, but the
*  methods in this class support the above categories.
*
* The categories are differentiated by the table column 'userorcontext'
*    0 - for the user calendar
*    1 - for the context calendar
*    2 - for the workgroup calendar
*
* The advantage of this approach is that events can be shared, instead of
* getting them from various tables.
*
* A note on multi-day events:
*/
class dbcalendar extends dbTable
{
    /**
    * @var boolean $showEditDelete A flag to indicate whether to show the edit delete buttons for a context calendar
    */
    public $showEditDelete = TRUE;

    /**
    * @var array $editDeletePermission - An array with list of permissions for editing/deleting events
    * The items in the array correspond with the userorcontext column.
    * $editDeletePermission[1] being TRUE allows the user to edit a context calendar entry
    */
    public $editDeletePermission = array (0=>TRUE, 1=>TRUE, 2=>TRUE, 4=>TRUE);

    /**
    * @var string $module The calling module - This is needed for the module part in generating URIs. This class is in calendar base, and was causing links to this module.
    */
    public $module = 'calendar';

    /**
    * @var string $editmodule The module to go to when adding / editing events. If Null, it uses $module. This check is done internally
    */
    public $editmodule = NULL;

    /**
    * @var string $eventsTag This is a string value for additions to next/previous month navigation. Parameter name is called 'events'
    * An example would be when you need to generate: index.php?module=calendar&events={something}
    */
    public $eventsTag = NULL;

    /**
    * Constructor method to define the table
    */
    public function init() {
        parent::init('tbl_calendar');
        $this->loadClass('link', 'htmlelements');
        $this->objDateFunctions = $this->getObject('dateandtime','utilities');
        $this->objCalendar = $this->getObject('calendargenerator');
        $this->objSimpleCal = $this->getObject('dateandtime','utilities');
        // Load Language Class
        $this->objLanguage = $this->getObject('language', 'language');

        //$this->objEventAttachments =& $this->getObject('dbeventattachments');
        $this->objEventAttachments =& $this->getObject('attachments','calendar');
        $this->objFileIcons = $this->getObject('fileicons', 'files');
    }

    /**
    * Method to insert a single day event into the database. This method is also used by the insertMultiDayEvent() function
    *
    * @param string $date - Date of the Event
    * @param string $multidayevent - Flag to indicate whether this is a multiday event, 0 = No, 1 = Yes
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $userorcontext - Flag to indicate whether the event is for user / context / workgroup
    * @param string $context - Context Code, if context calendar
    * @param string $workgroup - Workgroup code, if using workgroup
    * @param string $showusers - Flag whether context/workgroup event should appear on user's personal calendar
    * @param string $userFirstEntry - User Id of the person making the first entry - also the User Id for the user calendar
    * @param string $userLastModified - User Id of the person who last updated the entry
    * @param string $dateFirstEntry - Date the first entry was made
    * @param string $dateLastModified - Date the entry was last updated.
    */
    public function insertSingle($date, $multidayevent=0, $multidaystart=NULL, $eventtitle, $eventdetails, $eventurl, $userorcontext, $context, $workgroup, $showusers, $userFirstEntry, $userLastModified, $dateFirstEntry, $dateLastModified, $timeFrom = NULL, $timeTo = NULL)
    {

        // This is a check to ensure that the url entered is not the default 'http://'
        // If it is, discard the 'no link whatsoever'
        $eventUrlTemp = trim($eventurl);
        if ($eventUrlTemp == 'http://') {
            $eventurl = '';
        }

        $this->insert(array(
                'eventdate'                   => $date,
                'multiday_event'              => $multidayevent,
                'multiday_event_start_id'     => $multidaystart,
                'eventtitle'                  => $eventtitle,
                'eventdetails'                => $eventdetails,
                'eventurl'                    => $eventurl,
                'userorcontext'               => $userorcontext,
                'context'                     => $context,
                'workgroup'                   => $workgroup,
                'showusers'                   => $showusers,
                'userFirstEntry'              => $userFirstEntry,
                'userLastModified'            => $userLastModified,
                'dateFirstEntry'              => $dateFirstEntry,
                'dateLastModified'            => $dateLastModified,
                'timefrom'                    => $timeFrom,
                'timeto'                      => $timeTo
            ));

        return $this->getLastInsertId();
    }

    /**
    * Method to insert a multi day event into the database. This method is does some calculations, and then uses the insertSingle() function.
    *
    * @param string $date - Date of the Event
    * @param string $date2 - Date when the event ends
    * @param string $multidaystart - Record ID of the Start (First Day) of the multiday event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $userorcontext - Flag to indicate whether the event is for user / context / workgroup
    * @param string $context - Context Code, if context calendar
    * @param string $workgroup - Workgroup code, if using workgroup
    * @param string $showusers - Flag whether context/workgroup event should appear on user's personal calendar
    * @param string $userFirstEntry - User Id of the person making the first entry - also the User Id for the user calendar
    * @param string $userLastModified - User Id of the person who last updated the entry
    * @param string $dateFirstEntry - Date the first entry was made
    * @param string $dateLastModified - Date the entry was last updated.
    * @param string $eventStartId - Record ID of the Start (First Day) of the multiday event. If none is provided, the function will generate one.
    * Needed for when updating an event
    */
    public function insertMultiDayEvent ($date, $date2, $eventtitle, $eventdetails, $eventurl, $userorcontext, $context, $workgroup, $showusers, $userFirstEntry, $userLastModified, $dateFirstEntry, $dateLastModified, $eventStartId=NULL, $timeFrom = NULL, $timeTo = NULL)
    {
        // Switch Dates if the former is greater than the latter
            $this->objDateFunctions->smallDateBigDate($date, $date2);

            // Get the difference between the days
            $difference =$this->objDateFunctions->dateDifference($date, $date2);
            // get the number of days difference
            $dayDifference = $difference['d'];

            // if the day difference is 0, treat this as a single day event
            if ($dayDifference == 0) {
                $event = $this->insertSingle($date, 0, NULL, $eventtitle, $eventdetails, $eventurl, $userorcontext, $context, $workgroup, $showusers, $userFirstEntry, $userLastModified, $dateFirstEntry, $dateLastModified, $timeFrom, $timeTo);

                return $event;
            } else {
                // Multiday Event Processing

                $dateLastModified = strftime('%Y-%m-%d %H:%M:%S', mktime());

                $this->beginTransaction();

                if ($eventStartId == NULL) {
                    // Insert record to get record id of first day of event
                    $eventStartId = $this->insertSingle($date, 1, NULL, $eventtitle, $eventdetails, $eventurl, $userorcontext, $context, $workgroup, $showusers, $userFirstEntry, $userLastModified, $dateFirstEntry, $dateLastModified, $timeFrom, $timeTo);
                }

                // Create New Variable for Next Day
                $nextDay = $date;

                // Loop by the number of days difference
                for ($i = 0; $i < $dayDifference; $i++)
                {
                    // Calculate the next day
                    $nextDay = $this->objDateFunctions->nextDay($nextDay);
                    // Insert into database
                    $this->insertSingle($nextDay, 1, $eventStartId, $eventtitle, $eventdetails, $eventurl, $userorcontext, $context, $workgroup, $showusers, $userFirstEntry, $userLastModified, $dateFirstEntry, $dateLastModified, $timeFrom, $timeTo);
                }

                $this->commitTransaction();

                return $eventStartId;
            }
    }

    /**
    * Method to updated an event
    *
    * @param string $id - Record Id of the Event
    * @param string $multidayevent - Flag to indicate whether this is a multiday event, 0 = No, 1 = Yes
    * @param string $date - Date of the Event
    * @param string $multiday_startid - Record ID of the Start (First Day) of the multiday event
    * @param string $eventtitle - Title of the Event
    * @param string $eventdetails - Details of the event
    * @param string $eventurl - A URL for more information
    * @param string $userorcontext - Flag to indicate whether the event is for user / context / workgroup
    * @param string $context - Context Code, if context calendar
    * @param string $workgroup - Workgroup code, if using workgroup
    * @param string $showusers - Flag whether context/workgroup event should appear on user's personal calendar
    * @param string $userLastModified - User Id of the person who last updated the entry
    * @param string $dateLastModified - Date the entry was last updated.
    */
    public function updateSingle($id, $multidayevent, $date, $multiday_startid, $eventtitle, $eventdetails, $eventurl, $userorcontext, $context, $workgroup, $showusers, $userLastModified, $dateLastModified)
    {
        // This is a check to ensure that the url entered is not the default 'http://'
        // If it is, discard the 'no link whatsoever'
        $eventUrlTemp = trim($eventurl);
        if ($eventUrlTemp == 'http://') {
            $eventurl = '';
        }

        $this->update('id', $id, array(
                'eventdate'            => $date,
                'eventtitle'             => $eventtitle,
                'multiday_event'    => $multidayevent,
                'multiday_event_start_id' => $multiday_startid,
                'eventdetails'         => $eventdetails,
                'eventurl'                => $eventurl,
                'userorcontext'       => $userorcontext,
                'context'                 => $context,
                'workgroup'            => $workgroup,
                'showusers'            => $showusers,
                'userLastModified'  => $userLastModified,
                'dateLastModified' => $dateLastModified
            ));

        return;
    }

    /**
    * Method to get the details of a single event by providing the record Id
    *
    * @param string $id Record Id of the Event
    * @return array Associative Array with the record
    */
    public function getSingle($id)
    {
        return $this->getRow('id', $id);
    }

    /**
    * Method to delete a single event by providing the record Id
    *
    * @param string $id Record Id of the Event
    * @return TRUE |FALSE TRUE on success, FALSE on failure
    */
    public function deleteSingle($id)
    {
        return $this->delete('id', $id);
    }

    /**
    * Method to delete a multiday event by providing the event start Id
    *
    * @param string $multiEventId Record Id of the event start Id
    */
    public function deleteBatch($multiEventId)
    {
        $this->delete('id', $multiEventId);
        $this->delete('multiday_event_start_id', $multiEventId);
        return;
    }

    /**
    * Method to delete the child events by providing the event start Id
    *
    * This method is used when deleting existing events at an update.
    *
    * @param string $multiEventId Record Id of the event start Id
    */
    public function deleteMultiEventsChild($multiEventId)
    {
        $this->delete('multiday_event_start_id', $multiEventId);
        return;
    }

    /**
    * Method to get the first day in a multiday event
    *
    * @param string $start_id Record Id of the event start Id
    * @return date Date of the first event
    */
    public function getStartMultiDayEvent($start_id)
    {
        $record = $this->getRow('id', $start_id);
        return $record['eventdate'];
    }

    /**
    * Method to get the last day in a multiday event
    *
    * @param string $start_id Record Id of the event start Id
    * @return date Date of the Last Event event
    */
    public function getLastMultiDayEvent($start_id)
    {
        $sql = 'SELECT eventdate FROM `tbl_calendar`WHERE `multiday_event_start_id` = "'.$start_id.'" ORDER BY `eventdate` DESC LIMIT 1';

        $events = $this->getArray($sql);

        return $events[0]['eventdate'];
    }

    /**
    * Method to get a list of events for user/context between a certain period
    *
    * @param string $origin Who the calendar is for: user, context or workgroup
    * @param string $id Either user id, context code, or workgroup code, depending on $origin
    * @param string $startDate Date events should start from
    * @param string $endDate Date events should end by
    * @param string $limit The number of events to retrieve
    * @return array List of Events
    */
    public function getEvents($origin, $id, $startDate = NULL, $endDate = NULL, $limit=NULL)
    {
        $sql = 'SELECT tbl_calendar.*, tbl_calendar_event_attachment.id as attachment_id, multiday_attachment_table.id AS multiday_attachment_id
        FROM tbl_calendar
        LEFT JOIN tbl_calendar_event_attachment ON (tbl_calendar_event_attachment.event_id = tbl_calendar.id)
        LEFT JOIN tbl_calendar_event_attachment AS multiday_attachment_table ON (multiday_attachment_table.event_id = tbl_calendar.multiday_event_start_id)
        ';

        // Commence the sql filter based on origin.
        switch ($origin)
        {
            case 'user': $where = ' WHERE userorcontext="0" AND userFirstEntry="'.$id.'" '; break;
            case 'context': $where = ' WHERE userorcontext="1" AND context="'.$id.'" '; break;
            default:
            // Use the origin as filter in default value.
                $where = " WHERE $origin ";
        }


        // Add a start date filter
        if (isset($startDate)) {
            // set start date to previous day - else it will be excluded by the greater than sign
            $startDate = $this->objDateFunctions->previousDay($startDate);
            $where .= ' AND eventdate > "'.$startDate.'"';
        }

        // Add an end date filter
        if (isset($endDate)) {
            // set next date to previous day - else it will be excluded by the less than sign
            $endDate = $this->objDateFunctions->nextDay($endDate);
            $where .= ' AND eventdate < "'.$endDate.'"';
        }

        $where .= ' GROUP BY tbl_calendar.id';

        // Set the Order of return
        $where .= ' ORDER BY eventdate, timefrom';

        // Add a limit filter
        if (isset($limit)) {
            $where .= ' LIMIT '. $limit;
        }
        // Get the events
        return $this->getArray($sql . $where);
    }



    /**
    * Method to generate a calendar for events.
    *
    * @param string $origin Who the calendar is for: user, context or workgroup
    * @param string $controlId Either user id, context code, or workgroup code, depending on $origin
    * @param string $month Month the calendar should show
    * @param string $year Year the calendar should show
    * @param string $size Size of the Calendar
    * @return string A Calendar in table format
    */
    public function generateCalendar($origin, $controlId, $month=NULL, $year=NULL, $filter = NULL, $size='big')
    {
        if (!isset($month)) {
            $month = date('m');
        }

        if (!isset($year)) {
            $year = date('Y');
        }

        if(is_null($filter)){
            $filter = $origin;
        }



        $startDate = $year.'-'.$month.'-01';
        $endDate = $this->objDateFunctions->lastDateMonth($month, $year);

        $events = $this->getEvents($filter, $controlId, $startDate, $endDate);

        $preparedArray = $this->prepareEventsForCalendar ($events);

        $this->objCalendar->year = $year;
        $this->objCalendar->month = $month;
        $this->objCalendar->events = $preparedArray;

        $this->objCalendar->size = $size;

        $calendar = $this->objCalendar->show ();

        if ($size == 'big') {
            $eventsList = $this->generateEventsList ($events);
        } else {
            $events2 = $this->getEvents($filter, $controlId, NULL, NULL, 10);
            $eventsList = $this->generateSmallListing ($events2);
        }

        $navigation = $this->generateCalendarNavigation ($month, $year);

        return $navigation.$calendar.'<br /><br />'.$eventsList;
    }




    /**
    * Method to take a list of events and prepare them in a format for adding to the calendar class.
    * Amongst others, it removed duplication in events, etc.
    *
    * @param array $events List of Events
    * @return array $preparedArray List of ready to be sent to the calendar class
    */
    public function prepareEventsForCalendar (&$events)
    {
        $preparedArray = array();

        $objTrim =& $this->getObject('trimstr', 'strings');

        foreach ($events as $event)
        {
            $day = $this->objDateFunctions->dayofMonth($event['eventdate']);

            switch ($event['userorcontext'])
            {
                case '0': $image = 'event_user'; break;
                case '1': $image = 'event_context'; break;
                default: $image = NULL; break;
            }

            if ($event['userorcontext'] == 1 && $event['context'] == 'root') {
                $image = 'event_site';
            }

            if (array_key_exists($day, $preparedArray)) {
                $temp = rtrim ($preparedArray[$day], '</ul>');
                $preparedArray[$day] = $temp.'<li class="'.$image.'" title="'.stripslashes($event['eventtitle']).'">'.$objTrim->strTrim(stripslashes($event['eventtitle']), 8).'</li></ul>';
            } else {
                $preparedArray[$day] = '<ul><li class="'.$image.'">'.stripslashes($event['eventtitle']).'</li></ul>';
            }//&#8226;
        }

/*
        $calendarCSS = '<STYLE>
.event_user {
    list-style-image: url(modules/calendarbase/resources/event_user.gif);
    list-style-position: inside;
}
.event_context {
    list-style-image: url(modules/calendarbase/resources/event_context.gif);
    list-style-position: inside;
}
.event_workgroup {
    list-style-image: url(modules/calendarbase/resources/event_user.gif);
    list-style-position: inside;
}
.event_site {
    list-style-image: url(modules/calendarbase/resources/event_site.gif);
    list-style-position: inside;
}
</STYLE>';

        $this->appendArrayVar('headerParams', $calendarCSS);*/

        return $preparedArray;
    }

    /**
    * Method to create a small calendar. This is for display with in Personal Space, Context Home,
    * It determines events for the current month.
    *
    * Can be refactored to use generateCalendar() function - may deprecate.
    *
    * @param string $origin Who the calendar is for: user, context or workgroup
    * @param string $id Either user id, context code, or workgroup code, depending on $origin
    * @return string A Calendar in table format
    */
    public function generateSmallCalendar($origin, $id)
    {
        $month = date('m');
        $year = date('Y');

        // Set start date to first of the month
        $startDate = $year.'-'.$month.'-01';

        // Get all events for the month
        $events = $this->getEvents($origin, $id, $startDate);

        $preparedArray = $this->prepareEventsForCalendar ($events);

        $this->objCalendar->year = $year;
        $this->objCalendar->month = $month;
        $this->objCalendar->events = $preparedArray;

        $this->objCalendar->size = 'small';

        $calendar = $this->objCalendar->show ();

        return $calendar;
    }


    /**
    *  This method takes the event, and presents them into a two column table
    *
    * @param array $events List of Events
    * @return string A Calendar in table format
    */
    public function generateEventsList ($events)
    {
        $eventsTable=$this->newObject('htmltable','htmlelements');
        $eventsTable->cssClass='calendar';
        $eventsTable->width='99%';
        $eventsTable->cellspacing='0';
        $eventsTable->cellpadding='5';

        $eventsTable->startHeaderRow();
        $eventsTable->addHeaderCell($this->objLanguage->languageText('word_date'), '100');
        $eventsTable->addHeaderCell($this->objLanguage->languageText('mod_calendarbase_eventdetails','calendarbase'));
        $eventsTable->endHeaderRow();

        // Find the module to go to for editing events
        if ($this->editmodule != NULL) {
            $internaleditmodule = $this->editmodule;
        } else {
            $internaleditmodule = $this->module;
        }


        $calendarCSS = '<STYLE>
.cal_user_bkg {
    background-image: url(modules/calendarbase/resources/user.gif);
    background-repeat: no-repeat;
    background-position: top left;
    padding-left: 35px;
}
.cal_context_bkg {
    background-image: url(modules/calendarbase/resources/context.gif);
    background-repeat: no-repeat;
    background-position: top left;
    padding-left: 35px;
}
.cal_workgroup_bkg{
    background-image: url(modules/calendarbase/resources/user.gif);
    background-repeat: no-repeat;
    background-position: top left;
    padding-left: 35px;
}
.cal_site_bkg {
    background-image: url(modules/calendarbase/resources/site.gif);
    background-repeat: no-repeat;
    background-position: top left;
    padding-left: 35px;
}
.cal_other {
}
</STYLE>';

        $this->appendArrayVar('headerParams', $calendarCSS);



        // Array of Total Number of Events
        $arrayTotal = count($events);


        if ($arrayTotal > 0) {
            // Set Current Day (in loop) to be the first day in the array
            $currentDay = $this->objDateFunctions->dayofMonth($events[0]['eventdate']);

            // Set Existing Content to be empty
            $currentContent = '';
            // Counter
            $counter = 0;
            //Inital Style
            $tdrowclass = '';

            $icon = $this->getObject('geticon', 'htmlelements');

            foreach ($events as $event)
            {
                $counter++;

                switch ($event['userorcontext'])
                {
                    case '0': $tdclass = 'cal_user_bkg'; break;
                    case '1': $tdclass = 'cal_context_bkg'; break;
                    default: $tdclass = 'cal_other'; break;
                }

                $prevRowClass = $tdrowclass;

                if ($event['eventdate'] == date('Y-m-d')) {
                    $tdrowclass = 'todaycal event_context';
                } else {
                    $tdrowclass = 'even event_context';
                }

                if ($event['userorcontext'] == 1 && $event['context'] == 'root') {
                    $tdclass = 'cal_site_bkg';
                }


                // Get Day Part out of date
                $day = $this->objDateFunctions->dayofMonth($event['eventdate']);

                if ($event['multiday_event'] == 0) {
                    $eventId = $event['id'];
                } else if ($event['multiday_event_start_id'] == NULL) {
                    $eventId = $event['id'];
                } else {
                    $eventId = $event['multiday_event_start_id'];
                }

                $url= $this->uri(array('action'=>'edit', 'id'=>$eventId), $internaleditmodule);
                $editDeleteIcons = $icon->getEditIcon($url);

                $array = array('action'=>'delete', 'id'=>$eventId);

                $editDeleteIcons .= ' '.$icon->getDeleteIconWithConfirm($eventId, $array, $internaleditmodule, $this->objLanguage->languageText('mod_calendarbase_eventdeleterequestconfirm','calendar'));


                if ($this->editDeletePermission[$event['userorcontext']]) {
                    $editDeleteIcons = '<div style="float:right">'.$editDeleteIcons.'</div>';
                } else {
                    $editDeleteIcons = '';
                }

                // Prepare Events Details
                $cellContent = '<div class="'.$tdclass.'">'.$editDeleteIcons .'<strong>'.stripslashes($event['eventtitle']).'</strong><p>'.stripslashes($event['eventdetails']).'</p>';

                if ($event['eventurl'] != '') {
                    $cellContent .= '<p>'.$this->objLanguage->languageText('mod_calendarbase_relatedwebsite','calendar').': <a href="'.$event['eventurl'].'" target="calendarpop">'.$event['eventurl'].'</a></p>';
                }

                // Attachments
                if ($event['attachment_id'] != '' || $event['multiday_attachment_id'] != '') {
                    //$files = $this->objEventAttachments->getListAttachments($eventId);
                    $files = $this->objEventAttachments->listFiles($eventId);
                    $nextLine = '';
                    if (count($files) > 0) {
                        //$cellContent .= '<hr width="50%" align="left" size="1" />';
                        $cellContent .= '<br /><p><em>'.$this->objLanguage->languageText('word_attachments','calendar').':</em></p>';
                        $cellContent .= '<div style="padding-left: 20px;">';

                        foreach ($files as $file)
                        {
                            //$downloadLink = new link ($this->uri(array('action'=>'downloadattachment', 'id'=>$file['attachment_id'], 'event'=>$event['id']), $this->module));
                            //$downloadLink->link = $file['filename'];
                            //$cellContent .= $nextLine.$this->objFileIcons->getFileIcon($file['filename']).' '.$downloadLink->show();
                            $downloadLink = new link ($file['path']);
                            $downloadLink->link = $file['filename'];
                            $cellContent .= $nextLine.$this->objFileIcons->getFileIcon($file['filename']).' '.$downloadLink->show();
                            $nextLine =  '<br />';
                        }
                        $cellContent .= '</div>';
                    }
                }

                $cellContent .= '</div>';

                // If the Day Part != Current Day, and current day as a new table row.
                // Else append data to to Current Day
                if ($day != $currentDay) {
                    // Start New Row
                    $eventsTable->startRow();
                    // Big Number on the Left Hand Side
                    $dateCell = '<a name="'.$currentDay.'"></a><div class="bigDayNum">'.$currentDay.'</div><br />';
                    $month = $this->objDateFunctions->getMonthNumber($event['eventdate']);
                    $month = $this->objSimpleCal->monthFull($month);
                    $year = $this->objDateFunctions->getYearNumber($event['eventdate']);
                    $dateCell .= $month.' '.$year;

                    //echo $tdrowclass;
                    $eventsTable->addCell($dateCell, 100, 'top', NULL, $prevRowClass);

                    // Add Content for right side
                    $eventsTable->addCell($currentContent, NULL, NULL, NULL, $prevRowClass);

                    $eventsTable->endRow();

                    // Now that Row has been added, set the content part to be NEW current Content
                    $currentContent = $cellContent;
                } else {
                    if ($counter != 1) {
                    $currentContent .= '<hr>';
                    }
                    $currentContent .= $cellContent;
                }

                // Set Day Part to be current day
                $currentDay = $day;

                // This if statement is for the last record in the array
                // displays it because this is the end of the loop
                if ($counter == $arrayTotal){
                    // Start New Row
                    $eventsTable->startRow();
                    // Big Number on the Left Hand Side
                    $dateCell = '<a name="'.$currentDay.'"></a><div class="bigDayNum">'.$currentDay.'</div><br />';
                    $month = $this->objDateFunctions->getMonthNumber($event['eventdate']);
                    $month = $this->objSimpleCal->monthFull($month);
                    $year = $this->objDateFunctions->getYearNumber($event['eventdate']);
                    $dateCell .= $month.' '.$year;

                    $eventsTable->addCell($dateCell, 100, 'top', NULL, $tdrowclass);

                    // Add Content for right side
                    $eventsTable->addCell($currentContent, NULL, NULL, NULL, $tdrowclass);

                    $eventsTable->endRow();
                }
                // End If - last record in the
            } // End of For Loop
        } else {
            $eventsTable->startRow();
            $eventsTable->addCell('<span class="noRecordsMessage">'.$this->objLanguage->languageText('mod_calendarbase_noeventsforthismonth','calendar', 'No Events for this month').'</span>', NULL, 'center', 'center', NULL, 'colspan="2"');
            $eventsTable->endRow();
        }

        return $eventsTable->show();
    }


    /**
    * This method generates a table of events with fewer details
    * Used to generate the table on the sidebar in Personal Space, Context Home
    *
    * @param array $events List of Events
    * @return string A Calendar in table format
    */
    public function generateSmallListing($events)
    {
        $eventsTable=$this->newObject('htmltable','htmlelements');
        $eventsTable->cssClass='calendarList';
        $eventsTable->width='99%';
        $eventsTable->cellspacing='0';
        $eventsTable->cellpadding='3';

        $eventsTable->startHeaderRow();
        $eventsTable->addHeaderCell($this->objLanguage->languageText('word_date'), '50');
        $eventsTable->addHeaderCell($this->objLanguage->languageText('word_event'));
        $eventsTable->endHeaderRow();

        if (count($events) > 0) {
            foreach ($events as $event)
            {
                $eventsTable->startRow();
                $dDate = $this->objDateFunctions->reformatDateSmallMonth($event['eventdate']);
                $eventsTable->addCell('<nobr>'.$dDate.'</nobr>');
                $eventsTable->addCell(stripslashes($event['eventtitle']));
                $eventsTable->endRow();
            }
        } else {
            $eventsTable->startRow();
            $eventsTable->addCell('<span class="noRecordsMessage">'.$this->objLanguage->languageText('mod_calendarbase_noeventsforthismonth','calendar', 'No Events for this month').'</span>', NULL, 'center', 'center', NULL, 'colspan="2"');
            $eventsTable->endRow();
        }

        //return $eventsTable->show();
        $str = '<hr /><ul>Today';
        foreach ($events as $event)
        {
            $dDate = $this->objDateFunctions->reformatDateSmallMonth($event['eventdate']);
            $str .= '<li><span class="date">'.$dDate.'</span></li>';
        }
        $str .= '</ul><hr/>';
       // print '<pre>';
       // print_r($events).'</pre>';
        return $str;
    }

    /**
    * This method generates the navigation to view events for the previous/next month
    *
    * @param string $month The current month
    * @param string $year The current year
    * @return string A Calendar with the navigation
    */
    public function generateCalendarNavigation ($month = NULL, $year=NULL)
    {
        if (!isset($month)) {
            $month = date('m');
        }

        if (!isset($year)) {
            $year = date('Y');
        }

        $navTable=$this->newObject('htmltable','htmlelements');
        $navTable->width='99%';
        $navTable->cellspacing='0';
        $navTable->cellpadding='5';

        // Previous Month Settings
        $previousMonth = $this->objDateFunctions->previousMonthYear($month, $year);

        $previousIcon =& $this->getObject('geticon', 'htmlelements');
        $previousIcon->setIcon('prev');
        $previousIcon->title = $this->objLanguage->languageText('mod_calendarbase_previousmonth','calendar');

        $previousMonthLink = new link($this->uri(array('action' => 'cal_view', 'month' => $previousMonth['month'], 'year' => $previousMonth['year'], 'events'=>$this->eventsTag), $this->module));
        $previousMonthLink->link = $previousIcon->show().' '.$this->objSimpleCal->monthFull($previousMonth['month']).' '.$previousMonth['year'];

        // Next Month Settings
        $nextMonth = $this->objDateFunctions->nextMonthYear($month, $year);

        $nextIcon =& $this->getObject('geticon', 'htmlelements');
        $nextIcon->setIcon('next');
        $nextIcon->title = $this->objLanguage->languageText('mod_calendarbase_nextmonth','calendar');

        $nextMonthLink = new link($this->uri(array('action' => 'cal_view', 'month' => $nextMonth['month'], 'year' => $nextMonth['year'], 'events'=>$this->eventsTag), $this->module));
        $nextMonthLink->link = $this->objSimpleCal->monthFull($nextMonth['month']).' '.$nextMonth['year'].$nextIcon->show();

        // Navigation Table
        $navTable->startRow();
        $navTable->addCell($previousMonthLink->show(), '30%');
        $navTable->addCell($this->objSimpleCal->monthFull($month).' '.$year, '40%', NULL, 'center', 'bigDayNum');
        $navTable->addCell($nextMonthLink->show(), '30%', NULL, 'RIGHT');
        $navTable->endRow();

        return $navTable->show();
    }


    /**
    * This method out
    */
    public function getCalendarSQL($user=NULL, $context=NULL, $month, $year)
    {
        // Determine Start and End Date
        if (!isset($month)) {
            $month = date('m');
        }
        if (!isset($year)) {
            $year = date('Y');
        }

        $startDate = $year.'-'.$month.'-01';
        $startDate = $this->objDateFunctions->previousDay($startDate);
        $endDate = $this->objDateFunctions->lastDateMonth($month, $year);
        $endDate = $this->objDateFunctions->nextDay($endDate);
        // End Calculations of Dates

        $sqlArray = array();
        $datesArray = array();

        if ($user != '') {
            $sqlArray[] = '(userorcontext = "0" AND userFirstEntry="'.$user.'")';
        }

        if ($context != '') {
            $sqlArray[] = '(userorcontext = "1" AND context ="'.$context.'")';
        }

        $joiner = '';
        $filterSQL = ' ';
        $dateSQL = ' ';

        if (count($sqlArray) > 0) {

            foreach ($sqlArray AS $sqlStatement)
            {
                $filterSQL .= $joiner.' '.$sqlStatement;
                $joiner = ' OR ';
            }
        }

        $sql = '  ('.$filterSQL.') AND eventDate > "'.$startDate.'" AND eventDate < "'.$endDate.'"';

        return $sql;
    }
    
    /**
     *
     * Method to get the alerts
     * 
     * @access public
     * @param integer $alertTime The alert interval
     * @return array $alerts The alerts for the statusbar 
     */
    public function getAlerts($alertTime, $userId = NULL)
    {    
        
        $eventDate = date("Y-m-d", $alertTime);
        
        $sql = "SELECT * FROM tbl_calendar ";
        $sql .= "WHERE `eventdate` <= '$eventDate' ";
        $sql .= "AND `alert_state` = '0' ";
        $sql .= "AND `userorcontext` = '0' ";
        $sql .= "AND `userFirstEntry` = '$userId' ";
        $sql .= "ORDER BY eventdate ASC, timefrom ASC";
       
        $alerts = $this->getArray($sql);
       
        return $alerts;
    }
    
    /**
     *
     * Method to update event alert
     * 
     * @access public
     * @param string $id The id ov the event to update
     * @return VOID 
     */
    public function updateAlert($id)
    {
        return $this->update('id', $id, array('alert_state' => 1));
    }

} //end of class
?>