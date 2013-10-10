<?php
/* ----------- data class extends dbTable for tbl_calendar------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Model class for the table tbl_eventscalendar
*
* @author Wesley Nitsckie
* @copyright (c) 2005 University of the Western Cape
* @package eventscalendar
* @version 1
*
* 
*/
class dbeventscalendar extends dbTable
{
	
	/**
	* object $_objDBLookup;
	*/
	protected $_objDBLookup;
	
    
    /**
     * Constructor
     */
    public function init()
    {
    	
    	$this->_objUser = & $this->getObject('user', 'security');
        parent::init('tbl_eventscalendar');
       // $this->_objDBLookup = & $this->newObject('dbeventslookup', 'eventscalendar');
    }
    
    
    /**
     * Method to get the events for a givent user
     * @param string $catId $the User Id
     * @return array
     * @access public
     * 
     */
    public function getEventsByType($catId, $month = 0 , $yr = 0)
    {
    	
    	//get the events for the type
    	//$arrTypeEvents = $this->_objDBLookup->getEventsByType($type, $typeId);
       print 'THE STUPID FUNCTION';
          
    	$arrTypeEvents = $this->getAll("WHERE catid='".$catId."'");
//var_dump($arrTypeEvents); //die;
    	$events = array();
    	
    	if($yr == 0 )
    	{
    		$yr = date("Y");
    	}
    	
    	if($month == 0 )
    	{
    		$month = date("m");
    	}
    	
    
    	//get the start of the month
    	$startOfMonth = mktime(0,0,0,$month,1,$yr);
    
    	$lastDayOfTheMonth = date("t",mktime(23,59,59,$month, 0, $yr));
    	
    	//get the end of the month
    	$endOfMonth = mktime(23,59,59, $month , $lastDayOfTheMonth , $yr);
    	
    	
    	//get the events for the user for the given month and year
   		
    	foreach($arrTypeEvents as $arrTypeEvent)
    	{
    		$tempRow = $this->getRow('id', $arrTypeEvent['eventid']);	
    		if($tempRow['event_date'] > $startOfMonth && $tempRow['event_date'] < $endOfMonth)
    		{
    			$events[] = $tempRow;
    		}
    	}
        
        
    	if($events)
    	{
    		return $this->sortmddata($events , 'event_date', 'ASC', 'num');
    	} else {
    		return $events;
    	}
    	$sql = "WHERE event_date BETWEEN  ".$startOfMonth." AND  ".$endOfMonth;
  
        return $this->getAll($sql);
    }

    /**
    * Method to get list of events by Category
    * @param string $catId
    * @return array
    * @access public
    */
    public function getEventsByCategory($catId, $month = 0 , $yr = 0)
    {
        try{
           
            //print $catId;
            $arrTypeEvents = $this->getAll("WHERE catid='".$catId."'");

			

            $events = array();
            
            if($yr == 0 )
            {
                $yr = date("Y");
            }
            
            if($month == 0 )
            {
                $month = date("m");
            }
            
        
            //get the start of the month
            $startOfMonth = mktime(0,0,0,$month,1,$yr);
        
            $lastDayOfTheMonth = date("t",mktime(23,59,59,$month, 0, $yr));
            
            //get the end of the month
            $endOfMonth = mktime(23,59,59, $month , $lastDayOfTheMonth , $yr);
            
            
            //get the events for the user for the given month and year
         
            foreach($arrTypeEvents as $arrTypeEvent)
            {
                $tempRow = $this->getRow('id', $arrTypeEvent['id']);   
                if($tempRow['event_date'] > $startOfMonth && $tempRow['event_date'] < $endOfMonth)
                {
                    $events[] = $tempRow;
                }
            }
            
            $objShares = $this->getObject('dbeventscalendar_sharedevents', 'eventscalendar');
            $sharedEvents = $objShares->getEventsBySharedId($catId);
            
            
            //the shared events
            foreach($sharedEvents as $sharedEvent)
            {
				$tempRow = $this->getRow('id', $sharedEvent['eventid']);   
                if($tempRow['event_date'] > $startOfMonth && $tempRow['event_date'] < $endOfMonth)
                {
                    $events[] = $tempRow;
                }
			}
          
            
            if($events)
            {
                return $this->sortmddata($events , 'event_date', 'ASC', 'num');
            } else {
                return $events;
            }
           
        } 
        catch (customException $e)
        {
            echo customException::cleanUp($e);
            die();
        }
    }
    
    /**
     * Method to insert an event
     * @param catId The category
     * @return boolean
     * @access string the new id
     */
    public function addEvent($catId)
    {
        try
        {            
            $title = $this->getParam('title');
            $description = $this->getParam('description');
            $start_time = $this->getParam('start_time');
            $end_time = $this->getParam('end_time');
          
            $start_date = $this->getParam('start_date');
            $location = $this->getParam('location');
            
            $arrStart_date = spliti('-', $start_date);
            
            $start_date = mktime(0,0,0,$arrStart_date[1], $arrStart_date[2], $arrStart_date[0] );
            
            $fields = array (
                    'title' => $title,
                    'description' => $description,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'event_date' => $start_date,
                    'catid' => $catId,
                    'location' => $location
            
            );
           	
            
            $id =  $this->insert($fields);
          
            return $id;
        }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }
    
    /**
     * Method to edit an event
     * @param eventId The event Id
     * @return boolean
     * @access public
     */
    public function editEvent($eventId)
    {
        try 
        {
            $title = $this->getParam('title');
            $description = $this->getParam('description');
            $start_time = $this->getParam('start_time');
            $end_time = $this->getParam('end_time');
            $catid = $this->getParam('catid');
            $start_date = $this->getParam('start_date');
            $location = $this->getParam('location');
            
            $fields = array (
                    'title' => $title,
                    'description' => $description,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'start_date' => $start_date,
                   
                    'location' => $location
            
            );
            
            return $this->update('id', $eventId, $fields);
        }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
        
    }
    
    /**
    * Method to get an event for a given date
    * @param $year The year
    * @param $month The month
    * @param $day The day
    * @return array|boolean
    * @access public
    */
    public function getEvents($year, $month, $day)
    {
    	//$line = $this->getEventsByType('user', $this->_objUser->userId());
    	//$lines = $this->getUserEvents($this->_objUser->userId());
    	/*foreach ($lines as $line)
    	{
    			print $line['event_date'];;
    			
    	}
*/
    	//mktime(0,0,0,$month, $day, $year);	
    }
    
    
    /**
    * Method to sort the events
    */
    
	function sortmddata($array, $by, $order, $type)
	{
	
		//$array: the array you want to sort
		//$by: the associative array name that is one level deep
		////example: name
		//$order: ASC or DESC
		//$type: num or str
		      
		$sortby = "sort$by"; //This sets up what you are sorting by
		
		$firstval = current($array); //Pulls over the first array
		
		$vals = array_keys($firstval); //Grabs the associate Arrays
		
		foreach ($vals as $init)
		{
		   $keyname = "sort$init";
		   $$keyname = array();
		}
		//This was strange because I had problems adding
		//Multiple arrays into a variable variable
		//I got it to work by initializing the variable variables as arrays
		//Before I went any further
		
		foreach ($array as $key => $row) 
		{
			  
			foreach ($vals as $names)
			{
			   $keyname = "sort$names";
			   $test = array();
			   $test[$key] = $row[$names];
			   $$keyname = array_merge($$keyname,$test);
			  
			}
		
		}
		
		//This will create dynamic mini arrays so that I can perform
		//the array multisort with no problem
		//Notice the temp array... I had to do that because I
		//cannot assign additional array elements to a
		//varaiable variable           
		
		if ($order == "DESC")
		{   
			if ($type == "num")
			{
			array_multisort($$sortby,SORT_DESC, SORT_NUMERIC,$array);
			} else {
			array_multisort($$sortby,SORT_DESC, SORT_STRING,$array);
			}
		} else {
			if ($type == "num")
			{
			array_multisort($$sortby,SORT_ASC, SORT_NUMERIC,$array);
			} else {
			array_multisort($$sortby,SORT_ASC, SORT_STRING,$array);
			}
		}
	
		//This just goed through and asks the additional arguments
		//What they are doing and are doing variations of
		//the multisort
	
		return $array;
	}
	
	
	/**
	 * Method to merge context and personal 
	 * events
	 * 
	 * @param 
	 * @access public
	 * 
	 */
	public function mergeEvents()
	{
	    try {
    	    $userId = $this->_objUser->userId();
    	    $objContext = & $this->getObject('dbcontext','context');
    	    $objCat = & $this->getObject('dbeventscalendarcategories','eventscalendar');
    	    
    	    $contextCode = $objContext->getContextCode();
    	    $ids = $objCat->getCatId('context',$contextCode);
    	    $contextEventsArr = $this->getAll("WHERE catId='".$ids."'");
    	    $userIdCatId =  $objCat->getCatId('user',$userId);
    	    
    	    foreach ($contextEventsArr as $event)
    	    {
    	        $fields = array (
                        'title' => $event['title'],
                        'description' => $event['description'],
                        'start_time' => $event['start_time'],
                        'end_time' => $event['end_time'],
                        'event_date' => $event['event_date'],
                        'catid' => $userIdCatId,
                        'location' => $event['location'],
                        'contextevent_id' => $event['id']            
                );           	
               // print_r($fields);
               // die;
               // print $this->insert($fields);
    	    }
    	    
    	    
	    }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
	}
}