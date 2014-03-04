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
class calendarinterface extends object
{
    /**
     * An array of events formatted for rendering
     * 
     * @var string array
     * @access private
     * 
     */
    private $preparedEventsArray = array();
    /**
     *
     * An array listing events formatted for rendering
     * 
     * @var string array
     * @access private
     *  
     */
    private $preparedListArray = array();
    /**
     * The month of the calendar to render
     * @var string
     * @access public
     * 
     */
    public $month;
    /**
     *
     * The year of the calendar to render
     * 
     * @var string
     * @access public
     * 
     */
    public $year;
    /**
     * The calendar size big, medium or small
     * @var string
     * @access public
     * 
     */
    public $calendarSize = 'big';
    /**
     * If the lecturer is a lecturer in the current context
     * @var boolean
     * @access public
     * 
     */
    public $isContextLecturer = FALSE;
    /**
     *
     * If the person can manage the site calendar or not
     *  
     * @var boolean
     * @access public
     * 
     */
    public $manageSiteCalendar = FALSE;
    /**
     * The code of the current context
     * @var string
     * @access public
     */
    public $contextCode;
    
    
    /**
     * An array to hold events
     * @var string
     * @access public
     */
    public $rawEventsList;
    
    /**
     * An array to hold events
     * @var string
     * @access public
     */
    public $eventsList;
    
    /**
    * Standard constructor methos, set up commonly used
    * objects and parameters.
    * 
    * @return void
    * @access public
    * 
    */
    public function init()
    {
        $this->objCalendar = $this->getObject('dbcalendar', 'calendarbase');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objDateFunctions = $this->getObject('dateandtime','utilities');
        
        $this->loadClass('link', 'htmlelements');
        
        $this->month = date('m');
        $this->year = date('Y');
        
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext', 'context');
        
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
        
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objIcon->setIcon('edit');
        $this->editIcon = $this->objIcon->show();
        
        $this->confirmDel = $this->objLanguage->languageText('mod_calendarbase_eventdeleterequestconfirm', 'calendarbase', 'Are you sure you want to delete this event?');
    }
    
    public function resetEventsArray()
    {
        $this->eventsList = array();
        $this->preparedEventsArray = array();
        $this->preparedListArray = array();
    }
    
    public function setupCalendar($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
        
        $siteEvents = $this->getSiteEvents($month, $year);
        $userEvents = $this->getUserEvents($this->userId, $month, $year);
        if ($this->contextCode == 'root') {
            $contextEvents = array();
        } else {
            $contextEvents = $this->getContextEvents($this->contextCode, $month, $year);
        }
        $userContextsArray = array(); 
        $otherEvents = array();
        if (count($userContextsArray) > 0) {
            foreach ($userContextsArray as $context) {
                if ($context['contextcode'] != $this->contextCode) {
                    $otherContextEvents = $this->getContextEvents($context['contextcode'], $month, $year);
                    if (count($otherContextEvents) > 0) {
                        $otherEvents = array_merge($otherEvents, $otherContextEvents);
                    }
                }
            }
        }
        $this->prepareEventsForCalendar($userEvents, 'user');
        $this->prepareEventsForCalendar($siteEvents, 'site');
        $this->prepareEventsForCalendar($contextEvents, 'context');
        $this->prepareEventsForCalendar($otherEvents, 'other');
        
        $this->numUserEvents = count($userEvents);
        $this->numSiteEvents = count($siteEvents);
        $this->numContextEvents = count($contextEvents);
        $this->numOtherEvents = count($otherEvents);
    }
    
    
    
    private function getEvents($origin, $id, $month, $year)
    {
        if (!isset($month)) {
            $month = $this->month;
        }
        if (!isset($year)) {
            $year = $this->year;
        }
        $startDate = $year.'-'.$month.'-01';
        $endDate = $this->objDateFunctions->lastDateMonth($month, $year);
        return $this->objCalendar->getEvents($origin, $id, $startDate, $endDate);
    }
    
    public function getUserEvents($userId, $month=NULL, $year=NULL)
    {
        return $this->getEvents('user', $userId, $month, $year);
    }
    
    public function getContextEvents($contextCode, $month=NULL, $year=NULL)
    {
        return $this->getEvents('context', $contextCode, $month, $year);
    }
    
    public function getSiteEvents($month=NULL, $year=NULL)
    {
        return $this->getEvents('context', 'root', $month, $year);
    }
    
    /**
    * Take a list of events and prepare them in a format for adding to the calendar class.
    * Amongst others, it removed duplication in events, etc.
    *
    * @param array $events List of Events
    * @return array $preparedArray List of events ready to be sent to the calendar class
    */
    public function prepareEventsForCalendar (&$events, $type)
    {
        $objTrim = $this->getObject('trimstr', 'strings');
        $objWashout = $this->getObject('washout', 'utilities');
        foreach ($events as $event) {
            // Convert to day of the month.
            $day = $this->objDateFunctions->dayofMonth($event['eventdate']);
            // Initialize variables.
            $edit = '';
            $delete = '';
            $contextInfo = '';
            // Do different things depending on what type of calendar it is.
            switch ($type)
            {
                case 'user':
                    $css = 'event_user';
                    if ($event['userfirstentry'] == $this->objUser->userId()) {
                        $link = new link ($this->uri(array('action'=>'edit', 
                          'id'=>$event['id'])));
                        $link->link = $this->editIcon;
                        $edit = $link->show();
                        $delete = $this->objIcon->getDeleteIconWithConfirm(
                          '', array('action' => 'delete', 'id'=>$event['id']), 
                          'calendar', $this->confirmDel);
                    }
                    break;
                case 'context':
                    $css = 'event_context';
                    if ($this->objUser->isContextLecturer($this->objUser->userId(), $event['context']) || $this->objUser->isAdmin()) {
                        $link = new link ($this->uri(array('action'=>'edit', 'id'=>$event['id'])));
                        $link->link = $this->editIcon;
                        $edit = $link->show();
                        $delete = $this->objIcon->getDeleteIconWithConfirm(
                          '', array('action' => 'delete', 'id'=>$event['id']), 
                          'calendar', $this->confirmDel);
                    }
                    $contextInfo = '<strong>' 
                      . ucwords($this->objLanguage->code2Txt('mod_context_context', 
                          'context', NULL, '[-context-]')) .':</strong> '
                      . $this->objContext->getTitle($event['context']) . '<br />';
                    break;
                case 'site':
                    $css = 'event_site';
                    if ($this->objUser->isAdmin()) {
                        $link = new link ($this->uri(array('action'=>'edit', 
                          'id'=>$event['id'])));
                        $link->link = $this->editIcon;
                        $edit = $link->show();
                        $delete = $this->objIcon->getDeleteIconWithConfirm(
                          '', array('action' => 'delete', 'id'=>$event['id']), 
                          'calendar', $this->confirmDel);
                    }
                    break;
                case 'other':
                    $css = 'event_othercontext';
                    if ($this->objUser->isContextLecturer(
                      $this->objUser->userId(), $event['context']) || 
                      $this->objUser->isAdmin()) {
                        $link = new link ($this->uri(array('action'=>'edit', 
                          'id'=>$event['id'])));
                        $link->link = $this->editIcon;
                        $edit = $link->show();
                        $delete = $this->objIcon->getDeleteIconWithConfirm(
                          '', array('action' => 'delete', 'id'=>$event['id']), 
                          'calendar', $this->confirmDel);
                    }
                    $contextInfo = '<strong>'
                      . ucwords($this->objLanguage->code2Txt('mod_context_context', 
                      'context', NULL, '[-context-]')).':</strong> '
                      .$this->objContext->getTitle($event['context']).'<br />';
                    break;
                case 'workgroup':  $css = 'event_workgroup'; break;
                default:           $css = ''; break;
            }
            
            if ($this->calendarSize == 'small') {
                
            } else {
                
            }
            
            $this->rawEventsList[$day][] = $event; 

            $eventList = '<div class="eventlist ' . $css
              . '"><strong>' . $event['eventtitle'] . '</strong>';
            
            if ($event['timefrom'] != '') {
                $eventList .= '&nbsp;('. $event['timefrom'] . ' - ' 
                  . $event['timeto'] . ')';
            }
            $eventList .= '<div>'. $contextInfo . $event['eventdetails']
              .'</div>'. $edit . ' ' . $delete . '';
            if ($event['eventurl'] != NULL) {
                $link = new link($event['eventurl']);
                $link->link = $event['eventurl'];
                $eventList .= '<p>' . $link->show() . '</p>';
            }
            
            $eventList .= '</div>';

            if (array_key_exists($day, $this->preparedEventsArray)) {
                $temp = rtrim ($this->preparedEventsArray[$day], '</ul>');
                $this->preparedEventsArray[$day] = $temp.'<li class="'
                  .$css.'" title="'.stripslashes($event['eventtitle'])
                  .'">'.$objTrim->strTrim(stripslashes($event['eventtitle']), 8)
                  .'</li></ul>';
                $this->preparedListArray[$day] .= '<hr />'.$eventList;
            } else {
                $this->preparedEventsArray[$day] = '<ul><li class="'.$css.'">'.stripslashes($event['eventtitle']).'</li></ul>';
                $this->preparedListArray[$day] = $eventList;
            }
        }
    }
    
    /**
     *
     * Get a list of events
     * 
     * @return type 
     * 
     */
    public function getEventsList()
    {
        
        if (count($this->preparedListArray) == 0) {
            return '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_calendar_noeventsinmonth', 'calendar').'</div>';
        } else {
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cssClass = 'eventslist';
            
            ksort($this->preparedListArray);
// Temporary thing until I can figure out why it is doing all the SQL twice
            array_unique($this->preparedListArray);
            $monthYear = $this->objDateFunctions->monthFull($this->month).'<br />'.$this->year;
            
            foreach ($this->preparedListArray as $day=>$events) {
                $table->startRow();
                $table->addCell('<a name="'.$day.'" class="bigDayNum">'.$day.'</a><br />'.$monthYear, '50');
                $table->addCell($events);
                $table->endRow();
            }
            return $table->show();
        }
    }

    public function getSmallEventsList()
    {
        $jq = $this->getCalenderEvents();
        
        if (count($this->eventsList) == 0) {
            return '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_calendar_noeventsinmonth', 'calendar').'</div>';
        } else {
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cssClass = 'eventslist';
            $table->cellspacing = '1';
            $table->cellpadding = '0';

            foreach ($this->eventsList as $day=>$events)
            {
                $table->startRow();
                $table->addCell('<strong name="'.$day.'">'.$day.'</strong>', '14.29%', 'top', 'center', '', '');
                $table->addCell($events, '', 'top', '', '', 'colspan="6"');
                $table->endRow();
            }
            return $table->show() . $jq;
        }
    }

    public function getEventsArray()
    {
        return $this->preparedEventsArray;
    }
    
    public function getCalendar()
    {
        $jq = $this->getCalenderEvents('wide');
        
        $objCalendarGenerator = $this->getObject('calendargenerator', 'calendarbase');
        
        $objCalendarGenerator->year = $this->year;
        $objCalendarGenerator->month = $this->month;
        $objCalendarGenerator->events = $this->preparedEventsArray;
        $objCalendarGenerator->events = $this->eventsList;
        $objCalendarGenerator->size = $this->calendarSize;

        return $objCalendarGenerator->show ();
    }
    
    public function getNav()
    {
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('prev');
        $previous = $this->objDateFunctions->previousMonthYear($this->month, $this->year);
        $prevLink = new link ($this->uri(array('month'=>$previous['month'], 'year'=>$previous['year'])));
        $prevLink->link = $objIcon->show().' '.$this->objDateFunctions->monthFull($previous['month']).' '.$previous['year'];
        
        $objIcon->setIcon('next');
        $next = $this->objDateFunctions->nextMonthYear($this->month, $this->year);
        $nextLink = new link ($this->uri(array('month'=>$next['month'], 'year'=>$next['year'])));
        $nextLink->link = $this->objDateFunctions->monthFull($next['month']).' '.$next['year'].' '.$objIcon->show();
        
        $this->loadClass('htmlheading', 'htmlelements');
        $header = new htmlheading();
        $header->str = $this->objDateFunctions->monthFull($this->month).' '.$this->year;
        
        //nextMonthYear
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $table->addCell($prevLink->show(), '30%', NULL, 'left');
        $table->addCell($this->objDateFunctions->monthFull($this->month).' '.$this->year, '40%', NULL, 'center', 'bigDayNum');
        $table->addCell($nextLink->show(), '30%', NULL, 'right');
        $table->endRow();
        
        return $table->show();
    }
    
    
    /**
    * Method to check whether the user has access to edit an event
    * @param array $event Event Details
    * @return True if user has access, or redirects to screen with pop up message
     * @access public
    */
    public function checkEventEditPermission($event)
    {
        // If the event does not exists, return to the Calendar
        if ($event == FALSE) {
            return FALSE;
        }
        // Default to Access
        $okToEdit = TRUE;
        // Check if User has access to edit the event
        if ($event['userorcontext'] == 0 && $event['userFirstEntry'] != $this->userId) {
            $okToEdit = FALSE;
        }
        // Check that event is either for the current context or a site event
        if ($event['userorcontext'] == 1 && ($event['context'] == $this->contextCode || $event['context'] == 'root')) {
            $okToEdit = TRUE;
        } else if ($event['userorcontext'] == 1) { // Additional if to only use context events
            $okToEdit = FALSE;
        }
        // If event is for current event - check if user is lecturer
        if ($event['userorcontext'] == 1 && $event['context'] == $this->contextCode && !$this->isContextLecturer) {
            $okToEdit = FALSE;
        }
        // If event is a site event - check if user is admin
        if ($event['userorcontext'] == 1 && $event['context'] == 'root' && !$this->manageSiteCalendar) {
            $okToEdit = FALSE;
        }
        // Redirect if no permission
        if ($okToEdit == FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    /**
     *
     * Method to get the event for display in the small calender
     * 
     * @access private
     * @return VOID
     */
    private function getCalenderEvents($type = 'small')
    {
        $objTrim = $this->getObject('trimstr', 'strings');

        $timeLabel = $this->objLanguage->languageText('word_from', 'system');
        $titleLabel = $this->objLanguage->languageText('word_title', 'system');
        $detailsLabel = $this->objLanguage->languageText('word_details', 'system');
        
        if (!empty($this->rawEventsList))
        {
            ksort($this->rawEventsList);
            foreach ($this->rawEventsList as $day => $events)
            {
                if (count($events) > 1)
                {
                    $startTime = array();
                    //$events = $this->rawEventsList[$day];
                    foreach ($events as $key => $line)
                    {
                        $startTime[$key] = $line['timefrom'];
                    }
                    array_multisort($startTime, SORT_ASC, $events);
                    $this->rawEventsList[$day] = $events;
                }
            }

            $str = '';
            foreach ($this->rawEventsList as $day => $events)
            {
                if (count($events) > 1)
                {
                    $array = array('date' => date('l, j F Y', strtotime($events[0]['eventdate'])));
                    $title = $this->objLanguage->code2Txt('mod_calendar_events', 'calendar', $array);
                }
                else
                {
                    $array = array('date' => date('l, j F Y', strtotime($events[0]['eventdate'])));
                    $title = $this->objLanguage->code2Txt('mod_calendar_event', 'calendar', $array);
                }

                $string = '';
                foreach ($events as $key =>$event)
                {
                    if ($type == 'wide')
                    {
                        $temp = str_replace(' ', '~', strip_tags(stripcslashes($this->rawEventsList[$day][$key]['eventtitle'])));
                        $temp = wordwrap($temp, 15, '&#8203;', TRUE);
                        $temp = str_replace('~', ' ', $temp);
                        $string .= '<div style="padding: 0.25em;"><b>'.date('H:i', strtotime($this->rawEventsList[$day][$key]['timefrom'])).'</b>';
                        $string .= ' - '.$temp.'</div>';
                    }   
                    else
                    {
                        $eventTitle = strip_tags(wordwrap(stripslashes($this->rawEventsList[$day][$key]['eventtitle']), 25, '&#8203;', true));
                        $string .= '<a href="#" id="event_'.$day.'_'.$key.'" onclick="jQuery(\'#jqdialogue_'.$day.'_'.$key.'\').dialog(\'open\')">'.$eventTitle.'</a><br />';                
                    }

                    $table = $this->newObject('htmltable', 'htmlelements');
                    $table->startRow();
                    $table->addCell($titleLabel . ': ', '10%');
                    $table->addCell($event['eventtitle']);
                    $table->endRow();
                    $table->startRow();
                    $table->addCell($timeLabel . ': ', '10%');
                    $table->addCell(date('g:i a', strtotime($event['timefrom'])).'&nbsp;-&nbsp;'.date('g:i a', strtotime($event['timeto'])));
                    $table->endRow();
                    $table->startRow();
                    $table->addCell($detailsLabel . ': ', '10%');
                    $table->addCell(strip_tags($objTrim->strTrim(stripslashes($event['eventdetails'])), 120));
                    $table->endRow();                   
                    $content = $table->show().'<br /><br />';

                    $this->objDialog = $this->newObject('dialog', 'jquerycore');
                    $this->objDialog->setCssId('jqdialogue_'.$day.'_'.$key);
                    $this->objDialog->setTitle($title);
                    $this->objDialog->setContent($content);
                    $this->objDialog->setWidth(350);
                    $str .= $this->objDialog->show();
                }
                $this->eventsList[$day] = $string;
            }
            return $str;
        }
        return NULL;
    }
}
?>