<?php
/**
* A class to do translate the difference between two dates into a human readable format.
*
* Example: This message was sent 4 minutes ago, instead of having it in a number format.
*  
* @author Tohir Solomons
* @package utilities
* @version 1.0
* 
*/
class translatedatedifference extends object 
{

    /**
    * @var object $objLanguage Property for hte language object
    */
    public $objLanguage;

    /** 
    * Standard init function
    */
    public function init()
    {
        $this->objLanguage=& $this->getObject('language', 'language');
        $this->objDateFunctions =& $this->getObject('datefunctions', 'calendarbase');
    }
    
    /**
    * Method to get the difference between two dates translated
    * @param string $date1 First Date
    * @param string $date2 Second Date
    * @return string Difference translated and expressed in a human readable format
    */
    public function getDifference($date1, $date2='')
    {
        // Set 2nd Date to current date and time if not given
        if ($date2 == '') {
           $date2 = date('Y-m-d H:i:s');
        }
        
        // Swap around if first date is bigger than second date
        if ($date1 > $date2) {
            $temp = $date2;
            $date2 = $date1;
            $date1 = $temp;
        }
        
        // Get the difference as an array
        $difference = $this->objDateFunctions->dateDifference($date1, $date2);
        
        // If more than one days difference
        if ($difference['d'] > 1) {
            return $difference['d'].' '.$this->objLanguage->languageText('mod_datetime_days', 'utilities', 'days').' '.$this->objLanguage->languageText('mod_datetime_ago', 'utilities', 'ago');
        } else if ($difference['d'] > 0){ // If previous day
            return 24+$difference['h'].' '.$this->objLanguage->languageText('mod_datetime_hours', 'utilities').' '.$this->objLanguage->languageText('mod_datetime_ago', 'utilities', 'ago');
        } else { // If today
            if ($difference['h'] > 1) { // If more than one hour
                $returnString = $difference['h'].' '.$this->objLanguage->languageText('mod_datetime_hours', 'utilities', 'hours').', ';
            } else if ($difference['h'] > 0) {
                $returnString = $difference['h'].' '.$this->objLanguage->languageText('mod_datetime_hour', 'utilities', 'hour').', ';
            } else {
                $returnString = '';
            }
            
            if ($difference['m'] > 1) { // if more than one minute
                $returnString .= $difference['m'].' '.$this->objLanguage->languageText('mod_datetime_minutes', 'utilities', 'minutes').' ';
            } else if ($difference['m'] > 0) {
                $returnString = $difference['h'].' '.$this->objLanguage->languageText('mod_datetime_minute', 'utilities', 'minute').' ';
            }        
            
            if ($difference['h'] == 0 && $difference['m'] == 0) {
                return '<strong>'.$difference['s'].' '.$this->objLanguage->languageText('mod_datetime_seconds', 'utilities').' '.$this->objLanguage->languageText('mod_datetime_ago', 'utilities', 'ago').'</strong>';
            } else {
               return '<strong>'.$returnString.' '.$this->objLanguage->languageText('mod_datetime_ago', 'utilities', 'ago').'</strong>';
            }
            
            return $returnString;
        }
    }
    
} // class