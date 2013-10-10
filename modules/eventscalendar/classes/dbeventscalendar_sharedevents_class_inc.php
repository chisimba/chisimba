<?php



class dbeventscalendar_sharedevents extends dbTable
{
	
	
	public function init()
	{
		
		parent::init('tbl_eventscalendar_sharedevents');
	}
	
	public function addShare($eventId, $catId, $sharedWithId)
	{
	 	if($this->isShared($eventId, $sharedWithId) == FALSE)
	 	{
			$fields = array('eventid' => $eventId,
							'catid' => $catId,
							'sharedwithid' => $sharedWithId);
		
			return $this->insert($fields);		
			
		}
		
	}
	
	public function isShared($eventId, $sharedWithId)
	{
		$recs = $this->getAll("WHERE eventid='".$eventId."' AND sharedwithid='".$sharedWithId."'");
		//var_dump($eventId);
		if(count($recs) > 0)
		{
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function getEventsBySharedId($sharedId)
	{
		return $this->getAll("WHERE sharedwithid='$sharedId'");
		
	}
}