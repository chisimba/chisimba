<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
* Class to access and manipulate database of icon examples
* @author Nic Appleby 
* 
* $Id: filestable_class_inc.php, v 1.0 2006/01/05 09:42:39
*/

class filesTable extends dbTable {
    
    /**
    * Constructor to link to table
    */
    function init() {
	parent::init('tbl_files');
    }
    
    /**
    * Function to insert a file into database
    * @param string $reqId the foregin key for request database
    * @param string $fileName the name of the file on the server
    * @param int $size the size of the file on the server
    * @param string $user the user ID of the owner
    * @return string|FALSE Generated PKID on success FALSE on failure
    */
    function insertFile($reqId,$fileName,$size,$user) {
    	$newReq = array('reqid' => $reqId,'userid' => $user,'fileName' => $fileName,'size' => $size);
	return $this->insert($newReq);
    }
    
    /**
    * Function to delete a file from the database
    * @param string $id Request ID of file to be deleted
    * @return TRUE|FALSE TRUE on success FALSE on failure
    */
    function deleteFile($id) {
    	return $this->delete('reqid',$id);
    }

}
 
?>
