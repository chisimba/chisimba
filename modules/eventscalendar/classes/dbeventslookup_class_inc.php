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
class dbeventslookup extends dbTable
{
    
    /**
     * Constructor
     */
    public function init()
    {
    	
    	
        parent::init('tbl_eventscalendar_lookup');
    }
    
    /**
    * Method to get all the events for a given type
    * @param string $type The type
    * @return array
    * @access public
    */
    public function getEventsByType($type, $typeId = null)
    {
    	$sql = '';
    	if(!empty($typeId))
    	{
    		$sql = ' AND typeId="'.$typeId.'"';
    	} 
    	return $this->getAll ("WHERE type='".$type."'  $sql");
    		
    }
    
    /**
    * MEthod to add an event type
    * @param string $type The type
    * @param string $typeId The type id
	* @param string $eventId The event Id
	*/
    public function add($type, $typeId, $eventId)
    {
    
    		try
        {            
            
            
           
            $fields = array (
                    'type' => $type,
                    'typeid' => $typeId,
                    'eventid' => $eventId
            
            );
           	
            
            return  $this->insert($fields);
            
        }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }
}
?>