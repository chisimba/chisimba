<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }

/**
 * Model class for the table tbl_loggedinusers
 * @author Jeremy O'Connor
 * @version $Id: dump_tpl.php 21037 2011-03-29 13:20:11Z joconnor $
 * @copyright (C) 2009, 2011 AVOIR
 */

class siteload extends dbTable
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
    * Return count of users logged in in the last hour.
	* @return int Count
    */
	public function getCountLoggedIn()
	{

		$sql = "SELECT COUNT(id) AS cnt FROM {$this->_tableName} WHERE whenLoggedIn > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        $rs = $this->getArray($sql);
	    if (FALSE === $rs) {
	        return 0;
	    } else {
    	    return (int)$rs[0]['cnt'];
	    }
	    /*
        elseif (
	        is_array($rs)
    	    && isset($rs[0])
	        && is_array($rs[0])
    	    && isset($rs[0]['cnt'])
	    )
        */
	}
    /**
    * Return count of users active in the last 5 minutes.
	* @return int Count
    */
	public function getCountActive($offset = 5, $length = 5)
	{
	    $start = $offset;
	    $finish = $offset - $length;
		$sql = "SELECT COUNT(id) AS cnt FROM {$this->_tableName} WHERE whenlastactive > DATE_SUB(NOW(), INTERVAL {$start} MINUTE) AND whenlastactive <= DATE_SUB(NOW(), INTERVAL {$finish} MINUTE)";
        $rs = $this->getArray($sql);
	    if (FALSE === $rs) {
	        return 0;
	    } else {
    	    return (int)$rs[0]['cnt'];
	    }
	    /*
        elseif (
	        is_array($rs)
    	    && isset($rs[0])
	        && is_array($rs[0])
    	    && isset($rs[0]['cnt'])
	    )
        */
	}
}
?>