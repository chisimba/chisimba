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
        
        //get the calendar object
        $this->objCalendar = & $this->newObject('usercalendar' , 'calendar');
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
        //return $ret;
         $this->objCalendar->getListing = FALSE;
        return '<center>'.$this->objCalendar->show($use).'</center>';
    }
}