<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Calendar Controller
 * This class controls all functionality to run the calendar module. It now integrates user calendar and contextcalendar
 * @author Tohir Solomons & yasser buchana
 * @copyright (c) 2004 University of the Western Cape
 * @package calendar
 * @version 2
 */
class calendar extends controller {

    /**
     * Constructor method to instantiate objects and get variables
     */
    function init() {
        $this->objCalendar = $this->getObject('managecalendar');
        //$this->objCalendar =& $this->getObject('dbcalendar', 'calendarbase');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->dateFunctions = $this->getObject('dateandtime', 'utilities');
        $this->objICal = $this->getObject('ical');
        $this->objCalendarInterface = $this->getObject('calendarinterface');
        $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');
        // User Details
        $this->objUser = & $this->getObject('user', 'security');
        $this->setVarByRef('fullname', $this->objUser->fullname());
        $this->userId = $this->objUser->userId();


        // Determine if user is in a context
        $this->contextCode = $this->objContext->getContextCode();
        $this->contextTitle = $this->objContext->getTitle();
        if ($this->contextCode == NULL) {
            $this->contextCode = 'root';
            $this->isInContext = FALSE;
            $this->contextTitle = 'Lobby';
        } else {
            $this->isInContext = TRUE;
        }
        $this->setVarByRef('contextCode', $this->contextCode);
        $this->setVarByRef('courseTitle', $this->contextTitle);
        $this->setVarByRef('isInContext', $this->isInContext);

        // $objContextCondition = &$this->getObject('contextcondition','contextpermissions');
        $this->isContextLecturer = $this->objContextGroups->isContextLecturer();

        // Give User Lecturer Rights if User is Admin
        //if ($this->isValid('manage_course_event')) {
        /*
          if ($this->objUser->isCourseAdmin()) {
          $this->isContextLecturer = TRUE;
          }
         */


        //if ($this->isValid('manage_site_event')) {
        if ($this->objUser->isAdmin()) {
            $this->isContextLecturer = TRUE;
            $this->manageSiteCalendar = TRUE;
        } else {
            $this->manageSiteCalendar = FALSE;
        }


        $this->setVarByRef('isAdmin', $this->manageSiteCalendar);

        //echo $this->manageSiteCalendar;

        $this->setVarByRef('isContextLecturer', $this->isContextLecturer);

        $this->objCalendar->setContextPermissions($this->isContextLecturer);


        // Load Language Class
        $this->objLanguage = $this->getObject('language', 'language');

        $this->objAttachments = & $this->getObject('attachments');
    }

    /**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
    function dispatch($action=Null) {
        $this->setVar('pageSuppressXML', true);

        if ($this->isInContext) {
            $this->setLayoutTemplate('calendar_layout_tpl.php');
        } else {
            $this->setLayoutTemplate('user_layout_tpl.php');
        }

        switch ($action) {
            case 'add':
                return $this->showAddForm();

            case 'edit':
                return $this->showEditForm($this->getParam('id'));

            case 'saveevent':
                return $this->saveEvent($this->getParam('eventfor', '0'));

            case 'updateevent':
                return $this->updateEvent();

            case 'delete':
                return $this->deleteEvent($this->getParam('id'));

            case 'tempframe':
                return $this->attachmentWindow($this->getParam('id'), $this->getParam('mode'));

            case 'uploadattachment':
                return $this->uploadAttachment();

            case 'downloadattachment':
                return $this->downloadAttachment($this->getParam('id'), $this->getParam('event'));

            case 'deleteattachment':
                return $this->deleteAttachment($this->getParam('id'), $this->getParam('mode'), $this->getParam('filename'));

            case 'icalexport':
                $this->objICal->export();
                break;
            default:
                return $this->showEvents();
        }
    }

    /**
     * Method to show events for the current month. This is the default action
     */
    function showEvents() {
        $month = $this->getParam('month', date('m'));
        $year = $this->getParam('year', date('Y'));
        $groupid = $this->getParam("groupid");

        $this->setVarByRef('month', $month);
        $this->setVarByRef('year', $year);

        $this->objCalendarInterface->setupCalendar($month, $year);

        $eventsCalendar = $this->objCalendarInterface->getCalendar();

        $this->setVarByRef('userEvents', $this->objCalendarInterface->numUserEvents);
        $this->setVarByRef('contextEvents', $this->objCalendarInterface->numContextEvents);
        $this->setVarByRef('otherContextEvents', $this->objCalendarInterface->numOtherEvents);
        $this->setVarByRef('siteEvents', $this->objCalendarInterface->numSiteEvents);

        $this->setVarByRef('eventsCalendar', $eventsCalendar);
        $this->setVarByRef('calendarNavigation', $this->objCalendarInterface->getNav());
        $this->setVarByRef('eventsList', $this->objCalendarInterface->getEventsList());


        $this->setVarByRef("groupid", $groupid);

        return 'calendar_tpl.php';
    }

    /**
     * Method to show the Add Event Form
     */
    function showAddForm() {
        // Determines the default list of views available
        if ($this->isInContext) {
            $defaultList = 'all';
        } else {
            $defaultList = 'user';
        }

        $eventsList = $this->getParam('events', $defaultList);
        $this->setVarByRef('currentList', $eventsList);

        $month = $this->getParam('month', date('m'));
        $year = $this->getParam('year', date('Y'));
        $this->setVarByRef('month', $month);
        $this->setVarByRef('year', $year);

        $this->setVar('mode', 'add');

        $temporaryId = $this->objUser->userId() . '_' . mktime();
        $this->setVarByRef('temporaryId', $temporaryId);
        $this->setVarByRef('groupid', $this->getParam("groupid"));

        return 'addedit_event_tpl.php';
    }

    /**
     * Method to process a form and save an event
     */
    function saveEvent($eventFor) {
        $date = $this->getParam('date');
        $date2 = $this->getParam('date2');
        $eventtitle = $this->getParam('title');
        $eventdetails = $this->getParam('details');
        $eventurl = $this->getParam('url');
        $multidayevent = $this->getParam('multidayevent');
        $timeFrom = $this->getParam('timefrom');
        $timeTo = $this->getParam('timeto');

        $groupid = $this->getParam("groupid");


        $eventsList = 'all';

        // Check if day is a multiday event or not
        if (($multidayevent == 1) && ($date != '') && ($date2 != '')) {
            // Insert Multidate event
            //$this->objCalendar->insertMultiDayEvent ($date, $date2, $eventtitle, $eventdetails, $eventurl, $this->userId, $this->userId);
            // Insert Single Day event
            switch ($eventFor) {
                case 0: // Save User Event
                    //$event = $this->objCalendar->insertMultiDayUserEvent ($date, $date2, $eventtitle, $eventdetails, $eventurl, $this->userId, $this->userId, $timeFrom, $timeTo);

                    break;
                case 1: // Save Course Event
                
                    $event = $this->objCalendar->insertMultiDayContextEvent($date, $date2, $eventtitle, $eventdetails, $eventurl, $this->contextCode, $this->userId, $this->userId, NULL, NULL, NULL, $timeFrom, $timeTo);
                    break;
                case 2: // Save Single group Event
                   $event = $this->objCalendar->insertMultiDayGroupEvent($date, $date2, $eventtitle, $eventdetails, $eventurl, $this->contextCode, $this->userId, $this->userId, NULL, NULL, NULL, $groupid , $timeFrom, $timeTo);
                  break;


                case 3: // Save Site Event
                    $event = $this->objCalendar->insertMultiDayContextEvent($date, $date2, $eventtitle, $eventdetails, $eventurl, 'root', $this->userId, $this->userId, NULL, $timeFrom, $timeTo);
                    $eventsList = 'site';
                    break;
            }
        } else {
            // Insert Single Day event
            switch ($eventFor) {
                case 0: // Save Single User Event
                    $event = $this->objCalendar->insertSingleUserEvent($date, $eventtitle, $eventdetails, $eventurl, $this->userId, 0, NULL, NULL, $timeFrom, $timeTo);
                    $eventsList = 'user';
                    break;
                case 1: // Save Single Course Event
                    $event = $this->objCalendar->insertSingleContextEvent($date, $eventtitle, $eventdetails, $eventurl, $this->contextCode, $this->userId, 0, NULL, $timeFrom, $timeTo);
                    break;
                case 2: // Save Single group Event
                    $event = $this->objCalendar->insertSingleGroupEvent($date, $eventtitle, $eventdetails, $eventurl, $this->contextCode, $this->userId, 0, NULL, $timeFrom, $timeTo, $groupid);
                    break;

                case 3:  // Save Single Site Event
                    $event = $this->objCalendar->insertSingleContextEvent($date, $eventtitle, $eventdetails, $eventurl, 'root', $this->userId, 0, NULL, $timeFrom, $timeTo);
                    $eventsList = 'site';
                    break;
            }
            //$event = $this->objCalendar->insertUserEvent($date, $eventtitle, $eventdetails, $eventurl, $this->userId);
        }

        $monthYear = $this->dateFunctions->getMonthYear($date);

        // Get List of Temporary Files
//        $files = $this->objTempAttachments->getTransferList($_POST['temporary_id']);
//
        // Transfer as Proper Attachment
//        foreach ($files as $file)
//        {
//            $this->objEventAttachments->insertSingle($file['attachment_id'], $event, $file['userId']);
//            $this->objTempAttachments->deleteAttachment($file['id'], $_POST['temporary_id']);
//        }
//
        //$this->objAttachments->transfer($_POST['temporary_id'],$event);

        return $this->nextAction(NULL, array('message' => 'eventadded', 'month' => $monthYear['month'], 'year' => $monthYear['year'], 'events' => $eventsList));
    }

    /**
     * Method to prepare a form for editing an event
     *
     * @param string $id Record Id of the event to edit
     */
    function showEditForm($id) {
        // Determines the default list of views available
        if ($this->isInContext) {
            $defaultList = 'all';
        } else {
            $defaultList = 'user';
        }

        $eventsList = $this->getParam('events', $defaultList);
        $this->setVarByRef('currentList', $eventsList);

        $event = $this->objCalendar->getSingle($id);

        $monthYear = $this->dateFunctions->getMonthYear($event['eventdate']);
        $this->setVarByRef('month', $monthYear['month']);
        $this->setVarByRef('year', $monthYear['year']);

        // Check if user is able to edit event
        $this->checkEventEditPermission($event);

        // Check if the event is a multiday event or a single day
        if ($event['multiday_event'] == '1') {
            // If event start id is NULL, get the date of the first event
            if ($event['multiday_event_start_id'] != '') {
                $event['eventdate'] = $this->objCalendar->getStartMultiDayEvent($event['multiday_event_start_id']);
            } else {
                // Else set event start id to current id
                $event['multiday_event_start_id'] = $event['id'];
            }
            // get date of last event
            $event['eventdate2'] = $this->objCalendar->getLastMultiDayEvent($event['multiday_event_start_id']);
        } else {
            $event['eventdate2'] = '';
        }
        $this->setVarByRef('event', $event);
        $this->setVar('mode', 'edit');

        $this->setVarByRef('temporaryId', $id);

        return 'addedit_event_tpl.php';
    }

    /**
     * Method to process a form and update an event
     */
    function updateEvent() {

        $id = $this->getParam('id');
        $date = $this->getParam('date');
        $date2 = $this->getParam('date2');
        $eventtitle = $this->getParam('title');
        $eventdetails = $this->getParam('details');
        $eventurl = $this->getParam('url');
        $multidayevent = $this->getParam('multidayevent');
        $multiday_event_original = $this->getParam('multiday_event_original');

        $timeFrom = $this->getParam('timefrom');
        $timeTo = $this->getParam('timeto');

        $event = $this->objCalendar->getSingle($id);

        if ($event != FALSE) {
            $returnevents = 'all';

            //var_dump($event);

            $this->objCalendar->deleteBatch($id);

            return $this->saveEvent($event['userorcontext']);
        } else {
            return $this->nextAction(NULL);
        }
    }

    /**
     * Method to delete an event
     *
     * @param string $id Record Id of the Event
     */
    function deleteEvent($id) {
        // Get the event from the database
        $event = $this->objCalendar->getSingle($id);

        $returnArray = array();

        // get the date - this is necessary for the redirect
        $date = $event['eventdate'];

        if (count($event) != 0) {
            $monthYear = $this->dateFunctions->getMonthYear($date);
            $returnArray['year'] = $monthYear['year'];
            $returnArray['month'] = $monthYear['month'];
        }

        // Check if the event belongs to the user - to prevent hacking via URL
        if ($this->checkEventEditPermission($event)) {
            $this->objCalendar->deleteBatch($id);
            $returnArray['message'] = 'eventdeleted';
            if ($event['userorcontext'] == '1' && $event['context'] == 'root') {
                $returnArray['events'] = 'site';
            }
        } else {
            $returnArray['message'] = 'notallowedtodelete';
        }

        $this->objAttachments->deleteAllFiles($id);


        // Return to Calendar
        return $this->nextAction(NULL, $returnArray);
    }

    /**
     * Method to show the iframe containing the attachments when adding or editing events
     * @param string $id Temporary Id of event when adding, or Record Id when editing
     * @param string $mode - either 'add' or 'edit'
     */
    function attachmentWindow($id, $mode) {
        $this->setLayoutTemplate(NULL);

        $this->setVar('pageSuppressIM', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('suppressFooter', TRUE);

        $bodyParams = 'class="popupwindow" ';
        $this->setVar('bodyParams', $bodyParams);

        $this->setVarByRef('id', $id);
        $this->setVarByRef('mode', $mode);

        if ($mode == 'add') {
            $files = $this->objAttachments->listFiles($id);
        } else if ($mode == 'edit') {
            $files = $this->objAttachments->listFiles($id);
        }

        $this->setVarByRef('files', $files);

        return 'attachment_window.php';
    }

    /**
     * Method to upload an attachment
     */
    function uploadAttachment() {
        $id = $_POST['id'];
        $mode = $_POST['mode'];
        try {
            $this->objAttachments->uploadFile($id);
        } catch (CustomException $e) {
            die($e);
        }
//        if ($_FILES['userFile']['error'] != 4) {
//            $fileId =
//            if ($mode == 'add') {
//                $this->objTempAttachments->insertSingle($id, $fileId, $this->userId);
//            } else if ($mode == 'edit') {
//                $this->objEventAttachments->insertSingle($fileId, $id, $this->userId);
//            }
//        }
        return $this->nextAction('tempframe', array('id' => $id, 'mode' => $mode));
    }

    /**
     * Method to Download an Attachment
     * @param string $id Record Id of the attachment
     * @param string $event Record Id of the Event
     */
    function downloadAttachment($id, $event) {
        $file = $this->objEventAttachments->getFile($id, $event);

        if ($file == FALSE) {
            return $this->nextAction(NULL, array('error' => 'attachment'));
        } else {
            $this->objUploader->outputFile($file['attachment_id'], TRUE);
        }
    }

    function deleteAttachment($id, $mode, $filename) {
        $this->objAttachments->deleteFile($id, $filename);
        return $this->nextAction('tempframe', array('id' => $tempId, 'mode' => $mode));
    }

    /**
     * Method to check whether the user has access to edit an event
     * @param array $event Event Details
     * @return True if user has access, or redirects to screen with pop up message
     */
    function checkEventEditPermission($event) {
        // If the event does not exists, return to the Calendar
        if ($event == FALSE) {
            return $this->nextAction(NULL, array('message' => 'eventeditnotexists'));
        }

        // Default to Access
        $okToEdit = FALSE;


        switch ($event['userorcontext']) {
            case 0:
                if ($event['userfirstentry'] == $this->objUser->userId()) {
                    $okToEdit = TRUE;
                }
                break;
            case 1:
                if ($this->objUser->isContextLecturer($this->objUser->userId(), $event['context']) || $this->objUser->isAdmin()) {
                    $okToEdit = TRUE;
                }
                break;
            case 3:
                if ($this->objUser->isAdmin()) {
                    $okToEdit = TRUE;
                }
                break;
        }

        // Redirect if no permission
        if ($okToEdit == FALSE) {
            return $this->nextAction(NULL, array('message' => 'notallowedtoedit'));
        } else {
            return TRUE;
        }
    }

}

?>
