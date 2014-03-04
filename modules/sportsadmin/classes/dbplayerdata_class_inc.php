<?php 
//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
*  Playerdata class
* @copyright 2006 KEWL.NextGen
* @author Nsabagwa Mary
*
* 
*/

class dbplayerdata extends dbtable{


//user object 
var $objUser;

 function init(){
parent::init('tbl_playerdata');
$this->table = 'tbl_playerdata';

$this->objUser = & $this->getObject('user','security');
}



/*
* Function to add player information to the database
* @param = playerid
*/
 function addplayerinfo($playerid,$event){
$enteredBy = $this->objUser->userId();

$sql = $this->insert(
	array(
	'playerId' => $playerid,
	'enteredBy'=> $enteredBy,
	'event'=> $event	
	
	));

}

//function to pick all the infomation enetered on a specific player
 function getPlayerData($playerid){
  $sql = "SELECT * FROM ".$this->table." WHERE 	playerId='".$playerid."' order by dateEntered desc limit 3 ";
  $ar = $this->getArray($sql);
  if($ar){
   return $ar;
  }
  else return false;
}


//function to pick the playe information using the id
 function getplayerinfo($playerid){
$sql = "SELECT * FROM ".$this->table." WHERE id='".$playerid."'";
$ar = $this->getArray($sql);
if($ar){
  return $ar[0]['event'];
} else return false;

}

/*
* Function to delete the information entered for a player
*/
 function deleteplayerdata($infoid){
$sql = "DELETE FROM ".$this->table." WHERE id='".$infoid."'";
$res = $this->getArray($sql);
 if($sql){
   return true;
 }
else return false;
}

/*
* Function to modify the event entries for a player
* @Param $infoid - id of the event to be modified
*/
 function modifyplayerinfo($infoid,$event){
$updatedBy = $this->objUser->userId();
$this->update("id", $infoid, array(
			'updatedBy' => $updatedBy,
			'event' => $event,
			'updated' => date("Y-m-d H:m:s")));

}


}//end of class


?>