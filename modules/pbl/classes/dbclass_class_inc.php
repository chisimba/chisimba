<?php
/**
* Class class
* @package pbl
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class to modify the delete and update methods in dbtable
 *
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 0.9
 */

class dbClass extends dbTable 
{
    /**
    * @var string $table The database table 
    * @access private
    */
    private $table = 'tbl_pbl_cases';
    
    /**
     * Constructor method to define the default table
     */
    public function init()
    {
        parent::init('tbl_pbl_cases');
    }

    /**
     * Method to delete a record from the table.
     *
     * @param string $filter the filter on the delete
     * @param string $tablename the table if different from the default table
     */
    public function delete($filter, $tablename = '')
    {
        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE ";
        $result = $this->getArray($sql);

        if($result)
            $this->delete('id',$result[0]['id']);
    }

    /**
     * Method to update an existing record in the table.
     *
     * @param array $fields The record as an associative array containing field names as keys and field values as values.
     * @param string $filter The filter on the request
     * @param string $tablename The name of the table to update, if not the default (optional)
     * @return TRUE |FALSE TRUE on success, FALSE on failure
     */
    public function update($fields, $filter, $tablename = '')
    {
        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE ";
        $result = $this->getArray($sql);

        if($result)
            $this->update('id',$result[0]['id'],$fields);
    }
}

?>