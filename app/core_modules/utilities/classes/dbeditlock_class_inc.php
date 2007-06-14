<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

// end security check

/**
 * Class to keep track of locked edits for the Chisimba app.
 * Please note, this is NOT a replacement for rowlocking, but rather a way to track
 * users that are working on records at the application level.  IE, it does not 
 * provide protection at the database level
 * @author Ryan Whitney, ryan@greenlikeme.org 
 * @author Kevin Cyster 
 * @copyright GNU, 2007
 */
class dbeditlock extends dbTable 
{
    /**
    * @var string $table The name of the database table to be affected
    * @access private
    */
    private $table;

	/**
    * Method to define the table
    * 
    * @access public
    */
    public function init()
    {
        parent::init('tbl_edit_lock');
        $this->table = 'tbl_edit_lock';
    } 
  
    /**
    * Method that returns whether a edit is locked or not 
    *
    * @access public
    * @param string $tableName: The name of the table the row is in
    * @param string $rowid: The id of the row to evaluate
    * @returns boolean Whether edit is locked or not
    */
    public function isEditLocked ($tableName, $rowid)
    {
        // if a record exists then the edit is locked
    	//return $this->valueExists('rowid', $rowid);
		$sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE table_name = '".$tableName."'";
        $sql .= " AND rowid = '".$rowid."'";
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
	}

	/**
	* Method that returns whether the user has locked the record or not.  A return of FALSE
	* does not mean the file isn't locked, it just means that the user does not have a lock on the file.
	* For accuracy, the user should use isEditLocked as well.
	* 
	* @access public
	* @param string $tableName: The name of the table the row is in
	* @param string $rowid: id for the record in question
	* @param string $userid: id for the user we're checking for
	* @returns boolean TRUE if the user owns the lock, FALSE if they don't or the file isn't locked
	*/
	public function isEditLockedByUser($tableName, $rowid, $userid)
	{
		// we just try to get the pkid, if it doesn't exist then the user doesn't have a lock on the record
		if($this->_getpkID($tableName, $rowid, $userid)){
			return TRUE;
		}
		//else
		return FALSE;
	}

	/**
    * Method that locks a edit 
    *
    * @access public
	* @param string $tableName: The name of the table the row is in
	* @param string $rowid: id for the record in question
	* @param string $lockownerid: id for the user who is locking the record
    * @returns boolean TRUE or FALSE depending if edit was successfully 'locked' 
    */
	public function lockEdit ($tableName, $rowid, $lockownerid)
	{
		// If the user already has the record locked then just update the time
		if($this->isEditLockedByUser($tableName, $rowid, $lockownerid))
		{
			$id = $this->_getpkID($tableName, $rowid, $lockownerid);
	  		$fields = array();
			$fields['datelocked'] = date('Y-m-d H:i:s');
			return $this->update('id', $id, $fields);
		}

		// If the user does not own the record, but its still locked, return false
		if($this->isEditLocked($tableName, $rowid))
		{
			return FALSE;
		}

		// else record the lock
	  	$fields = array();
    	$fields['table_name'] = $tableName;
    	$fields['rowid'] = $rowid;
    	$fields['lockownerid'] = $lockownerid;
		$fields['datelocked'] = date('Y-m-d H:i:s');

		// Run insert
    	return $this->insert($fields);
	}
	
	/**
    * Method that unlocks a edit  
    *
    * @access public
	* @param string $tableName: The name of the table the row is in
	* @param string $rowid: id for the record in question
	* @param string $lockownerid: id for the user who is locking the record
    * @returns boolean When edit is unlocked
    */
	public function unlockEdit($tableName, $rowid, $lockownerid)
	{
		// Get the id of the editlock edit
		$id = $this->_getpkID($tableName, $rowid, $lockownerid);

		// If found, we delete
		if($id){
			return $this->delete('id', $id);
		}

		// Not found, return 0 for an error
		return FALSE;
	}

	/**
    * Method that forces an unlocks of a edit.  This should only be used
	* in a case where the system cannot verify or identify the owner of the lock
	* and the edit needs to be unlocked  
    *
    * @access public
	* @param string $tableName: The name of the table the row is in
	* @param string $rowid: id for the record in question
    * @returns boolean When edit is unlocked
    */
	public function forceEditUnlock($tableName, $rowid)
	{
		$row = $this->isEditLocked($tableName, $rowid);
		return $this->delete('id', $row['id']);
        //return $this->delete('rowid', $rowid);
	}

	/**
	* Method for getting the time the lockout was set.  Useful for determining on whether
	* a lock should be removed due to timeout.
	*
	* @access public
	* @param string $tableName: The name of the table the row is in
	* @param string $rowid: id for the record in question
	* @returns string timestamp of when the record was locked
	*/
	public function timeLockWasSet($tableName, $rowid)
	{
		$record = $this->isEditLocked($tableName, $rowid);
		//$record = $this->getRow('rowid', $rowid);
		return $record['datelocked'];
	}

	/**
	* Method to set a timeout of the lock so it isn't marked as such forever 
	* (in case the user goes to another page or leaves it open forever)
	*
	* @access public
	* @param $rowid id for the record that is locked
	* @param $lockownerid userid for the person who's locked the row
	* @param $seconds time (in seconds) when the lock should be released
	*/
	/** TODO: Figure out how to make this happen
	public function setUnlockTimeout($rowid, $lockownerid, $seconds = '3600')
	{
		
	}
	*/

	/**
    * Method that returns the pkid for the record of the edit that is to be unlocked 
    *
    * @access public
	* @param string $tableName: The name of the table the row is in
	* @param string $rowid: id for the record in question
	* @param string $lockownerid: id for the user who is locking the record
    * @returns int pkid for the locked edit entry or 0 if none is found
    */
	private function _getpkID($tableName, $rowid, $lockownerid)
	{
/*
		//Populate the fields
	  	$fields = array();
		$fields['rowid'] = $rowid;
		$fields['lockownerid'] = $lockownerid;

		// Execute the query to get the id of the editlock edit
		$result =  $this->getArray('SELECT id FROM tbl_edit_lock WHERE rowid = \'' . $rowid . '\' AND lockownerid = ' . $lockownerid);
*/
        $sql = "SELECT * FROM ".$this->table;
        $sql .= " WHERE table_name = '".$tableName."'";
        $sql .= " AND rowid = '".$rowid."'";
        $sql .= " AND lockownerid = '".$lockownerid."'";
        $result = $this->getArray($sql);
		// If there is a result, we return the id
		if($result){
			return $result[0]['id'];
		}
		// else return false
		return FALSE;
	}
}    
?>