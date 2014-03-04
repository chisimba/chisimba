<?php
/* ----------- data class extends dbTable for tbl_sports------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_sports
* Autheos : Nsabagwa Mary, Kaddu Ismeal
*/
class dbsports extends dbTable
{
	//variable holding the user object
	var $objUser;
	//variable holding the language object
	var $objLanguage;
	
	//Icon object
	var $objGetIcon;
    /**
    * Constructor method to define the table
    */
     function init() 
    {
        parent::init('tbl_sports');
		$this->table = 'tbl_sports';
		// Get an instance of the user object
		$this->objUser = & $this->getObject('user', 'security');
    }
	/**
	* function to a given sport from the table
	* based on the id
	*/
	 function getSportsById($id)
	{
		$sql = 'SELECT tbl_sports.* FROM '.$this->table;
		$sql .= " WHERE id='".$id."'";
		$ar=$this->getArray($sql);
		if($ar) {
			return $ar[0]['name'];
		} else {
			return FALSE;
		}
	}
	/**
	* Return all records
	* @return array The entries
	*/
	 function listAll()
	{
		$sql = "SELECT tbl_sports.* FROM ".$this->table." ";
		return $this->getArray($sql);
	}

	/**
	* function to insert data into the database
	* the parsed parameters are
	* $name - insert into name:tbl_sports
	* $description - insert into description:tbl_sports
	*/		
	 function insertSport($name,$player_no,$description,$evaluation)
	{
		$this->insert(array(
		'name' => $name,
		'player_no' => $player_no,
		'userId' => $this->objUser->fullName(),
		'evaluationMode'=>$evaluation,
		'description' => $description,
		'dateCreated' => strftime('%Y-%m-%d %H:%M:%S', mktime())));
	}
	
	/**
	* function to update data within the database
	* the parsed parameters are
	* $id - update based on the id
	* $name - update name:tbl_sports
	* $description - update description:tbl_sports
	*/
     function updateSport($id,$name,$player_no,$description,$evaluation)
    {
		$this->update("id", $id, array(
			'name' => $name,
			'player_no' => $player_no,
			'userId' => $this->objUser->fullName(),
			'evaluationMode'=>$evaluation,			
			'description' => $description,
			'updated' => date("Y-m-d H:m:s")));
    }
	/**
	* function to remove Sport based on $id
	* that is,sport whose dateEnd field < curdate();
	*/

	 function deleteSport($id)
	{
		$sql = "DELETE FROM ".$this->table." WHERE id='$id'";
		return $this->query($sql);
	}
	/**
	* function to read a specific student's faculty description/comment from the table
	* based on the id
	*/
	 function getSportsDescriptionById($id)
	{
		$sql = 'SELECT tbl_sports.* FROM '.$this->table;
		$sql .= " WHERE id='".$id."'";
		$ar=$this->getArray($sql);
		if($ar) {
			return $ar[0]['description'];
		} else {
			return FALSE;
		}
	}
	
	/**
	* Function to return the evaluation of the sport 
	*/
	 function getevaluation($sportid)
	{  
		$sql = 'SELECT tbl_sports.* FROM '.$this->table;
		$sql .= " WHERE id='".$sportid."'";
		$ar=$this->getArray($sql);
		if($ar) {
			return $ar[0]['evaluationMode'];
		} else {
			return FALSE;
		}
	}
	
	//function to select all the registered sport activities and displaying them
	 function listAllSports(){
	
	$sql = "SELECT tbl_sports.* from ".$this->table;
	$ar = $this->getArray($sql);
	if($ar){
	return $ar;
		
	}else{
	     return FALSE;
		 }
	}
	
	
	
}
?>