<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* class to control the utilty method for the events calendar
*
* @author Wesley Nitsckie
* @copyright (c) 2005 University of the Western Cape
* @package eventscalendar
* @version 1
*
* 
*/
class utils extends object
{
    
    /**
     * Constructor
     */
    public function init()
    {
       
    }
    
    /**
     * Method to get the navigation nodes
     * @return array
     * @access public
     * 
     */
   public function getNavNodes()
	{
	    $nodes = array();
		$nodes[] = array('text' => 'Events', 'uri' => $this->uri(null,'eventscalendar'));
		//$nodes[] = array('text' => 'Event Categories', 'uri' => $this->uri(array('action' => 'categories'), 'eventscalendar'));
		
		
		return  $nodes;

	}
    
	/**
	 * Method to get the navigation
	 * @access public
	 * @return string
	 */
	public function getNav()
	{
	    $nodes = $this->getNavNodes();
	    $objNav = $this->newObject('sidebar', 'navigation');
		
		return $objNav->show($nodes);
	}
	
	
	  /**
   * Method to get a time dropdown
   * @return string
   * @param $minute The selected minute
   * @param $hour The selected hour
   */
   public function getTimeDropDown($name, $hour = null , $minute = null)
   {
   		
   		
   		$str = '<p><select  name="'.$name.'_hours" id="hours">';
   		for($i = 0; $i < 24; $i++)
   		{
   			
   			$zero = ($i<10) ? '0' : '';
   			//$selected = ($i==9) ? 'selected="selected"' : '';
			$str .= '<option value="'.$zero.($i).'" '.$selected.' >&nbsp;'.$zero.$i.'&nbsp;</option>';   				
   		}
   		$str .= '</select>h ';
   		
   		
   		
   		$str .= '<select name="'.$name.'_minutes" id="minutes">';
   		for($i = 0; $i < 60; $i++)
   		{
   			
   			$zero = ($i<10) ? '0' : '';
   			
			$str .= '<option value="'.$zero.$i.'"   >&nbsp;'.$zero.$i.'&nbsp;</option>';   				
   		}
   		$str .= '</select></p>';
   		return $str;
   }
   
   /**
    * Method that the context designer will use to get the list of available links
    * 
    * @return array
    * @access public
    * 
    */
   public function getLinks($contextCode)
   {
       
       $arr = $this->getAll('WHERE contextcode = "'.$contextCode.'" ');
       return $arr;
   }
   
   /**
   * Method to merge a user's calendar
   * with a context calendar events
   *
   */
   public function mergeCalendars()
   {

		try{
		 	$objDBCats = $this->getObject('dbeventscalendarcategories', 'eventscalendar');
		 	$objDBEvents = $this->getObject('dbeventscalendar', 'eventscalendar');
		 	$objDBContext = $this->getObject('dbcontext', 'context');
		 	$objShares = $this->getObject('dbeventscalendar_sharedevents', 'eventscalendar');
		 	$objUser = $this->getObject('user', 'security');
		 	
		 	
			//get the context calendar events
			$arrCats = $objDBCats->getCategories('context', $objDBContext->getContextCode());
			
			//
			$arrUserCat = $objDBCats->getCategories('user', $objUser->userId());
			$events = $objDBEvents->getEventsByCategory($arrCats[0]['id']);
		
			foreach($events as $event)
			{
				$objShares->addShare($event['id'], $arrCats[0]['id'], $arrUserCat[0]['id'] );			
			}
			
			
			//add them to the user's events
//			$this->_objUser->userId();
			
				
		}
		catch (Exception $e){
       		echo 'Caught exception: ',  $e->getMessage();
        	exit();
        }


	
   }
	
}
?>