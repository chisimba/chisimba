<?php
/**
* WTM Controller
*
* PHP version 5
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the
* Free Software Foundation, Inc.,
* 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*
* @category Chisimba
* @package WTM
* @author Yen-Hsiang Huang <wtm.jason@gmail.com>
* @copyright 2007 AVOIR
* @license http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version CVS: $Id: demo_class_inc.php,v 1.4 2007-08-03 10:33:34 Exp $
* @link http://avoir.uwc.ac.za
*/

/**
* Security check: the $GLOBALS is an array used to control access to certain constants.
* Here it is used to check if the file is opening in engine, if not it
* stops the file from running.
*
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name $kewl_entry_point_run
*/
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}


class WTM extends controller
{
	public $objLanguage;	

	public $objDBBuildings;
	
	public $objDBEvents;
	
	public $objCoordinate;

	/**
	* Constructor method to instantiate the language,
	* events and buildings DBs and coordinate objects
	*/
	public function init()
	{
  		$this->objLanguage = $this->getObject('language','language');
		
  		$this->objDBBuildings = $this->getObject('dbwtm_buildings','wtm');
		
  		$this->objDBEvents = $this->getObject('dbwtm_events','wtm');
		
		$this->objCoordinate = $this->getObject('coordinate','wtm');
		
	}

	/**
	* Standard controller dispatch method, 
	* which receives an action and executes it.
	* @param $action action
	*/
  	public function dispatch($action)
 	{
		//Default starting action is viewBuildings
   		$action=$this->getParam('action', 'viewBuildings');
		//Convert the action into a method.
   		$method = $this->__getMethod($action);
   		return $this->$method();
 	}

	/**
	* Action validating method. Checks if the action 
	* corresponds to an existing method.
	*/
 	private function __validAction(& $action)
 	{
   		if (method_exists($this, "__".$action)) 
		{
			return TRUE;
   		} 
		else 
		{
     		return FALSE;
   		}
 	}

	/**
	* Get method function. Converts action into 
	* the corresponding method.
	*/
 	private function __getMethod(& $action)
 	{
     	if ($this->__validAction($action)) 
		{
			return "__" . $action;
     	}
		else 
		{
		    return "__actionError";
     	}
 	}

	/**
	* Action error method. Displays error message 
	* for an invalid action.
	*/
 	private function __actionError()
 	{
     	$action=$this->getParam('action');
     	$this->setVar('str', "<h3>" . "Unrecognized action: " . $action . "</h3>");
     	return 'actionError_tpl.php';
 	}

	/**
	* Add building method. Returns add/edit building 
	* template.
	*/
 	private function __addBuilding()
 	{
		return 'editBuilding_tpl.php';
 	}

	/**
	* View buildings method. Returns list-all 
	* buildings template.
	*/
 	private function __viewBuildings()
 	{
		return 'listallBuildings_tpl.php';
 	}

	/**
	* Add new building method, inserts the form 
	* data into the database and returns the list-all 
	* buildings template.
	*/
	private function __addNewBuilding()
 	{
    	//Coordinates are multiplied by 10^5 to eliminate decimals.
    	$building = $this->getParam('building');
    	$longcoordinate = 100000 * $this->getParam('longcoordinate'); 
		$latcoordinate = 100000 * $this->getParam('latcoordinate'); 
		$xexpand = 100000 * $this->getParam('xexpand');
		$yexpand = 100000 * $this->getParam('yexpand');
		
    	$id = $this->objDBBuildings->insertSingle($building,$longcoordinate,$latcoordinate,$xexpand,$yexpand);
    	return 'listallBuildings_tpl.php';
 	}

	/**
	* Edit building method. Retrieves and passes the 
	* building id to the edit building template.
	*/
 	private function __editBuilding()
 	{
    	$id = $this->getParam('id');
		
    	$this->setVar('id', $id);
    	return "editBuilding_tpl.php";
 	}

	/**
	* Update building method. Gets updated form 
	* data and modifies the entry in the database. 
	* Returns the list-all buildings template.
	*/
 	private function __updateBuilding()
 	{
    		$id = $this->getParam('id');
    		$building = $this->getParam('building');
    		$longcoordinate = $this->getParam('longcoordinate'); 
			$latcoordinate = $this->getParam('latcoordinate'); 
			$xexpand = $this->getParam('xexpand');
			$yexpand = $this->getParam('yexpand');
			
    		$id = $this->objDBBuildings->updateSingle($id,$building,$longcoordinate,$latcoordinate,$xexpand,$yexpand);
    		return "listallBuildings_tpl.php";
 	} 

	/**
	* Delete building method. Gets the building ID 
	* and deletes the building from the database. 
	* Returns the list-all buildings template.
	*/
 	private function __deleteBuilding()
 	{
    		$id = $this->getParam('id');
			
    		$id = $this->objDBBuildings->deleteSingle($id);
    		return "listallBuildings_tpl.php";
 	}
	
	/**
	* Add event method. Returns the edit 
	* event template
	*/
 	private function __addEvent()
 	{
     		return 'editEvent_tpl.php';
 	}

	/**
	* View events method. Passes the event ID
	* and returns the list-all events template.
	*/
 	private function __viewEvents()
 	{
			$this->setVar('refID', $this->getParam('refID'));
     		return 'listallEvents_tpl.php';
 	}

	/**
	* Add new event method. Inserts the form data 
	* into the database and returns the list-all events 
	* template.
	*/
	private function __addNewEvent()
 	{
    		$buildingid = $this->getParam('buildingid');
    		$event = $this->getParam('event');
			$date = $this->getParam('date');
			$description = $this->getParam('description');
			$imagename = $this->getParam('imagename');
			$videoname = $this->getParam('videoname');
			
    		$id = $this->objDBEvents->insertSingle($buildingid,$event,$date,$description,$imagename,$videoname);
			
			//Building id is passed to the list all events template to identify relevant events
			$this->setVar('refID', $buildingid);
    		return 'listallEvents_tpl.php';
 	}

	/**
	* Edit events method. Passes the event and 
	* building ID and returns the edit event template
	*/
 	private function __editEvent()
 	{
			//Event ID
			$id = $this->getParam('id');
			//Building ID and name
			$refID = $this->getParam('refID');
			$refbuilding = $this->getParam('refbuilding');
			
    		//Passes all the information to the edit event template
			$this->setVar('id', $id);
			$this->setVar('refID', $refID);
			$this->setVar('refbuilding', $refbuilding);
    		return "editEvent_tpl.php";
 	}

	/**
	* Update event method. Gets updated form data 
	* and modifies the entry in the database. Returns 
	* the list-all events template.
	*/
 	private function __updateEvent()
 	{
    		$id = $this->getParam('id');
    		$buildingid = $this->getParam('buildingid');
    		$event = $this->getParam('event'); 
			$date = $this->getParam('date');
			$description = $this->getParam('description');
			$imagename = $this->getParam('imagename');
			$videoname = $this->getParam('videoname');
			
    		$id = $this->objDBEvents->updateSingle($id,$buildingid,$event,$date,$description,$imagename,$videoname);
			//Pass the building id to the template to identify relevant events
			$this->setVar('refID', $buildingid);
    		return "listallEvents_tpl.php";
 	} 

	/**
	* Delete event method. Gets the event id and 
	* deletes from the database. Returns the list-all 
	* events template.
	*/
 	private function __deleteEvent()
 	{
    		$id = $this->getParam('id');
			
    		$id = $this->objDBEvents->deleteSingle($id); 
			//Pass the building id to the template to identify relevant events
			$this->setVar('refID', $buildingid);
    		return "listallEvents_tpl.php";
 	}
	
	/**Search for building method. Receives a 
	* longitude and latitude coordinates as well as a 
	* compass heading and determines if any 
	*building is in the direction relative to the 
	*coordinates.
	*/
	private function __search()
	{
		$longcoordinate = $this->getParam('longcoordinate');
		$latcoordinate = $this->getParam('latcoordinate');
		$angle = $this->getParam('angle');
		
		$this->objCoordinate->search($longcoordinate,$latcoordinate,$angle);
	}
	
	/**Retrieve events method for a specific building. 
	* Receives a building id which is used as a search 
	* filter parameter to search through the events 
	* database.
	*/
	private function __retrieve()
	{
		$buildingid = $this->getParam('buildingid');
		
		$this->objCoordinate->retrieve($buildingid);
	}
	
	/**
	* Retrieve specific method for specific media 
	* types. Receives an event id and the requested 
	* media type number which it uses to access the 
	* specific media type for the specific event.
	*/
	private function __retrievemedia()
	{
		$eventid = $this->getParam('eventid');
		$num = $this->getParam('num');
		
		$this->objCoordinate->retrievemedia($eventid, $num);
	}
	
	/**
	* Overrides login when accessing from outside.
	*/
	function requiresLogin()
	{
		return false;
	}
	
	/**
	* String output testing function
	*/
	private function __test()
	{
		print "wtf";
		
	}
}
?>