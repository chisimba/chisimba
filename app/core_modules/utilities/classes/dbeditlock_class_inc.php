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
 * @copyright GNU, 2007
 */
class dbeditlock extends dbTable 
{
	/**
     * Method to define the table
     * 
     * @access public
     */
    public function init()
    {
        parent::init('tbl_edit_lock');
    } 
  
    /**
    * Method that returns whether a edit is locked or not 
    *
    * @access public
    * @returns boolean Whether edit is locked or not
    */
    public function isEditLocked ($rowid)
    {
		// if a record exists then the edit is locked
    	return $this->valueExists('rowid', $rowid);
	}

	/**
	* Method that returns whether the user has locked the record or not.  A return of FALSE
	* does not mean the file isn't locked, it just means that the user does not have a lock on the file.
	* For accuracy, the user should use isEditLocked as well.
	* 
	* @access public
	* @param $rowid id for the record in question
	* @param $userid id for the user we're checking for
	* @returns boolean TRUE if the user owns the lock, FALSE if they don't or the file isn't locked
	*/
	public function isEditLockedByUser($rowid, $userid)
	{
		// we just try to get the pkid, if it doesn't exist then the user doesn't have a lock on the record
		if($this->_getpkID($rowid, $userid))
			return 1;
		
		//else
		return 0;
	}

	/**
    * Method that locks a edit 
    *
    * @access public
    * @returns boolean TRUE or FALSE depending if edit was successfully 'locked' 
    */
	public function lockEdit ($rowid, $lockownerid)
	{
		// If the user already has the record locked then just update the time
		if($this->isEditLockedByUser($rowid, $lockownerid))
		{
			$id = $this->_getpkID($rowid, $lockownerid);
	  		$fields = array();
			$fields['datelocked'] = $this->now();
			return $this->update('id', $id, $fields);
		}

		// If the user does not own the record, but its still locked, return false
		if($this->isEditLocked($rowid))
		{
			return 0;
		}

		// else record the lock
	  	$fields = array();
    	$fields['rowid'] = $rowid;
    	$fields['lockownerid'] = $lockownerid;
		$fields['datelocked'] = $this->now();

		// Run insert
    	return $this->insert($fields);
	}
	
	/**
    * Method that unlocks a edit  
    *
    * @access public
    * @returns boolean When edit is unlocked
    */
	public function unlockEdit($rowid, $lockownerid)
	{
		// Get the id of the editlock edit
		$id = $this->_getpkID($rowid, $lockownerid);

		// If found, we delete
		if($id){
			return $this->delete('id', $id);
		}

		// Not found, return 0 for an error
		return 0;
	}

	/**
    * Method that forces an unlocks of a edit.  This should only be used
	* in a case where the system cannot verify or identify the owner of the lock
	* and the edit needs to be unlocked  
    *
    * @access public
    * @returns boolean When edit is unlocked
    */
	public function forceEditUnlock($rowid)
	{
		return $this->delete('rowid', $rowid);
	}

	/**
	* Method for getting the time the lockout was set.  Useful for determining on whether
	* a lock should be removed due to timeout.
	*
	* @access public
	* @param $rowid id for the record we're checking for a lock
	* @returns string timestamp of when the record was locked
	*/
	public function timeLockWasSet($rowid)
	{
		$record = $this->getRow('rowid', $rowid);
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
    * @returns int pkid for the locked edit entry or 0 if none is found
    */
	private function _getpkID($rowid, $lockownerid)
	{
		//Populate the fields
	  	$fields = array();
		$fields['rowid'] = $rowid;
		$fields['lockownerid'] = $lockownerid;

		// Execute the query to get the id of the editlock edit
		$result =  $this->getArray('SELECT id FROM tbl_edit_lock WHERE rowid = \'' . $rowid . '\' AND lockownerid = ' . $lockownerid);

		// If there is a result, we return the id
		if($result){
			return $result[0]['id'];
		}

		// else return false
		return 0;
	}
}    

?>
