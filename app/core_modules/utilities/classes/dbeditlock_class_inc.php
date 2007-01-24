<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to keep track of locked edits for the Chisimba app.
 * Please note, this is NOT real edit locking but merely a way to keep track
 * at the application level when an user is working with a certain record in the database.
 * AKA, that means if a edit is set as locked Chisimba could still make changes to the edit, 
 * it does not provide protection at the database level
 * @author Ryan Whitney, ryan@greenlikeme.org 
 */
class dbeditlock extends dbTable 
{

	// Personal debugger - TODO: REMOVE BEFORE COMMITTING FOR PRODUCTION!
	public function dbg($sErr){
		$handle = fopen('error_log/my_debug.log', 'a');
		fwrite($handle, "[" . strftime("%b %d %Y %H:%M:%S") ."]/classes/wwPage :: " . $sErr . "\n");
		fclose($handle);
	}
	

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
    * Method that locks a edit 
    *
    * @access public
    * @returns boolean TRUE or FALSE depending if edit was successfully 'locked' 
    */
	public function lockEdit ($rowid, $lockownerid)
	{
		// Create the fields to be populated
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

		$this->dbg('id = ' . $id);
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

		$this->dbg('result from _getpkID = ' . var_export($result, 1));
		// If there is a result, we return the id
		if($result){
			return $result[0]['id'];
		}

		// else return false
		return 0;
	}
}    

?>
