<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
* Class to access and manipulate database of icon requests
* @author Nic Appleby 
* 
* $Id: requesttable_class_inc.php, v 1.0 2006/01/05 09:42:39
*/

class requestTable extends dbTable {
    
    /**
    * Constructor to link to table
    */
    function init() {
	parent::init('tbl_request');
    }
    
    /**
    * Function to insert record into database
    * @param request $request the icon request to be inserted
    * @return string|FALSE Generated PKID on success, FALSE on failure
    */
    function insertRec($request) {
    	$newReq = array('reqid' => $request-> reqid,
			'modname' => $request->modname,
			'priority' => $request->priority,
			'type' => $request->type,
			'phptype' => $request->Phpversion,
			'iconname' => $request->iconname,
			'description' => $request->description,
			'uri1' => $request->uri1,
			'uri2' => $request->uri2,
			'complete' => $request->complete,
			'uploaded' => $request->uploaded,
			'time' => Null);
	//var_dump($newReq);
	return $this->insert($newReq);
    }
    
    /**
    * Function to update the percentage complete of a record in the DB
    * @param string $pk the primary key of the record to be changed
    * @param int $pt the new percentage complete
    * @return TRUE|FALSE TRUE on success FALSE on failure
    */
    function updatePerc($pk,$pt) {
    	$rec = $this->getRow('id',$pk);
    	$rec['complete'] = $pt;
    	return $this->update('id',$pk,$rec); 
    }
    
    /**
    * Function to delete a record from the database
    * @param string $pk primary key of record to be deleted
    * @return TRUE|FALSE TRUE on success FALSE on failure
    */
    function deleteRec($pk) {
    	return $this->delete('id',$pk);
    }

}
 
?>
