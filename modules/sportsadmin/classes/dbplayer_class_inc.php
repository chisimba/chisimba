<?php
/* ----------- data class extends dbTable for tbl_sports------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_player
*/
class dbplayer extends dbTable {

 var $objUser;
 var $objLanguage;

	 function init(){
		parent::init('tbl_player');
		$this->table = "tbl_player";

		$this->objUser = & $this->getObject('user','security');
		$this->objLanguage = & $this->getObject('language','language');
	}

/*
* Function to pick all the entries from tbl_player
*
*/
 function geAll(){

$sql = "SELECT tbl_player.* FROM ".$this->table." ";
$ar = $this->getArray($sql);
if($ar){
return $ar;

}

else 

 return FALSE;

}


//function to get all the players for a specific sport
 function getPlayersForSport($sportid){
//echo $sportid;
$sql = "SELECT * FROM ".$this->table." WHERE sportId='".$sportid."'";


$ar = $this->getArray($sql);
if($ar){
return $ar;
}

else 

 return FALSE;


}

/*function to get the details of the player 
* @param - $playerid  id of the player whose details are being picked
*/
 function getPlayerDetails($playerid){
$sql = "SELECT * FROM ".$this->table." WHERE id='".$playerid."'";
$ar = $this->getArray($sql);
if($ar){
return $ar;
    }

}

/*function to get the details of the player 
* @param - $playerid  id of the player whose details are being picked
*/
 function getPlayerNameById($playerid){
$sql = "SELECT * FROM ".$this->table." WHERE id='".$playerid."'";
$ar = $this->getArray($sql);
if($ar){
	return $ar[0]['name'];
    }
else 
	return false;

}


/*
* Function to insert vales entered for player into tbl_player
* @param $name - Name of the player
* @param $team - the team where player is stationed
* @param $country- country of origin of the player
* @param $dob - Date of birth of the player
* @param  $position - Role of player in the team(eg captain, coach,stricker,etc)
* @param  $sportid  - id of the sport to which the player is registered
*/
 function saveplayer($name,$team ,$country, $dob ,$position, $sportid,$imagefile ){

$this->insert(array(
	'name'=>$name,
	'sportId'=> $sportid ,
	'team'=>$team,
	'country'=>$country,
	'dateOfBirth'=>$dob,
	'playerimage'=>$imagefile,
	'position'=>$position
)
);

}

/*function to get members of a given team
*
* Param $teamid - the unique identification of the selected team
*/
 function getTeamMembers($teamid,$sportid){
$sql =" SELECT * FROM ".$this->table." WHERE team='".$teamid."' and sportId='".$sportid."' ";
$ar = $this->getArray($sql);
if($ar){
     return $ar;
}
else  
	return false;
}

/*
* Function to pick a list f all players in a specific team
* @Author Nsabagwa Mary
* @param $teamid - the id of the team whose players are required
*/

 function getPlayersForTeam($teamid){

$sql = " SELECT * FROM ".$this->table." WHERE team='".$teamid."'";
$ar  = $this->getArray($sql);
 if($ar){
   return $ar;
 }
 else 
	return false;

}

/*
* Function to search for a player and return the results
* @Param $searchfield - the data that is being searched for 
* @param $option- search criteria a corresponding column name in tbl_player
*/
 function searchForPlayer($searchfield,$option){

$sql = "SELECT * FROM ".$this->table." where ".$option." like '%$searchfield%' ";

$ar = $this->getArray($sql);

	if($ar){
	   return $ar;
	}
	else 
	  return false;
	
	}
	
	
	/*
	Function to delete the selected player
	* @param playerid - the id of the player to be deleted
	*/
	
	 function deleteplayer($playerid){
	$sql = "DELETE FROM ".$this->table." WHERE id='".$playerid."'";
    $res = $this->getArray($sql);
   if($sql){
     return true;
   }
  else return false;	
	
	}
	
 function modifyplayer($playerid,$position,$name,$team,$country){
	$updatedBy = $this->objUser->userId();
       $this->update("id", $playerid, array(
			'country' => $country,
			'position' => $position,
			'name'=>$name,
			'team'=>$team,
			'updated' => date("Y-m-d H:m:s")));
	
	
}
//function to return the total number of players for a given sport 
 function numberOfPlayers($sportid){
$sql = "select count(sportId) as playerno from ".$this->table." where sportId= '$sportid' ";
	$ar = $this->getArray($sql);
	return $ar[0]['playerno'];


}


     /**
    * method to create specified folder
    * @access public
    * @param string $folder The folder that needs to be created
    */
    function makeFolder($folder_name)
    {     
            $oldumask = umask(0);
            $ret = mkdir($folder_name, 0777);
            umask($oldumask);
        
       
        return $ret;
    }
	
	
function uploadfile($folder){

$this->objConfig = & $this->getObject('config','config');

 if (is_uploaded_file($_FILES['playerimage']['tmp_name'])) {
   $file_name = $_FILES['playerimage']['name'];   
   $tmp = $_FILES['playerimage']['tmp_name'];
   $size = $_FILES['playerimage']['size'];    
  
   $folderpath = $folder."/".$file_name;     
   $path = str_replace('\\', '/',$folderpath);  
  
   $path_info = pathinfo($file_name);
   $extn = $path_info['extension'];
	 //check extensions
    switch($extn){ 
			case "jpeg":
				case "gif":
				case "png":
				case "tiff":
				case "psd":
				case "swf":
				case "bmp":
				case "jpg":
				case "JPG":
						
			 $res= move_uploaded_file($tmp,$path);
			break;
		
		}//closing the switch	
	 if($res){

        return true;
  }
  else return false;	
}//closing if not uploaded

}



}//closing the class