<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/* A class for interacting with tbl_gameinfo
*
* @Author Nsabagwa Mary
* @Author Kaddu Ismeal
*/

class dbgameinfo extends dbtable{

	 function init(){
		parent::init('tbl_gameinfo');
		$this->table = 'tbl_gameinfo';

		//instance of the language items
		$this->objLanguage = & $this->getObject('language','language');

	}

/*  function to find out if there are fixtures for a certain sport
*   @param $id - sportid for which the item belongs 
*/
	 function getAll($sportid){

		$sql = "SELECT * FROM ".$this->table;
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
	 function insertdata($sportid,$tournamentId,$teamAId,$teamBId ,$teamAscores,$teamBscores){
		$this->insert(
			array(
				'tournamentId'=>$tournamentId,
				'teamAId'=>$teamAId,
				'teamBId'=>$teamBId,
				'teamAscores'=>$teamAscores,
				'teamBscores'=>$teamBscores,
				'creationDate'=> date("Y-m-d"),
				'sportId'=>$sportid
			)
		);
	}
}//close class

?>