<?php
/**
* @package popupcalendar
*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Class datepickajax displays a pop up calendar for selecting the date and time.
* The pop up displays a calendar for selecting the date and two dropdowns for selecting the time.
* The time can be hidden be setting showtime = 'no' in the url for the pop up.
*
* @copyright University of the Western Cape 2005
* @author Megan Watson
*
* The datepicker will be called from within a module as shown in the example.
* The example sets 'showtime' = no, so the time element is not displayed.
* The example sets 'showmonths' = no, so the month letter element is not displayed.
* Example:
* $this->objPopupcal = &$this->getObject('datepickajax', 'popupcalendar');
* $dateField = $this->objPopupcal->show('dateFieldName', 'no', 'no', '');
* echo $dateField;
*/
class datepickajax extends object
{
    /**
    * @var string $str Property to display the calendar on first opening.
    */
    var $str = '';
    /**
    * @var string $formStr Property to display the form containing the calling form information.
    */
    var $formStr = '';
    /**
    * @var string $timeStr Property to display the time on first opening.
    */
    var $timeStr = '';
    /**
    * @var string $showTime Property to determine whether to display the time for editing.
    */
    var $showTime = TRUE;
    /**
    * @var string $showMonths Property to determine whether to display the months as single letter links.
    */
    var $showMonths = TRUE;

    /**
    * Constructor
    *
    * @access public
    * @return
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objDate = $this->newObject('dateandtime', 'utilities');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        // Load scriptaclous since we can no longer guarantee it is there
        $scriptaculous = $this->getObject('scriptaculous', 'prototype');
        $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
    }

    /**
    * Method to get the icons for moving the next month/year
    *
    * @access private
    * @param string $day The currently selected day
    * @param string $month The currently selected month
    * @param string $year The currently selected year
    * @return The icons
    */
    private function getNext($day, $month, $year)
    {
        $nextMonth = $this->objLanguage->languageText('mod_popupcalendar_nextmonth', 'popupcalendar');
        $nextYear = $this->objLanguage->languageText('mod_popupcalendar_nextyear', 'popupcalendar');
        $nxMonth = $month+1;
        $nxYear = $year+1;
        if ($month == 12) {
            $nxMonth = 1;
            $year = $year+1;
        }

        $this->objIcon->setIcon('next');
        $this->objIcon->title = $nextMonth;
        $this->objIcon->extra = ' height="20" width="20"';
        $objLink = new link('#');
        $objLink->link = $this->objIcon->show();
        $objLink->extra = ' onclick="javascript:jsBuildCal('.$day.', '.$nxMonth.', '.$year.'); jsInsertDate('.$day.', '.$nxMonth.', '.$year.')"';
        $icon = $objLink->show();

        $this->objIcon->setIcon('last');
        $this->objIcon->title = $nextYear;
        $this->objIcon->extra = ' height="20" width="20"';
        $objLink = new link('#');
        $objLink->link = $this->objIcon->show();
        $objLink->extra = ' onclick="javascript:jsBuildCal('.$day.', '.$month.', '.$nxYear.'); jsInsertDate('.$day.', '.$month.', '.$nxYear.')"';
        $icon.= $objLink->show();

        return $icon;
    }

    /**
    * Method to get the icons for moving the previous month/year
    *
    * @access private
    * @param string $day The currently selected day
    * @param string $month The currently selected month
    * @param string $year The currently selected year
    * @return The icons
    */
    private function getPrevious($day, $month, $year)
    {
        $prevMonth = $this->objLanguage->languageText('mod_popupcalendar_prevmonth', 'popupcalendar');
        $prevYear = $this->objLanguage->languageText('mod_popupcalendar_prevyear', 'popupcalendar');
        $prMonth = $month-1;
        $prYear = $year-1;
        if ($month == 1) {
            $prMonth = 12;
            $year = $year-1;
        }

        $this->objIcon->setIcon('first');
        $this->objIcon->title = $prevYear;
        $this->objIcon->extra = ' height="20" width="20"';
        $objLink = new link('#');
        $objLink->link = $this->objIcon->show();
        $objLink->extra = ' onclick="javascript:jsBuildCal('.$day.', '.$month.', '.$prYear.'); jsInsertDate('.$day.', '.$month.', '.$prYear.')"';
        $icon = $objLink->show();

        $this->objIcon->setIcon('prev');
        $this->objIcon->title = $prevMonth;
        $this->objIcon->extra = ' height="20" width="20"';
        $objLink = new link('#');
        $objLink->link = $this->objIcon->show();
        $objLink->extra = ' onclick="javascript:jsBuildCal('.$day.', '.$prMonth.', '.$year.'); jsInsertDate('.$day.', '.$prMonth.', '.$year.')"';
        $icon.= $objLink->show();

        return $icon;
    }

    /**
    * Method to get a list of the months
    *
    * @access private
    * @param string $day The currently selected day
    * @param string $month The currently selected month
    * @param string $year The currently selected year
    * @return The month list
    */
    private function getAllMonths($day, $month, $year)
    {
        $mnths = $this->objDate->getMonthsAsArray('1letter');
        $str = '';
        foreach($mnths as $key => $item) {
            if (!empty($str)) {
                $str.= '&#160;&#160;';
            }
            $newmonth = $key+1;
            $objLink = new link('#');
            $objLink->link = $item;
	        $objLink->extra = ' onclick="javascript:jsBuildCal('.$day.', '.$newmonth.', '.$year.'); jsInsertDate('.$day.', '.$newmonth.', '.$year.')"';
            $str.= $objLink->show();
        }

        return $str;
    }

    /**
    * Method to get the form containing the selected date and time.
    *
    * @access private
    * @param string $field The field in the form calling the calendar.
    * @param string $date The current date contained in the field.
    * @param bool $showTime Determines whether to display the time.
    * @return The form
    */
    private function getForm($date)
    {
        $save = $this->objLanguage->languageText('word_save');
        $field = $this->session('field');
        $arrDate = explode(' ', $date);

        $objForm = new form('select', $this->uri(array(
            'action' => ''
        )));

        $objInput = new textinput('field', $field);
        $objInput->fldType = 'hidden';
        $objForm->addToForm($objInput->show());

        $objInput = new textinput('date', $arrDate[0]);
        $objInput->fldType = 'hidden';
        $objForm->addToForm($objInput->show());

        $javaScriptTime = '';
        $timeValue = '';
        $showTime = $this->session($field.'_showTime');
        if ($showTime) {
            $objInput = new textinput('time', $arrDate[1]);
            $objInput->fldType = 'hidden';
            $objForm->addToForm($objInput->show());
            $javaScriptTime = '
                var hrSelect = $F(\'input_hour\');
                var mnSelect = $F(\'input_min\');
                jsInsertTime(hrSelect, mnSelect);
            ';
            $timeValue = '+\' \'+$F(\'input_time\')';
        }
        $objButton = new button('save', $save);
        $objButton->extra = ' onclick="javascript:
            '.$javaScriptTime.'
            window.opener.document.getElementById(\'input_'.$field.'\').value = $F(\'input_date\')'.$timeValue.';
            $(\'form_select\').submit();
            window.close();
        "';
        $saveButton = $objButton->show();
        $objForm->addToForm($saveButton);

        return $objForm->show();
    }

    /**
    * Method to build the calendar
    *
    * @access public
    * @param string $day The default day
    * @param string $mnth The default month
    * @param string $year The default year
    * @return The XML for the function
    */
    public function buildCal($day = NULL, $mnth = NULL, $year = NULL, $call = NULL)
    {
        //$objResponse = &new xajaxResponse();
        if (is_null($day)) {
            $day = date('d');
        }
        if (is_null($mnth)) {
            $mnth = date('m');
        }
        if (is_null($year)) {
            $year = date('Y');
        }

        // get days of the week
        $week = $this->objDate->getDaysAsArray('3letter');
        $weekFull = $this->objDate->getDaysAsArray();

        // get month name
        $month = $this->objDate->monthFull($mnth);

        // set up month format/layout
        $first = '1 '.$month.' '.$year;
        $timestamp = strtotime($first);
        $numDays = date('t', $timestamp);
        $startDay = date('w', $timestamp);
        if ($startDay == 0) {
            $startDay = 7;
        }

        // offset = the difference between the start of the week and the first day of the month
        $offset = $startDay-1;

        // get the number of weeks to display
        $numWks = ceil(($numDays+$offset) /7);
        $objTable = new htmltable();
        $objTable->init();
        $objTable->cellpadding = 1;
        $objTable->row_attributes = ' height="15"';

        // Row containing month and links to next/previous month/year
        $next = $this->getNext($day, $mnth, $year);
        $prev = $this->getPrevious($day, $mnth, $year);
        $smallMonths = $this->getAllMonths($day, $mnth, $year);

        $field = $this->session('field');
        $showMonths = $this->session($field.'_showMonths');
        if ($showMonths) {
            $objTable->startRow();
            $objTable->addCell($smallMonths, '', '', 'center', 'even', ' colspan="3"');
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell($prev, '', '', 'left', 'heading');
        $objTable->addCell($month.' '.$year, '', '', 'center', 'heading');
        $objTable->addCell($next, '', '', 'right', 'heading');
        $objTable->endRow();
        $str = $objTable->show();

        $objTable = new htmltable();
        $objTable->init();
        $objTable->cellpadding = 1;
        $objTable->border = 1;
        $objTable->row_attributes = ' height="15"';
        // Heading - days of the week
        $objTable->addRow($week, 'heading');
        // Content - days of the month: weekends = odd; selected day = HighLightText.
        $x = 0;
        for ($i = 0 ; $i < $numWks ; $i++) {
            $objTable->startRow();
            for ($j = 1 ; $j <= 7 ; $j++) {
                $skip = FALSE;
                // Calculate offset from the start of the week.
                $mnDay = (($i*7) +$j) -$offset;
                if ($mnDay <= 0 || $mnDay > $numDays) {
                    $mnDay = '';
                    $skip = TRUE;
                }
                // Calculate class for the cell
                $class = 'even';
                if ($j > 5) {
                    $class = 'odd';
                }
                if ($mnDay == $day) {
                    $class = 'HighLightText';
                }
                if ($skip) {
                    $link = '';
                } else {
                    // Make the day a link
                    $objLink = new link('#');
			        $objLink->extra = ' onclick="javascript:jsBuildCal('.$mnDay.', '.$mnth.', '.$year.'); jsInsertDate('.$mnDay.', '.$mnth.', '.$year.')"';
                    $objLink->link = $mnDay;
                    $link = $objLink->show();
                }
                $objTable->addCell($link, round((1/7*100) , 2) .'%', '', 'center', $class);
            }
            $objTable->endRow();
        }
        // Display Table
        $str.= $objTable->show();
        // Display Date in text format
        $show = $day.' '.$month.' '.$year;
        $newTimestamp = strtotime($show);
        $numDay = date('w', $newTimestamp);
        if ($numDay == 0) {
            $numDay = 7;
        }
        $wkDay = $weekFull[$numDay-1];
        $str.= '<p align = "center"><b><i> '.$wkDay.' '.$show.'</i></b></p>';
        $this->str = $str;
        if($call != 'js'){
            return $str;
        }else{
            echo $str;
        }
    }

    /**
    * Method to render the time element
    *
    * @access public
    * @param string $hour The default hour
    * @param string $min The default minutes
    * @return The XML for the function
    */
    public function buildTime($hour, $min)
    {
        //$objResponse = &new xajaxResponse();
        $field = $this->session('field');
        $defaultDate = $this->session($field.'_defaultDate');
        if ($defaultDate != NULL) {
            $date = strtotime($defaultDate);
            $hour = date('H', $date);
            $min = date('i', $date);
        }
        $time = $this->objLanguage->languageText('mod_popupcalendar_time', 'popupcalendar');
        $timeStr = '<b>'.$time.':</b>&#160;&#160;';

        $objDrop = new dropdown('hour');
        for ($i = 0 ; $i <= 23 ; $i++) {
            if (strlen($i) == 1) {
                $i = '0'.$i;
            }
            $objDrop->addOption($i, $i.'&#160;');
        }
        $objDrop->setSelected($hour);
        $objDrop->extra = 'onchange="javascript:var hrSelect = $F(\'input_hour\');var mnSelect = $F(\'input_min\'); jsInsertTime(hrSelect, mnSelect);"';
        $timeStr.= $objDrop->show();

        $objDrop = new dropdown('min');
        for ($i = 0 ; $i <= 59 ; $i++) {
            if (strlen($i) == 1) {
                $i = '0'.$i;
            }
            $objDrop->addOption($i, $i.'&#160;');
        }
        $objDrop->setSelected($min);
        $objDrop->extra = 'onchange="javascript:var hrSelect = $F(\'input_hour\');var mnSelect = $F(\'input_min\'); jsInsertTime(hrSelect, mnSelect);"';
        $timeStr.= '<b>:</b>&#160;&#160;'.$objDrop->show();

        $objForm = new form('seltime', $this->uri(NULL));
        $objForm->addToForm($timeStr);
        $this->timeStr = $objForm->show();
    }

    /**
    * Method to set up the calling module's info
    *
    * @access private
    * @return
    */
    private function setUpInfo()
    {
        $field = $this->getParam('field');
        $value = $this->getParam('fieldvalue', NULL);
        $showtime = $this->getParam('showtime', FALSE);
        $showmonths = $this->getParam('showmonths', FALSE);
        $this->session('field', $field);
        if (strtolower($showtime) == 'yes' || strtolower($showtime) == 'true') {
            $showtime = TRUE;
        } else {
            $showtime = FALSE;
        }
        $this->session($field.'_showTime', $showtime);
        if (strtolower($showmonths) == 'yes' || strtolower($showmonths) == 'true') {
            $showmonths = TRUE;
        } else {
            $showmonths = FALSE;
        }
        $this->session($field.'_showMonths', $showmonths);
        // initialise starting date
        $defaultDate = $this->session($field.'_defaultDate');
        if ((is_null($value) || $value == 0) && $defaultDate == NULL) {
            $day = date('d');
            $mnth = date('m');
            $year = date('Y');
            $hour = 12;
            $min = '00';
            $value = $year.'-'.$mnth.'-'.$day.' '.$hour.':'.$min;
        } else {
            if($value != NULL){
                $value = date("Y-m-d H:i", strtotime($value));
            }else{
                $value = date("Y-m-d H:i", strtotime($this->session($field.'_defaultDate')));
            }
        }
        $this->formStr = $this->getForm($value);
        return $value;
    }

    /**
    * Method to build the ajax code
    *
    * @access public
    * @return array $return An array of variables to be passed to the template
    */
    public function showCal()
    {
        $field = $this->session('field');
        $showTime = $this->session($field.'_showTime');

        $initDate = $this->setUpInfo();
        $arrDateTime = explode(' ', $initDate);
        $arrDate = explode('-', $arrDateTime[0]);
        $arrTime = array(
            '',
            ''
        );
        if (isset($arrDateTime[1])) {
            $arrTime = explode(':', $arrDateTime[1]);
        }
        $this->buildCal($arrDate[2], $arrDate[1], $arrDate[0]);
        $field = $this->session('field');
        $showTime = $this->session($field.'_showTime');
        if ($showTime) {
            $this->buildTime($arrTime[0], $arrTime[1]);
        }
        $return = array();
        $return[] = $this->str;
        $return[] = $this->formStr;
        $return[] = $this->timeStr;
        // return initial set up
        return $return;
    }

    /**
    * Method to show the input field and the select date icons
    *
    * @access public
    * @param string $field The name of the date field
    * @param bool $showTime Indicate whether the time must be shown - TRUE=yes FALSE=no
    * @param bool $showMonths Indicate whether the month letters must be shown - TRUE=yes FALSE=no
    * @param string $defaultDate The default date to use
    * @return string $str The html string
    */
    public function show($field, $showTime = 'no', $showMonths = 'no', $defaultDate = NULL)
    {
        $selectLabel = $this->objLanguage->languageText('phrase_selectdate');

        //set the height of the popup window
        if (strtolower($showTime) == 'no' || strtolower($showTime) == 'false') {
            if($defaultDate != NULL){
                $defaultDate = date("Y-m-d", strtotime($defaultDate));
            }
            $length = 10;
            if (strtolower($showMonths) == 'no' || strtolower($showMonths) == 'false') {
                $height = 'height=363';
            } else {
                $height = 'height=383';
            }
        } else {
            if($defaultDate != NULL){
                $defaultDate = date("Y-m-d H:i", strtotime($defaultDate));
            }
            $length = 16;
            if (strtolower($showMonths) == 'no' || strtolower($showMonths) == 'false') {
                $height = 'height=406';
            } else {
                $height = 'height=426';
            }
        }
        $objInput = new textinput($field, $defaultDate, '', $length);
        $objInput->extra = ' readonly="readonly"';
        $dateText = $objInput->show();
        $url = $this->uri(array(
            'field' => $field,
            'fieldvalue' => $defaultDate,
            'showtime' => $showTime,
            'showmonths' => $showMonths,
        ) , 'popupcalendar');
        $onclick = "javascript:window.open('".$url."','popupcal','width=300,".$height.",scrollbars=1,resizable=1')";
        $this->objIcon->title = $selectLabel;
        $this->objIcon->setIcon('select_date');
        $objLink = new link('#');
        $objLink->extra = "onclick=\"$onclick\"";
        $objLink->link = $this->objIcon->show();
        $dateLink = $objLink->show();
        $objLink->extra = ''; //clear the 'extra' just in case
        $str = $dateText.'&#160;'.$dateLink;
        return $str;
    }

    /**
    * Method to get and set sessions
    *
    * @access public
    * @param string $session The name of the session
    * @param value $value The value to set, if no $array given the current session value is returned
    * @return array $value The value stored in session
    */
    public function session($session, $value = NULL)
    {
        if ($value != NULL) {
            $this->setSession($session, $value);
        } else {
            $value = $this->getSession($session);
            return $value;
        }
    }
}
?>