<?php 
// check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	    die("You cannot view this page directly");
} 

$this->loadClass('dbeditlock', 'utilities');
/**
* Class wikipagelock: Extends the dbeditlock module for use in the wiki 
* 
* @author Ryan Whitney, Kevin Cyster
*/

class wikipagelock extends dbeditlock 
{
    /**
    * @var string $table The name of the database table to be affected
    * @access private
    */
    private $table;

	/** 
	* Init method
	*
	*/
	public function init()
	{
		parent::init();
		$this->table = 'tbl_wiki_pages';
		$this->objDateTime = $this->getObject("dateandtime", "utilities");	
		$this->objLanguage = $this->getObject('language', 'language');
	}

	/** 
	* Method returns whether the user has rights to edit the given wiki page 
	*
	* @access public
	* @param String wiki pageid who's edit we're checking
	* @param String user id
	* @return boolean 
	*/
	public function canUserEdit($id, $userid)
	{

		// if the file isn't locked, then  user can edit!
		if(!$this->isEditLocked($this->table, $id))
		{
			return TRUE;
		}
		// If the user is the one who's locked it, they can edit it
		else if($this->isEditLockedByUser($this->table, $id, $userid))
		{ 
			return TRUE;
		} 
		// If the system is locked but its been more than one minute, then give user the right to edit
		else if($this->isEditLocked($this->table, $id) && $this->lockTimedOut($id))
		{
			return TRUE;
		} 

		// All checks failed, the user cannot edit the file
		return FALSE;
	}

	/**
	* Method to determine if their has been a timeout.  Default is one minute
	* 
	* @access public
	* @param String pageid of the wiki page in question
	* @return boolean
	*/
	public function lockTimedOut($id)
	{
		if($this->objDateTime->getDateDifference($this->objDateTime->sqlToUnixTime($this->timeLockWasSet($this->table, $id)),time(), 'm', 'unixts') > 1){
			return TRUE;
        }
        return FALSE;
	}
}
?>