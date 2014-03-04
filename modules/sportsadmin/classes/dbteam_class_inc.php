<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/* A class for interacting with tbl_team
*
* @Author Nsabagwa Mary
* @Author Kaddu Ismeal
*/

class dbteam extends dbtable{

 function init(){
parent::init('tbl_team');
$this->table = 'tbl_team';

//instance of the language items
$this->objLanguage = & $this->getObject('language','language');

}

/*  function to find pick the teams registered
*   @param $id - sportid for which the item belongs 
*/
 function getAll($sportid){

$sql = "SELECT tbl_team.* FROM ".$this->table;
$sql .=" WHERE sportId='".$sportid."' ";

$ar = $this->getArray($sql);
return $ar;
}


/*
* A function to save the results entered in form into tbl_team
* @Param $sportId- id of the sport to which the team belongs
* @Param $name - name of the team
* @Param $homeground - the home ground of the team
* @Param $coach- coach of the team
*/
 function saveteam($sportId, $name,$homeground,$filename,$motto){
	$this->insert(
	array(
	'name'=>$name,
	'homeGround'=>$homeground,	
	'logofile'=>$filename,
	'motto'=>$motto,
	'sportId'=>$sportId
	));


}


/*
* Function to pick the name of the team using the id
*/

 function getTeamNameById($id){
$sql = "SELECT * FROM ".$this->table;
$sql .=" WHERE id='".$id."' ";

$ar =  $this->getArray($sql);


if($ar){
   return $ar['0']['name'];
  }
  else return false; 

}

/*
* Function to pick the details of a selected team
*/

 function getTeamDetails($id){

$sql = "SELECT * FROM ".$this->table;
$sql .=" WHERE id='".$id."' ";

$ar =  $this->getArray($sql);

if($ar){
   return $ar;
  }
  

}


/*
* A function to delete an entry in tbl_team
* $param $sportid - Id of the sport to which the tournament belongs
* @ param $teamid - id of the team to be deleted
*/

 function deleteTeam($sportid,$teamid){

$sql = "DELETE FROM ".$this->table." WHERE sportId='".$sportid."' and id='".$teamid."'";
return $this->query($sql);

}
/*
* Function to to return the number of teams registered in a sport
* @param $sportid - id of the sport whose team numbers are required
*/
 function getTeamNumber($sportid){
$sql = "select count(id) as number from ".$this->table." where sportId='".$sportid."'";
$ar = $this->getArray($sql);
return $ar[0]['number'];

}

//function to get the id of the team
 function getTeamId($teamname){

$sql = "SELECT * FROM ".$this->table." WHERE name like '%$teamname%' ";

 $ar = $this->getArray($sql);
 if($ar){
   return $ar[0]['id'];
 }
 else return false;

}

/*
* Function to modify the team information
*/

 function modifyteam($teamid,$name,$homeground,$motto){
//$updatedBy = $this->objUser->userId();
$this->update("id", $teamid, array(
			'name' => $name,
			'homeGround' => $homeground,
			'motto' => $motto
			));

}


}//closing a class
?>