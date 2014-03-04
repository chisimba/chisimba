<?php
/**
* Class dbCases extends dbTable.
* @author Fernando Martinez
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class for providing access to the cases table in the database.
 *
 * The table contains information about the PBL cases.
 *
 * @author Fernando Martinez
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class dbCases extends dbTable
{
    /**
    * @var string $table The name of the database table being accessed
    * @access private
    */
    private $table = 'tbl_pbl_cases';
    
    /**
     * Constructor method to define the table
     */
    public function init()
    {
        parent::init('tbl_pbl_cases');
    }

    /**
     * Method to add a new case to the database.
     *
     * @param string $name The name of the case
     * @param string $entry The id of first scene in the scenes table
     * @param string $owner The lecturer installing case; defaults to admin
     * @param string $context The context in which the case is used
     * @return string $id The id of the new case.
     */
    public function addCase($name, $entry, $owner = '1', $context = 'lobby')
    {
        $fields = array();
        $fields['name'] = $name;
        $fields['entry_point'] = $entry;
        $fields['context'] = $context;
        $fields['owner'] = $owner;
        $fields['updated'] = date('Y-m-d H:i');
        if($this->insert($fields)){
            $id = $this->getLastInsertId();
            return $id;
        }
        return FALSE;
    }

    /**
    * Method to update a case
    * @return
    */
    public function updateCase($id, $fields)
    {
        $this->update('id', $id, $fields);
    }

    /**
    * Method to delete a case from the database
    * @return
    */
    public function deleteCase($id)
    {
        $this->delete('id',$id);
    }

    /**
     * Method to get the case id, owner and entry point into case where name = $name.
     *
     * @param string $name The name of the case
     * @return array $rows The case id, entry point and owner
     */
    public function getId($name)
    {
        $sql = "select id,entry_point,owner from tbl_pbl_cases where name='" . $name . "'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        return $rows;
    }

    /**
     * Method to get all case information.
     *
     * @param string $filter The filter to be applied
     * @return array $case A list of cases and their details
     */
    public function getCases($filter = NULL)
    {
        $case = array();
        $sql = 'select id,name,context,owner from tbl_pbl_cases';
        if (!empty($filter)){
            $sql .= ' where ' . $filter;
        }
        
        $data = $this->getArray($sql);
        return $data;
    }

    /**
     * Method to get the name and entry-point of a case where id = $id.
     *
     * @param string $id The id of the case
     * @return array $case The case name and entry point
     */
    public function getEntry($id)
    {
        $case = array();
        $sql = "select name,entry_point from tbl_pbl_cases where id='$id'";
        $rows = $this->getArray($sql);
        if (!$rows)
            return FALSE;
        $case['name'] = $rows[0]['name'];
        $case['entry_point'] = $rows[0]['entry_point'];
        return $case;
    }
}

?>