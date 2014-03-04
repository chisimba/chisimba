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
class siteload extends dbtable
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
    * Return all records
	* @return array All users
    */
	public function Count()
	{
		$sql =
        "SELECT COUNT(id) AS CNT FROM {$this->_tableName}";
        $rs = $this->getArray($sql);
//	    echo '<pre>';
//	    var_dump($rs);
//	    echo '</pre>';
		return $rs[0]['cnt'];
	}
}
?>