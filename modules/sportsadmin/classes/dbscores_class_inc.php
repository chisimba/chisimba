<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/* A class for interacting with tbl_scores
*
* @Author Nsabagwa Mary
* @Author Kaddu Ismeal
*/

class dbscores extends dbtable{


//variable
var $objUser;

 function init(){
parent::init('tbl_scores');
$this->table = 'tbl_scores';

//instance of user class
$this->objUser  =& $this->getObject('user','security');
}

/*  
*   function to get the scores
*  
*/
 function getAll(){

$sql = "SELECT * FROM ".$this->table;
$sql .=" WHERE sportId='".$sportid."' ";

$ar = $this->getArray($sql);
return $ar;

}

/*
* Function to get the scores for a given fixture
*/
 function getFixtureScore($fixtureid){

$sql = "SELECT * FROM ".$this->table." WHERE fixtureId='".$fixtureid."'";

$ar = $this->getArray($sql);
 if($ar){
 return $ar;
 }
 else {return false;}

}

/*
* A function to save the results entered in form into tbl_team
* @Param $sportdd- id of the sport to which the scores are attached
* @Param $teamAscores - score for team a
* @param $teamBscores - score for the second team
* @Param $fixtureid - id of the fixture
* 
*/
 function insertdata($sportid,$tournamentId,$teamid,$fixtureid,$time,$playerid ){

$this->insert(
	array(
	'tournamentId'=>$tournamentId,	
	'time'=>$time,
	'playerId'=>$playerid,
	'teamId'=>$teamid,
	'creationDate'=> date("Y-m-d"),
	'fixtureId'=>$fixtureid,
	'sportId'=>$sportid,
	'enteredBy'=>$this->objUser->userId()
	));
}

/*
* Function to get the scores for the player in a fixture
* @param $fixtureid- fixture 
* @param $playerid- id of the player
*/

 function getscoresForPlayer($playerid,$fixtureid){
	$sql = "select count(playerId) as scores from ".$this->table." where playerId= '$playerid' and fixtureId='$fixtureid'";
	$ar = $this->getArray($sql);
	return $ar[0]['scores'];
}


//functon to get the total number of goals for a team in a tournament
 function getGoalForTeam($tournamentid,$teamid){
$sql = "select count(teamId) as goals from ".$this->table." where tournamentId='$tournamentid' and teamId='$teamid' ";
$ar = $this->getArray($sql);
return $ar[0]['goals'];

}


///function to get the number of wins in a tournament
function getWinsForTeam($teamid,$tournamentid){

$w =0;//to store the wins for a team

//create an instance of a class
$this->objDbfixtures = $this->getObject('dbfixtures');

//get fixtures in which team is participating
$fixturelist = $this->objDbfixtures->getAllFixturesForTournament($teamid,$tournamentid);

if(!empty($fixturelist)){
foreach($fixturelist as $f){

$opponent = $this->objDbfixtures->getOpponent($teamid,$f['id']);

$teamscores = $this->getscoresForteam($teamid,$f['id']);
$opponentscores = $this->getscoresForteam($opponent,$f['id']);

//check the diffence between the two scores
$scorediff = $teamscores - $opponentscores;

print_r ($scorediff);
//if scorediff is positive, then it is a win, if negative, loss and if zero is a draw
	 if($scorediff>0){
	 $w +=1;
	 }
	 
}
}
return $w;


}//closing the function



/*function to get the numbet of losses for a team in all the fixtures it is participating
* In one tournament
*/
 function getLossesForTeam($teamid,$tournamentid){

$l= 0; //to store the losses for the team

//create an instance of a class
$this->objDbfixtures = $this->getObject('dbfixtures');

//get fixtures in which team is participating
$fixturelist = $this->objDbfixtures->getAllFixturesForTournament($teamid,$tournamentid);
if(!empty($fixturelist))
foreach($fixturelist as $f){

$opponent = $this->objDbfixtures->getOpponent($teamid,$f['id']);

$teamscores = $this->getscoresForteam($teamid,$f['id']);
$opponentscores = $this->getscoresForteam($opponent,$f['id']);
//check the diffence between the two scores
$scorediff = $teamscores - $opponentscores;


//if scorediff is positive, then it is a win, if negative, loss and if zero is a draw
	 if($scorediff<0){
	 $l +=1;
	 }
	 
}

return $l;


}//closing the function


//function to get the number of draws for a team

 function getDrawsForTeam($teamid,$tournamentid){

$d= 0; //to store the losses for the team

//create an instance of a class
$this->objDbfixtures = $this->getObject('dbfixtures');

//get fixtures in which team is participating
$fixturelist = $this->objDbfixtures->getAllFixturesForTournament($teamid,$tournamentid);

if(!empty($fixturelist)){
foreach($fixturelist as $f){
$opponent = $this->objDbfixtures->getOpponent($teamid,$f['id']);

$teamscores = $this->getscoresForteam($teamid,$f['id']);
$opponentscores = $this->getscoresForteam($opponent,$f['id']);

//check the diffence between the two scores
$scorediff = $teamscores - $opponentscores;

//if scorediff is zero
	 if($scorediff==0){
	 $d +=1;
	 }
	} 
}

return $d;


}//closing the function


//function to get the score details for the match 
 function getscoredetails($teamid,$fixtureid){
$sql = "select * from ".$this->table." where teamId='$teamid' and fixtureId='$fixtureid'";
$ar = $this->getArray($sql);
if($ar){
  return $ar;
} 
 else
 
   return false;
}




/*
* Function to get team total in a tournament
* @param teamid - id of the team 
*@param $fixtureid - fixture in questin
*/
 function getscoresForteam($teamid,$fixtureid){

	$sql = "select count(playerId) as scores from ".$this->table." where teamId= '$teamid' and fixtureId='$fixtureid'";

	$ar = $this->getArray($sql);
	return $ar[0]['scores'];
	}

}

?>