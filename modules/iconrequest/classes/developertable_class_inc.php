<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
* Class to access and manipulate database storing information of the icon developer
* @author Nic Appleby 
* 
* $Id: developertable_class_inc.php, v 1.0 2006/01/05 09:42:39
*/

class developerTable extends dbTable {
    
    /**
    * constructor to connect to database
    */
    function init() {
	 parent::init('tbl_developer');
    }
    
    /**
    * function to insert a record into the database
    *
    * @param string $name The name of the icon developer
    * @param string $email The email address of the developer
    *
    * @return string |FALSE Generated PK ID on success, FALSE on failure
    */
     function insertRec($userId) {
    	$newReq = array('id' => $userId);
	   //$newReq = array('Id' => $userId);
	   return $this->insert($newReq);
    }
    
    /**
    * Function to update the record containing the icon developer information
    *
    * Inserts a record into the table if it is empty or updates the record if it exists
    *
    * @param string $name The name of the icon developer
    * @param string $email The email address of the developer
    *
    * @return string|TRUE|FALSE Generated PKID on insert success, TRUE on update success, FALSE on failure
    */
     function updateRec($userId) {
    	$newReq = array('id' => $userId);
      //$newReq = array('Id' => $userId);
    	$pkID = $this->getPK();
	   return ($this->isEmpty())? $this->insert($newReq) : $this->update('id',$pkID,$newReq);
     }
    
    /**
    * Function to find the primary key of the record of the icon developer
    */
    function getPK() {
    	$ret = "";
    	if (!$this->isEmpty()) {
		$dev = $this->getAll();
		foreach ($dev as $rec) {
			$ret = $rec['id'];
			}
		}
    	return $ret;
    }
    
    /**
    * Function to get the developer's UID
    *
    * @return string The ID of icon developer
    */
    function getId() {
    $ret = $this->getRow('id',$this->getPK());
        return $ret['id'];
    }

    /**
    * Function to check whether or not the DB is empty
    *
    * @return TRUE|FALSE TRUE if table is empty, otherwise FALSE
    */
    function isEmpty() {
    	$dev = $this->getAll();
    	return ($dev==null)? true : false;
    }
    
    /**
    * Function to delete the record of the icon developer
    *
    * probably only needed for debugging purposes as we can just update the record
    *
    * @return TRUE |FALSE TRUE on success, FALSE on failure
    */
    function deleteRec() {
    	$pk = $this->getPK();
    	return $this->delete('id',$pk);
    }
}
 ?>
