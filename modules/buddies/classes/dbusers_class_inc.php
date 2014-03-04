<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_users
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbUsers extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init() 
    {
        parent::init('tbl_users');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

    /**
    * Return all records
	* @return array All users
    */
	public function listAll()
	{
		$sql = "SELECT userId, firstName, surname, emailAddress FROM tbl_users"
		. " ORDER BY firstName, surname";
		return $this->getArray($sql);
	}

    /**
    * Return selected records
	* @param string $how What to search on
	* @param string $searchField WHat to search for
	* @return array Selected users
    */
	public function listSelected($how, $searchField)
	{
		$sql = "SELECT userId, firstName, surname, emailAddress FROM tbl_users"
		. " WHERE " . $how . " LIKE '" . $searchField . "%'"
		. " ORDER BY firstName, surname";
		return $this->getArray($sql);
	}
}
?>