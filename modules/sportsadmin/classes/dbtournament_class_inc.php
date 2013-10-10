<?php
/*
* Class for interaction with tbl_tournament
* @author Nsabagwa Mary 
* @author Kaddu Ismeal
*
*/

class dbtournament extends dbTable{

public $table;

public $objUser;

//language object
public $objLanguage;


public function init(){
parent::init('tbl_tournament');
$this->table = 'tbl_tournament';


//instance of user class
$this->objUser =& $this->getObject('user','security');

$this->objLanguage =& $this->getObject('language','language');

}


public function getAll(){
 $sql = "SELECT * FROM ".$this->table;
 
 $ar = $this->getArray( $sql);
 if( $ar){
   return $ar;
 }
else 
   return false;
}

public function getTournamentsById($sportid){

$sql = "SELECT * FROM ".$this->table;
$sql .= " WHERE sportId='".$sportid."'";
return $this->getArray($sql);

}

//function to get the tournament by its id
public function getTournamentdetails($sportid,$tournamentid){
$sql = "SELECT * FROM ".$this->table." WHERE sportId ='".$sportid."' and id='".$tournamentid."' ";
$ar = $this->getArray($sql);
if($ar){
return $ar[0]['name'];

}
else 

 return FALSE;

}
//getTournamentNameById
//function to get the tournament name by its id
public function getTournamentNameById($id){
$sql = "SELECT * FROM ".$this->table." WHERE id ='".$id."' ";
$ar = $this->getArray($sql);
if($ar){
return $ar[0]['name'];

}
else 

 return FALSE;

}

//function to get the tournament by its id
public function pickTournamentduration($sportid,$tournamentid){

$sql = "SELECT * FROM ".$this->table." WHERE sportId ='".$sportid."' and id='".$tournamentid."' ";
$ar = $this->getArray($sql);
if($ar){

return $ar;

}
else 

 return FALSE;

}



/*
*  Sending the submitted tournament information into tbl_tournament
*
*
*/
public function saveTournament($tournament,$sportId,$sponsor,$enddate,$startdate){
$this->insert(array(
	'name' => $tournament,
	'sportId' => $sportId,
	'sponsorName'=> $sponsor,
	'creator'=> $this->objUser->fullname($this->objUser->userId()),
    'startDate' => $startdate,
	'endDate'=> $enddate,
	
	
	));


}


/*
* A function to delete an entry in tbl_tournament
* $param $sportid - Id of the sport to which the tournament belongs
* @ param $tournamentid id of the tournament to be deleted
*/

public function deleteTournament($sportid,$tournamentid){

$sql = "DELETE FROM ".$this->table." WHERE sportId='".$sportid."' and id='".$tournamentid."'";
return $this->query($sql);

}


/**
	* function to update data within the database
	* the parsed parameters are
	* $tournamentid - tournament id
	* $sportId - id of the sport
	* $sponsor - id of the sponsor of the tournamnt
	* $enddate - End date
	* $startdate - Start date
	* 
	*/
    public function updateTournament($tournamentid,$sponsor,$enddate,$startdate,$tournamentname)
    {
		$this->update("id", $tournamentid, array(
			'name' => $tournamentname,
			'updated' => $this->objUser->fullName(),
			'sponsorName' => $sponsor,
			'startDate'=>$startdate,
			'endDate'=>$enddate));
    }


}
?>