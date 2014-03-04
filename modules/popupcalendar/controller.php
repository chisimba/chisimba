<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Module for a php popupcalendar
* @copyright (c) 2004 KEWL.NextGen
* @version 1.0
* @package popupcalendar
* @author Megan Watson, Kevin Cyster
*
* $Id: controller.php
*/
class popupcalendar extends controller
{
    /**
    * Constructor
    *
    * @access public
    * @return
    */
    public function init()
    {
        $this->datePickajax = &$this->newObject('datepickajax');
        $this->objLanguage = &$this->newObject('language', 'language');
    }

    /**
    * Dispatch Method
    *
    * @access public
    * @return
    */
    public function dispatch($action)
    {
        switch ($action){
            case 'buildcal':
                $day = $this->getParam('day');
                $month = $this->getParam('month');
                $year = $this->getParam('year');
                return $this->datePickajax->buildCal($day, $month, $year, 'js');
            default:
                $field = $this->getParam('field', NULL);
                $date = $this->getParam('date', NULL);
                $time = $this->getParam('time', NULL);
                if ($date != NULL) {
                    $defaultDate = $date;
                    if ($time != NULL) {
                        $defaultDate.= ' '.$time;
                    }
                    $this->datePickajax->session($field.'_defaultDate', $defaultDate);
                }
                return $this->showAjaxCal();
        }
    }

    /**
    * Method to render the calendar using ajax
    *
    * @access public
    * @return template The template for the popup window
    */
    public function showAjaxCal()
    {
        $array = $this->datePickajax->showCal();
        $this->setVarByRef('str', $array[0]);
        $this->setVarByRef('formStr', $array[1]);
        $this->setVarByRef('timeStr', $array[2]);
        return 'datepick_tpl.php';
    }

    /**
    * This is a method to determine if the user has to be logged in or not
    * It overides that in the parent class
    *
    * @access public
    * @returns boolean TRUE or FALSE
    */
    public function requiresLogin()
    {
        $action = $this->getParam('action', 'NULL');
        switch ($action) {
            default:
                return FALSE;
        }
    }
}
?>