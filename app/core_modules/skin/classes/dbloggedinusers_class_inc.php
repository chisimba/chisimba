<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
* Model class for the table tbl_loggedinusers
* @author Jeremy O'Connor
* @copyright 2009 University of the Western Cape
*/
class dbloggedinusers extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_loggedinusers');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

    /**
    * Return number of rows in table.
	* @return integer number of rows in table
    */

	public function count()
	{
    	return $this->getRecordCount();
    	/*
		$sql =
        "SELECT COUNT(id) AS cnt FROM {$this->_tableName}";
        $rs = $this->getArray($sql);
        //var_dump($rs);
		return $rs[0]['cnt'];
		*/
	}
}
?>