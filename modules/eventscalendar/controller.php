<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * The events calendar manages the events
 * @author Wesley Nitsckie
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package eventscalendar
 **/

class eventscalendar extends controller
{
    
    /**
     * Constructor
     */
    public function init()
    {
        
        $this->_objDBEventsCalendar =  $this->getObject('dbeventscalendar', 'eventscalendar');
        $this->_objCalendarBiulder =  $this->getObject('calendarbiulder', 'eventscalendar');
        $this->_objDBCategories =  $this->getObject('dbeventscalendarcategories', 'eventscalendar');
        $this->_objDBContext =  $this->getObject('dbcontext', 'context');
        $this->_objUser =  $this->getObject('user', 'security');
        $this->_objUtils =  $this->getObject('utils');
        $this->objLanguage =  $this->getObject('language','language');
        $this->_objContextModules =  $this->getObject('dbcontextmodules', 'context');
        
        //create an entry for this context that your are in if it doesnt exists
        if($this->_objDBContext->isInContext() && !$this->_objDBCategories->typeExist('context', $this->_objDBContext->getContextCode()))
        {
            $this->_objDBCategories->addCat('context', $this->_objDBContext->getContextCode());           
        }

	    //check if this user has entry
	    if(!$this->_objDBCategories->typeExist('user', $this->_objUser->userId()))
	    {
		    //create the entry
		    $this->_objDBCategories->addCat('user', $this->_objUser->userId());
	    }
    }
    
    
    /**
     *The standard dispatch method
     */
    public function dispatch()
    {
        $this->setVar('pageSuppressXML',true);
        $action = $this->getParam("action");
        $this->setLayoutTemplate('layout_tpl.php');
        
        //before showing the calendar check if the context has the calendar
        //is registered as a plugin with this context
        if($this->_objContextModules->isContextPlugin($this->_objDBContext->getContextCode(), 'eventscalendar'))
        {
            $this->isContextPlugin = TRUE;
        } else {
            $this->isContextPlugin = FALSE;
        }

        //get the calendar type    
        $type = $this->getParam("type");
        $typeId = $this->getParam("typeid");
    
        //set the default calendar to the  context if the user is in a context
        if($type=='' && $this->_objDBContext->isInContext() && $this->isContextPlugin)
        {
            $type = 'context';
            $typeId = $this->_objDBContext->getContextCode();
        }
        
        //if the user is not in a context then show the user's calendar
        if($type == null)
        {
        	$type = 'user';
            $typeId = $this->_objUser->userId();
        }

        
        switch ($action)
        {
        	
          
            case null:
            case 'events':
            	$mon =  $this->getParam('month');
            	$year = $this->getParam('year');
            
            	//$arrEvents = $this->_objDBEventsCalendar->getUserEvents($this->_objUser->userId(), $mon , $year	);
                
                $catId = $this->_objDBCategories->getCatId($type, $typeId);

            	$arrEvents = $this->_objDBEventsCalendar->getEventsByCategory($catId, $mon , $year);
               	$this->setVar('events', $arrEvents);
            	$this->setVar('calType', $type);
                $this->setVar('catId', $catId);
            	$this->_objCalendarBiulder->assignDate($mon , $year);
            	$this->setVar('calendar', $this->_objCalendarBiulder->show('big', $arrEvents));
                
                return 'events_tpl.php';
            case 'addevent':
            
                return 'add_tpl.php';
            case 'saveevent':
            	//  die($this->getParam('start_date'));
                if($this->getParam('mode') == 'edit')
                {
                   
                    $this->_objDBEventsCalendar->editEvent($typeId);
                } else {//die($this->getParam('catid'));
                     $this->_objDBEventsCalendar->addEvent($this->getParam('catid'));
                }
              
                return $this->nextAction(null);
                
            //categories
            case 'categories':
                $this->setVar('categories', $this->_objDBCategories->getCategories('user', $this->_objUser->userId()));
                return 'categories_tpl.php';
            case 'addcat':
                return 'addcat_tpl.php';
            case 'savecat':
                $catId = $this->getParam('id');
                
                if($this->getParam('mode') == 'edit')
                {
                    $this->_objDBCategories->editCategory($catId);
                } else {

                    $this->_objDBCategories->addCategory($this->_objUser->userId());
                }
                return $this->nextAction('categories');
                
             case 'merge';
                $this->_objUtils->mergeCalendars();
                
				return $this->nextAction(null);
        }
    }
    
    /**
     * Method to get the menu
     * @return string 
     */
    public function getMenu()
    {
        return $this->_objUtils->getNav();
    }
    
    

}

?>