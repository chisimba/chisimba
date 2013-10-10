<?php
//security chech that must be put on ever page
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/*
* Author : Nsabagwa Mary
*  Class to interact with tbl_match_player
*/

class dbmatchplayer extends dbtable{

 function init(){
	parent::init('tbl_match_players');
	$this->table = "tbl_match_players";

}

 function getAll(){
$sql = "select * from ".$this->table;

$ar = $this->getArray($sql);
if($sql){
return $ar;
}
else return false;

}


 function saveplayer($n,$teamid,$fixtureid,$tournamentid,$sportid){
$sql = $this->insert(
   array(
   'playerId'=>$n,
   'teamId'=>$teamid,
   'fixtureId'=>$fixtureid,
   'tournamentId'=> $tournamentid,
   'sportId'=> $sportid   
   ) );
   
  if(!$sql){
  return false;
  }

}


/*
* function to get the players for a given match in a fixture
* @param $teamid - id of th team to which the player belongs
* @param $fixtureid - fixture to witch the match belongs
*/
 function getPlayersForTeamInFixture($teamid,$fixtureid){
$sql = "select  playerId from ".$this->table." where teamId='".$teamid."' and fixtureId='".$fixtureid."'";
$ar = $this->getArray($sql);
if($sql){
  return $ar;
}

else 
  return false;

}

//function to check if team has players in a agiven match
 function playersExist($teamid,$fixtureid){
$sql = "select * from ".$this->table." where teamId='$teamid' and fixtureId='$fixtureid'";
return $this->getArray($sql);
}


//function to return members of a team not yet in the fixture/match
 function getTeamMembersNotInMatch($teamid,$fixtureid){
$t = "tbl_player";
$s = "tbl_match_players";

$sql = "select $t.id,name from $t where $t.team = '$teamid'";
$ar = $this->getArray($sql);
$sql = "select  $s.playerId as id from $s where teamId='".$teamid."' and fixtureId='".$fixtureid."'";
$ar2 = $this->getArray($sql);

$sql1="SELECT name,id from tbl_player where ";
foreach($ar as $r) 
	foreach($ar2 as $rr)
		if($r[id] == $rr[id])
			$sql1 .=" id != '$r[id]' AND";
if (strlen($sql1) >37)
	$sql1 = substr($sql1,0,strlen($sql1)-3);
if(strlen($sql1) >37)
	$sql1.= " And ";
$sql1 .="$t.team = '$teamid'";
$ar3 = $this->getArray($sql1);

if($ar3){
  return $ar3;
}
else 
 return false;
}

/*
* Function to delete the players from a match
$ @param $playerid- id of the player to be deleted
*/
 function removePlayer($playerid){
$sql = "DELETE FROM ".$this->table." WHERE playerId='".$playerid."'";
	return $this->query($sql);
}


}//closing the class


?>