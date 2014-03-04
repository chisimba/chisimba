<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class to get a simple calendar
*
* @author Wesley Nitsckie
* @category Chisimba
* @package Calendar block
* @version
* 
*
*/
class block_calendar extends object
{
    var $title;
    
    /**
    * Constructor for the class
    */
    function init()
    {
        //Create an instance of the help object
        $this->objHelp=& $this->getObject('helplink','help');
 		//Create an instance of the language object
        $this->objLanguage =& $this->getObject('language','language');
        //Set the title
        $this->title='My Calendar';//$this->objLanguage->languageText("mod_postlogin_helptitle",'postlogin');
         $this->_objDBEventsCalendar = & $this->newObject('dbeventscalendar', 'eventscalendar');
        //get the calendar object
        $this->objCalendarBiulder = & $this->newObject('calendarbiulder' , 'eventscalendar');
        $this->_objDBCategories = & $this->newObject('dbeventscalendarcategories', 'eventscalendar');
        $this->_objUser = & $this->newObject('user', 'security');
    }
    
    /**
    * Method to output a block with information on how help works
    */
    function show($use = NULL)
	{
        //Add the text tot he output
        $ret = $this->objLanguage->languageText("mod_postlogin_helphowto",'postlogin');
        //Create an instance of the help object
        $objHelp = & $this->getObject('helplink','help');
        //Add the help link to the output
        $ret .= "&nbsp;".$this->objHelp->show('mod_postlogin_helphowto','postlogin');
        $mon =  $this->getParam('month');
        $year = $this->getParam('year');
    
        //$arrEvents = $this->_objDBEventsCalendar->getUserEvents($this->_objUser->userId(), $mon , $year	);
        $catId = $this->_objDBCategories->getCatId('user', $this->_objUser->userId());
        $arrEvents = $this->_objDBEventsCalendar->getEventsByCategory($catId, $mon , $year);

        $this->setVar('events', $arrEvents);
        
        $this->objCalendarBiulder->assignDate($mon , $year);
        $ret =  $this->objCalendarBiulder->show('simple', $arrEvents);
        return $ret;
    }
}