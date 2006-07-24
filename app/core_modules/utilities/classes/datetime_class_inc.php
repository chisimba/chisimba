<?php
/**
 * Utilities package datetime
 * The utilities are a set of objects that extends the core::object package
 * that include utilities for manipulating a number of system wide operations including
 * the filesystem, email, date and time functions etc
 * Please take a look at the roadmap for further reference.
 *
 * @package utilities
 * @copyright GNU/GPL AVOIR UWC 2006
 * @author Paul Scott
 * @access public
 */

class datetime extends object
{
    /**
 * Date and time class
 * This class will encapsulate all of the date and time functions that
 * are needed in the framework
 * If you need to add extra functionality around date and time in your module,
 * that will definitely NOT be useful ANYWHERE else, then, and only then, make your own class
 * otherwise simply add to this file...
 *
 * @abstract datetime and timer from kinky
 * @access public
 */

    /**
     * Public variable to hold the language items
     *
     * @access public
     * @var object $objLanguage Property for the language object
     */
    public $objLanguage;

    /**
     * The beginnig date for the specified period
     * as set by one of the methods.
     *
     * @access public
     * @var string
     */
    public $startDate;

    /**
    * The ending date for the specified period
    * as set by one of the methods
    *
    * @access public
    * @var string
    */
    public $endDate;

    /**
     * The config object
     *
     * @access pubilc
     * @var object
     */
    public $objConfig;

   /**
    * The URI to link to in the linked date in the
    * calendar
    *
    * @access public
    * @var string $calLinkto
    */
    public $calLinkto;

   /**
    * The width of the calendar table border
    * defaults to 0
    *
    * @var string $border
    * @access public
    */
    public $border="0";

   /**
    * The cellpaddign of the calendar table
    * defaults to 2
    *
    * @access public
    * @var string $cellpadding
    */
    public $cellpadding="2";

   /**
    * The cellspacing of the calendar table
    * defaults to 2
    *
    * @access public
    * @var string $cellspacing
    */
    public $cellspacing="2";

   /**
    * The width of the calendar table
    *
    * @access public
    * @var string $calWidth
    */
    public $calWidth = "140";

   /**
    * The modulecode of the module using
    * the calendar. Used to build the links for each day
    *
    * @access public
    * @var string $callingModule
    */
    public $callingModule;

   /**
    * The day on which the week starts mon | sun
    *
    * @access public
    * @var string $startweek
    * @todo -csimplecal Implement ability to change day to sunday. It
    * doesn't work and I need some help with it.
    */
    public $startweek="mon";

   /**
    * An array containing any additional query
    * items to pass in the query string from the linked date in the
    * calendar
    *
    * @access public
    * @var array $queryItems
    */
    public $queryItems=array();

   /**
    * An array to check if there are data for a particular day.
    * It contains 'day', 'month', and 'year'
    *
    * @access public
    * @var array
    */
    public $dayDataChkArray=array();

   /**
    * An array to check entry points
    *
    * @access public
    * @var array $entryCheckArray
    */
    public $entryCheckArray=array();


    /**
     * Standard init method
     * This method instantiates the parent __constructor
     *
     * @access public
     * @param void
     * @return void
     */
    public function init()
    {
        $this->objConfig=&$this->getObject('altconfig','config');
        $this->objLanguage = & $this->getObject("language", "language");
    }

    /**
     * Date manipulation methods.
     */


   /**
    * Method to calculate the day of the week when given a date,
    * month and year
    *
    * @access public
    * @param string $day
    * @param string $month
    * @param string $year
    * @return string $day
    */
    public function dayOfWeek($day, $month, $year)
    {
        $a = floor((14 - $month) / 12);
        $y = $year - $a;
        $m = $month + 12 * $a-2;
        $day = ($day + $y + floor($y / 4) - floor($y / 100) + floor($y / 400) + floor(31 * $m / 12)) % 7;
        return $day;
    } //function dayOfWeek

   /**
    * Method to return the numeric value of month when
    * supplied as either three letter abbreviation or full
    * month. Only works for english.
    *
    * @access public
    * @param string $mo The month as text
    * @return int number of the month
    */
    public function numericMonth($mo)
    {
        $mo=strtolower($mo);
        switch($mo){
        	case "jan":
            case "january":
        		return "01";
        		break;
        	case "feb":
            case "february":
        		return "02";
        		break;
        	case "mar":
            case "march":
        		return "03";
        		break;
        	case "apr":
            case "april":
        		return "04";
        		break;
        	case "may":
        		return "05";
        		break;
        	case "jun":
            case "june":
        		return "06";
        		break;
        	case "jul":
            case "july":
        		return "07";
        		break;
        	case "aug":
            case "august":
        		return "08";
        		break;
        	case "sep":
            case "september":
        		return "09";
        		break;
        	case "oct":
            case "october":
        		return "10";
        		break;
        	case "nov":
            case "november":
        		return "11";
        		break;
        	case "dec":
            case "december":
        		return "12";
        		break;
        	default:
                $this->objLanguage = & $this->getObject("language", "language");
        		die($this->objLanguage->languageText("mod_datetime_unrecogmont").": ".$mo."!");
                break;
        } // switch
    } //function numericMonth

    /**
     * Method to set the startdate and enddate for this week
     *
     * @access public
     * @param void
     * @return True
     */
    public function thisWeek()
    {
        $this->startDate = $this->_getAsCompDate($this->_getStartOfWeek());
        $this->endDate = $this->_getAsCompDate($this->_getDateTomorrow());
        return true;
    }

    /**
     * Method to set the startdate and enddate for this month
     *
     * @access public
     * @param void
     * @return bool True on success
     */
    public function thisMonth()
    {
        $this->startDate = $this->_getStartOfMonth();
        $this->endDate = $this->_getAsCompDate($this->_getDateTomorrow());
        return true;
    }

    /**
     * Standard show method to returnr the sql generated from datetime manipulations
     * in this case it returns the SQL WHERE
     * clause
     *
     * @access public
     * @param string $dateFieldName The name of the date field, defaults
     * to dateAdded
     * @return string sql clause
     */
    public function sqlShow($dateFieldName = "dateAdded")
    {
        return " " . $dateFieldName . " >= '" . $this->startDate
         . "' AND " . $dateFieldName . " <= '" . $this->endDate . "'";
    }

    /**
    * Method to set the start date and end date to the beginning and
    * end of the month when passed the shift from the current month
    *
    * @access public
    * @param int $shift The number of months to shift from the current month
    * @return bool true on success
    */
    public function setMonthPair($shift)
    {
        $myDateLower = mktime(0, 0, 0, date("n") + $shift, 1, date("Y")); //first day of month
        $myDateUpper = mktime(0, 0, 0, date("n") + $shift + 1, 0, date("Y")); // last day of the month
        $this->startDate = $this->_getAsCompDate($myDateLower);
        $this->endDate = $this->_getAsCompDate($myDateUpper);
        return true;
    }

    /**
    * Method to evaluate if a year is a leap year or not
    *
    * @access public
    * @param int $year The year to check
    * @return bool True | False
    */
    public function isLeapYear($year)
    {
        if (($year % 4 == 0) && (($year % 100 != 0) || ($year % 400 == 0))) {
            return "true";
        } else {
            return "false";
        }
    }

    /**
     * Public Method to convert the dates to the form that can be used
     * in a SQL query (YYYY-MM-DD)
     *
     * @access public
     * @param string
     * @return string query
     * @see _getAsCompDate()
     */
    public function sqlDate($str)
    {
        return $this->_getAsCompDate($str);
    }

   /**
    * Method to check if a given date has past
    *
    * @access public
    * @param string date
    * @return TRUE | FALSE
    */
    public function hasExpired($strDate)
    {
        //Convert the date to be checked to unix timestamp
        $exCh = strtotime($strDate);
        //Get the date now as a unix timestamp
        $tdCh = time();
        if ( $tdCh > $exCh ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

   /**
    * Expiration methods
    */

    /**
     * Method to return an expiration icon. It makes use of the icon object.
     *
     * @access public
     * @param void
     * @return string Icon->show method
     * @see htmlelements::getIcon()
     */
     public function getExpiredIcon()
     {
        $objClock = $this->newObject("geticon", "htmlelements");
        $objClock->setIcon("clock");
        $objClock->alt = $this->objLanguage->code2Txt("mod_stories_expired");
        return $objClock->show();
     }


   /**
    * Method to send a message to the content owner on expiration.
    * It checks if a message has been sent today, and if not it sends one.
    * If a message has already been sent, then it does not send another
    * in the same day. Note that the table needs to have a field called
    * expNotifDate for it to work.
    *
    * @access public
    * @param string $dbClass The table class to
    * @param string $pModule The module in which the data class resides
    * @param string $toId The userId of the user it is being sent to
    * @param string $subject The subject of the message
    * @param string $body The body of the message
    * @param string $itemId The id field of the notification item
    * @return bool true on success else false
    *
    */
    public function sendExpiredMsg($dbClass, $pModule, $toId, $title, $body, $itemId)
    {
        //Instantiate the connection to the appropriate table/module
        $objDb = $this->getObject($dbClass, $pModule);
        //Get the last expNotifDate from the database
        $ar = $objDb->getRow('id', $itemId);
        //Initialize $chk
        $chk = 0;
        //Get the notification date and convert to unix timestamp
        if ( $ar['notificationDate'] !== NULL ) {
            $notificationDate  = strtotime($ar['notificationDate']);
            //Get the unix timestamp now
            $nowDate=time();
            //Calculate the time elapsed since last notification
            $chk = $nowDate - $notificationDate;
        } else {
            $notificationDate = "";
        }
        //If a notification date has not been set or its more than 24 hrs old
        if ( $notificationDate==NULL || $notificationDate =="0000-00-00 00:00:00" || $chk > 86400) {
            //Establish the mail subject
            $rep=array('kng' => $this->objConfig->siteName());
            $subject = $this->objLanguage->code2Txt('mod_datetime_contentexpired', $rep);
            $mailBody = $title . "\n\n\n". $body;
            $objMail = & $this->getObject('kngmail', 'email');
            $objMail->sendMail('1', $toId, $subject, $mailBody);
            //Add the current date to the expNotifDate field
            $save=array('notificationDate' => date('Y-m-d H:m:s'));
            $objDb->update("id", $itemId, $save);
            return TRUE;
        } else {
            return FALSE;
        }
    }

   /**
    * Method to tag expired content
    *
    * @access public
    * @param String $str The string to be tagged.
    * @return string icon
    */
    public function tagExpiredContent($str)
    {
        return $str . $this->getExpiredIcon();
    }

    /**
     * Simple Calendar methods
     */

    /**
     * Method to return the language version of a month
     *
     * @access public
     * @param string $month
     * @return string
     */
    public function monthShort($month)
     {
        switch ($month) {
            case "Jan":
                return $this->objLanguage->languageText("mod_datetime_jan",'datetime');
                break;
            case "Feb":
                return $this->objLanguage->languageText("mod_datetime_feb",'datetime');
                break;
            case "Mar":
                return $this->objLanguage->languageText("mod_datetime_mar",'datetime');
                break;
            case "Apr":
                return $this->objLanguage->languageText("mod_datetime_apr",'datetime');
                break;
            case "May":
                return $this->objLanguage->languageText("mod_datetime_may",'datetime');
                break;
            case "Jun":
                return $this->objLanguage->languageText("mod_datetime_jun",'datetime');
                break;
            case "Jul":
                return $this->objLanguage->languageText("mod_datetime_jul",'datetime');
                break;
            case "Aug":
                return $this->objLanguage->languageText("mod_datetime_aug",'datetime');
                break;
            case "Sep":
                return $this->objLanguage->languageText("mod_datetime_sep",'datetime');
                break;
            case "Oct":
                return $this->objLanguage->languageText("mod_datetime_oct",'datetime');
                break;
            case "Nov":
                return $this->objLanguage->languageText("mod_datetime_nov",'datetime');
                break;
            case "Dec":
                return $this->objLanguage->languageText("mod_datetime_dec",'datetime');
                break;
            default:
                return $month;
        }
    }

   /**
    * Method to return the full text of the month when passed the
    * numeric two digit representation of month
    *
    * @access public
    * @param string $numMonth the numeric two digit representation of a month
    * @return string
    * @see show()
    */
    public function monthFull($numMonth)
    {
        $calMes["01"] = $this->objLanguage->languageText("mod_datetime_january",'datetime');
        $calMes["1"] = $this->objLanguage->languageText("mod_datetime_january",'datetime');
        $calMes["02"] = $this->objLanguage->languageText("mod_datetime_february",'datetime');
        $calMes["2"] = $this->objLanguage->languageText("mod_datetime_february",'datetime');
        $calMes["03"] = $this->objLanguage->languageText("mod_datetime_march",'datetime');
        $calMes["3"] = $this->objLanguage->languageText("mod_datetime_march",'datetime');
        $calMes["04"] = $this->objLanguage->languageText("mod_datetime_april",'datetime');
        $calMes["4"] = $this->objLanguage->languageText("mod_datetime_april",'datetime');
        $calMes["05"] = $this->objLanguage->languageText("mod_datetime_may",'datetime');
        $calMes["5"] = $this->objLanguage->languageText("mod_datetime_may",'datetime');
        $calMes["06"] = $this->objLanguage->languageText("mod_datetime_june",'datetime');
        $calMes["6"] = $this->objLanguage->languageText("mod_datetime_june",'datetime');
        $calMes["07"] = $this->objLanguage->languageText("mod_datetime_july",'datetime');
        $calMes["7"] = $this->objLanguage->languageText("mod_datetime_july",'datetime');
        $calMes["08"] = $this->objLanguage->languageText("mod_datetime_august",'datetime');
        $calMes["8"] = $this->objLanguage->languageText("mod_datetime_august",'datetime');
        $calMes["09"] = $this->objLanguage->languageText("mod_datetime_september",'datetime');
        $calMes["9"] = $this->objLanguage->languageText("mod_datetime_september",'datetime');
        $calMes["10"] = $this->objLanguage->languageText("mod_datetime_october",'datetime');
        $calMes["11"] = $this->objLanguage->languageText("mod_datetime_november",'datetime');
        $calMes["12"] = $this->objLanguage->languageText("mod_datetime_december",'datetime');
        return $calMes[$numMonth];
    }

   /**
    * Method to return an array of months
    *
    * @access public
    * @param string $abbrev
    * @return array $calMes an array of 12 months
    */
    public function getMonthsAsArray($abbrev=Null)
    {
        switch($abbrev){
            case "3letter":
                $calMes = array(
                    $this->objLanguage->languageText("mod_datetime_jan",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_feb",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_mar",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_apr",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_may",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_jun",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_jul",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_aug",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_sep",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_oct",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_nov",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_dec",'datetime'));
                break;
            case "1letter":
                $calMes = array(
                    $this->objLanguage->languageText("mod_datetime_jy",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_fy",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_mm",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_al",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_my",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_jn",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_jl",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_ag",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_st",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_ot",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_nv",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_dc",'datetime'));
                break;
            default:
                $calMes = array($this->objLanguage->languageText("mod_datetime_january",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_february",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_march",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_april",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_may",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_june",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_july",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_august",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_september",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_october",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_november",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_december",'datetime'));
                break;;
        } // switch
        return $calMes;

    }

    /**
     * Method to get days as an array
     *
     * @access public
     * @param string $abbrev
     * @return string language item
     */
    public function getDaysAsArray($abbrev=Null)
    {
        switch($abbrev){
            case "3letter":
                $caldays = array(
                    $this->objLanguage->languageText("mod_datetime_mon",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_tue",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_wed",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_thu",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_fri",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_sat",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_sun",'datetime'));
                break;
            case "2letter":
                $caldays = array(
                    $this->objLanguage->languageText("mod_datetime_mo",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_tu",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_we",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_th",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_fr",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_sa",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_su",'datetime'));
                break;

            case "1letter":
                $caldays = array(
                    $this->objLanguage->languageText("mod_datetime_m",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_tuy",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_w",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_thy",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_f",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_say",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_suy",'datetime'));

                break;

            default:
                $caldays = array(
                    $this->objLanguage->languageText("mod_datetime_monday",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_tuesday",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_wednesday",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_thursday",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_friday",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_saturday",'datetime'),
                    $this->objLanguage->languageText("mod_datetime_sunday",'datetime'));
                break;
        }
        if ($this->startweek=="sun") { // Take sunday from the end and put it first
            $sunday=$caldays[6];
            array_splice($caldays, 6,1);
            array_unshift($caldays, $sunday);

        }
        //echo implode($caldays, "!");
        return $caldays;
    }

    /**
    * The standard show method
    *
    * @access public
    * @param integer $shift The number of months to shift from the current month
    * @return string HTML Table
    */
    public function calShow($shift = 0)
    {
        /*
        * This section assigns timestamps to dates, starting with today as an unshifted
        * (i.e. non relative) date
        */
        // non relative date
        $todayTs = mktime(0, 0, 0, date("n"), date("d"), date("Y"));
        // first day of the month, either this month or this month + $shift
        $firstdayMonthTs = mktime(0, 0, 0, date("n") + $shift, 1, date("Y"));
        /* last day of the month, either this month or this month + $shift
           $shift + 1 is used with a 0 day to return the 0th day of next month
           which happens to be the last day of this month */
        $lastdayMonthTs = mktime(0, 0, 0, date("n") + $shift + 1, 0, date("Y")); // last day of the month
        // Assign the numeric value of the year
        $numYear = date("Y", $firstdayMonthTs);
        // Assign the numeric value of the month
        $numMonth = date("m", $firstdayMonthTs);
        // Assign the text value of the month
        $textMonth = $this->monthFull(date("n", $firstdayMonthTs));
        // Calculate the days in the month
        $daysInMonth = date("t", $firstdayMonthTs);
        // raplace day 0 for day 7, week starts on monday
        $dayMonth_start = date("w", $firstdayMonthTs);
        if ($dayMonth_start == 0) {
            $dayMonth_start = 7;
        }
        $dayMonth_end = date("w", $lastdayMonthTs);
        if ($dayMonth_end == 0) {
            $dayMonth_end = 7;
        }
        // formating output as a table using the htmltable object of the KNG framework
        $this->objTable=& $this->newObject('htmltable', 'htmlelements');
        // Use the table with property to control the width of the calendar
        $this->objTable->width = $this->calWidth;
        // Use the cal-main CSS entry to control the look of the calendar
        $this->objTable->cssClass = "cal-main";
        //Specify the border of the calendar table defaulting to 0
        $this->objTable->border=$this->border;
        $this->objTable->cellpadding=$this->cellpadding;
        $this->objTable->cellspacing=$this->cellspacing;
        // Make the caption
        $this->objTable->caption=$textMonth . "&nbsp;&nbsp;" . $numYear;
        // load the days of the week, 2 letter version
        $this->objTable->addRow($this->getDaysAsArray("2letter"));

        // Load the days with no date into the table for output
        $this->objTable->startRow();
        // Fill with white spaces until the first day
        for ($k = 1; $k < $dayMonth_start; $k++) {
            $this->objTable->addcell("&nbsp;","20", "top");
        }
        //Today as integer
        $dayToday=date("j", time());
        //This month as month
        $monthToday=date("m", time());
        //This year as year
        $yearToday=date("Y", time());

        //Loop over the days in the month
        for ($i = 1; $i <= $daysInMonth; $i++) {
            // Assigns a timestamp to day i
            $day_i_ts = mktime(0, 0, 0, date("n", $firstdayMonthTs), $i, date("Y", $firstdayMonthTs));
            $day_i = date("w", $day_i_ts);
            // Placing Sunday as last day of the week
            //-----> I have not figured out how to make this work with Sunday as the first day.
            if ($day_i == 0) {
                $day_i = 7;
            }

            // Target link from the arrays
            $d2_i = date("d", $day_i_ts);
            $links = array(
                'month' => $numMonth,
                'year' => $numYear,
                'day' => $d2_i,
                'shift'=>$shift);
            // Add any link elements that are passed in this->queryitems
            if (is_array($this->queryItems)) {
                $links = array_merge($links, $this->queryItems);
            }
            $link_i = $this->uri($links, $this->callingModule);

            /*------------ SECTION TO PRINT DATES LINED -----
           This section parses the dates and determines:
            1. Are there any entries?
            2. Is the date in the future? */
            //If it is a year in the future then no links
            if ( $numYear > $yearToday ) {
                $link_i = $i;
            } else {
                //If it it this year
                if ( $numYear == $yearToday ) {
                    //If is a future month in this year
                    if ( $numMonth > $monthToday ) {
                        $link_i = $i;
                    } else {
                        //If it is this month
                        if ( $i > $dayToday ) {
                            $link_i = $i;
                        } else {
                             if ( $this->dayHasData($i, $numMonth, $numYear) ) {
                                $link_i = "<a href=\"" . $link_i . "\">" . $i . "</a>";
                            } else {
                                $link_i = $i;
                            }
                        }
                    }
                 } else {
                        //Now check if there are data for the day before
                        //making the link
                        if ( $this->dayHasData($i, $numMonth, $numYear, $this->entryCheckArray) ) {
                         $link_i = "<a href=\"" . $link_i . "\">" . $i . "</a>";
                     } else {
                         $link_i = $i;
                     }
                 }
            }

            // Plancing day i on calendar
            if ($shift == 0 && $todayTs == $day_i_ts) {
                $this->objTable->addcell($link_i, "20", "top", Null, "cal-today");
            } else {
                if ($day_i==6 || $day_i==7) {
                    $css="cal-weekend";
                } else {
                    $css="cal-default";
                }
                $this->objTable->addcell($link_i, "20", "top", Null, $css);
            }
            if ($day_i == 7 && $i < $daysInMonth) {
                $this->objTable->endRow();
                $this->objTable->startRow();
            } else if ($day_i == 7 && $i == $daysInMonth) {
                $this->objTable->endRow();
            } else if ($i == $daysInMonth) {
                for ($h = $dayMonth_end; $h < 7; $h++) {
                    $this->objTable->addcell("&nbsp;");
                }
                $this->objTable->endRow();
            }
        } // end for
        $nextAr=array('shift'=>$shift+1);
        $prevAr=array('shift'=>$shift-1);
        $linkNx=$this->uri(array_merge($nextAr, $this->queryItems), $this->callingModule);
        $linkPv=$this->uri(array_merge($prevAr, $this->queryItems), $this->callingModule);
        //Create the anchor link object
        $this->objAnchor=&$this->newObject('link', 'htmlelements');
        // Add the next link
        $this->objIcon=& $this->newObject('geticon', 'htmlelements');
        $this->objIcon->setIcon("next");
        $this->objAnchor->href = $linkNx;
        $this->objAnchor->link = $this->objIcon->show();
        $next=$this->objAnchor->show();
        // Add the previous link
        $this->objAnchor->href = $linkPv;
        $this->objIcon->setIcon("prev");
        $this->objAnchor->link = $this->objIcon->show();
        $prev=$this->objAnchor->show();

        // Start a table row, insert the two links, and show the table
        $this->objTable->startRow();
        $this->objTable->addcell($prev, Null, Null, "left", Null, "colspan='3'");
        $this->objTable->addcell($next, Null, Null, "right", Null, "colspan='4'");
        $this->objTable->endRow();
        return $this->objTable->show();
    } // end function


   /**
    * Method to check if a given day has data
    *
    * @access public
    * @param string $numDay
    * @param string $numMonth
    * @param string $numYear
    * @return bool
    */
    public function dayHasData($numDay, $numMonth, $numYear)  {
        $ch = $numYear.$numMonth.$numDay;
        foreach ($this->dayDataChkArray as $line) {
            if ( $line['date']==$ch ) {
                  return TRUE;
            }
        }
        return FALSE;
    }

   /**
    * Method to take a date/datetime string and return a formatted date.
    *
    * @access public
    * @param string $date The date in datetime format - yyyy-mm-dd hh:mm.
    * @return string $ret The formatted date - 30 June 2005 11:50.
    */
    public function formatDate($date)
    {
        $array = explode(' ', $date);

        $date = explode('-', $array[0]);
        $format = $date[2].' ';
        $format .= $this->monthFull($date[1]).' ';
        $format .= $date[0];

        if(isset($array[1]) && $array[1] != 0){
            $format .= ' '.substr($array[1],0,5);
        }
        return $format;
    }

    /**
    * Simple function to time the execution of a script
    *
    * @example $time_start = microtime_float();
    *
    * 	// Sleep for a while
    * 	usleep(100);
    *
    * 	$time_end = microtime_float();
    * 	$time = $time_end - $time_start;
    *
    * 	echo "Did nothing in $time seconds\n";
    * @access public
    * @param void
    * @return void
    */
    public function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * Method to wait for usecs time
     *
     * @access public
     * @param int $usecs
     * @return void
     */
    public function wait($usecs)
    {
        $temp = gettimeofday();
        $start = (int)$temp["usec"];
        while (1) {
            $temp = gettimeofday();
            $stop = (int)$temp["usec"];
            if ($stop - $start >= $usecs)
                break;
        }
    }

    /**
    * Calculates the difference for two given dates, and returns the result
    * in specified unit.
    *
    * @access public
    * @param string $ Initial date (format: [dd-mm-YYYY hh:mm:ss], hh is in 24hrs format)
    * @param string $ Last date (format: [dd-mm-YYYY hh:mm:ss], hh is in 24hrs format)
    * @param char $ 'd' to obtain results as days, 'h' for hours, 'm' for minutes, 's' for seconds, and 'a' to get an indexed array of days, hours, minutes, and seconds
    * @return mixed The result in the unit specified (float for all cases, except when unit='a', in which case an indexed array), or null if it could not be obtained
    */
    public function getDateDifference($dateFrom, $dateTo, $unit = 'd')
    {
        $difference = null;

        $dateFromElements = split(' ', $dateFrom);
        $dateToElements = split(' ', $dateTo);

        $dateFromDateElements = split('-', $dateFromElements[0]);
        $dateFromTimeElements = split(':', $dateFromElements[1]);
        $dateToDateElements = split('-', $dateToElements[0]);
        $dateToTimeElements = split(':', $dateToElements[1]);
        // Get unix timestamp for both dates
        $date1 = mktime($dateFromTimeElements[0], $dateFromTimeElements[1], $dateFromTimeElements[2], $dateFromDateElements[1], $dateFromDateElements[0], $dateFromDateElements[2]);
        $date2 = mktime($dateToTimeElements[0], $dateToTimeElements[1], $dateToTimeElements[2], $dateToDateElements[1], $dateToDateElements[0], $dateToDateElements[2]);

        if ($date1 > $date2) {
            return null;
        }

        $diff = $date2 - $date1;

        $days = 0;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        if ($diff % 86400 <= 0) { // there are 86,400 seconds in a day
                $days = $diff / 86400;
        }

        if ($diff % 86400 > 0) {
            $rest = ($diff % 86400);
            $days = ($diff - $rest) / 86400;
            if ($rest % 3600 > 0) {
                $rest1 = ($rest % 3600);
                $hours = ($rest - $rest1) / 3600;

                if ($rest1 % 60 > 0) {
                    $rest2 = ($rest1 % 60);
                    $minutes = ($rest1 - $rest2) / 60;
                    $seconds = $rest2;
                } else {
                    $minutes = $rest1 / 60;
                }
            } else {
                $hours = $rest / 3600;
            }
        }

        switch ($unit) {
            case 'd':
            case 'D':

                $partialDays = 0;

                $partialDays += ($seconds / 86400);
                $partialDays += ($minutes / 1440);
                $partialDays += ($hours / 24);

                $difference = $days + $partialDays;

                break;

            case 'h':
            case 'H':

                $partialHours = 0;

                $partialHours += ($seconds / 3600);
                $partialHours += ($minutes / 60);

                $difference = $hours + ($days * 24) + $partialHours;

                break;

            case 'm':
            case 'M':

                $partialMinutes = 0;

                $partialMinutes += ($seconds / 60);

                $difference = $minutes + ($days * 1440) + ($hours * 60) + $partialMinutes;

                break;

            case 's':
            case 'S':

                $difference = $seconds + ($days * 86400) + ($hours * 3600) + ($minutes * 60);

                break;

            case 'a':
            case 'A':

                $difference = array ("days" => $days,
                    "hours" => $hours,
                    "minutes" => $minutes,
                    "seconds" => $seconds
                    );

                break;
        }

        return $difference;
    }

    /*------------------------- PRIVATE METHODS BELOW LINE ---------------------*/

    /**
    * Return todays date as a unix timestamp
    *
    * @access private
    * @param void
    * @return string date
    */
    private function _getDateToday()
    {
        return mktime(0, 0, 0, date("n"),
            date("d"), date("Y"));
    }

    /**
    * Return tomorrow's date as a unix timestamp
    *
    * @access private
    * @param void
    * @return string
    */
    private function _getDateTomorrow()
    {
        return $this->_getDateToday() + 86400;
    }


    /**
    * Return the first day of the week as a unix timestamp
    *
    * @access private
    * @param void
    * @return string date
    */
    private function _getStartOfWeek()
    {
        return mktime(0, 0, 0, date("n"),
            (date("j") - date("w")), date("Y"));
    }

    /**
    * Return last day of the week as a unix timestamp
    *
    * @access private
    * @param void
    * @return string date
    */
    private function _getEndOfWeek()
    {
        return mktime(23, 59, 59, date("n"), (date("j") + (6 - date("w"))), date("Y"));
    }

    /**
    * Return the first day of the month as a date
    *
    * @access private
    * @param void
    * @return string date
    */
    private function _getStartOfMonth()
    {
        // Get today
        $d = $this->_getDateToday();
        // Get today's year
        $y = date("Y", $d);
        $m = date("m", $d);
        return $y . "-" . $m . "-01";
    }

    /**
    * Method to convert the dates to the form that can be used
    * in a SQL query (YYYY-MM-DD)
    *
    * @access private
    * @param void
    * @return string query
    */
    private function _getAsCompDate($str)
    {
        return date("Y", $str) . "-" . date("m", $str) . "-" . date("d", $str);
    }

    /**
    * Return a date in YYYY MM DD format as a unix timestamp
    *
    * @access private
    * @param string
    * @return string date
    */
    private function _getCompDateAsUnix($str)
    {
        return strtotime($str);
    }
}//end class
?>