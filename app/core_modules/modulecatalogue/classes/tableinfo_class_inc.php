<?php
/*
 * Class for manipulating tables for moduleadmin
 * @author Paul Scott
 * @version $Id$
 * @copyright 2004
 * @license GNU GPL
 */
class tableinfo extends dbtable
{
    
    public function init()
    {
        parent::init('tbl_users');
    }
    
   /**
    * This is a method to return a list of the tables in the database.
    * 
    * @access public 
    * @param void
    * @returns array $list
    */
    public function tablelist()
    {
    	return $this->listDbTables();
    }
    
    /**
    * This is a method to check if a given table's name is in an array - by default the array used is class variable $tables
    * 
    * @access public
    * @param string $name
    * @param array $list (optional)
    * @returns TRUE or FALSE
    */
    public function checktable($name,$list=NULL)
    {
        if (is_null($list)){
            $list = $this->tablelist();
        }
        return in_array($name, $list);
    }
}
?>