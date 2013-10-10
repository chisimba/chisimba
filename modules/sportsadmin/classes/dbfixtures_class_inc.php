<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/* A class for interacting with tbl_fixtures
*
* @Author Nsabagwa Mary
*
*/

class dbfixtures extends dbtable{

function init(){
parent::init('tbl_fixtures');
$this->table = 'tbl_fixtures';

//instance of the language items
$this->objLanguage = & $this->getObject('language','language');

}

/*
* Function to insert fixture data into tbl_fixtures
* @ param $sportId- Identfier of th sport
* @ param $teamA- team one in fixture
* @param $place- field or place to play from
* @ param $startDate - Date when match will take place
* @parm  $time - start time of the match
*/

function insertFixture($sportId,$teamA,$teamB,$place,$startDate,$tournamentId){

$this->insert(
array(
 'team_A'=> $teamA,
 'team_B'=>$teamB,
 'matchDate'=> $startDate,
 'place'=>$place,
  'sportId'=>$sportId,
 'tournamentId'=>$tournamentId

));

}

/*
* Function to delete the a fixture
* @param  $sportid - id of the sport from where the fixture is t be deleted
* @param  $fixtureId - id of the fixture to delete

*/

function deletefixture($sportid,$fixtureId){

$sql = "delete from ".$this->table." where sportId ='".$sportid."' and id='".$fixtureId."' "; 

return $this->query($sql);

}

//function to get fixtures by sports id
function getFixturesBySportId($sportid){

$sql = "SELECT * from ".$this->table;
$sql .=" WHERE sportId='".$sportid."'";

$ar = $this->getArray($sql);

if($ar){
return $ar;
}
else 

return false;


}

function getFixtureforgame($sportid,$fixtureid){

$sql = "SELECT * from ".$this->table;
$sql .=" WHERE sportId='".$sportid."' and id='".$fixtureid."'";

$ar = $this->getArray($sql);

if($ar){
return $ar;
}
 else 

    return false;

}

//function to modify a fixture
function modifyfixture($fixtureid,$place,$startDate){

$this->update("id", $fixtureid, array(
			'place' => $place,
			'matchDate' => $startDate,
			'updated' => date("Y-m-d H:m:s")));
}



//function to get name of fixture by id
function getFixtureById($id){

$sql = "SELECT * FROM ".$this->table ." WHERE id='".$id."'";

$ar = $this->getArray($sql);

if($ar){
 return $ar;

}

else 
  return false;

}

//function to pick all the fixtures of the tournament
function getFixturesForTournament($tournamentid,$sportid){
$sql = "SELECT * FROM ".$this->table." WHERE sportId= '".$sportid."' and  tournamentId='".$tournamentid."'";
$ar = $this->getArray($sql);
if($ar){
return $ar;

}

else 

 return FALSE;

}

/*
* Function to get the fixtures of thet team
*
* @ Author Nsabagwa Mary
* @param $teamid - name of the team whose fixture are being searched
*/

function getAllFixturesForTournament($teamid){
$sql = "SELECT * FROM ".$this->table." WHERE team_A= '".$teamid."' or team_B='".$teamid."'";
$ar = $this->getArray($sql);
 if ($ar){
   return $ar;
 
 }
  else return false;
}



/*
* function to pick all the teams participating in a tournament
* @param - $tournamentid - id of the tournament to which the teams that
*  are being searched for are attached
*/
function getTournamentteams($tournamentid){
$sql = "select team_A, team_B from ".$this->table." where tournamentId='$tournamentid'";
$ar = $this->getArray($sql);
if($ar){
  return $ar;
}

else 
    return false;


}


//function to pick the opponent of a team

function getOpponent($teamid,$fixtureid){
$sql = "select * from ".$this->table." where team_A='$teamid' or team_B='$teamid'  and id='$fixtureid'";
$ar = $this->getArray($sql);
if(!$sql){return false;}
	if($ar[0]['team_a']==$teamid){
	  return $ar[0]['team_b'];
	}
	else 
	  return $ar[0]['team_a'];

}

 

}//closing the class


?>