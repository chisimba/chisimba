<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Data access (db model) Class for the karma module
 *
 * This is a database model class for the karma module. All database transaactions will go through
 * this class. This class is derived from the top level dbTable superclass in the framework core.
 *
 * @author Brent van Rensburg
 * @filesource
 * @copyright AVOIR
 * @package tagging
 * @category chisimba
 * @access public
 */

class dbkarma extends dbTable
{

	/**
	 * Standard init function - Class Constructor
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$this->objLanguage = $this->getObject("language", "language");
		//Set the table in the parent class
		parent::init('tbl_karmapoints');
	}
	
	/**
	 * Method to add an arbitary number of points to a users point collection
	 * 
	 * @param string $userid
	 * @param integer $points
	 * @param string $contribution
	 */
	public function addPoints($userid, $contribution, $points)
	{
		$this->_changeTable('tbl_karmapoints');
		// check first that the userid exists
		$check = $this->getAll("WHERE userid = '$userid' AND contribution = '$contribution'");
		if(empty($check))
		{
			return $this->insert(array('points' => $points, 'userid' => $userid, 'contribution' => $contribution), 'tbl_karmapoints');
		}
		else {
			$points = $check[0]['points'] + $points;
			return $this->update('id', $check[0]['id'], array('points' => $points, 'userid' => $userid, 'contribution' => $contribution), 'tbl_karmapoints');
		}
	}
	

	/**
	 * Method to dynamically switch tables
	 *
	 * @param string $table
	 * @return boolean
	 * @access private
	 */
	private function _changeTable($table)
	{
		try {
			parent::init($table);
			return TRUE;
		}
		catch (customException $e)
		{
			customException::cleanUp();
			return FALSE;
		}
	}


	/**
	 * Method to return all the usernames of users for the dropdown list
	 *
	 */
	public function getNames()
	{
		$this->_changeTable('tbl_karmapoints');
		$sql = "SELECT DISTINCT userid FROM tbl_karmapoints";
		return $this->getArray($sql);
    	}


	/**
	 * Method to users to the table
	 *
	 * @param string $userid
	 * @param string $contribution
	 */
	public function getContribution($userid, $contribution)
	{
		$this->_changeTable('tbl_karmapoints');
		$sql = "WHERE contribution ='$contribution' AND userid ='$userid'";
		return $this->getAll($sql);
    	}
}
?>